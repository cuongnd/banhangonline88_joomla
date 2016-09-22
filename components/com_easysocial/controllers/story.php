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

// Import main controller
FD::import( 'site:/controllers/controller' );

class EasySocialControllerStory extends EasySocialController
{
	/**
	 * Allows caller to update a stream
	 *
	 * @since	1.2
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function update()
	{
		// Check for request forgeries
		FD::checkToken();

		// Check for valid users
		FD::requireLogin();

		// Get the current view
		$view 	= $this->getCurrentView();

		// Get the stream id
		$id 	= JRequest::getInt('id');
		$stream = FD::table('Stream');
		$stream->load($id);

		// Check for valid stream id's.
		if (!$id || !$stream->id) {
			$view->setMessage(JText::_('COM_EASYSOCIAL_STREAM_INVALID_ID_PROVIDED'), SOCIAL_MSG_ERROR);

			return $view->call(__FUNCTION__);
		}

		// @TODO: Check for permissions
		$my 	= FD::user();

		if ($stream->cluster_id) {

			$group 	= FD::group($stream->cluster_id);

			if (!$my->isSiteAdmin() && $stream->actor_id != $my->id && !$group->isAdmin()) {
				$view->setMessage(JText::_('COM_EASYSOCIAL_STREAM_NO_PERMISSIONS_TO_EDIT'), SOCIAL_MSG_ERROR);

				return $view->call(__FUNCTION__);
			}
		} else {
			if (!$my->isSiteAdmin() && $stream->actor_id != $my->id) {
				$view->setMessage(JText::_('COM_EASYSOCIAL_STREAM_NO_PERMISSIONS_TO_EDIT'), SOCIAL_MSG_ERROR);

				return $view->call(__FUNCTION__);
			}
		}

		$content = JRequest::getVar('content', '', 'post', 'string', JREQUEST_ALLOWRAW);
		$mentions = JRequest::getVar('mentions');

		// Format the json string to array
		if (!empty($mentions)) {
			foreach ($mentions as &$mention) {
				$mention = FD::json()->decode($mention);
			}
		}

		// Process the content
		$stream->content 	= $content;

		// Set the last edited date
		$stream->edited 	= FD::date()->toSql();

		// Get the stream model and remove mentions
		$model 	= FD::model('Stream');
		$model->removeMentions($stream->id);

		// Now we need to add new mentions
		if ($mentions) {
			$model->addMentions($stream->id, $mentions);
		}

		// Save the stream
		$stream->store();

		// Because we know that story posts only has 1 item, we may safely assume that the first index.
		$items	= $stream->getItems();
		$item 	= $items[0];

		return $view->call(__FUNCTION__, $item);
	}

	/**
	 * Stores a new story item
	 *
	 * @since	1.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function create()
	{
		// Check for request forgeries
		FD::checkToken();

		// Check for valid users.
		FD::requireLogin();

		// Get the current view.
		$view 	= $this->getCurrentView();
		$my 	= FD::user();

		// Load our story library
		$story 	= FD::story(SOCIAL_TYPE_USER);

		// Get posted data.
		$post 	= JRequest::get( 'post' );

		// check if the user being viewed the same user or other user.
		$id 		= $post[ 'target' ];
		$targetId	= ( $my->id != $id ) ? $id : '';

		// Determine the post types.
		$type 		= isset($post['attachment']) && !empty($post['attachment']) ? $post['attachment'] : SOCIAL_TYPE_STORY;

		// Check if the content is empty only for story based items.
		if ((!isset($post['content']) || empty($post['content'])) && $type == SOCIAL_TYPE_STORY) {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_STORY_PLEASE_POST_MESSAGE' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// Check if the content is empty and there's no photos.
		if ((!isset( $post[ 'photos' ] ) || empty($post[ 'photos']) ) && $type == 'photos') {
			$view->setMessage( JText::_( 'COM_EASYSOCIAL_STORY_PLEASE_ADD_PHOTO' ) , SOCIAL_MSG_ERROR );
			return $view->call( __FUNCTION__ );
		}

		// We need to allow raw because we want to allow <,> in the text but it should be escaped during display
		$content = $this->input->get('content', '', 'raw');

		// Check whether the user can really post something on the target
		if ($targetId) {
			$privacy 	= FD::privacy( $my->id );
			$state = $privacy->validate( 'profiles.post.status' , $targetId , SOCIAL_TYPE_USER );

			if( ! $state )
			{
				$view->setMessage( JText::_( 'COM_EASYSOCIAL_STORY_NOT_ALLOW_TO_POST' ) , SOCIAL_MSG_ERROR );
				return $view->call( __FUNCTION__ );
			}
		}

		// Store the location for this story
		$shortAddress	= JRequest::getVar('locations_short_address' , '' );
		$address 		= JRequest::getVar('locations_formatted_address' , '' );
		$lat 			= JRequest::getVar('locations_lat' , '' );
		$lng 			= JRequest::getVar('locations_lng'	, '' );
		$locationData	= JRequest::getVar('locations_data', '');
		$location 		= null;

		// Only store location when there is location data
		if (!empty($address) && !empty($lat) && !empty($lng)) {

			$location 				= FD::table( 'Location' );
			$location->short_address	= $shortAddress;
			$location->address 		= $address;
			$location->longitude 	= $lng;
			$location->latitude		= $lat;
			$location->uid 			= $story->id;
			$location->type 		= $type;
			$location->user_id 		= $my->id;
			$location->params 		= $locationData;

			// Try to save the location data.
			$state 	= $location->store();
		}

		// Get which users are tagged in this post.
		$friendIds = JRequest::getVar('friends_tags' , '' );
		$friends = array();

		if (!empty($friendIds)) {

			// Get the friends model
			$model = FD::model('Friends');

			foreach ($friendIds as $id) {
				// Check if the user is really a friend of him / her.
				if (!$model->isFriends($my->id, $id)) {
					continue;
				}

				$friends[]	= $id;
			}
		}

		$contextIds = 0;

		// For photos that are posted on the story form
		if ($type == 'photos' && isset($post['photos'])) {
			$contextIds = $post['photos'];
		}

		// Check if there are mentions provided from the post.
		$mentions = isset($post['mentions']) ? $post['mentions'] : array();

		// Format the json string to array
		if (isset($post['mentions'])) {

			$mentions = $post['mentions'];

			foreach ($mentions as &$mention) {
				$mention = FD::json()->decode($mention);
			}
		}

		// Process moods here
		$mood = FD::table('Mood');
		$hasMood = $mood->bindPost($post);

		// If this exists, we need to store them
		if ($hasMood) {
			$mood->user_id			= $my->id;
			$mood->store();
		}

		// Set the privacy for the album
		$privacy = $this->input->get('privacy', '', 'default');
		$customPrivacy = $this->input->get('privacyCustom', '', 'string');
		$privacyRule = ( $type == 'photos' ) ? 'photos.view' : 'story.view';

		$cluster = isset( $post[ 'cluster' ]  ) ? $post[ 'cluster' ] : '';
		$clusterType = isset( $post[ 'clusterType' ]  ) ? $post[ 'clusterType' ] : '';
		$isCluster = ($cluster) ? true : false;

		$args 		= array(
								'content' => $content,
								'contextIds' => $contextIds,
								'contextType'	=> $type,
								'actorId'		=> $my->id,
								'targetId'		=> $targetId,
								'location'		=> $location,
								'with'			=> $friends,
								'mentions'		=> $mentions,
								'cluster'		=> $cluster,
								'clusterType'	=> $clusterType,
								'mood'			=> $mood,
								'privacyRule'	=> $privacyRule,
								'privacyValue'	=> $privacy,
								'privacyCustom'	=> $customPrivacy
					  		);

		// Create the stream item
		$stream		= $story->create($args);

		if ($hasMood) {
			$mood->namespace		= 'story.user.create';
			$mood->namespace_uid	= $stream->id;

			$mood->store();
		}

		// Update with the stream's id. after the stream is created.
		if (!empty($address) && !empty($lat) && !empty($lng)) {
			$location->uid 			= $stream->id;

			// Try to save the location data.
			$state 	= $location->store();
		}

		// @badge: story.create
		// Add badge for the author when a report is created.
		$badge 	= FD::badges();
		$badge->log( 'com_easysocial' , 'story.create' , $my->id , JText::_( 'COM_EASYSOCIAL_STORY_BADGE_CREATED_STORY' ) );

		// @points: story.create
		// Add points for the author when a report is created.
		$points = FD::points();
		$points->assign( 'story.create' , 'com_easysocial' , $my->id );

		// cluster stream do not need privacy ( at the moment )
		if (!$isCluster) {

			$privacyLib		= FD::privacy();

			if( $type == 'photos' )
			{
				$photoIds = ( is_array( $contextIds ) ) ? $contextIds : array( $contextIds );

				foreach( $photoIds as $photoId )
				{
					$privacyLib->add( $privacyRule , $photoId , $type , $privacy, null, $customPrivacy );
				}

			}
			else
			{
				$privacyLib->add( $privacyRule , $stream->uid , $type , $privacy, null, $customPrivacy );
			}
		}

		return $view->call( __FUNCTION__ , $stream );
	}

}
