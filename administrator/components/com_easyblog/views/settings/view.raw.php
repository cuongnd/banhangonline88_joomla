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

jimport( 'joomla.html.pane' );
require( EBLOG_ADMIN_ROOT . '/views.php');

class EasyBlogViewSettings extends EasyBlogAdminView
{
	public function export()
	{
		// @rule: Test for user access if on 1.6 and above
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('easyblog.manage.setting' , 'com_easyblog') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}

		$db 	= JFactory::getDBO();

		$query 	= 'SELECT `params` FROM ' . $db->quoteName( '#__easyblog_configs' ) . ' WHERE `name` = ' . $db->Quote( 'config' );
		$db->setQuery( $query );

		$data 	= $db->loadResult();

		// Get the file size
		$size 		= strlen( $data );

		header('Content-Description: File Transfer');
		header('Content-Type: application/octet-stream');
		header('Content-Disposition: attachment; filename=settings.json' );
		header('Content-Transfer-Encoding: binary');
		header('Expires: 0');
		header('Cache-Control: must-revalidate, post-check=0, pre-check=0');
		header('Pragma: public');
		header('Content-Length: ' . $size );
		ob_clean();
		flush();
		echo $data;
		exit;
	}
}
