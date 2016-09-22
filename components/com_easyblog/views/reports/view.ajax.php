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
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'string.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'date.php' );

class EasyBlogViewReports extends EasyBlogView
{
	public function show()
	{
		$theme 		= new CodeThemes();
		$ajax		= EasyBlogHelper::getHelper( 'Ajax' );

		$type 		= JRequest::getCmd( 'type' );
		$id 		= JRequest::getInt( 'id' );

		$theme->set( 'id' 	, $id );
		$theme->set( 'type'	, $type );

		$contents 	= $theme->fetch( 'ajax.dialog.report.form.php' );

		$ajax->success( JText::_( 'COM_EASYBLOG_REPORT_THIS_BLOG_POST' ) , $contents );
		$ajax->send();
	}
}
