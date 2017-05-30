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

class EasyBlogFollowersHelper
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

		// Guest shouldn't see this
		if ($my->guest) {
			return;
		}
		
		// We don't want to show the link to the same user
		if( $my->id == $bloggerid )
		{
			return;
		}

		$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

		if( $config->get( 'integrations_easysocial_followers' ) && $easysocial->exists() )
		{
			$user 	= Foundry::user( $bloggerid );

			$followed 	= $user->isFollowed( $my->id );

			if( $followed )
			{
				return;
			}

			$easysocial->init();

			$theme 	= new CodeThemes();
			$theme->set( 'id' , $bloggerid );
			$html 	= $theme->fetch( 'easysocial.followers.php' );
		}

		return $html;
	}
}
