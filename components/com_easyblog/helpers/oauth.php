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
jimport( 'joomla.filesystem.folder' );

class EasyBlogOauthHelper
{
	/**
	 * Detects where the system has been setup before
	 */
	public function isAssociated( $type )
	{
		$db 		= EasyBlogHelper::db();
		$query		= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__easyblog_oauth' ) . ' '
					. 'WHERE ' . $db->nameQuote( 'system' ) . '=' . $db->Quote( 1 ) . ' '
					. 'AND ' . $db->nameQuote( 'type' ) . '=' . $db->Quote( $type ) . ' '
					. 'AND ' . $db->nameQuote( 'access_token' ) . ' !=""';
		$db->setQuery( $query );

		return $db->loadResult() > 0;
	}

	/**
	 * Try to get the consumer type based on the given type.
	 *
	 * @param	string	$type	The client app type.
	 * @param	string	$api	The API key required for most oauth clients
	 * @param	string	$secret	The API secret required for oauth to work
	 * @param	string	$callback	The callback URL.
	 *
	 * @return	oauth objects.
	 **/
	function getConsumer( $type , $api , $secret , $callback )
	{
		static $loaded	= array();

		if( !isset( $loaded[ $type ] ) )
		{
			$file	= EBLOG_CLASSES . DIRECTORY_SEPARATOR . $type . DIRECTORY_SEPARATOR . 'helper.php';

			if( JFile::exists( $file ) )
			{
				require_once( $file );
				$class	= 'EasyBlog' . ucfirst( $type );

				if( class_exists( ucfirst( $class ) ) )
				{
					$loaded[ $type ]	= new $class( $api , $secret , $callback );
				}
				else
				{
					$loaded[ $type ]	= false;
				}
			}
			else
			{
				$loaded[ $type ]	= false;
			}

		}
		return $loaded[ $type ];
	}
}
