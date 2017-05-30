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

require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );

class EasyBlogAudioHelper
{
	public function strip( $content )
	{

		// In case Joomla tries to entity the contents, we need to replace accordingly.
		$content	= str_ireplace( '&quot;' , '"' , $content );
		$pattern	= '/\[embed=audio\](.*)\[\/embed\]/uiU';
		$replace    = '';

		return preg_replace( $pattern , $replace , $content );
	}

	public function getHTMLArray( $content )
	{
		$pattern	= '/\[embed=audio\](.*)\[\/embed\]/uiU';
		$result 	= '';

		preg_match_all( $pattern , $content , $matches , PREG_SET_ORDER );

		if( !empty( $matches ) )
		{
			$cfg		= EasyBlogHelper::getConfig();
			$json		= new Services_JSON();

			foreach( $matches as $match )
			{
				// The full text of the matched content.
				$text	= $match[ 0 ];

				// The json string
				$jsonString	= $match[ 1 ];

				$obj		= $json->decode( $jsonString );

				// If there's nothing, let's just remove the tag.
				if( !$obj )
				{
					continue;
				}

				$file		= $obj->file;
				$autoplay	= ( isset( $obj->autostart ) ) ? $obj->autostart : '0';
				$place		= $obj->place;

				if( $place == 'shared' )
				{
					$url		= rtrim( JURI::root() , '/' ) . '/' . trim( str_ireplace( '\\' , '/' , $cfg->get( 'main_shared_path' ) ) , '/\\') . '/' . $file;
				}
				else
				{
					$place 			= explode( ':' , $place );
					$url			= rtrim( JURI::root() , '/' ) . '/' . trim( $cfg->get( 'main_image_path' ) , '/\\') . '/' . $place[1] . '/' . $file;
				}

				$theme		= new CodeThemes();

				$theme->set( 'uid'	, uniqid() );
				$theme->set( 'url'	, $url );
				$theme->set( 'autoplay'	, $autoplay );

				$result[]	= $theme->fetch( 'blog.audio.php' );
			}
		}

		return $result;
	}

	/**
	 * Processes an audio tag and replaces it with the necessary embed codes.
	 *
	 * @param	string	$content	The content that should be looked up on.
	 */
	public function process( $content )
	{
		$pattern	= '/\[embed=audio\](.*)\[\/embed\]/uiU';

		preg_match_all( $pattern , $content , $matches , PREG_SET_ORDER );

		if( !empty( $matches ) )
		{
			$cfg		= EasyBlogHelper::getConfig();
			$json		= new Services_JSON();

			foreach( $matches as $match )
			{
				// The full text of the matched content.
				$text	= $match[ 0 ];

				// The json string
				$jsonString	= $match[ 1 ];

				$obj		= $json->decode( $jsonString );

				// If there's nothing, let's just remove the tag.
				if( !$obj )
				{
					$content	= str_ireplace( $text , '' , $content );

					return $content;
				}

				$file		= $obj->file;
				$autoplay	= ( isset( $obj->autostart ) ) ? $obj->autostart : '0';
				$place		= $obj->place;

				if( $place == 'shared' )
				{
					$url		= rtrim( JURI::root() , '/' ) . '/' . trim( str_ireplace( '\\' , '/' , $cfg->get( 'main_shared_path' ) ) , '/\\') . '/' . $file;
				}
				else
				{
					$place 			= explode( ':' , $place );
					$url			= rtrim( JURI::root() , '/' ) . '/' . trim( $cfg->get( 'main_image_path' ) , '/\\') . '/' . $place[1] . '/' . $file;
				}

				$theme		= new CodeThemes();

				$uid 		= uniqid();
				$theme->set( 'uid'	, $uid );
				$theme->set( 'url'	, $url );
				$theme->set( 'autoplay'	, $autoplay );

				$result		= $theme->fetch( 'blog.audio.php' );

				// Now, we'll need to alter the original contents.
				$position 	= strpos( $content , $text );
				$content 	= substr_replace( $content , $result , $position , JString::strlen( $text ) );

				// $content	= str_replace( $text , $result , $content );
			}
		}

		return $content;
	}
}
