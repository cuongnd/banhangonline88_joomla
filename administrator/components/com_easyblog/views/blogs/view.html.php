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

class EasyBlogViewBlogs extends EasyBlogAdminView
{
	function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('easyblog.manage.blog' , 'com_easyblog') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}

		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		JHTML::_('behavior.tooltip');

		$filter_state		= $mainframe->getUserStateFromRequest( 'com_easyblog.blogs.filter_state', 		'filter_state', 	'*', 'word' );
		$search				= $mainframe->getUserStateFromRequest( 'com_easyblog.blogs.search', 			'search', 			'', 'string' );
		$filter_category	= $mainframe->getUserStateFromRequest( 'com_easyblog.blogs.filter_category', 	'filter_category', 	'*', 'int' );
		$filterLanguage		= $mainframe->getUserStateFromRequest( 'com_easyblog.blogs.filter_language', 	'filter_language', 	'', '' );
		$search				= trim(JString::strtolower( $search ) );
		$order				= $mainframe->getUserStateFromRequest( 'com_easyblog.blogs.filter_order', 		'filter_order', 	'a.id', 'cmd' );
		$orderDirection		= $mainframe->getUserStateFromRequest( 'com_easyblog.blogs.filter_order_Dir',	'filter_order_Dir',	'desc', 'word' );
		$source				= JRequest::getVar( 'filter_source' , '-1' );
		$filteredBlogger	= $mainframe->getUserStateFromRequest( 'com_easyblog.blogs.filter_blogger' , 'filter_blogger' , '' , 'int' );

		//Get data from the model
		$blogs			= $this->get( 'Blogs' );
		$pagination		= $this->get( 'Pagination' );

		$catFilter		= $this->getFilterCategory( $filter_category );

		$browse			= JRequest::getInt( 'browse' , 0 );
		$browsefunction	= JRequest::getVar('browsefunction', 'insertBlog');




		// @task: Get the centralized oauth consumers
		$consumers				= array();
		$sites					= array( 'twitter' , 'facebook' , 'linkedin' );
		$centralizedConfigured  = false;

		foreach( $sites as $site )
		{
			$consumer	= EasyBlogHelper::getTable( 'OAuth' );
			$consumer->loadSystemByType( $site );

			if( !empty( $consumer->id ) )
				$centralizedConfigured  = true;

			$consumers[]	= $consumer;
		}

		$this->assignRef( 'consumers'	, $consumers );
		$this->assignRef( 'centralizedConfigured'	, $centralizedConfigured );
		$this->assignRef( 'source' 		, $source );
		$this->assign( 'filterLanguage'	, $filterLanguage );
		$this->assign( 'filteredBlogger' , $filteredBlogger );
		$this->assign( 'browse' , $browse );
		$this->assign( 'browseFunction' , $browsefunction );
		$this->assignRef( 'blogs' 		, $blogs );
		$this->assignRef( 'pagination'	, $pagination );
		$this->assignRef( 'filter_state', $filter_state );
		$this->assign( 'state'			, $this->getFilterState($filter_state));
		$this->assign( 'category'		, $catFilter );
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		parent::display($tpl);
	}

	public function getLanguageTitle( $code )
	{
		$db 	= EasyBlogHelper::db();
		$query	= 'SELECT ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'title' ) . ' FROM '
				. EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__languages' ) . ' WHERE '
				. EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'lang_code' ) . '=' . $db->Quote( $code );
		$db->setQuery( $query );

		$title 	= $db->loadResult();

		return $title;
	}

	public function getFilterBlogger( $filter_type = '*' )
	{
		$model 		= EasyBlogHelper::getModel( 'Blogger' , true );
		$bloggers		= $model->getBloggers( 'alphabet' , null , 'showbloggerwithpost' );
		$filter[]		= JHTML::_('select.option', '', '- '. JText::_( 'COM_EASYBLOG_SELECT_BLOGGER' ) .' -' );
		foreach( $bloggers as $blogger )
		{
			$filter[] = JHTML::_('select.option', $blogger->id, $blogger->name );
		}

		return JHTML::_('select.genericlist', $filter, 'filter_blogger', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_type );
	}

	function getCategories($filter_type = '*')
	{
		$filter[]	= JHTML::_('select.option', '', '- '. JText::_( 'Select Category' ) .' -' );

		$model 		= EasyBlogHelper::getModel( 'Categories' , true );
		$categories	= $model->getAllCategories();

		foreach($categories as $cat)
		{
			$filter[] = JHTML::_('select.option', $cat->id, $cat->title );
		}

		return JHTML::_('select.genericlist', $filter, 'filter_category', 'class="inputbox" size="1"', 'value', 'text', $filter_type );
	}

	function getFilterCategory($filter_type = '*')
	{
		$filter[]	= JHTML::_('select.option', '', '- '. JText::_( 'COM_EASYBLOG_SELECT_CATEGORY' ) .' -' );

		$model 		= EasyBlogHelper::getModel( 'Categories' , true );
		$categories	= $model->getAllCategories();

		foreach($categories as $cat)
		{
			$filter[] = JHTML::_('select.option', $cat->id, $cat->title );
		}

		return JHTML::_('select.genericlist', $filter, 'filter_category', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_type );
	}

	function getFilterState ($filter_state='*')
	{
		$state[] = JHTML::_('select.option',  '', '- '. JText::_( 'COM_EASYBLOG_SELECT_STATE' ) .' -' );
		$state[] = JHTML::_('select.option',  'P', JText::_( 'COM_EASYBLOG_PUBLISHED' ) );
		$state[] = JHTML::_('select.option',  'U', JText::_( 'COM_EASYBLOG_UNPUBLISHED' ) );
		$state[] = JHTML::_('select.option',  'S', JText::_( 'COM_EASYBLOG_SCHEDULED' ) );
		$state[] = JHTML::_('select.option',  'T', JText::_( 'COM_EASYBLOG_TRASHED' ) );
		$state[] = JHTML::_('select.option',  'F', JText::_( 'COM_EASYBLOG_STATE_FEATURED' ) );
		return JHTML::_('select.genericlist',   $state, 'filter_state', 'class="inputbox" size="1" onchange="submitform( );"', 'value', 'text', $filter_state );
	}

	function getCategoryName( $id )
	{
		$category	= EasyBlogHelper::getTable( 'Category' , 'Table');
		$category->load( $id );
		return JText::_( $category->title );
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYBLOG_BLOGS_ALL_BLOG_ENTRIES_TITLE' ), 'blogs' );

		JToolbarHelper::back( JText::_( 'COM_EASYBLOG_TOOLBAR_HOME' ) , 'index.php?option=com_easyblog' );
		JToolbarHelper::divider();
		
		JToolBarHelper::addNew( 'addNew' );
		JToolBarHelper::divider();

		$app 			= JFactory::getApplication();
		$filterState	= $app->getUserStateFromRequest( 'com_easyblog.blogs.filter_state', 		'filter_state', 	'*', 'word' );

		if( $filterState != 'T' )
		{
			JToolBarHelper::custom( 'feature' , 'star' , '' , JText::_( 'COM_EASYBLOG_FEATURE_TOOLBAR' ) );
			JToolBarHelper::custom( 'unfeature' , 'star-empty' , '' , JText::_( 'COM_EASYBLOG_UNFEATURE_TOOLBAR' ) );
			JToolbarHelper::publishList();
			JToolbarHelper::unpublishList();

			JToolBarHelper::custom('toggleFrontpage', 'featured.png', 'featured_f2.png', JText::_( 'COM_EASYBLOG_FRONTPAGE_TOOLBAR' ) , true);
			JToolBarHelper::divider();
		}

		JToolBarHelper::custom( 'showMove' , 'move' , '' , JText::_( 'COM_EASYBLOG_MOVE' ) );
		JToolBarHelper::custom( 'copy' , 'copy' , '' , JText::_( 'COM_EASYBLOG_COPY' ) );
		JToolBarHelper::divider();

		$state 	= $app->getUserStateFromRequest( 'com_easyblog.blogs.filter_state', 		'filter_state', 	'*', 'word' );

		// If this is on the trash view, we need to show empty trash icon
		if( $state == 'T' )
		{
			JToolbarHelper::publishList( 'restore' , JText::_( 'COM_EASYBLOG_RESTORE' ) );
			JToolbarHelper::deleteList();
		}
		else
		{
			JToolbarHelper::trash( 'trash' );
		}
	}
}
