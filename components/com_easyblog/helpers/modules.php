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

require_once(JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'helpers' . DIRECTORY_SEPARATOR . 'helper.php');
require_once(EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'easysimpleimage.php' );

jimport('joomla.system.file');
jimport('joomla.system.folder');


class EasyBlogModulesHelper
{
	public static function getMedia( &$row , $params, $size = array() )
	{
		$media  = '';
		$type	= 'image'; //default to image only.

		switch( $type )
		{
			case 'video':
				$row->intro		= EasyBlogHelper::getHelper( 'Videos' )->processVideos( $row->intro );
				$row->content	= EasyBlogHelper::getHelper( 'Videos' )->processVideos( $row->content );

				break;
			case 'audio':
				$row->intro		= EasyBlogHelper::getHelper( 'Audio' )->process( $row->intro );
				$row->content	= EasyBlogHelper::getHelper( 'Audio' )->process( $row->content );
				break;
			case 'image':

				$imgSize    = '';

				if( !empty( $size ) )
				{
					if( isset( $size['width'] ) && isset( $size['height'] ) )
					{
						$width	 	= $size[ 'width' ] != 'auto' ? 'max-width: ' .$size['width'] . 'px; width:auto' : 'width:auto';
						$height		= $size[ 'height' ] != 'auto' ? 'max-height:' . $size['height'] . 'px; height:auto' : 'height:auto';

						$imgSize    = ' style="' . $width . ';' . $height . ';"';
					}
				}

				if( $row->getImage() )
				{
					$media	=	$row->getImage()->getSource( 'small' );
					if( !empty( $imgSize ) )
					{
						$media  = str_replace('<img', '<img ' . $imgSize . ' ', $media);
					}

				}

				if( empty( $media ) )
				{
					$media = self::getFeaturedImage( $row, $params);
					if( !empty( $imgSize ) )
					{
						$media  = str_replace('<img', '<img ' . $imgSize . ' ', $media);
					}
				}
				else
				{
					$media	= '<img src=" ' . $media . '" class="blog-image" style="margin: 0 5px 5px 0;border: 1px solid #ccc;padding:3px;" ' .$imgSize.'/>';
				}

				break;
			default:
				break;
		}

		if( $type != 'image')
		{
			// remove images.
			$pattern				= '#<img[^>]*>#i';
			preg_match( $pattern , $row->intro . $row->content , $matches );
			if( isset( $matches[0] ) )
			{
				// After extracting the image, remove that image from the post.
				$row->intro		= str_ireplace( $matches[0] , '' , $row->intro );
				$row->content	= str_ireplace( $matches[0] , '' , $row->intro );
			}
		}

		return $media;
	}


	public static function getFeaturedImage( &$row , &$params )
	{
		$pattern	= '#<img class="featured"[^>]*>#i';
		$content	= $row->intro . $row->content;

		preg_match( $pattern , $content , $matches );

		if( isset( $matches[0] ) )
		{
			return self::getResizedImage($matches[0] , $params );
		}

		// If featured image is not supplied, try to use the first image as the featured post.
		$pattern				= '#<img[^>]*>#i';

		preg_match( $pattern , $content , $matches );

		if( isset( $matches[0] ) )
		{
			// After extracting the image, remove that image from the post.
			$row->intro		= str_ireplace( $matches[0] , '' , $row->intro );
			$row->content	= str_ireplace( $matches[0] , '' , $row->intro );

			return self::getResizedImage($matches[0] , $params );
		}

		// If all else fail, try to use the default image
		return false;
	}

	public static function getResizedImage( $img , $params )
	{
		preg_match( '/src= *[\"¦\']{0,1}([^\"\'\>]*)/i' , $img , $matches );

		if( !isset( $matches[ 1 ] ) )
		{
			return $img;
		}

		// We find the thumb and make it a popup
		if( stristr( $matches[1] , 'thumb_' ) === false )
		{
			return $img;
		}

		// Test if the full image exists.
		jimport( 'joomla.filesystem.file' );

		$info	= pathinfo( $matches[ 1 ] );

		$thumb	= JPATH_ROOT . DIRECTORY_SEPARATOR . str_ireplace( '/' , DIRECTORY_SEPARATOR , $matches[ 1 ] );
		$full	= str_ireplace( 'thumb_' , '' , $thumb );

		if( !JFile::exists( $full ) )
		{
			return $img;
		}

		return '<a href="' . str_ireplace( 'thumb_' , '' , $matches[1] ) . '" class="easyblog-thumb-preview">'
			 . $img . '</a>';
	}

	public static function getThumbnailImage($img)
	{
		$srcpattern = '/src=".*?"/';

		preg_match( $srcpattern , $img , $src );

		if(isset($src[0]))
		{
			$imagepath	= trim(str_ireplace('src=', '', $src[0]) , '"');
			$segment 	= explode('/', $imagepath);
			$file 		= end($segment);
			$thumbnailpath = str_ireplace($file, 'thumb_'.$file, implode('/', $segment));

			if(!JFile::exists($thumbnailpath))
			{
				$image = new EasySimpleImage();
				$image->load($imagepath);
				$image->resize(64, 64);
				$image->save($thumbnailpath);
			}

			$newSrc = 'src="'.$thumbnailpath.'"';
		}
		else
		{
			return false;
		}

		$oldAttributes = array('src'=>$srcpattern, 'width'=>'/width=".*?"/', 'height'=>'/height=".*?"/');
		$newAttributes = array('src'=>$newSrc,'width'=>'', 'height'=>'');

		return preg_replace($oldAttributes, $newAttributes, $img);
	}


}
