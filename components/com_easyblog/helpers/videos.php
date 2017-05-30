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

class EasyBlogVideosHelper
{
	private $patterns	= array(
									'youtube.com'		=> 'youtube',
									'youtu.be'			=> 'youtube',
									'vimeo.com'			=> 'vimeo',
									'yahoo.com'			=> 'yahoo',
									'metacafe.com'		=> 'metacafe',
									'google.com'		=> 'google',
									'mtv.com'			=> 'mtv',
									'liveleak.com'		=> 'liveleak',
									'revver.com'		=> 'revver',
									'dailymotion.com'	=> 'dailymotion',
									'nicovideo.jp'		=> 'nicovideo',
									'blip.tv'			=> 'blip'
								);

	public function strip( $content )
	{

		// In case Joomla tries to entity the contents, we need to replace accordingly.
		$content	= str_ireplace( '&quot;' , '"' , $content );

		$pattern	= array('/\{video:.*?\}/',
							'/\{"video":.*?\}/',
							'/\[embed=.*?\].*?\[\/embed\]/'
							);

		$replace    = array('','');


		return preg_replace( $pattern , $replace , $content );
	}

	/*
		isPlain - return only video url in the content when is true. else, return embeded video object
	*/
	public function processVideos( $content, $isPlain = false )
	{

		// @since 3.5
		// New pattern uses [embed=videolink] to process embedded videos from external URLs.
		//
		// videolink - External video URLs like Youtube, Google videos, MTV
		// video - Internal video URLs that are uploaded via media manager
		$pattern	= '/\[embed=(.*)\](.*)\[\/embed\]/uiU';
		preg_match_all( $pattern , $content , $matches , PREG_SET_ORDER );

		if( !empty( $matches ) )
		{
			foreach( $matches as $match )
			{
				$type 	= $match[1];
				$search	= $match[0];
				$result	= $match[2];

				switch( $type )
				{
					// Videos that are linked from external sites such as Youtube.
					case 'videolink':
						$content	= $this->processExternalVideos( $content , $isPlain , $search , $result );
					break;
					// Normal video embeds that are uploaded via media manager.
					case 'video':
						$content	= $this->processInternalVideos( $content , $isPlain , $search , $result );
					break;
				}
			}
		}

		// @legacy Prior to 3.5, internal videos uses the format of <div class="easyblog-placeholder-video">
		$content	= $this->processInternalVideos( $content , $isPlain );

		// @legacy Prior to 3.5, videos only uses {"video":"url" } so we still need to replace this if it exists.
		$content	= $this->processExternalVideos( $content , $isPlain );

		return $content;
	}

	public function processInternalVideos( $content , $isPlain = false , $findText = '' , $result = '' )
	{
		$cfg		= EasyBlogHelper::getConfig();

		// Since 3.0 uses a different video format, we need to do some tests here.
		if( $result )
		{
			require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );

			$json	= new Services_JSON();
			$data	= $json->decode( $result );

			$file		= trim( $data->file , '/\\' );
			$width		= $data->width;
			$height		= $data->height;
			$autostart	= $data->autostart;
			$place		= $data->place;

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

			// Give a unique id for the video.
			$theme->set( 'uid'			, uniqid() );
			$theme->set( 'width' 		, $width );
			$theme->set( 'height'		, $height );
			$theme->set( 'autoplay'		, $autostart );
			$theme->set( 'url'			, $url );
			$output		= $theme->fetch( 'blog.video.php' );

			$content	= str_ireplace( $findText , $output , $content );

			return $content;
		}


		// @legacy Legacy support for prior to 3.5
		// This should be removed later.
		$pattern	= '#"easyblog-video*[^>]*#is';
		$html		= $content;

		preg_match( $pattern , $content , $matches );

