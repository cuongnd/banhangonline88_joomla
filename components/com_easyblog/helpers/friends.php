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

class EasyBlogFriendsHelper
{
	/*
	 * Allows user to add the author as a friend.
	 *
	 * @param	int	$bloggerid	The blogger id.
	 */
	public function getHTML($bloggerid="")
	{
		$html	= '';
		$my		= JFactory::getUser();
		$config	= EasyBlogHelper::getConfig();

		// We don't want to show the link to the same user
		if( $my->id == $bloggerid )
		{
			return;
		}

		if( $config->get( 'main_jomsocial_friends' ) )
		{
			$file_core		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';
			$file_messaging	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'friends.php';

			jimport( 'joomla.filesystem.file' );

			if( JFile::exists($file_core) && JFile::exists($file_messaging))
			{
				require_once( $file_core );
				require_once( $file_messaging );
				$user		= CFactory::getUser();

				$model		= CFactory::getModel( 'Friends' );
				$friends	= $model->getFriendIds( $bloggerid );

				if( !in_array( $my->id , $friends ) )
				{
					$cfriend = new CFriends();

					$html = '<a href="javascript:void(0);" onclick="'. $cfriend->getPopup( $bloggerid ) .'" class="author-friend"><span>' . JText::_( 'COM_EASYBLOG_ADD_FRIEND' ) . '</span></a>';
				}
			}
		}

		$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

		if( $config->get( 'integrations_easysocial_friends' ) && $easysocial->exists() )
		{
			$user	= Foundry::user( $bloggerid );

			// Check if the user is friends with the current viewer.
			if( $user->isFriends( $my->id ) )
			{
				return;
			}

			$easysocial->init();

			$theme 	= new CodeThemes();
			$theme->set( 'id' , $bloggerid );
			$html 	= $theme->fetch( 'easysocial.friends.php' );
		}

		return $html;
	}
}
