<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR .'image.php' );

class EasyBlogMicroBlogPhoto
{
	/**
	 * Responsible to perform the blog mappings
	 */
	public function bind( &$blog )
	{
		$imageSource 	= JRequest::getVar( 'imageSource' );
		$desc 			= JRequest::getVar( 'content' );
		$title 			= JRequest::getVar( 'title' , '' );

		// If title is not set, we use the image name as the title
		if( $title == JText::_( 'COM_EASYBLOG_MICROBLOG_TITLE_OPTIONAL' , true ) || empty( $title ) )
		{
			$parts 		= parse_url($imageSource );
			$title 		= str_ireplace( dirname( $parts[ 'path' ] ) , '' , $parts[ 'path' ] );

			// Remove slashes and known file extensions
			$title 		= str_ireplace( array( '/' , '.jpg' , '.png' , '.jpeg' , '.gif' ) , '' , $title );

			// Replace underscores or dashes with space so that it looks like a proper title.
			$title 		= str_ireplace( array( '_' , '-' ) , ' ' , $title );
		}

		// Now we need to embed the image URL into the blog content.
		// @TODO: Specify width / height.
		$content 		= '<img src="' . $imageSource . '" class="photo-item" />';

		// @task: If user specified some description, append it into the content.
		if( !empty( $desc ) )
		{
			// @task: Replace newlines with <br /> tags since the form is a plain textarea.
			$desc 		= nl2br( $desc );
			$content		.= '<p class="photo-desc">' . $desc . '</p>';
		}

		$blog->set( 'title'		, $title );
		$blog->set( 'intro' 	, $content );
		$blog->set( 'source'	, EBLOG_MICROBLOG_PHOTO );
	}

	/**
	 * Method to validate a post
	 */
	public function validate()
	{
		$imageSource 	= JRequest::getVar( 'imageSource' );

		if( empty( $imageSource ) )
		{
			return JText::_( 'COM_EASYBLOG_MICROBLOG_ERROR_EMPTY_PHOTO' );
		}

		return true;
	}

	/**
	 * Since quotes are stored in the title, we don't really need to do anything here
	 */
	public function afterSave( &$blog )
	{
		return true;
	}

	public function getSuccessMessage()
	{
		return JText::_( 'COM_EASYBLOG_MICROBLOG_PHOTO_POSTED_SUCCESSFULLY' );
	}

	public function processOutput( &$row )
	{
		// Find and replace all images in intro.
		$obj			= self::getAndRemoveImages( $row->intro );

		if( $obj )
		{
			$row->intro 	= $obj->content;
			$row->images 	= $obj->images;			
		}

		// Lets strip out the images from the text / content.
		$obj			= self::getAndRemoveImages( $row->content );

		if( $obj )
		{
			$row->content 	= $obj->content;
			$row->images	= array_merge( $obj->images , $row->images );			
		}

		return $row;
	}

	public static function getAndRemoveImages( $content )
	{
		//try to search for the 1st img in the blog
		$img            = '';
		$pattern		= '#<img[^>]*>#i';
		$result 		= array();

		preg_match( $pattern , $content , $matches );

		if( isset( $matches[ 0 ] ) && !empty( $matches[ 0 ] ) )
		{
			
			$images 	= $matches[ 0 ];

			if( !is_array( $images ) )
			{
				$images 	= array( $images );
			}

			foreach( $images as $image )
			{
				$content 	= str_ireplace( $image , '' , $content );

				// Get the URL to the image
				$pattern = '/src=[\"\']?([^\"\']?.*(png|jpg|jpeg|gif))[\"\']?/i';
				preg_match($pattern, $image , $matches);

				if($matches)
				{
					$imgPath	= $matches[1];
					$source		= EasyImageHelper::rel2abs( $imgPath , JURI::root() );

					$result[]	= $source;
				}
			}

		}

		$obj 			= new stdClass();
		$obj->content	= $content;
		$obj->images	= $result;

		return $obj;
	}
}
