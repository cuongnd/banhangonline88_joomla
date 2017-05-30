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

require( EBLOG_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views.php');

class EasyBlogViewSpools extends EasyBlogAdminView 
{
	public function preview( $blogId )
	{
		$ajax	= new Ejax();
		$mailq	= EasyBlogHelper::getTable( 'Mailqueue' );
		$mailq->load( $blogId );
		
		$url 		= JURI::root() . 'administrator/index.php?option=com_easyblog&c=spools&task=preview&id=' . $mailq->id . '&' . EasyBlogHelper::getToken() . '=1';

		$options	= new stdClass();
		$options->title		= JText::_( 'COM_EASYBLOG_EMAIL_PREVIEW' );
		$options->content	= '<iframe src="' . $url . '" width="100%" height="500"></iframe>';
		$options->width 	= '750';
		$ajax->dialog( $options );
		$ajax->send();
	}
}