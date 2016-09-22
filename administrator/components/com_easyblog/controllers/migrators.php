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

jimport('joomla.application.component.controller');

class EasyBlogControllerMigrators extends EasyBlogController
{
	public function purge()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'migrator' );

		$db 	= EasyBlogHelper::db();

		$query 	= 'TRUNCATE TABLE ' . $db->nameQuote( '#__easyblog_migrate_content' );
		$db->setQuery( $query );

		$db->Query();

		if( $db->getError() )
		{
			JFactory::getApplication()->redirect( 'index.php?option=com_easyblog&view=migrators' , JText::_( 'COM_EASYBLOG_PURGE_ERROR') , 'error' );
		}

		JFactory::getApplication()->redirect( 'index.php?option=com_easyblog&view=migrators' , JText::_( 'COM_EASYBLOG_PURGE_SUCCESS' ) );
	}
}