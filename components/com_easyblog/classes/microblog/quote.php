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

class EasyBlogMicroBlogQuote
{
	/**
	 * Responsible to perform the blog mappings
	 */
	public function bind( &$blog )
	{
		$content 		= JRequest::getVar( 'content' );
		$title 			= JRequest::getVar( 'quote' , '' );

		// @task: Replace newlines with <br /> tags since the form is a plain textarea.
		$content 		= nl2br( $content );
		
		$blog->set( 'title'		, $title );
		$blog->set( 'content' 	, $content );
		$blog->set( 'source'	, EBLOG_MICROBLOG_QUOTE );

		$blog->set( '_checkLength'	, false );
	}

	/**
	 * Method to validate a post
	 */
	public function validate()
	{
		$quote 	= JRequest::getVar( 'quote' );

		if( empty( $quote ) )
		{
			return JText::_( 'COM_EASYBLOG_MICROBLOG_ERROR_EMPTY_CONTENT' );
		}

		return true;
	}

	/**
	 * Since quotes are stored in the title, we don't really need to do anything here
	 */
	public function afterSave( &$blog )
	{
		// // Store the quote source
		// $source		= JRequest::getVar( 'quote-source' , '' );

		// if( !empty( $source ) )
		// {
		// 	$asset	= EasyBlogHelper::getTable( 'BlogAsset' );
		// 	$asset->set( 'post_id'	, $blog->id );
		// 	$asset->set( 'type'		, )
		// }
		return true;
	}

	public function getSuccessMessage()
	{
		return JText::_( 'COM_EASYBLOG_MICROBLOG_QUOTE_POSTED_SUCCESSFULLY' );
	}

	public function processOutput( &$row )
	{
		
	}
}
