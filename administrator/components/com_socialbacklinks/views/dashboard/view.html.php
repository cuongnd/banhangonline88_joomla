<?php
/**
 * SocialBacklinks dashboard view
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
 * SocialBacklinks Dashboard view class
 */
class SBViewsDashboard extends SBViewsBase
{
	/**
	 * Displays default layout
	 *
	 * @return void
	 */
	protected function _default( )
	{
		// Get main configuration
		$configs = $this->get( 'list' );

		// Get last sync date
		$last_sync = $this->getModel( )->section( 'basic' )->name( 'last_sync' )->getItem( );

		if ( !empty( $last_sync ) && !empty( $last_sync->value ) ) {
			$date = SBHelpersSync::convertDate( $last_sync->value );
			$this->assignRef( 'last_sync', $date );
		}

		// Set last record id of histories
		$this->assign( 'last_history_id', SBHelpersSync::getLastId( ) );
	}

}
