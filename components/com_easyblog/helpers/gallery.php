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

class EasyBlogGalleryHelper
{
	/**
	 * Search and removes the gallery tag from the content.
	 *
	 * @access	public
	 * @param	string	$content	The content to search on.
	 *
	 */
	public function strip( $content )
	{
		$pattern	= '/\[embed=gallery\].*?\[\/embed\]/';

		return preg_replace( $pattern , '' , $content );
	}

	/**
	 * Processes an audio tag and replaces it with the necessary embed codes.
	 *
	 * @param	string	$content	The content that should be looked up on.
	 */
	public function getHTMLArray( $content , $userId = '' )
	{
		// @task: Process new gallery tags. These tags are only used in 3.5 onwards.
		$pattern	= '/\[embed=gallery\](.*)\[\/embed\]/i';
		$result		= array();

		preg_match_all( $pattern , $content , $matches , PREG_SET_ORDER );

		if( !empty( $matches ) )
		{
			$cfg		= EasyBlogHelper::getConfig();
			$json		= new Services_JSON();

			foreach( $matches as $match )
			{
				// The full text of the matched content.
				$text		= $match[ 0 ];

				// The json string
				$jsonString	= $match[ 1 ];

				// Let's parse the JSON string and get the result.
				$output		= self::parseJSON( $jsonString );

				// @task: When there's nothing there, we just return the original content.
				if( $output === false )
				{
					continue;
				}

				$result[]	= $output;
			}
		}

		// @task: Process legacy items
		// Lookup for div's with specific gallery classes.
		$pattern	= '#"easyblog-gallery[^>]*#is';

		preg_match_all( $pattern , $content , $matches );

		if( $matches && count( $matches[0] ) > 0 )
		{
			$loaded	= array();

			foreach( $matches[0] as $match )
			{
				$input		= $match;

				preg_match( '/value="(.*\})"/i' , $input , $data );


				if( $data )
				{
					$result[]	= self::parseJSON( $data[1] , $userId );
				}
			}
		}

		return $result;
	}

	/**
	 * Processes an audio tag and replaces it with the necessary embed codes.
	 *
	 * @param	string	$content	The content that should be looked up on.
	 */
	public function process( $content , $userId = '' )
	{
		// @task: Process new gallery tags. These tags are only used in 3.5 onwards.
		$pattern	= '/\[embed=gallery\](.*)\[\/embed\]/i';

		preg_match_all( $pattern , $content , $matches , PREG_SET_ORDER );

		if( !empty( $matches ) )
		{
			$cfg		= EasyBlogHelper::getConfig();
			$json		= new Services_JSON();

			foreach( $matches as $match )
			{
				// The full text of the matched content.
				$text		= $match[ 0 ];

				// The json string
				$jsonString	= $match[ 1 ];

				// Let's parse the JSON string and get the result.
				$result		= self::parseJSON( $jsonString );

				// @task: When there's nothing there, we just return the original content.
				if( $result === false )
				{
					// @TODO: Remove the gallery tag.
					return $content;
				}

				// Now, we'll need to alter the original contents.
				$content	= str_ireplace( $text , $result , $content );
			}
		}

		$content	= $this->processLegacyGallery( $content , $userId );

		return $content;
	}

