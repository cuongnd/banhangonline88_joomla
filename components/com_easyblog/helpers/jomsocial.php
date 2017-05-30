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

class EasyBlogJomSocialHelper
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
		$file 		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';

		if( !JFile::exists( $file ) )
		{
			return false;
		}

		include_once( $file );

		return true;
	}

	/**
	 * Adds a notification item in JomSocial
	 *
	 * @access	public
	 * @param 	TableBlog	$blog 	The blog table.
	 */
	public function addNotification( $title , $type , $target , $author , $link )
	{
		jimport( 'joomla.filesystem.file' );

		// @since this only works with JomSocial 2.6, we need to test certain files.
		$file 	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'notificationtypes.php';

		if( !$this->exists || empty( $target ) || $target[0] == $author->getId() || !JFile::exists( $file ) )
		{
			return false;
		}

		CFactory::load( 'helpers' , 'notificationtypes' );
		CFactory::load( 'helpers' , 'content' );
		CFactory::load( 'libraries', 'notificationtypes' );

		// @task: Set the necessary parameters first.
		$params 		= EasyBlogHelper::getRegistry( '' );
		$params->set( 'url' , str_replace("administrator/","", $author->getProfileLink()) );

		// @task: Obtain model from jomsocial.
		$model			= CFactory::getModel( 'Notification' );

		// @task: Currently we are not using this, so we should just skip this.
		$requireAction	= 0;

		if( !is_array( $target ) )
		{
			$target 	= array( $target );
		}

		foreach( $target as $targetId )
		{
			JTable::addIncludePath( JPATH_ROOT . '/components/com_community/tables' );
			$notification 	= JTable::getInstance( 'Notification' , 'CTable' );

			$notification->actor	= $author->getId();
			$notification->target	= $targetId;
			$notification->content	= $title;
			$notification->created	= EasyBlogHelper::getDate()->toMySQL();
			$notification->params	= $params->toString();
			
			$notification->cmd_type = CNotificationTypesHelper::convertNotifId( $type );
			$notification->type		= 0;
			
			$notification->store();
		}

		return true;
	}
}