		if( !empty($matches) && isset( $matches[0 ] ) )
		{
			// Match the value
			$input		= $matches[ 0 ];
			preg_match( '/value="(.*})"/i' , $input , $data );

			if( isset( $data[1] ) )
			{
				$json		= new Services_JSON();
				$data[1]	= str_ireplace( '"' , '' , $data[ 1 ] );
				$obj		= $json->decode( $data[ 1 ] );

				$theme		= new CodeThemes();
				$uri		= rtrim( JURI::root() , '/' ) . '/' . str_ireplace( "\\" , '/' , rtrim( $cfg->get( 'main_image_path' ) , '/' ) ) . '/' . $userId;
				$uri		.= '/' . $obj->path;

				$storage	= str_ireplace( '/' , DIRECTORY_SEPARATOR , rtrim( $cfg->get( 'main_image_path' ) , '/' ) );

				if( !isset( $obj->autoplay ) )
				{
					$obj->autoplay	= false;
				}

				$theme->set( 'url'		, $uri );
				$theme->set( 'width'	, $obj->width );
				$theme->set( 'height'	, $obj->height );
				$theme->set( 'autoplay'	, false );
				$theme->set( 'video'	, $obj );
				$theme->set( 'uid' 		, uniqid() );

				$output		= $theme->fetch( 'blog.video.php' );

				$pattern	= array( '#<div class="easyblog-placeholder-video(.*?)</div>(.*?)#is' );
				$content	= preg_replace( $pattern , $output , $content );

				// Remove unwanted data
				$content	= preg_replace( '#<p>&nbsp;</p>(|\n|\r\n)<p><input class="easyblog-video(.*?)</p>#is' , '' , $content );
				return $content;
			}
		}

