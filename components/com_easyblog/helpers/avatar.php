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

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'image.php' );
require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'avatar.php' );

class EasyBlogAvatarHelper
{
	public function getAvatarURL( $profile )
	{
		$config		= EasyBlogHelper::getConfig();

		$class		= 'EasyBlogAvatar' . $config->get( 'layout_avatarIntegration' );
		$obj		= new $class();

		if( $obj->_init() )
		{
			return $obj->_getAvatar( $profile )->link;
		}

		$obj		= new EasyBlogAvatarDefault();
		return $obj->_getAvatar( $profile )->link;
	}
}
