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

class EasyBlogViewSubscriptions extends EasyBlogAdminView
{
	function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('easyblog.manage.subscription' , 'com_easyblog') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}

		//initialise variables
		$document		= JFactory::getDocument();
		$user			= JFactory::getUser();
		$mainframe		= JFactory::getApplication();

		$filter			= $mainframe->getUserStateFromRequest( 'com_easyblog.subscriptions.filter', 	'filter', 	'site', 		'word' );
		$search			= $mainframe->getUserStateFromRequest( 'com_easyblog.subscriptions.search', 	'search', 	'', 			'string' );

		$search			= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easyblog.subscriptions.filter_order', 		'filter_order', 	'bname', 	'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easyblog.subscriptions.filter_order_Dir',	'filter_order_Dir',	'', 		'word' );

		//Get data from the model
		$subscriptions	= $this->get( 'Subscriptions' );
		$pagination		= $this->get( 'Pagination' );

		$this->assignRef( 'subscriptions'	, $subscriptions );
		$this->assignRef( 'pagination'		, $pagination );
		$this->assign( 'filter'				, $filter );
		$this->assign( 'filterList'			, $this->_getFilter($filter) );
		$this->assign( 'search'				, $search );
		$this->assign( 'order'				, $order );
		$this->assign( 'orderDirection'		, $orderDirection );

		parent::display($tpl);
	}

	function _getFilter( $filter )
	{
		$filterType = array();
		$attribs	= 'size="1" class="inputbox" onchange="submitform();"';

		$filterType[] = JHTML::_('select.option', 'blogger', JText::_( 'COM_EASYBLOG_BLOGGER_OPTION' ) );
		$filterType[] = JHTML::_('select.option', 'blog', JText::_( 'COM_EASYBLOG_BLOG_POST_OPTION' ) );
		$filterType[] = JHTML::_('select.option', 'category', JText::_( 'COM_EASYBLOG_CATEGORY_OPTION' ) );
		$filterType[] = JHTML::_('select.option', 'site', JText::_( 'COM_EASYBLOG_SITE_OPTION' ) );
		$filterType[] = JHTML::_('select.option', 'team', JText::_( 'COM_EASYBLOG_TEAM_OPTION' ) );


		return JHTML::_('select.genericlist',   $filterType, 'filter', $attribs, 'value', 'text', $filter );
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYBLOG_SUBSCRIPTION' ), 'subscriptions' );

		JToolbarHelper::back( JText::_( 'COM_EASYBLOG_TOOLBAR_HOME' ) , 'index.php?option=com_easyblog' );
		JToolbarHelper::divider();
		JToolbarHelper::deleteList();
	}
}
