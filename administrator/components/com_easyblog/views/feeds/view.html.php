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

class EasyBlogViewFeeds extends EasyBlogAdminView
{
	function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('easyblog.manage.feeds' , 'com_easyblog') )
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

		$layout		= $this->getLayout();

		if( $layout != 'default' )
		{
			$this->$layout( $tpl );
			return;
		}

		JToolBarHelper::title( JText::_( 'COM_EASYBLOG_BLOGS_FEEDS_TITLE' ), 'feeds' );

		JToolbarHelper::back( JText::_( 'COM_EASYBLOG_TOOLBAR_HOME' ) , 'index.php?option=com_easyblog' );
		JToolBarHelper::divider();
		JToolBarHelper::custom('addNew','new.png','new_f2.png', JText::_( 'COM_EASYBLOG_ADD_BUTTON' ) , false);
		JToolbarHelper::divider();
		JToolbarHelper::publishList('publish');
		JToolbarHelper::unpublishList('unpublish');
		JToolBarHelper::divider();
		JToolBarHelper::custom('download','download','download.png', JText::_( 'Execute' ) , false);
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();

		$feeds			= $this->get( 'Data' );
		$pagination		= $this->get( 'Pagination' );

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easyblog.categories.filter_state', 		'filter_state', 	'*', 'word' );
		$search			= $mainframe->getUserStateFromRequest( 'com_easyblog.categories.search', 			'search', 			'', 'string' );
		$search			= trim(JString::strtolower( $search ) );

		$this->assign( 'state'	, JHTML::_('grid.state', $filter_state ) );
		$this->assign( 'search' , $search );
		$this->assign( 'feeds'	, $feeds );
		$this->assign( 'pagination'	, $pagination );
		parent::display($tpl);
	}

	public function form( $tpl = null )
	{
		JHTML::_('behavior.modal' , 'a.modal' );
		$feed	= EasyBlogHelper::getTable( 'Feed' , 'Table' );

		JToolBarHelper::title( JText::_( 'COM_EASYBLOG_BLOGS_FEEDS_CREATE_NEW_TITLE' ), 'feeds' );

		JToolBarHelper::custom('save','save.png','save_f2.png', 'COM_EASYBLOG_SAVE', false);
		JToolbarHelper::cancel();

		$cid	= JRequest::getVar( 'cid' , '' , 'REQUEST' );

		if( !empty( $cid ) )
		{
			$feed->load( $cid );
		}

		$post	= JRequest::get( 'POST' );

		if( ! empty( $post ) )
		{
			$feed->bind( $post );
		}

		$categoryName	= '';
		$authorName		= '';

		if( !empty($feed->item_category) )
		{
			$categoryName	= $feed->getCategoryName();
		}

		if( !empty($feed->item_creator) )
		{
			$author			= JFactory::getUser($feed->item_creator);
			$authorName		= $author->name;
		}

		$params				= EasyBlogHelper::getRegistry( $feed->params );

		$this->assignRef( 'params'			, $params );
		$this->assignRef( 'feed' 			, $feed );
		$this->assignRef( 'categoryName' 	, $categoryName );
		$this->assignRef( 'authorName' 		, $authorName );

		parent::display( $tpl );
	}
}
