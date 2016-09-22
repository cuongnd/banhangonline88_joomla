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

class EasyBlogBookmark
{
	public static function getHTML()
	{
		$config = EasyBlogHelper::getConfig();
		$bookmark_provider 	= $config->get('social_provider','addthis');
		$bookmarkEnable 	= $config->get('layout_enablebookmark');

		if($bookmarkEnable == '0')
			return '';

		if( $bookmarkEnable == '2')
		{
			$my   = JFactory::getUser();
			if( $my->id == 0)
			{
				return '';
			}
		}

		if( $bookmark_provider == 'sharethis' )
		{
			return EasyBlogBookmark::getShareThis();
		}
		else
		{
			return EasyBlogBookmark::getAddThis();
		}

	}

	public static function getAddThis()
	{
		$config = EasyBlogHelper::getConfig();

		$addthis_customcode = $config->get('social_addthis_customcode', 'xa-4be11e1875bf6363');
		$addthis_style 		= $config->get('social_addthis_style', '2');
		$displayText    	= JText::_('COM_EASYBLOG_BOOKMARK');

		$theme	= new CodeThemes();
		$theme->set( 'addthis_customcode'	, $addthis_customcode );
		$theme->set( 'displayText'			, $displayText );
		return $theme->fetch( 'addthis.button.style'.$addthis_style.'.php' );
	}

	public static function getShareThis()
	{

		$config = EasyBlogHelper::getConfig();

		$sharethis_publishers 	= $config->get('social_sharethis_publishers','');
		$bookmark               = '';

		if( !empty($sharethis_publishers) )
		{
			$theme 			= $config->get( 'layout_theme' );
			$displayText    = ($theme == 'default') ? '' : JText::_('COM_EASYBLOG_BOOKMARK');

			$bookmark = '<li id="bookmark-link" class="bookmark"><span class="st_sharethis" displayText="' .$displayText. '"></span>';
			$bookmark .= '<script type="text/javascript" src="https://ws.sharethis.com/button/buttons.js"></script><script type="text/javascript">stLight.options({publisher:\'' . $sharethis_publishers . '\'});</script>';
			$bookmark .= '</li>';
		}

		return $bookmark;
	}
}
