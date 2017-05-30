<?php
/**
 * SocialBacklinks content plugin
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
 * Plugin for default Joomla content
 */
class PlgSBJoomlaAdapter extends SBPluginsContent
{
	/**
	 * The fields map
	 * @var array
	 */
	protected $_map = array(
		'items_table' => array( '__table' => '#__content' ),
		'categories_table' => array( '__table' => '#__categories' )
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
			'fields_to_show_in_query_list' => array(
				array(
					'title' => 'Title',
					'field' => 'title',
					'class' => 'first'
				),
				array(
					'title' => 'Category',
					'field' => 'cctitle',
					'width' => '100'
				),
				array(
					'title' => 'Author',
					'field' => 'author',
					'width' => '80'
				),
				array(
					'title' => 'Date',
					'field' => 'created',
					'width' => '40'
				),
				array(
					'title' => 'ID',
					'field' => 'id',
					'width' => '10'
				)
			) 
		);

		$options = array_merge( $default, $options );
		parent::__construct( $caller, $options );
	}

	/**
	 * @see SBAdaptersPlugin::getAlias()
	 */
	public function getAlias( )
	{
		return 'content';
	}

	/**
	 * @see SBPluginsContentsInterface::getNewItemsConditions()
	 */
	public function getNewItemsConditions( $settings )
	{
		$where = parent::getNewItemsConditions( $settings );
		$where[] = 'tbl.`state` = 1';
		return $where;
	}

	/**
	 * @see SBPluginsContentsInterface::getItemRoute()
	 */
	public function getItemRoute( $item )
	{
		JLoader::register( 'ContentHelperRoute', JPATH_ROOT . '/components/com_content/helpers/route.php' );
		return ContentHelperRoute::getArticleRoute( $item->id, $item->catid );
	}

	/**
	 * @see SBPluginsContentsInterface::getTreeOfCategories()
	 */
	public function getTreeOfCategories( )
	{
		$db = JFactory::getDBO( );
		$query = 'SELECT cat.`id`, cat.`title`, cat.level, cat.parent_id' 
			. ' FROM `#__categories` AS cat' 
			. ' WHERE cat.`parent_id` > 0'
			. ' AND cat.`extension` = ' . $db->quote( 'com_content' )
			. ' AND cat.`published` = 1'
			. ' ORDER BY cat.`lft` ASC';
		$db->setQuery( $query );
		$categories = $db->loadObjectList( );
		
		$result = array();
		$reg = array( );
		while (count( $categories )) {
			foreach ($categories as $k => $category) {
				if ( $category->parent_id == 1 )
					$category->parent_id = 0;
				$item = array(
					'title' => $category->title,
					'id' => $category->id,
					'_children' => array( )
				);
				if ( $category->parent_id != 0 ) {
					//subcategories
					if ( isset( $reg[$category->parent_id] ) ) {
						//parent exists
						$reg[$category->id] = &$item['_children'];
						$reg[$category->parent_id][] = $item;
						unset( $categories[$k] );
					}
					else {
						continue;
					}
				}
				else {
					//top category
					$result[$category->id] = $item;
					$reg[$category->id] = &$result[$category->id]['_children'];
					unset( $categories[$k] );
				}
			}
		}
		return $result;
	}
	
}
