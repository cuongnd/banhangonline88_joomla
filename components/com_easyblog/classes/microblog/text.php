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

class EasyBlogMicroBlogText
{
	/**
	 * Responsible to perform the blog mappings
	 */
	public function bind( &$blog )
	{
		$content 		= JRequest::getVar( 'content' );
		$title 			= JRequest::getVar( 'title' , '' );

		// @rule: Title will be optional here
		if( empty($title) || $title == JText::_( 'COM_EASYBLOG_MICROBLOG_TITLE_OPTIONAL' , true ) )
		{
			$title			= JString::substr( $content , 0 , 10 ) . '...';
		}
		
		// @task: Replace newlines with <br /> tags since the form is a plain textarea.
		$content 		= nl2br( $content );
		
		$blog->set( 'title'		, $title );
		$blog->set( 'content' 	, $content );
	}

	/**
	 * Method to validate a post
	 */
	public function validate()
	{
		$content 	= JRequest::getVar( 'content' );

		if( empty( $content ) )
		{
			return JText::_( 'COM_EASYBLOG_MICROBLOG_ERROR_EMPTY_CONTENT' );
		}

		return true;
	}

	/**
	 * Since normal text posts doesn't contains any assets.
	 */
	public function afterSave()
	{
		return true;
	}

	public function getSuccessMessage()
	{
		return JText::_( 'COM_EASYBLOG_MICROBLOG_TEXT_POSTED_SUCCESSFULLY' );
	}
}
