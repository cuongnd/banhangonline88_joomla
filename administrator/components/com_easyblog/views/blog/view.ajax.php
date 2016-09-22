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
require( EBLOG_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views.php');

class EasyBlogViewBlog extends EasyBlogAdminView
{
	public function resetHits()
	{
		$id		= JRequest::getInt( 'id' );

		if( !$id )
		{
			exit;
		}

		$ajax	= EasyBlogHelper::getHelper( 'Ajax' );
		$blog	= EasyBlogHelper::getTable( 'Blog' );
		$blog->load( $id );

		$blog->hits	= 0;
		$blog->store();

		$ajax->success( true );
	}
}
