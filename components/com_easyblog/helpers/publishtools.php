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
require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'bookmark.php' );

class EasyBlogPublishToolsHelper
{
	function getHTML( $blogId )
	{
		$config = EasyBlogHelper::getConfig();
		$my     = JFactory::getUser();

		$pdfEnabled      = $config->get( 'layout_enablepdf' );
		$printEnabled    = $config->get( 'layout_enableprint' );

		// check if pdf enabled
		if( $pdfEnabled == '2' )
		{
			if( $my->id == 0 )
				$pdfEnabled = 0;
		}

		// check if pdf enabled
		if( $printEnabled == '2' )
		{
			if( $my->id == 0 )
				$printEnabled = 0;
		}


		$theme	= new CodeThemes();
		$theme->set( 'blogId'		, $blogId );
		$theme->set( 'pdfEnabled'	, $pdfEnabled );
		$theme->set( 'printEnabled'	, $printEnabled );
		$theme->set( 'pdfLinkProperties', EasyBlogHelper::getPDFlinkProperties() );
		$html 		= $theme->fetch( 'blog.publishing.tool.php' );

		$bookmark	= EasyBlogBookmark::getHTML();
		return $html . $bookmark;
	}
}
