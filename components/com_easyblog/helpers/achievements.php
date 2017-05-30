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

class EasyBlogAchievementsHelper
{
	/*
	 * Allows user to add the author as a friend.
	 *
	 * @param	int	$bloggerid	The blogger id.
	 */
	public function getHTML($bloggerid="")
	{
		$config = EasyBlogHelper::getConfig();
		$html	= '';

		$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

		if( $config->get( 'integrations_easysocial_badges' ) && $easysocial->exists() )
		{
			$easysocial->init();

			$user 	= Foundry::user( $bloggerid );
			$badges	= $user->getBadges();

			$theme 	= new CodeThemes();
			$theme->set( 'badges' , $badges );
			$html 	= $theme->fetch( 'easysocial.achievements.php' );
		}

		return $html;
	}
}
