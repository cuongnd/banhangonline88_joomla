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

jimport( 'joomla.filesystem.file' );

class EasyBlogCommentLiveFyre
{
	public static function getHTML( $blog )
	{
		$theme		= new CodeThemes();
		$config		= EasyBlogHelper::getConfig();

		$siteId		= $config->get( 'comment_livefyre_siteid' );
		
		$theme->set( 'siteId' , $siteId );
		$theme->set( 'blog' , $blog );
		$contents	= $theme->fetch( 'comment.livefyre.php' );

		return $contents;
	}
}
