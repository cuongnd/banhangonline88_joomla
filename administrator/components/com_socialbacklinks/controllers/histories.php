<?php
/**
 * SocialBacklinks Histories controller
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
 * SocialBacklinks Histories controller class, which manage histories during post content in social networks
 */
class SBControllersHistories extends SBControllersBase
{
	/**
	 * Checks is history was updated
	 * @return void
	 */
	public function checkHistoryUpdate( )
	{
		$last_id = $this->_request->get( 'last_id', -1, 'int' );
		$model = $this->getModel( );

		$session = JFactory::getSession( );
		$last_sync = JModelLegacy::getInstance( 'SBModelsConfig' )->reset( )->section( 'basic' )->name( 'last_sync' )->getItem( )->value;

		if ( empty( $last_sync ) ) {
			$sync_update = false;
		}
		else {
			if ( $session->get( 'sb_last_sync', '0000-00-00 00:00:00' ) == $last_sync ) {
				$sync_update = false;
			}
			else {
				$sync_update = true;

				$session->set( 'sb_last_sync', $last_sync );
				$last_sync = SBHelpersSync::convertDate( $last_sync );
			}
		}

		$current_last_id = SBHelpersSync::getLastId( );
		if ( ($last_id < 0) || ($current_last_id == $last_id) ) {
			$response = array(
				'updated' => false,
				'update_sync_date' => $sync_update
			);

			if ( $sync_update ) {
				$response['last_sync'] = array(
					'day' => $last_sync->format( 'D', true ),
					'date' => $last_sync->format( 'd M Y', true ),
					'time' => $last_sync->format( 'h:i a', true )
				);
			}

			echo json_encode( $response );
			JFactory::getApplication( )->close( );
		}
		else {
			// Get statistics information of current day
			$date = SBHelpersSync::convertDate( gmdate( 'Y-m-d' ) );
			
			$data = $model->from_date( $date->format( 'Y-m-d H:i:s' ) )->getGroupedList( );

			if ( empty( $data ) ) {
				$response = array(
					'updated' => false,
					'update_sync_date' => $sync_update
				);

				if ( $sync_update ) {
					$response['last_sync'] = array(
						'day' => $last_sync->format( 'D', true ),
						'date' => $last_sync->format( 'd M Y', true ),
						'time' => $last_sync->format( 'h:i a', true )
					);
				}
				echo json_encode( $response );
				JFactory::getApplication( )->close( );
				return true;
			}

			$contents = '';
			ob_start( );
			SBDispatcher::getInstance( )->runController( 'histories', array( 'layout' => 'sync_statistics', 'task'=>'display' ) );
			$contents = ob_get_contents( );
			ob_clean( );
			$response = array(
				'updated' => true,
				'update_sync_date' => $sync_update,
				'last_id' => $current_last_id,
				'html' => $contents
			);

			if ( $sync_update ) {
				$response['last_sync'] = array(
					'day' => $last_sync->format( 'D', true ),
					'date' => $last_sync->format( 'd M Y', true ),
					'time' => $last_sync->format( 'h:i a', true )
				);
			}

			echo json_encode( $response );
			JFactory::getApplication( )->close( );
		}
	}

}
