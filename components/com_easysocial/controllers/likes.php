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

FD::import( 'site:/controllers/controller' );

class EasySocialControllerLikes extends EasySocialController
{

	/**
	 * display the remainder's name.
	 *
	 * @since	1.0
	 * @access	public
	 * @param
	 * @return	string
	 */

	public function showOthers()
	{
		// Check for request forgeries.
		FD::checkToken();

		// User needs to be logged in.
		FD::requireLogin();

		$uid 	= JRequest::getInt( 'uid' );
		$type 	= JRequest::getVar( 'type' );
		$group 	= JRequest::getVar( 'group', SOCIAL_APPS_GROUP_USER );
		$verb 	= JRequest::getVar( 'verb', '' );

		// Get the list of excluded ids
		$excludeIds = JRequest::getVar( 'exclude' );

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the likes model
		$model 	= FD::model( 'Likes' );

		// Form the exclusion id's into an array
		$excludeIds	= explode( ',', $excludeIds );

		$key	= $type . '.' . $group;

		// If verb is set, use the verb
		if( !empty($verb) )
		{
			$key 	.= '.' . $verb;
		}

		$userIds = $model->getLikerIds( $uid, $key, $excludeIds );

		$users	= array();

		if( $userIds && count( $userIds ) > 0 )
		{
			$users = FD::user( $userIds );
		}

		return $view->call( __FUNCTION__ , $users );
	}



	/**
	 * Toggle the likes on an object.
	 *
	 * @since	1.0
	 * @access	public
	 * @param
	 * @return	string
	 */
	public function toggle()
	{
		// Check for request forgeries.
		FD::checkToken();

		// User needs to be logged in.
		FD::requireLogin();

		// Get the stream id.
		$id 		= JRequest::getInt( 'id' );
		$type 		= JRequest::getString( 'type' );
		$group 		= JRequest::getString( 'group', SOCIAL_APPS_GROUP_USER );
		$itemVerb 	= JRequest::getString( 'verb', '' );
		$streamid 	= JRequest::getVar( 'streamid', '' );

		// Get the view.
		$view 	= $this->getCurrentView();

		// If id is invalid, throw an error.
		if (!$id || !$type) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_ERROR_UNABLE_TO_LOCATE_ID' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Get current logged in user.
		$my 		= FD::user();

		// Load likes library.
		$model 		= FD::model( 'Likes' );

		// Build the key for likes
		$key		= $type . '.' . $group;
		if ($itemVerb) {
			$key = $key . '.' . $itemVerb;
		}

		// Determine if user has liked this item previously.
		$hasLiked	= $model->hasLiked( $id , $key, $my->id );

		// If user had already liked this item, we need to unlike it.
		if ($hasLiked) {
			$useStreamId = ($type == 'albums') ? '' : $streamid;
			$state 	= $model->unlike( $id , $key , $my->id, $useStreamId );

		} else {
			$useStreamId = ($type == 'albums') ? '' : $streamid;
			$state 	= $model->like( $id , $key , $my->id, $useStreamId );

			//now we need to update the associated stream id from the liked object
			if ($streamid) {
				$doUpdate = true;
				if ($type == 'photos') {
					$sModel = FD::model('Stream');
					$totalItem = $sModel->getStreamItemsCount($streamid);

					if ($totalItem > 1) {
						$doUpdate = false;
					}
				}

				if ($doUpdate) {
					$stream = FD::stream();
					$stream->updateModified( $streamid );
				}
			}
		}

		// The current action
		$verb 	= $hasLiked ? 'unlike' : 'like';

		// If there's an error, log this down here.
		if (!$state) {
			// Set the view with error
			$view->setMessage( $model->getError() , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ , $verb, $id , $type, $group, $itemVerb );
		}

		return $view->call( __FUNCTION__ , $verb , $id , $type, $group, $itemVerb );
	}
}
