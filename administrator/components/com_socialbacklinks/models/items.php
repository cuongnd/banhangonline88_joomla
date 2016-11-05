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
 * Models for items
 */
class SBModelsItems extends SBModelsBase
{
	/**
	 * @see SBModelsBase::getList()
	 */
	public function getList( $where = array(), $limitstart = 0, $limit = 0 )
	{
		if ( $plugin = $this->plugin ) {
			$this->ignore( array(
				'plugin',
				'ids'
			) );
			if ( !$items = $this->ids ) {
				$items = $plugin->getOption( 'items' );
				$items = empty( $items ) ? array( ) : $items;
			}

			if ( count( $items ) ) {
				$query = $plugin->getItemsDetailed();
				$this->select( $query->select );
				$this->join( $query->join );
				
				$where[] = 'tbl.`' . $plugin->get( 'items_table.id' ) . '` IN (' . implode( ', ', $items ) . ')';
				
				$ret = array();
				try
				{
					$ret = parent::getList( $where, $limitstart, $limit );
				}
				catch(Exception $e)
				{
				}
				
				return $ret;
			}
		}
		return array( );
	}

	/**
	 * Returns the list of content plugin items by the category
	 * @param int Limitstart
	 * @param int Limit
	 * @return string
	 */
	public function getListByCategory( $limitstart = 0, $limit = 0 )
	{
		if ( ($plugin = $this->plugin) && ($category = $this->category) && ($level = $this->level) ) {
			$this->ignore( array(
				'plugin',
				'category',
				'level',
				'filter'
			) );

			$query = $plugin->getCategoryItems( $category, $level );
			if ( $filter = $this->filter ) {
				$query .= 'AND tbl.`' . $plugin->get( 'items_table.title' ) . "` LIKE '$filter%';";
			}
			$this->select( $query );

			$ret = array();
			try
			{
				$ret = parent::getList( array( ), $limitstart, $limit );
			}
			catch(Exception $e)
			{
			}
			
			return $ret;
		}
		return array( );
	}

	/**
	 * Returns the list of items newer than given date
	 * @return array
	 */
	public function getNewList( $last_sync )
	{
		if ( !($plugin = $this->plugin) || (!$plugin->sync_updated && !$plugin->sync_published) ) {
			return array( );
		}

		$this->ignore( array( 'plugin' ) );

		$nowdate = $this->_db->quote( SBHelpersSync::convertDate( )->toSql() );
		$nulldate = $this->_db->quote( $this->_db->getNullDate( ) );
		$last_sync = is_null( $last_sync ) ? $nulldate : $this->_db->quote( $last_sync->toSql( ) );
		$where = $plugin->getNewItemsConditions( array( 'nowdate' => $nowdate, 'last_sync' => $last_sync, 'nulldate' => $nulldate ) );

		$query = $plugin->getItemsDetailed();
		
		$this->select( $query->select );
		$this->join( @$query->join );
		
		$ret = array();
		try
		{
			$ret = parent::getList( $where );
		}
		catch(Exception $e)
		{
		}
		
		return $ret;
	}

}
