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

class EasyBlogViewCategories extends EasyBlogAdminView
{
	function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('easyblog.manage.category' , 'com_easyblog') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}

		//initialise variables
		$document		= JFactory::getDocument();
		$user			= JFactory::getUser();
		$mainframe		= JFactory::getApplication();

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easyblog.categories.filter_state', 		'filter_state', 	'*', 'word' );
		$search			= $mainframe->getUserStateFromRequest( 'com_easyblog.categories.search', 			'search', 			'', 'string' );

		$search			= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easyblog.categories.filter_order', 		'filter_order', 	'lft', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easyblog.categories.filter_order_Dir',	'filter_order_Dir',	'asc', 'word' );

		$publishedOnly	= JRequest::getVar( 'p' , '0');

		JTable::addIncludePath( EBLOG_TABLES );
		$category			= EasyBlogHelper::getTable( 'Category' , 'Table' );
		$category->rebuildOrdering();

		//Get data from the model
		$ordering	= array();
		$model		= $this->getModel();

		$categories	= $model->getData( true, $publishedOnly );


		for( $i = 0 ; $i < count( $categories ); $i++ )
		{
			$category				= $categories[ $i ];

			$category->count		= $model->getUsedCount( $category->id );
			$category->child_count	= $model->getChildCount( $category->id );

			$ordering[$category->parent_id][] = $category->id;
		}
		$pagination 	= $this->get( 'Pagination' );

		$browse			= JRequest::getInt( 'browse' , 0 );
		$browsefunction = JRequest::getVar('browsefunction', 'insertCategory');
		$this->assign( 'browse' , $browse );
		$this->assign( 'browsefunction' , $browsefunction );

		$this->assignRef( 'categories' 	, $categories );
		$this->assignRef( 'pagination'	, $pagination );
		$this->assignRef( 'ordering'	, $ordering );


		$this->assign( 'state'			, JHTML::_('grid.state', $filter_state ) );
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'saveOrder'		, $order == 'lft' && $orderDirection == 'asc' );
		$this->assign( 'ordering'		, $ordering );
		$this->assign( 'orderDirection'	, $orderDirection );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYBLOG_CATEGORIES_TITLE' ), 'category' );

		JToolbarHelper::back( JText::_( 'COM_EASYBLOG_TOOLBAR_HOME' ) , 'index.php?option=com_easyblog' );
		JToolBarHelper::divider();
		JToolbarHelper::addNew();
		JToolBarHelper::divider();
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();

	}
}
