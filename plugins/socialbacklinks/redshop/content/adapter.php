<?php
/**
 * SocialBacklinks Redshop plugin
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
 * Plugin for default Redshop content
 */
class PlgSBRedshopAdapter extends SBPluginsContent
{
	/**
	 * The fields map
	 * @var array
	 */
	protected $_map = array(
	'items_table' => array( 
		'__table' => '#__redshop_product',
		'id' => 'product_id',
		'title' => 'product_name', 
		'content' => 'product_desc',
		'created' => 'publish_date',
		'created_by' => null,
		'modified' => 'update_date',
		'modified_by' => null,
		'publish_up' => null,
		'publish_down' => null,
		),
	'categories_table' => array( 
		'__table' => '#__redshop_category' ,
		'id' => 'category_id',
		'title' => 'category_name'
		)
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
			'sync_desc' => false,
			'fields_to_show_in_query_list' => array(
					array(
						'title' => 'Title',
						'field' => 'title',
						'class' => 'first'
					),
					array(
						'title' => 'Categories',
						'field' => 'cctitle',
						'width' => '140'
					),
					array(
						'title' => 'Modification',
						'field' => 'author',
						'width' => '40'
					),
					array(
						'title' => 'Creation',
						'field' => 'product_created',
						'width' => '40'
					),
					array(
						'title' => 'ID',
						'field' => 'product_id',
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
		return 'redshop';
	}

	/**
	 * @see SBPluginsContentsInterface::getNewItemsConditions()
	 */
	public function getNewItemsConditions( $settings )
	{
		$where = array();
		$nowdate = $settings['nowdate'];
		$last_sync = $settings['last_sync'];
		$nulldate = $settings['nulldate'];

		// Redshop update_date has an ON UPDATE CURRENT_TIMESTAMP clause, we means the update_date gets update every time someone views the product from the
		// front-end (because of the "views" field in the db). Therefore we can't rely on the update_date field at all.
		//if ( $this->sync_updated ) {
			//$where[] = '( TIMEDIFF(CONVERT_TZ((tbl.`' . $this->get( 'items_table.modified' ) . "`), @@session.time_zone, '+00:00'), $last_sync) > 0 OR TIMEDIFF(CONVERT_TZ(tbl.`" . $this->get( 'items_table.created' ) . "`, @@session.time_zone, '+00:00'), $last_sync) > 0)";
		//}
		//else {
			$where[] = "(TIMEDIFF(CONVERT_TZ(tbl.`" . $this->get( 'items_table.created' ) . "`, @@session.time_zone, '+00:00'), $last_sync) > 0)";
		//}

		if ( $this->selected_content ) {
			$condition = '';

			if ( count( $this->items ) ) {
				$condition = ' tbl.`' . $this->get( 'items_table.id' ) . '` IN (' . implode( ', ', $this->items ) . ') ';
			}
			if ( count( $this->categories ) ) {
				$condition .= !empty( $condition ) ? 'OR ' : '';
				$condition .= ' tbl.`' . $this->get( 'items_table.catid' ) . '` IN (' . implode( ', ', $this->categories ) . ') ';
			}
			if ( !empty( $condition ) ) {
				$where['selected_content'] = "$condition";
			}
		}
		
		$where[] = 'tbl.`published` = 1';
		
		if ( isset( $where['selected_content'] ) ) {
			$condition = '';

			if ( count( $this->items ) ) {
				$condition = ' tbl.`' . $this->get( 'items_table.id' ) . '` IN (' . implode( ', ', $this->items ) . ') ';
			}
			if ( count( $this->categories ) ) {
				$condition .= !empty( $condition ) ? 'OR ' : '';
				$condition .= ' EXISTS( SELECT null FROM `#__redshop_product_category_xref` cati'
					. ' WHERE cati.`product_id` = tbl.`' . $this->get( 'items_table.id' ) . '`'
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
		$id = ($item->id) ? $item->id : $item->item_id;
		return 'index.php?option=com_redshop&view=product&pid=' . $id;
	}

	/**
	 * @see SBPluginsContentsInterface::getTreeOfCategories()
	 */
	public function getTreeOfCategories( )
	{
		$db = JFactory::getDBO ();
		$query = 'SELECT `category_id`, `category_name`, `category_parent_id`'
				. ' FROM #__redshop_category c'
				. ' LEFT JOIN #__redshop_category_xref cxref ON c.`category_id` = cxref.`category_child_id`'
				. ' WHERE c.`published` = 1'
				. ' ORDER BY `ordering`';
		$db->setQuery ($query);
		$cats = $db->loadObjectList ();
		
		$categories = array();
		foreach ($cats as $cat)
		{
			$categories[] = array(
				'_type' => 'category',
				'title' => $cat->category_name,
				'id' => $cat->category_id,
				'parent_id' => $cat->category_parent_id,
				'_hasChildren' => false,
				'_children' => array()
			);
		}

		$root = array(
				'_type' => 'category',
				'title' => 'SB_UNCATEGORISED',
				'id' => 0,
				'parent_id' => null,
				'_hasChildren' => false,
				'_children' => array( )
			);
		$this->assignChildren( $root, $categories );
		
		$result[] = $root;
		
		return $result;
	}

	/**
	 * Recursive function that uses pointers to get the Tree
	 */
	public function assignChildren( &$item, &$categories )
	{
		if ($item['_hasChildren'])
			return;
			
		$item['_hasChildren'] = true;
		foreach( $categories as &$category )
		{
			if ( $category['parent_id'] == $item['id'] )
			{
				$item['_children'][] = &$category;
				$this->assignChildren( $category, $categories );
			}
		}
	}

	/**
	 * @see SBPluginsContentsInterface::getCategoryItems()
	 */
	public function getCategoryItems( $category_id, $level )
	{
		$query = 'SELECT tbl.`' . $this->get( 'items_table.title' ) . '` AS title, tbl.`' . $this->get( 'items_table.id' ) . '` AS id'
			. ' FROM ' . $this->get( 'items_table.__table' ) . ' AS tbl'
			. ' INNER JOIN `#__redshop_product_category_xref` cati ON tbl.`product_id` = cati.`product_id`'
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
			. ' INNER JOIN `#__redshop_product_category_xref` pcxref ON cat.`category_id` = pcxref.`category_id`'
			. ' WHERE pcxref.`product_id` = tbl.`' . $this->get( 'items_table.id' ) . '`'
			. ' GROUP BY pcxref.`product_id`)';
		
		$query->select = 'SELECT tbl.`'. $this->get('items_table.id') .'` AS id, tbl.`' . $this->get('items_table.created') . '` AS created, tbl.`' . $this->get('items_table.modified') . '` AS author, tbl.`' . $this->get( 'items_table.title' ) . '` AS title, ' . $subquery . ' AS cctitle, \'\' AS rien'
			. ' FROM `' . $this->get( 'items_table.__table' ) . '` AS tbl';
		
		return $query;
	}
	
}
