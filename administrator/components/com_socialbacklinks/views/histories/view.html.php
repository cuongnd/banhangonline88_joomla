<?php
/**
 * SocialBacklinks Histories view
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
 * SocialBacklinks Histories view class
 */
class SBViewsHistories extends SBViewsBase
{
	/**
	 * Displays list of history
	 * @return void
	 */
	protected function _default( )
	{
		$app = JFactory::getApplication( );
		$model = $this->getModel( );
		
		$filter_order = $app->getUserStateFromRequest( "{$this->option}.histories.filter_order", 'filter_order', 'socialbacklinks_history_id', 'cmd' );
		$filter_order_Dir = $app->getUserStateFromRequest( "{$this->option}.histories.filter_order_Dir", 'filter_order_Dir', 'DESC', 'word' );
		$limit = $app->getUserStateFromRequest( 'global.list.limit', 'limit', $app->getCfg( 'list_limit' ), 'int' );
		$limitstart = $app->getUserStateFromRequest( "{$this->option}.histories.limitstart", 'limitstart', 0, 'int' );

		// Tell the model how to sort and get pagination to page
		$total = $model->filter_order( $filter_order )->filter_order_Dir( $filter_order_Dir )->getTotal( );

		// Tell the model the limits
		$model = $model->limit( $limit )->limitstart( $limitstart );
		$rows = $model->getList( );

		// table ordering
		$lists = array(
			'order_Dir' => $filter_order_Dir,
			'order' => $filter_order,
			'limit' => $limit,
			'limitstart' => $limitstart,
			'total' => $total
		);
		$this->assign( 'lists', $lists );
		$this->assign( 'rows', $rows );
	}

	/**
	 * Generate information of last sync and assigns its data to layout
	 * @return void
	 */
	protected function _sync_statistics( )
	{
		// Get statistics information of current day
		$date = SBHelpersSync::convertDate( gmdate( 'Y-m-d' ) );

		// Get histories information
		$data = $this->getModel( )->from_date( $date->format( 'Y-m-d H:i:s' ) )->getGroupedList( );
		$statistics = array( );
		if ( is_array( $data ) && count( $data ) ) {
			foreach ($data as $item) {
				if ( empty( $statistics[$item->result] ) ) {
					$statistics[$item->result] = array( );
				}
				if ( empty( $statistics[$item->result][$item->extension] ) ) {
					$statistics[$item->result][$item->extension] = array( );
				}
				$statistics[$item->result][$item->extension][$item->network] = $item->count;
			}
		}

		$this->assignRef( 'statistics', $statistics );
	}

	/**
	 * Shows a list with results of last synchronization
	 * @return void
	 */
	protected function _sync_result( )
	{
		if ( empty($this->loading) ) {
			$model = $this->getModel( );
			
			$rows = !is_null( $model->getState( 'last_id' ) ) ? $model->getList( ) : array( );
			
			$show_errors_button = false;
			if ( !empty( $rows ) ) {
				foreach ($rows as $row) {
					if ( isset( $row->result ) && !$row->result ) {
						$show_errors_button = true;
						break;
					}
				}
			}
			$this->assign( 'rows', $rows );
			$this->assign( 'show_errors_button', $show_errors_button );
		}
	}

}
