<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR .'helper.php' );

class EasyBlogGoogleAdsense
{
	public static function getHTML( $bloggerId )
	{
		$config = EasyBlogHelper::getConfig();

		$adsenseObj = new stdClass;
		$adsenseObj->header			= '';
		$adsenseObj->beforecomments	= '';
		$adsenseObj->footer			= '';

		$defaultCode    	= '';
		$defaultDisplay		= '';
		$my					= JFactory::getUser();

		if( $config->get( 'integration_google_adsense_display_access' ) == 'members' && $my->id == 0 )
		{
			return $adsenseObj;
		}

		if( $config->get( 'integration_google_adsense_display_access' ) == 'guests' && $my->id > 0 )
		{
			return $adsenseObj;
		}

		if(! $config->get( 'integration_google_adsense_enable' ))
		{
			return $adsenseObj;
		}

		if($config->get('integration_google_adsense_centralized'))
		{
			$adminAdsenseCode		= $config->get('integration_google_adsense_code');
			$adminAdsenseDisplay	= $config->get('integration_google_adsense_display');

			if(! empty($adminAdsenseCode))
			{
				$defaultCode		= $adminAdsenseCode;
				$defaultDisplay		= $adminAdsenseDisplay;
			}
		}

		//blogger adsense
		//now we check whether user enabled adsense or not.
		$bloggerAdsense = EasyBlogHelper::getTable( 'Adsense', 'Table' );
		$bloggerAdsense->load($bloggerId);

		if( !empty($bloggerAdsense->code) && $bloggerAdsense->published )
		{
			$defaultCode	= $bloggerAdsense->code;
			$defaultDisplay	= $bloggerAdsense->display;
		}

		// @task: If the user did not enter any adsense codes, fallback to the site admin's code
		if( empty( $defaultCode ) )
		{
			$adminAdsenseCode		= $config->get('integration_google_adsense_code');
			$adminAdsenseDisplay	= $config->get('integration_google_adsense_display');

			if(! empty($adminAdsenseCode))
			{
				$defaultCode		= $adminAdsenseCode;
				$defaultDisplay		= $adminAdsenseDisplay;
			}
		}

		if( $defaultDisplay == 'userspecified')
		{
			return $adsenseObj;
		}

		if(! empty($defaultCode))
		{
			$adTheme	= new CodeThemes();
			$adTheme->set( 'adsense'	, $defaultCode);
			$adsenseHTML = $adTheme->fetch( 'blog.adsense.php' );

			switch( $defaultDisplay )
			{
				case 'beforecomments':
					$adsenseObj->beforecomments = $adsenseHTML;
					break;
				case 'header':
					$adsenseObj->header = $adsenseHTML;
					break;
				case 'footer':
					$adsenseObj->footer = $adsenseHTML;
					break;
				case 'both':
				default :
					$adsenseObj->header = $adsenseHTML;
					$adsenseObj->footer = $adsenseHTML;
					break;
			}
		}//end if

		return $adsenseObj;
	}

	public static function processsAdsenseCode( $content, $bloggerId )
	{
		if( empty($content) )
			return $content;

		if( empty($bloggerId) )
			return $content;

		//$content = 'sdfaa sdfsaf asdfafasf';
		//$content = $content . ' ' . $content;

		$pattern	= '/\{eblogads.*\}/i';

		preg_match_all( $pattern , $content , $matches );
		$adscode	= $matches[0];

		if( count( $adscode ) > 0 )
		{
			foreach($adscode as $code)
			{
				$codes  	= explode(' ', $code);
				$alignment  = ( isset( $codes[1] ) ) ? $codes[1] : '';
				$alignment  = str_ireplace( '}' , '' , $alignment );

				$html   	= EasyBlogGoogleAdsense::_getAdsenseTemplate( $bloggerId, $alignment );
				$content	= str_ireplace( $code , $html , $content );
			}
		}

		return $content;
	}

	public static function stripAdsenseCode( $content )
	{
		$pattern	= '/\{eblogads.*\}/i';
		return preg_replace( $pattern , '' , $content );
	}

	private static function _getAdsenseTemplate( $bloggerId, $alignment = '')
	{
		$config = EasyBlogHelper::getConfig();
		$my = JFactory::getUser();


		if( $config->get( 'integration_google_adsense_display_access' ) == 'members' && $my->id == 0 )
		{
			return '';
		}

		if( $config->get( 'integration_google_adsense_display_access' ) == 'guests' && $my->id > 0 )
		{
			return '';
		}

		if(! $config->get( 'integration_google_adsense_enable' ))
		{
			return '';
		}

		if($config->get('integration_google_adsense_centralized'))
		{
			$adminAdsenseCode		= $config->get('integration_google_adsense_code');
			$adminAdsenseDisplay	= $config->get('integration_google_adsense_display');

			if(! empty($adminAdsenseCode))
			{
				$defaultCode		= $adminAdsenseCode;
				$defaultDisplay		= $adminAdsenseDisplay;
			}
		}

		//blogger adsense
		//now we check whether user enabled adsense or not.
		$bloggerAdsense = EasyBlogHelper::getTable( 'Adsense', 'Table' );
		$bloggerAdsense->load($bloggerId);

		if( !empty($bloggerAdsense->code) && $bloggerAdsense->published )
		{
			$defaultCode	= $bloggerAdsense->code;
			$defaultDisplay	= $bloggerAdsense->display;
		}

		// @task: If the user did not enter any adsense codes, fallback to the site admin's code
		if( empty( $defaultCode ) )
		{
			$adminAdsenseCode		= $config->get('integration_google_adsense_code');
			$adminAdsenseDisplay	= $config->get('integration_google_adsense_display');

			if(! empty($adminAdsenseCode))
			{
				$defaultCode		= $adminAdsenseCode;
				$defaultDisplay		= $adminAdsenseDisplay;
			}
		}

		if( $defaultDisplay != 'userspecified')
		{
			return '';
		}


		$adTheme	= new CodeThemes();
		$adTheme->set( 'adsense'	, $defaultCode);
		$adTheme->set( 'alignment'	, $alignment);

		$adsenseHTML = $adTheme->fetch( 'blog.adsense.php' );

		return $adsenseHTML;
	}
}
