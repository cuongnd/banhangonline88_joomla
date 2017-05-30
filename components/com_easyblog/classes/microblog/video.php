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

class EasyBlogMicroBlogVideo
{
	/**
	 * Responsible to perform the blog mappings
	 */
	public function bind( &$blog )
	{
		$videoSource 	= JRequest::getVar( 'videoSource' );
		$desc 			= JRequest::getVar( 'content' );
		$title 			= JRequest::getVar( 'title' , '' );


		// If title is not set, we use the image name as the title
		if( $title == JText::_( 'COM_EASYBLOG_MICROBLOG_TITLE_OPTIONAL' , true ) || empty( $title ) )
		{
			$date 		= EasyBlogHelper::getHelper( 'Date' )->dateWithOffSet( EasyBlogHelper::getDate()->toMySQL() );
			$dateString	= EasyBlogHelper::getHelper( 'Date' )->toFormat( $date , '%d-%m-%Y' );

			// Define a generic video title
			$title 		= JText::sprintf( 'COM_EASYBLOG_MICROBLOG_VIDEO_TITLE_GENERIC' , $dateString );
		}

		// Get the default settings
		$config 	= EasyBlogHelper::getConfig();
		$width 		= $config->get( 'dashboard_video_width' );
		$height 	= $config->get( 'dashboard_video_height' );

		// Now we need to embed the image URL into the blog content.
		$content 		= '[embed=videolink]{"video":"' . $videoSource . '","width":"' . $width . '","height":"' .$height . '"}[/embed]';

		// @task: If user specified some description, append it into the content.
		if( !empty( $desc ) )
		{
			// @task: Replace newlines with <br /> tags since the form is a plain textarea.
			$desc 			= nl2br( $desc );

			$content		.= '<p>' . $desc . '</p>';
		}

		$blog->set( 'title'		, $title );
		$blog->set( 'content' 	, $content );
		$blog->set( 'source'	, EBLOG_MICROBLOG_VIDEO );
	}

	/**
	 * Method to validate a post
	 */
	public function validate()
	{
		$videoSource 	= JRequest::getVar( 'videoSource' );

		if( empty( $videoSource ) )
		{
			return JText::_( 'COM_EASYBLOG_MICROBLOG_ERROR_EMPTY_VIDEO' );
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
		return JText::_( 'COM_EASYBLOG_MICROBLOG_VIDEO_POSTED_SUCCESSFULLY' );
	}

	public function processOutput( &$row )
	{
		// Find and replace all images in intro.
		$obj			= self::getAndRemoveVideo( $row->intro );

		if( $obj )
		{
			$row->intro 	= $obj->content;
			$row->videos 	= $obj->videos;			
		}

		// Lets strip out the images from the text / content.
		$obj			= self::getAndRemoveVideo( $row->content );

		if( $obj )
		{
			$row->content 	= $obj->content;
			$row->videos	= array_merge( $obj->videos , $row->videos );			
		}

		return $row;
	}

	public static function getAndRemoveVideo( $content )
	{
		// @task: Retrieve all videos from the content
		$videos		= EasyBlogHelper::getHelper( 'Videos' )->getVideoObjects( $content , true );

		// @task: Strip out all video codes from the content
		$content 	= EasyBlogHelper::getHelper( 'Videos' )->strip( $content );

		$obj 			= new stdClass();
		$obj->content	= $content;
		$obj->videos 	= $videos;

		return $obj;
	}
}
