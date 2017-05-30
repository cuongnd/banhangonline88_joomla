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

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );

class EasyBlogPaginationHelper
{
	public function getLimit( $key = 'listlength' )
	{
		$app		= JFactory::getApplication();
		$default 	= EasyBlogHelper::getJConfig()->get( 'list_limit' );

		if( $app->isAdmin() )
		{
			return $default;
		}

		$menus	= JFactory::getApplication()->getMenu();
		$menu	= $menus->getActive();
		$limit  = -2;

		if( is_object( $menu ) )
		{
			$params 	= EasyBlogHelper::getRegistry();
			$params->load( $menu->params );

			$limit      = $params->get( 'limit' , '-2' );
		}

		// if menu did not specify the limit, then we use easyblog setting.
		if( $limit == '-2' )
		{
			// Use default configurations.
			$config		= EasyBlogHelper::getConfig();

			// @rule: For compatibility between older versions
			if( $key == 'listlength' )
			{
				$key 	= 'layout_listlength';
			}
			else
			{
				$key 	= 'layout_pagination_' . $key;
			}

			$limit      = $config->get( $key );
		}

		// Revert to joomla's pagination if configured to inherit from Joomla
		if( $limit == '0' || $limit == '-1' || $limit == '-2' )
		{
			$limit		= $default;
		}

		return $limit;
	}
}