	private static function parseJSON( $jsonString , $userId = '' )
	{
		if( !$jsonString )
		{
			return false;
		}

		$cfg		= EasyBlogHelper::getConfig();
		$json		= new Services_JSON();


		$jsonString	= str_ireplace( '\\' , '\\\\' , $jsonString );
		$obj		= $json->decode( $jsonString );
		$url		= '';

		if( isset( $obj->place ) )
		{
			$place		= $obj->place;
			$folder		= trim( $obj->file , '/\\' );

			// This will be the storage path.
			$storage	= '';

			if( $place == 'shared' )
			{
				$storage	= JPATH_ROOT . DIRECTORY_SEPARATOR . trim( $cfg->get( 'main_shared_path' ) , '/\\' ) . DIRECTORY_SEPARATOR . $folder;
				$url		= rtrim( JURI::root() , '/' ) . '/' . str_ireplace( '\\' , '/' , trim( $cfg->get( 'main_shared_path' ) ) ) . '/' . $folder;
			}
			else
			{
				$place 			= explode( ':' , $place );

				$storage		= JPATH_ROOT . DIRECTORY_SEPARATOR . trim( $cfg->get( 'main_image_path' ) , '/\\' ) . DIRECTORY_SEPARATOR . $place[1] . DIRECTORY_SEPARATOR . $folder;
				$url			= rtrim( JURI::root() , '/' ) . '/' . str_ireplace( '\\' , '/' , trim( $cfg->get( 'main_image_path' ) ) ) . '/' . $place[1] . '/' . $folder;
			}
		}
		else
		{
			// Legacy method.
			$tmpStorage	= str_ireplace( '/' , DIRECTORY_SEPARATOR , rtrim( $cfg->get( 'main_image_path' ) , '/' ) );
			$url		= rtrim( JURI::root() , '/' ) . '/' . str_ireplace( '\\' , '/' , $tmpStorage ) . '/' . $userId . '/' . $obj->path;
			$storage	= JPATH_ROOT . DIRECTORY_SEPARATOR . $tmpStorage . DIRECTORY_SEPARATOR . $userId . DIRECTORY_SEPARATOR . $obj->path;
		}

		// @task: Replace all / and \ from storage to the directory structure.
		$storage        = str_ireplace( array( '\\'  , '/' ) , DIRECTORY_SEPARATOR , $storage );

		// @task: Let's test if the folder really exists.
		if( !JFolder::exists( $storage ) )
		{
			return false;
		}

		// @task: Do not include image variations in the list.
		$excludeFilter     = array(EBLOG_MEDIA_THUMBNAIL_PREFIX , EBLOG_BLOG_IMAGE_PREFIX . '_*', EBLOG_USER_VARIATION_PREFIX , EBLOG_SYSTEM_VARIATION_PREFIX );

		// @task: Only allow specific file types here.
		$allowed           = EBLOG_GALLERY_EXTENSION;

		// @task: Let's get a list of images within this folder.
		$items		= JFolder::files( $storage , $allowed , false , false , array('.svn', 'CVS', '.DS_Store', '__MACOSX','index.html') , $excludeFilter );
		$images		= array();

		if( !$items )
		{
			return false;
		}

		// @task: For Joomla 1.5 sake, we need to filter this out again :(
		$regexFilter = '/(' . implode('|', $excludeFilter) . ')/';

		foreach( $items as $item )
		{
			// Legacy fixes for Joomla 1.5
			if( !preg_match( $regexFilter , $item ) )
			{
				// @task: This is the path to the original image.
				$itemPath		= $storage . DIRECTORY_SEPARATOR . $item;

				// @TODO: Currently the thumbnail variation is hardcoded. Perhaps make this configurable.
				$itemThumbPath	= $storage . DIRECTORY_SEPARATOR . EBLOG_SYSTEM_VARIATION_PREFIX . '_thumbnail_' . $item;

				// The original item needs to exist.
				if( JFile::exists( $itemPath ) )
				{
					$image				= new stdClass();
					$image->original	= $item;
					$image->thumbnail	= EBLOG_SYSTEM_VARIATION_PREFIX . '_thumbnail_' . $item;

					// @task: Test to see if the thumbnail variation exists.
					if( !JFile::exists( $itemThumbPath ) )
					{
						// @legacy Try to search for old prior to 3.5 image thumb format.
						$itemThumbPath		= $storage . DIRECTORY_SEPARATOR . EBLOG_MEDIA_THUMBNAIL_PREFIX . $item;
						$image->thumbnail	= EBLOG_MEDIA_THUMBNAIL_PREFIX . $item;
						if( !JFile::exists( $itemThumbPath ) )
						{
							$image->thumbnail	= $item;
						}
					}

					// Add this to the result.
					$images[]	= $image;
				}
			}
		}

		$theme	= new CodeThemes();
		$theme->set( 'uid'		, uniqid() );
		$theme->set( 'images' 	, $images );
		$theme->set( 'baseURI'	, $url );

		$output 	= $theme->fetch( 'blog.gallery.php' );


		return $output;
	}

	/**
	 * @legacy Legacy method to support older galleries prior to 3.5
	 *
	 */
	public static function processLegacyGallery( $content , $userId )
	{

		// Lookup for div's with specific gallery classes.
		$pattern	= '#"easyblog-gallery[^>]*#is';

		preg_match_all( $pattern , $content , $matches );

		if( $matches && count( $matches[0] ) > 0 )
		{

			$loaded	= array();

			foreach( $matches[0] as $match )
			{
				$input		= $match;

				preg_match( '/value="(.*\})"/i' , $input , $data );


				if( $data )
				{
					$gallery = self::parseJSON( $data[ 1 ] , $userId );

					// Remove gallery from content
					$pattern	= '#<div class="easyblog-placeholder-gallery"(.*)</div>#is';

					$content 	= preg_replace( $pattern , '' , $content );

					$content	.= $gallery;
				}
			}
		}
		return $content;
	}

}
