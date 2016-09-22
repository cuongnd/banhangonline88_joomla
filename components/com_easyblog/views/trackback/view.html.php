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

jimport( 'joomla.application.component.view');

class EasyBlogViewTrackback extends EasyBlogView
{
	function display()
	{
		JTable::addIncludePath( EBLOG_TABLES );
		$document	= JFactory::getDocument();
		$viewType	= $document->getType();
		$config     = EasyBlogHelper::getConfig();
		$id			= JRequest::getInt( 'post_id' , 0 );

		if( !$config->get( 'main_trackbacks') )
		{
			echo JText::_('Trackback disabled');
			return;
		}

		if( $id == 0 )
		{
			echo JText::_('COM_EASYBLOG_TRACKBACK_INVALID_BLOG_ENTRY_PROVIDED');
			return;
		}

		$blog		= EasyBlogHelper::getTable( 'Blog' , 'Table' );
		$blog->load( $id );

		header('Content-Type: text/xml');

		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'trackback.php' );

		$data		= JRequest::get( 'REQUEST' );
		$trackback	= EasyBlogHelper::getTable( 'Trackback' , 'Table' );
		$trackback->bind( $data );

		$title		= JString::trim( $trackback->title );
		$excerpt	= JString::trim( $trackback->excerpt );
		$url		= JString::trim( $trackback->url );
		$tb			= new EasyBlogTrackback( '','' , 'UTF-8');

		//@task: We must have at least the important information
		if( empty( $title  ) )
		{
			echo $tb->receive( false, JText::_('COM_EASYBLOG_TRACKBACK_INVALID_TITLE_PROVIDED') );
			exit;
		}

		//@task: We must have at least the important information
		if( empty( $excerpt ) )
		{
			echo $tb->receive( false, JText::_('COM_EASYBLOG_TRACKBACK_INVALID_EXCERT_PROVIDED') );
			exit;
		}

		//@task: We must have at least the important information
		if( empty( $url ) )
		{
			echo $tb->receive( false, JText::_('COM_EASYBLOG_TRACKBACK_INVALID_URL_PROVIDED') );
			exit;
		}

		// Check for spams with Akismet
		if( $config->get( 'comment_akismet_trackback' ) )
		{
			$data = array(
					'author'    => $title,
					'email'     => '',
					'website'   => JURI::root() ,
					'body'      => $excerpt,
					'permalink' => $url
			);

			if( EasyBlogHelper::getHelper( 'Akismet' )->isSpam( $data ) )
			{
				echo $tb->receive( false , JText::_( 'COM_EASYBLOG_TRACKBACK_MARKED_AS_SPAM' ) );
				exit;
			}
		}

		$trackback->created		= EasyBlogHelper::getDate()->toMySQL();
		$trackback->published	= '0';
		$trackback->ip			= isset( $_SERVER['REMOTE_ADDR'] ) ? $_SERVER['REMOTE_ADDR'] : '';
		if( $trackback->store() )
		{
			echo $tb->receive( true );
			exit;
		}

		echo $tb->receive( false, JText::_('COM_EASYBLOG_ERROR') );
		exit;
	}
}
