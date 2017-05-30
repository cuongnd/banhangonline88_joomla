<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );

// Import parent controller
FD::import( 'site:/controllers/controller' );

class EasySocialControllerFollowers extends EasySocialController
{
	protected $app	= null;

	/**
	 * Suggest a list of friend names for a user.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	null
	 *
	 */
	public function filter()
	{
		// Check for valid tokens.
		FD::checkToken();

		// Check for valid user.
		FD::requireLogin();

		// Load friends model.
		$model 		= FD::model( 'Followers' );

		// Load the view.
		$view 		= $this->getCurrentView();

		// Get the filter types.
		$type 		= JRequest::getVar( 'type' );

		// Get the user id that we should load for.
		$userId 	= JRequest::getInt( 'id' );

		if( !$userId )
		{
			$userId 	= null;
		}
		// Try to load the target user.
		$user 		= FD::user( $userId );

		$users 		= array();

		if( $type == 'followers' )
		{
			$users 	= $model->getFollowers( $userId );
		}

		if( $type == 'following' )
		{
			$users 	= $model->getFollowing( $userId );
		}

		return $view->call( __FUNCTION__ , $type , $users , $userId );
	}

	/**
	 * Unfollows a user
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function unfollow()
	{
		// Check for valid tokens.
		FD::checkToken();

		// Check for valid user.
		FD::requireLogin();

		// Load friends model.
		$model 		= FD::model( 'Followers' );

		// Load the view.
		$view 		= $this->getCurrentView();

		// Get the user id that we should load for.
		$userId 	= JRequest::getInt( 'id' );

		// Get the current logged in user
		$my 		= FD::user();

		// Loads the followers record
		$follower 	= FD::table( 'Subscription' );
		$follower->load( array( 'uid' => $userId , 'type' => 'user.user' , 'user_id' => $my->id ) );

		if( !$follower->id || !$userId )
		{
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_FOLLOWERS_INVALID_ID' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Delete the record
		$state 	= $follower->delete();

		$view->call( __FUNCTION__ );
	}
}
