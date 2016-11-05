<?php
/**
 * SocialBacklinks Histories model
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
 * SocialBacklinks Model Histories class, which manage history of posts messages into social networks
 */
class SBModelsHistories extends SBModelsBase
{
	/**
	 * @see SBModelsBase->_table
	 */
	protected $_table = 'history';

	/**
	 * Method to get the starting number of items for the data set.
	 * @return  integer  The starting number of items available in the data set.
	 */
	public function getStart( )
	{
		$start = $this->getState( 'limitstart', 0 );
		$limit = $this->getState( 'limit', 0 );
		$total = $this->getTotal( );
		if ( $start > $total - $limit ) {
			$start = max( 0, ( int )(ceil( $total / $limit ) - 1) * $limit );
		}

		if ( ($limit == 0) && ($start != 0) ) {
			$start = 0;
		}
		return $start;
	}

	/**
	 * @see SBModelsBase::getList()
	 */
	public function getList( $where = array(), $limitstart = 0, $limit = 0 )
	{
		if ( $plugin = $this->plugin ) {
			$this->extension( $plugin->getAlias( ) )->ignore( 'plugin' );
		}
		$periodicity = ( int )$this->getState( 'periodicity', 0 );
		$this->ignore( array('periodicity', 'task', 'last_id') );
		if ( $periodicity ) {
			$where[] = 'DATEDIFF( CURDATE(), `created` ) > ' . $this->_db->Quote( $periodicity );
		}

		$last_id = ( int ) $this->getState( 'last_id', 0 );
		if ( $last_id ) {
			$where[] = "`socialbacklinks_history_id` > {$last_id}";
		}

		$limitstart = $this->getState( 'limitstart', null );
		$limit = $this->getState( 'limit', null );

		if ( !is_null( $limit ) && !is_null( $limitstart ) ) {
			return parent::getList( $where, $this->getStart( ), $limit );
		}
		return parent::getList( $where );
	}

	/**
	 * Returns the list of grouped histories
	 * @return array
	 */
	public function getGroupedList( )
	{
		$where = array( );
		$from = $this->getState( 'from_date' );
		if ( !empty( $from ) ) {
			$where[] = '`created` > ' . $this->_db->Quote( $from );
		}

		$last_id = ( int )$this->getState( 'last_id', 0 );
		if ( $last_id ) {
			$where[] = "`socialbacklinks_history_id` > {$last_id}";
		}

		$this->from_date( null )->last_id( null )->select( 'COUNT( * ) AS count, `extension`, `network`, `result`' . ' FROM `#__socialbacklinks_histories`' )->group_by( '`extension`, `network`, `result`' );

		return $this->getList( $where );
	}

	/**
	 * Inserts new item into history
	 * @return boolean
	 */
	public function insert( )
	{
		$table = $this->getTable( );
		$data = $this->getData( );
		$params = array(
			'network' => $data->get( 'network' ),
			'extension' => $data->get( 'extension' ),
			'item_id' => $data->get( 'item_id' ),
			'title' => $data->get( 'title' ),
			'result' => $data->get( 'result' ),
			'created' => $data->get( 'created' )
		);
		return $table->save( $params );
	}

	/**
	 * Deletes old records from history
	 * @return boolean
	 */
	public function delete( )
	{
		$table = $this->getTable( );
		$periodicity = ( int )$this->getData( 'periodicity', 0 );
		$rows = $this->reset( )->periodicity( $periodicity )->getList( );

		$success = true;
		if ( $rows ) {
			foreach ($rows as $row) {
				if ( !$table->delete( $row->socialbacklinks_history_id ) ) {
					$success = false;
				}
			}
		}
		return $success;
	}

}
