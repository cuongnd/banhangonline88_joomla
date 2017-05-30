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
	function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if( !JFactory::getUser()->authorise('easyblog.manage.mail' , 'com_easyblog') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}
		//initialise variables
		$document		= JFactory::getDocument();
		$user			= JFactory::getUser();
		$mainframe		= JFactory::getApplication();

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easyblog.spools.filter_state', 		'filter_state', 	'*', 		'word' );
		$search			= $mainframe->getUserStateFromRequest( 'com_easyblog.spools.search', 			'search', 			'', 		'string' );

		$search			= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easyblog.spools.filter_order', 		'filter_order', 	'created', 	'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easyblog.spools.filter_order_Dir',	'filter_order_Dir',	'asc', 		'word' );

		$mails			= $this->get( 'Data' );
		$pagination		= $this->get( 'Pagination' );

		$this->assign( 'mails'			, $mails );
		$this->assign( 'pagination'		, $pagination );
		$this->assign( 'state'			, JHTML::_('grid.state', $filter_state , JText::_( 'COM_EASYBLOG_SENT' ) , JText::_( 'COM_EASYBLOG_PENDING' ) ) );
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYBLOG_MAIL_POOL_TITLE' ), 'spools' );

		JToolbarHelper::back( JText::_( 'COM_EASYBLOG_TOOLBAR_HOME' ) , 'index.php?option=com_easyblog' );
		JToolbarHelper::divider();
		JToolbarHelper::deleteList();
		JToolBarHelper::divider();
		JToolBarHelper::custom('purge','purge','icon-32-unpublish.png', 'COM_EASYBLOG_PURGE_ALL', false);
	}
}
