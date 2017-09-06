<?php
/**
 * SocialBacklinks Errors view
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
class SBViewsErrors extends SBViewsBase
{
	/**
	 * Displays list of histories
	 * @return void
	 */
	protected function _default( )
	{
		$rows = array( );
		$model = $this->getModel( );
		foreach (SBPlugin::get( 'content.' ) as $plugin) {
			$list = $model->reset( )->plugin( $plugin )->getList( );
			if ( $list ) {
				$rows = array_merge( $rows, $list );
			}
		}
		$this->assign( 'rows', $rows );
	}

}
