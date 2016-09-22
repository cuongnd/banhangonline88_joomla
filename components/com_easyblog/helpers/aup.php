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

class EasyBlogAUPHelper
{
	public function __construct()
	{
		$lang		= JFactory::getLanguage();
		$lang->load( 'com_easyblog' , JPATH_ROOT );
	}

	private function loadHelper()
	{
		$file	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_alphauserpoints' . DIRECTORY_SEPARATOR . 'helper.php';
		if( !JFile::exists( $file ) )
		{
			return false;
		}

		require_once( $file );

		return true;
	}

	public function getPoints( $userId )
	{
		$config	= EasyBlogHelper::getConfig();

		if( !$config->get( 'main_alpha_userpoint_points' ) )
		{
			return false;
		}

		if(!$this->loadHelper() )
		{
			return false;
		}

		$info		= AlphaUserPointsHelper::getUserInfo( '' , $userId );

		if( !$info )
		{
			return '';
		}

		return JText::sprintf( 'COM_EASYBLOG_AUP_POINTS_EARNED' , $info->points );
	}

	public function getMedals( $userId )
	{
		$config	= EasyBlogHelper::getConfig();

		if( !$config->get( 'main_alpha_userpoint_medals' ) )
		{
			return false;
		}

		if(!$this->loadHelper() )
		{
			return false;
		}

		if( ! method_exists('AlphaUserPointsHelper','getUserMedals'))
		{
		    return false;
		}

		$medals		= AlphaUserPointsHelper::getUserMedals( '' , $userId );

		$theme		= new CodeThemes();
		$theme->set( 'medals' , $medals );
		return $theme->fetch( 'author.aup.medals.php' );
	}

	public function getRanks( $userId )
	{
		$config	= EasyBlogHelper::getConfig();

		if( !$config->get( 'main_alpha_userpoint_ranks' ) )
		{
			return false;
		}

		if(!$this->loadHelper() )
		{
			return false;
		}

		if( ! method_exists('AlphaUserPointsHelper','getUserRank'))
		{
		    return false;
		}

		$rank		= AlphaUserPointsHelper::getUserRank( '' , $userId );
		$theme		= new CodeThemes();
		$theme->set( 'rank' , $rank );
		return $theme->fetch( 'author.aup.ranks.php' );
	}
}
