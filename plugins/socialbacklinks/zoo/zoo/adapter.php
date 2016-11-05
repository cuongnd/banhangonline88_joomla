<?php
/**
 * SocialBacklinks ZOO plugin
 *
 * We developed this code with our hearts and passion.
 * We hope you found it useful, easy to understand and change.
 * Otherwise, please feel free to contact us at contact@joomunited.com
 *
 * @package 	Social Backlinks
 * @copyright 	Copyright (C) 2012 JoomUnited (http://www.joomunited.com). All rights reserved.
 * @license 	GNU General Public License version 2 or later; http://www.gnu.org/licenses/gpl-2.0.html
 */
 
// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

/**
 * Plugin for default ZOO content
 */
class PlgSBZooAdapter extends SBPluginsContent
{
	/**
	 * The fields map
	 * @var array
	 */
	protected $_map = array(
		'items_table' => array( '__table' => '#__zoo_item', 'title'=>'name' ),
		'categories_table' => array( '__table' => '#__zoo_category', 'title'=>'name' )
	);

	/**
	 * Constructor
	 * @param  Jplugin Object that has registered current plugin
	 * @param  array  The list of plugin options
	 * @return void
	 */
	public function __construct( $caller, $options = array() )
	{
		$default = array(
			'add_plugin_content_items_js_file' => true,
			'use_plugin_category_row_tmpl' => true, 
			'sync_desc' => false
		);

		$options = array_merge( $default, $options );
		parent::__construct( $caller, $options );
	}

	/**
	 * @see SBAdaptersPlugin::getAlias()
	 */
	public function getAlias( )
	{
		return 'zoo';
	}

	/**
	 * @see SBPluginsContentsInterface::getNewItemsConditions()
	 */
	public function getNewItemsConditions( $settings )
	{
		$where = parent::getNewItemsConditions( $settings );
		$where[] = 'tbl.`state` = 1';
		
		if ( isset( $where['selected_content'] ) ) {
			$condition = '';

			if ( count( $this->items ) ) {
				$condition = ' tbl.`' . $this->get( 'items_table.id' ) . '` IN (' . implode( ', ', $this->items ) . ') ';
			}
			if ( count( $this->categories ) ) {
				$condition .= !empty( $condition ) ? 'OR ' : '';
				$condition .= ' EXISTS( SELECT null FROM `#__zoo_category_item` cati'
					. ' WHERE cati.`item_id` = tbl.`' . $this->get( 'items_table.id' ) . '`'
					. ' AND cati.`category_id` IN (' . implode( ', ', $this->categories ) . ') ) ';
			}
			if ( !empty( $condition ) ) {
				$where['selected_content'] = "$condition";
			}
		}
		return $where;
	}

	/**
	 * @see SBPluginsContentsInterface::getItemRoute()
	 */
	public function getItemRoute( $item )
	{
		return 'index.php?option=com_zoo&task=item&item_id=' . $item->id;
	}

	/**
	 * @see SBPluginsContentsInterface::getTreeOfCategories()
	 */
	public function getTreeOfCategories( )
	{
		$db = JFactory::getDBO( );
		$query = 'SELECT cat.`id`, cat.`name`, cat.`parent`, app.`id` AS app_id, app.`name` AS app_name' 
			. ' FROM `#__zoo_category` AS cat' . ' JOIN `#__zoo_application` AS app ON app.`id` = cat.`application_id`' 
			. ' WHERE cat.`published` = 1'
			. ' ORDER BY app.`id` ASC, cat.`ordering`';
		$db->setQuery( $query );
		$categories = $db->loadObjectList( );
		
		$app_id = array_unique( JArrayHelper::getColumn( $categories, 'app_id' ) );
		$app_name = array_unique( JArrayHelper::getColumn( $categories, 'app_name' ) );
		$apps = ( !empty($app_id) && !empty($app_name) ) ? array_combine( $app_id, $app_name ) : array();

		$result = array( array(
				'_type' => 'category',
				'title' => 'SB_UNCATEGORISED',
				'id' => 0,
				'_children' => array( )
			) );
		foreach ($apps as $id => $name) {
			$item = array(
				'_type' => null,
				'title' => $name,
				'id' => $id,
				'_children' => array( )
			);
			
			$reg = array( );
			$children = array();
			do {
				$continue = false;
				foreach ($categories as $k => $category) {
					if ( $category->app_id == $id ) {
						$cat = array(
							'title' => $category->name,
							'id' => $category->id,
							'_children' => array( )
						);
						if ( $category->parent ) {
							//subcategories
							if ( isset( $reg[$category->parent] ) ) {
								//parent exists
								$reg[$category->id] = &$cat['_children'];
								$reg[$category->parent][] = $cat;
								unset( $categories[$k] );
							}
							else {
								continue;
							}
						}
						else {
							//top category
							$children[$category->id] = $cat;
							$reg[$category->id] = &$children[$category->id]['_children'];
							unset( $categories[$k] );
						}
						$continue = true;
					}
				}
			} while( $continue );
			
			$item['_children'] = $children;
			$result[] = $item;
		}

		return $result;
	}

	/**
	 * @see SBPluginsContentsInterface::getCategoryItems()
	 */
	public function getCategoryItems( $category_id, $level )
	{
		$query = 'SELECT tbl.`' . $this->get( 'items_table.title' ) . '` AS title, tbl.`' . $this->get( 'items_table.id' ) . '` AS id'
			. ' FROM ' . $this->get( 'items_table.__table' ) . ' AS tbl'
			. ' INNER JOIN `#__zoo_category_item` cati ON cati.`item_id` = tbl.`' . $this->get( 'items_table.id' ) . '`'
			. " WHERE cati.`category_id` = '$category_id'";
		return $query;
	}
	
	/**
	 * @see SBPluginsContentsInterface::getItemsDetailed()
	 */
	public function getItemsDetailed()
	{
		$query = new stdClass();
		
		$subquery = "(SELECT GROUP_CONCAT(DISTINCT cat.`" . $this->get( 'categories_table.title' ) . "` ORDER BY cat.`" . $this->get( 'categories_table.title' ) . "` DESC SEPARATOR ', ')"
			. ' FROM `' . $this->get( 'categories_table.__table' ) . '` AS cat'
			. ' INNER JOIN `#__zoo_category_item` AS cati ON cati.`category_id` = cat.`' . $this->get( 'categories_table.id' ) . '`'
			. ' WHERE cati.`item_id` = tbl.`' . $this->get( 'items_table.id' ) . '`'
			. ' GROUP BY cati.`item_id`)';
		
		$query->select = 'SELECT tbl.*, tbl.`' . $this->get( 'items_table.title' ) . '` AS title, ' . $subquery . ' AS cctitle, user.`name` AS author'
			. ' FROM `' . $this->get( 'items_table.__table' ) . '` AS tbl';
		$query->join = array( 'LEFT JOIN `#__users` AS user ON user.`id` = tbl.`' . $this->get( 'items_table.created_by' ) . '`' );
		
		return $query;
	}
	
}
