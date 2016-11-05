<?php
/**
 * SocialBacklinks basic class for content plugins
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
 * Basic class for content plugin
 * @abstract
 */
abstract class SBPluginsContent extends SBPluginsAbstract implements SBPluginsContentsInterface
{
	protected $_map = array( );

	private $_map_default = array(
		'items_table' => array(
			'__table' => null,
			'id' => 'id',
			'title' => 'title',
			'content' => 'fulltext',
			'created' => 'created',
			'created_by' => 'created_by',
			'modified' => 'modified',
			'modified_by' => 'modified_by',
			'publish_up' => 'publish_up',
			'publish_down' => 'publish_down',
			'catid' => 'catid'
		),
		'categories_table' => array(
			'__table' => null,
			'id' => 'id',
			'title' => 'title'
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
			'sync_availability' => array(
				'sync_when_publish' => array( 'enabled' => 1 ),
				'sync_when_update' => array( 'enabled' => 1 )
			),
			'icons' => array(
				'active' => null,
				'incative' => null
			),
			'selected_content' => 0,
			'sync_published' => 1,
			'sync_updated' => 0,
			'sync_queue' => 1,
			'items' => array( ),
			'categories' => array( ),
			'add_plugin_content_items_js_file' => false,
			'use_plugin_category_row_tmpl' => false,
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
			),
			'sync_desc' => true
		);

		$options = array_merge( $default, $options );
		parent::__construct( $caller, $options );
	}

	/**
	 * Returns the map value
	 * @param  string Fields of a map to be returned
	 * @return string
	 */
	public function get( $property )
	{
		if ( strpos( $property, '.' ) !== false ) {
			list( $top, $key ) = explode( '.', $property );
			return isset( $this->_map[$top] ) && isset( $this->_map[$top][$key] ) ? $this->_map[$top][$key] : (isset( $this->_map_default[$top] ) && isset( $this->_map_default[$top][$key] ) ? $this->_map_default[$top][$key] : null);
		}
		else {
			return isset( $this->_map[$property] ) ? $this->_map[$property] : (isset( $this->_map_default[$property] ) ? $this->_map_default[$property] : null);
		}
	}

	/**
	 * @see SBPluginsContentsInterface::getCategoryItems()
	 */
	public function getCategoryItems( $category_id, $level )
	{
		$query = 'SELECT tbl.`' . $this->get( 'items_table.title' ) . '` AS title, tbl.`' . $this->get( 'items_table.id' ) . '` AS id FROM ' . $this->get( 'items_table.__table' ) . ' AS tbl WHERE tbl.`' . $this->get( 'items_table.catid' ) . "`='$category_id'";
		return $query;
	}

	/**
	 * @see SBPluginsContentsInterface::getItemsDetailed()
	 */
	public function getItemsDetailed()
	{
		$query = new stdClass();

		$query->select = 'SELECT tbl.*, cat.`' . $this->get( 'categories_table.title' ) . '` AS cctitle, user.`name` AS author FROM `' . $this->get( 'items_table.__table' ) . '` AS tbl';
		$query->join = array(
			'LEFT JOIN `' . $this->get( 'categories_table.__table' ) . '` AS cat ON cat.`id`=tbl.`' . $this->get( 'items_table.catid' ) . '`',
			'LEFT JOIN `#__users` AS user ON user.`id`=tbl.`' . $this->get( 'items_table.created_by' ) . '`'
		);

		return $query;
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
		
		$where['publish_up'] = 'tbl.`' . $this->get( 'items_table.publish_up' ) . '`<=' . $nowdate;
		$where['publish_down'] = '(tbl.`' . $this->get( 'items_table.publish_down' ) . "` = $nulldate OR tbl.`" . $this->get( 'items_table.publish_down' ) . "`> $nowdate)";

		if ( $this->sync_updated ) {
			$where[] = '(tbl.`' . $this->get( 'items_table.modified' ) . "` >= $last_sync OR tbl.`" . $this->get( 'items_table.publish_up' ) . "` >= $last_sync)";
		}
		else {
			$where[] = 'tbl.`' . $this->get( 'items_table.publish_up' ) . "` >= $last_sync";
		}

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
		
		return $where;
	}

}