		return $content;
	}

	/**
	 * Processes external video links such as youtube, vimeo etc.
	 *
	 * @access	public
	 *
	 */
	public function processExternalVideos( $content , $isPlain = false , $findText = '' , $jsonString = '' )
	{
		$cfg		= EasyBlogHelper::getConfig();

		if( !empty( $jsonString ) )
		{
			$json	= new Services_JSON();

			$data		= $json->decode( $jsonString );

			$search 	= !empty( $findText ) ? $findText : $jsonString;

			if( $isPlain )
			{
				$html 		= ' ' . $data->video . ' ';
				$content	= str_ireplace( $search , $html , $content );
			}
			else
			{
				// @task: Ensure that the width doesn't exceed the maximum width settings.
				$data->width	= $data->width > $cfg->get( 'max_video_width' ) ? $cfg->get( 'max_video_width' ) : $data->width;

				// @task: Ensure that the height doesn't exceed the maximum height settings.
				$data->height	= $data->height > $cfg->get( 'max_video_height' ) ? $cfg->get( 'max_video_height' ) : $data->height;

				$data->video	= strip_tags( $data->video );
				$output 	= $this->processVideoLink( $data->video , $data->width , $data->height );

				if( $output !== false )
				{
					$content	= str_ireplace( $search , $output , $content );
				}
			}
		}

		if( empty( $jsonString ) )
		{
			// @since 2.0.3515, video:"url" has been changed to "video":"url"
			$pattern	= '/\{"video":.*?\}/i';

			// In case Joomla tries to entity the contents, we need to replace accordingly.
			$tmpContent	= str_ireplace( '&quot;' , '"' , $content );

			preg_match_all( $pattern , $tmpContent , $matches );
			$videos		= $matches[0];

			// @since 2.0.3515 Legacy support for older codes video:"url"
			if( empty( $videos ) )
			{
				$pattern	= '/\{video:.*?\}/i';
				preg_match_all( $pattern , $tmpContent , $matches );
				$videos		= $matches[0];
			}

			foreach( $videos as $video )
			{
				$json	= new Services_JSON();

				$data		= $json->decode( $video );

				$search 	= !empty( $findText ) ? $findText : $video;

				if( $isPlain )
				{
					$html 		= ' ' . $data->video . ' ';
					$content	= str_ireplace( $search , $html , $content );
				}
				else
				{
					// @task: Ensure that the width doesn't exceed the maximum width settings.
					$data->width	= $data->width > $cfg->get( 'max_video_width' ) ? $cfg->get( 'max_video_width' ) : $data->width;

					// @task: Ensure that the height doesn't exceed the maximum height settings.
					$data->height	= $data->height > $cfg->get( 'max_video_height' ) ? $cfg->get( 'max_video_height' ) : $data->height;

					$data->video	= strip_tags( $data->video );
					$output 	= $this->processVideoLink( $data->video , $data->width , $data->height );

					if( $output !== false )
					{
						$content	= str_ireplace( $search , $output , $content );
					}
				}
			}
		}

		return $content;
	}


	/**
	 * Given a set of content, try to match and return the list of videos that are found in the content.
	 * This is only applicable for videos that are supported by the library.
	 *
	 * @author	imarklee
	 * @access	public
	 * @param	string	$content	The html contents that we should look for.
	 * @return	Array				An array of videos that are found.
	 */
	public function getHTMLArray( $content )
	{
		// This will eventually contain all the video objects
		$result 	= array();

		// Store temporary content for legacy fixes.
		$tmpContent	= $content;

		// @since 3.5
		// New pattern uses [embed=videolink] to process embedded videos from external URLs.
		//
		// videolink - External video URLs like Youtube, Google videos, MTV
		// video - Internal video URLs that are uploaded via media manager
		$pattern	= '/\[embed=(.*)\](.*)\[\/embed\]/uiU';
		preg_match_all( $pattern , $content , $matches , PREG_SET_ORDER );

		if( !empty( $matches ) )
		{
			foreach( $matches as $match )
			{
				$type 		= $match[1];
				$search		= $match[0];
				$rawJSON	= $match[2];

				if( $type == 'videolink' || $type == 'video' )
				{
					$data		= $this->parseJSON( $rawJSON );

					// Let's remove it from the temporary content.
					$tmpContent	= str_ireplace( $search , '' , $tmpContent );

					switch( $type )
					{
						// Videos that are linked from external sites such as Youtube.
						case 'videolink':
							$html 	= $this->processVideoLink( $data->video , $data->width , $data->height );
						break;
						// Normal video embeds that are uploaded via media manager.
						case 'video':
							$html	= $this->processInternalVideoLink( $content , $rawJSON );
						break;
					}

					// Now, let's add the data object back to the result list.
					$result[]	= $html;
				}
			}
		}

		// @legacy prior to EasyBlog 3.5
		// @since 2.0.3515, video:"url" has been changed to "video":"url"
		$pattern	= '/\{"video":.*?\}/i';

		// In case Joomla tries to entity the contents, we need to replace accordingly.
		$tmpContent	= str_ireplace( '&quot;' , '"' , $tmpContent );

		// @task: We search in $tmpContent instead of $content because the new [embed] codes might already replace the values.
		preg_match_all( $pattern , $tmpContent , $matches );
		$videos		= $matches[0];

		if( empty( $videos ) )
		{
			return $result;
		}

		foreach( $videos as $video )
		{
			$data		= $this->parseJSON( $video );
			$output		= $this->processVideoLink( $data->video , $data->width , $data->height );

			if( $output !== false )
			{
				$result[]		= $output;
			}
		}

		return $result;
	}

	/**
	 * Given a set of content, try to match and return the list of videos that are found in the content.
	 * This is only applicable for videos that are supported by the library.
	 *
	 * @author	imarklee
	 * @access	public
	 * @param	string	$content	The html contents that we should look for.
	 * @return	Array				An array of videos that are found.
	 */
	public function getVideoObjects( $content )
	{
		// This will eventually contain all the video objects
		$result 	= array();

		// Store temporary content for legacy fixes.
		$tmpContent	= $content;

		// @since 3.5
		// New pattern uses [embed=videolink] to process embedded videos from external URLs.
		//
		// videolink - External video URLs like Youtube, Google videos, MTV
		// video - Internal video URLs that are uploaded via media manager
		$pattern	= '/\[embed=(.*)\](.*)\[\/embed\]/uiU';
		preg_match_all( $pattern , $content , $matches , PREG_SET_ORDER );

		if( !empty( $matches ) )
		{
			foreach( $matches as $match )
			{
				$type 		= $match[1];
				$search		= $match[0];
				$rawJSON	= $match[2];

				$data		= $this->parseJSON( $rawJSON );

				// Let's remove it from the temporary content.
				$tmpContent	= str_ireplace( $search , '' , $tmpContent );

				switch( $type )
				{
					// Videos that are linked from external sites such as Youtube.
					case 'videolink':
						$data->html 	= $this->processVideoLink( $data->video , $data->width , $data->height );
					break;
					// Normal video embeds that are uploaded via media manager.
					case 'video':
						// TODO
					break;
				}

				// Now, let's add the data object back to the result list.
				$result[]	= $data;
			}
		}

		// @legacy prior to EasyBlog 3.5
		// @since 2.0.3515, video:"url" has been changed to "video":"url"
		$pattern	= '/\{"video":.*?\}/i';

		// In case Joomla tries to entity the contents, we need to replace accordingly.
		$tmpContent	= str_ireplace( '&quot;' , '"' , $tmpContent );

		// @task: We search in $tmpContent instead of $content because the new [embed] codes might already replace the values.
		preg_match_all( $pattern , $tmpContent , $matches );
		$videos		= $matches[0];

		if( empty( $videos ) )
		{
			return $result;
		}

		foreach( $videos as $video )
		{
			$data		= $this->parseJSON( $video );
			$output		= $this->processVideoLink( $data->video , $data->width , $data->height );

			if( $output !== false )
			{
				$data->html 	= $output;
				$result[]		= $data;
			}
		}

		return $result;
	}

	private function parseJSON( $jsonRaw )
	{
		// Initialize the JSON library
		$json	= new Services_JSON();

		return $json->decode( $jsonRaw );
	}

	private function getDomain( $link )
	{

		$link = strip_tags( $link );
		$domain = "";

		if( stristr( $link , 'http://') === false && stristr( $link , 'https://') === false )
		{
			$link 	= 'http://' . $link;
		}

		// Remove any html codes if any
		$link	= strip_tags( $link );

		$link	= parse_url( $link );
		$link 	= explode( '.' , $link[ 'host' ] );

		if( count($link) >= 2 )
		{
			$domain = $link[ count( $link ) - 2 ] . '.' . $link[ count( $link ) - 1 ];
		}

		return $domain;
	}

	public function processInternalVideoLink( $content , $jsonString = '' )
	{
		$cfg		= EasyBlogHelper::getConfig();

		// Since 3.0 uses a different video format, we need to do some tests here.
		if( $jsonString )
		{
			require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'json.php' );

			$json	= new Services_JSON();

			$data	= $json->decode( $jsonString );

			$file		= trim( $data->file , '/\\' );
			$width		= isset( $data->width ) ? $data->width : 0;
			$height		= $data->height;
			$autostart	= $data->autostart;
			$place		= $data->place;

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

			// Give a unique id for the video.
			$theme->set( 'uid'			, uniqid() );
			$theme->set( 'width' 		, $width );
			$theme->set( 'height'		, $height );
			$theme->set( 'autoplay'		, $autostart );
			$theme->set( 'url'			, $url );
			$output		= $theme->fetch( 'blog.video.php' );

			return $output;
		}


		// @legacy Legacy support for prior to 3.5
		// This should be removed later.
		$pattern	= '#"easyblog-video*[^>]*#is';

		preg_match( $pattern , $content , $matches );

		if( !empty($matches) && isset( $matches[0 ] ) )
		{
			// Match the value
			$input		= $matches[ 0 ];
			preg_match( '/value="(.*})"/i' , $input , $data );

			if( isset( $data[1] ) )
			{
				$json		= new Services_JSON();
				$data[1]	= str_ireplace( '"' , '' , $data[ 1 ] );
				$obj		= $json->decode( $data[ 1 ] );

				$theme		= new CodeThemes();
				$uri		= rtrim( JURI::root() , '/' ) . '/' . str_ireplace( "\\" , '/' , rtrim( $cfg->get( 'main_image_path' ) , '/' ) ) . '/' . $userId;
				$uri		.= '/' . $obj->path;

				$storage	= str_ireplace( '/' , DIRECTORY_SEPARATOR , rtrim( $cfg->get( 'main_image_path' ) , '/' ) );

				if( !isset( $obj->autoplay ) )
				{
					$obj->autoplay	= false;
				}

				$theme->set( 'url'		, $uri );
				$theme->set( 'width'	, $obj->width );
				$theme->set( 'height'	, $obj->height );
				$theme->set( 'autoplay'	, false );
				$theme->set( 'video'	, $obj );
				$theme->set( 'uid' 		, uniqid() );

				$output		= $theme->fetch( 'blog.video.php' );

				$pattern	= array( '#<div class="easyblog-placeholder-video(.*?)</div>(.*?)#is' );
				$content	= preg_replace( $pattern , $output , $content );

				return $content;
			}
		}
	}

	public function processVideoLink( $link , $width , $height )
	{
		$domain 	= $this->getDomain( $link );

		if( !array_key_exists( $domain , $this->patterns ) )
		{
			// $content	= str_ireplace( $video , $html , $content );
			return false;
		}

		$provider	= JString::strtolower( $this->patterns[ $domain ] );

		$path		= EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'videos' . DIRECTORY_SEPARATOR . $provider . '.php';

		require_once( $path );

		$class	= 'EasyBlogVideo' . ucfirst( $this->patterns[ $domain ] );

		if( class_exists( $class ) )
		{
			$object		= new $class();
			$html		= $object->getEmbedHTML( $link , $width , $height );

			return $html;
		}

		return false;
	}
}
