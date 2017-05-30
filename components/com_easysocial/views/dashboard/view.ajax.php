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

// Necessary to import the custom view.
FD::import( 'site:/views/views' );

class EasySocialViewDashboard extends EasySocialSiteView
{
	/**
	 * Responsible to output the application contents.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	SocialAppTable	The application ORM.
	 */
	public function getAppContents( $app )
	{
		$ajax 	= FD::ajax();

		// If there's an error throw it back to the caller.
		if( $this->hasErrors() )
		{
			return $ajax->reject( $this->getMessage() );
		}

		// Get the current logged in user.
		$my 		= FD::user();

		// Load the library.
		$lib		= FD::getInstance( 'Apps' );
		$contents 	= $lib->renderView( SOCIAL_APPS_VIEW_TYPE_EMBED , 'dashboard' , $app , array( 'userId' => $my->id ) );

		// Return the contents
		return $ajax->resolve( $contents );
	}

	/**
	 * Retrieves the stream contents.
	 *
	 * @since	1.0
	 * @access	public
	 */
	public function getStream($stream , $type = '', $hashtags = array())
	{
		$ajax 	= FD::ajax();

		if ($this->hasErrors()) {
			return $ajax->reject($this->getMessage());
		}

		$streamCnt = $stream->getCount();

		// Initialize the default ids
		$groupId = false;
		$eventId = false;

		// Retrieve the story lib
		$story = FD::get('Story', SOCIAL_TYPE_USER);

		// Get the tags
		if ($hashtags) {
			$hashtags = FD::makeArray($hashtags);
			$story->setHashtags($hashtags);
		}

		if ($type == SOCIAL_TYPE_GROUP) {
			$story = FD::get('Story', $type);

			$groupId = $this->input->getInt('id', 0);

			$story->setCluster($groupId, $type);
			$story->showPrivacy(false);
		}

		if ($type == SOCIAL_TYPE_EVENT) {
			$story = FD::get('Story', $type);

			$eventId = $this->input->getInt('id', 0);

			$story->setCluster($eventId, $type);
			$story->showPrivacy(false);
		}

		$stream->story  = $story;

		$theme = FD::themes();

		$theme->set('eventId', $eventId);
		$theme->set('groupId', $groupId);
		$theme->set('hashtag', false);
		$theme->set('stream', $stream);
		$theme->set('story', $story);
		$theme->set('streamcount', $streamCnt );

		$contents = $theme->output('site/dashboard/feeds');

		return $ajax->resolve( $contents, $streamCnt );
	}
}
