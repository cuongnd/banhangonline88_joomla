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

class EasyBlogPingomaticHelper
{
	public function ping( $title , $url )
	{
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'pingomatic.php' );

		$title		= htmlspecialchars( $title );
		$pingomatic	= new EasyBlogPingomatic();
		$response	= $pingomatic->ping( $title , $url );

		if( $response[ 'status' ] == 'ko' )
		{
			return false;
		}
		return true;
	}
}
