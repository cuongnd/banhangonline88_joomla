<?php
/**
 * SocialBacklinks Errors controller
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
 * SocialBacklinks Errors controller class, which manage error during post content in social networks
 */
class SBControllersErrors extends SBControllersBase
{
	/**
	 * Deletes records
	 * @return void
	 */
	public function remove( )
	{
		$errors_id = $this->_request->get( 'post.errors_id', array(), 'array' );
		JArrayHelper::toInteger( $errors_id );

		if ( empty( $errors_id ) ) {
			$response = array(
				'error' => true,
				'msg' => JText::_( 'SB_NO_ITEM_SELECTED' )
			);
			echo json_encode( $response );
			JFactory::getApplication( )->close( );
			return true;
		}

		$success = $this->getModel( )->setData( array( 'cid' => $errors_id ) )->delete( );

		if ( $success ) {
			$response = array( 'error' => false );
		}
		else {
			$response = array(
				'error' => true,
				'msg' => JText::_( "SB_OTHER_ERROR" )
			);
		}

		echo json_encode( $response );
		JFactory::getApplication( )->close( );
	}

}
