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
jimport( 'joomla.html.toolbar' );

class EasyBlogViewEntry extends EasyBlogView
{
	function display( $tmpl = null )
	{
		$config	= EasyBlogHelper::getConfig();
		$id 	= JRequest::getInt( 'id' );
		$my		= JFactory::getUser();
		
		if( !$id )
		{
			echo JText::_( 'COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND' );
			exit;
		}

		// @task: Do not allow access to read when configured to.
		if( $my->id <= 0 && $config->get( 'main_login_read' ) )
		{
			echo JText::_( 'You are not allowed to read this' );
			exit;
		}

		// @task: Do not allow users to read password protected entries
		if($config->get('main_password_protect', true) && !empty($blog->blogpassword))
		{
			echo JText::_( 'Password protected entries are not allowed yet.' );
			exit;
		}

		$blog	= EasyBlogHelper::getTable( 'Blog' );
		$blog->load( $id );

		$weever	= EasyBlogHelper::getHelper( 'Weever' )->getDetailsFeed();
		$weever->map( $blog );

		$weever->toJSON( true , JRequest::getVar( 'callback') );
	}
}
