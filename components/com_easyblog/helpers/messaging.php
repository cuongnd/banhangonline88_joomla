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

class EasyBlogMessagingHelper
{
	/**
	 * Shares a story through 3rd party oauth clients
	 *
	 * @param	TableBlog	$blog	A blog table object
	 * @param	string		$type	The type of oauth client
	 *
	 * @return	boolean		True on success and false otherwise.
	 **/
	public function getHTML($bloggerid="")
	{
		$html	= '';
		$my		= JFactory::getUser();
		$config	= EasyBlogHelper::getConfig();

		if( $config->get( 'main_jomsocial_messaging' ) && $my->id != $bloggerid )
		{
			$file_core		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';
			$file_messaging	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'messaging.php';
			if(file_exists($file_core) && file_exists($file_messaging))
			{
				require_once( $file_core );
				require_once( $file_messaging );
				CMessaging::load();

				$html = '<a href="javascript:void(0);" onclick="'.CMessaging::getPopup( $bloggerid ).'" class="author-message" title="' . JText::_( 'COM_EASYBLOG_MESSAGE_AUTHOR' ) . '"><span>' . JText::_( 'COM_EASYBLOG_MESSAGE_AUTHOR' ) . '</span></a>';
			}
		}


		$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

		if( $config->get( 'integrations_easysocial_conversations' ) && $easysocial->exists() && $my->id != $bloggerid )
		{
			$easysocial->init();

			$user 	= Foundry::user( $bloggerid );
			$theme 	= new CodeThemes();

			$theme->set( 'user' , $user );

			$html 	= $theme->fetch( 'easysocial.conversation.php' );
		}
		return $html;
	}
}
