<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2011 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.filesystem.file' );

class EasyBlogEasyDiscussHelper
{
	private $exists	= false;
	private $config = null;

	public function __construct()
	{
		$lang		= JFactory::getLanguage();
		$lang->load( 'com_easyblog' , JPATH_ROOT );

		$this->exists		= $this->exists();
		$this->config		= EasyBlogHelper::getConfig();
	}

	/**
	 * Determines whether EasyDiscuss exists in the current environment.
	 **/
	public function exists()
	{
		jimport( 'joomla.filesystem.file' );
		$file 		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easydiscuss' . DIRECTORY_SEPARATOR . 'constants.php';

		if( !JFile::exists( $file ) )
		{
			return false;
		}

		include_once( $file );
		include_once( DISCUSS_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );

		return true;
	}

	/**
	 * Adds a point for the particular user based on it's actions.
	 *
	 * @access	public
	 * @param	string	$action 	The action string.
	 * @param	int 	$targetId	The user's id.
	 * @return	boolean 			True on success, false otherwise.
	 */
	public function addPoint( $action , $targetId )
	{
		if( !$this->exists )
		{
			return false;
		}

		if( !$this->config->get( 'integrations_easydiscuss_points' ) )
		{
			return false;
		}

		return DiscussHelper::getHelper( 'Points' )->assign( $action , $targetId );
	}

	/**
	 * Adds a badge for the particular user based on it's actions.
	 *
	 * @access	public
	 * @param	string	$action 	The action string.
	 * @param	int 	$targetId	The user's id.
	 * @return	boolean 			True on success, false otherwise.
	 */
	public function addBadge( $action , $targetId )
	{
		if( !$this->exists )
		{
			return false;
		}

		if( !$this->config->get( 'integrations_easydiscuss_badges' ) )
		{
			return false;
		}

		return DiscussHelper::getHelper( 'Badges' )->assign( $action , $targetId );
	}

	/**
	 * TODO: Not sure how we should add the ranks if they are based on posts count
	 */
	public function addRank( $targetId )
	{
		if( !$this->exists )
		{
			return false;
		}

		DiscussHelper::getHelper( 'ranks' )->assignRank( $targetId );
	}

	/**
	 * Add's a history in EasyDiscuss which will be later reused to calculate their badges or
	 * achievements.
	 *
	 * @access	public
	 * @param	string	$action		The unique action string.
	 * @param	int 	$targetId	The target user's id.
	 * @param	string 	$title 		The title of the action / history.
	 * @return 	boolean 			True on success, false otherwise.
	 */
	public function log( $action , $targetId , $title )
	{
		if( !$this->exists )
		{
			return false;
		}

		if( !$this->config->get( 'integrations_easydiscuss_badges' ) && !$this->config->get( 'integrations_easydiscuss_points') )
		{
			return false;
		}

		return DiscussHelper::getHelper( 'History' )->log( $action , $targetId , $title );
	}

	public function readNotification( $targetId , $notificationType )
	{
		if( !$this->exists )
		{
			return false;
		}

		// Get group members emails
		if( !class_exists( 'EasyDiscussModelNotification' ) )
		{
			jimport( 'joomla.application.component.model' );
			JLoader::import( 'notification' , JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easydiscuss' . DIRECTORY_SEPARATOR . 'models' );
		}

		// @rule: Get current logged in user.
		$my		= JFactory::getUser();

		// @rule: Clear up any notifications that are visible for the user.
		$notifications 		= DiscussHelper::getModel( 'Notification' );
		$notifications->markRead( $my->id , $targetId , $notificationType );
	}

	/**
	 * Adds a notification item in EasyDiscuss
	 *
	 * @access	public
	 * @param 	TableBlog	$blog 	The blog table.
	 */
	public function addNotification( &$blog , $title , $type , $target , $author , $link )
	{
		if( !$this->exists )
		{
			return false;
		}

		if( !is_array( $target ) )
		{
			$target 	= array( $target );
		}

		foreach( $target as $targetId )
		{
			$notification	= DiscussHelper::getTable( 'Notifications' );

			$notification->bind( array(
					'title'		=> $title,
					'cid'		=> $blog->id,
					'type'		=> $type,
					'target'	=> $targetId,
					'author'	=> $author,
					'permalink'	=> $link,
					'favicon'	=> JURI::root() . 'components/com_easyblog/assets/images/discuss_notifications/' . $type . '.png',
					'component'	=> 'com_easyblog'
				) );
			$notification->store();
		}

	}
}
