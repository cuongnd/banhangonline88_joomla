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
require( EBLOG_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views.php');

class EasyBlogViewTags extends EasyBlogAdminView
{
	function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('easyblog.manage.tag' , 'com_easyblog') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}

		//initialise variables
		$document		= JFactory::getDocument();
		$user			= JFactory::getUser();
		$mainframe		= JFactory::getApplication();

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easyblog.tags.filter_state', 		'filter_state', 	'*', 'word' );
		$search			= $mainframe->getUserStateFromRequest( 'com_easyblog.tags.search', 			'search', 			'', 'string' );

		$search			= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easyblog.tags.filter_order', 		'filter_order', 	'ordering', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easyblog.tags.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		//Get data from the model
		$tags				= $this->get( 'Data' );
		$model				= $this->getModel('Tags');

		for( $i = 0 ; $i < count( $tags ); $i++ )
		{
			$tag		= $tags[ $i ];
			$tag->count	= $model->getUsedCount( $tag->id );
			$tag->title	= JString::trim($tag->title);
			$tag->alias	= JString::trim($tag->alias);
		}
		$pagination		= $this->get( 'Pagination' );

		$browse			= JRequest::getInt( 'browse' , 0 );
		$browsefunction	= JRequest::getVar('browsefunction', 'insertTag');
		$this->assign( 'browse' , $browse );
		$this->assign( 'browsefunction' , $browsefunction );

		$this->assignRef( 'tags' 		, $tags );
		$this->assignRef( 'pagination'	, $pagination );
		$this->assign( 'state'			, JHTML::_('grid.state', $filter_state ) );
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYBLOG_TAGS' ), 'tags' );

		JToolbarHelper::back( JText::_( 'COM_EASYBLOG_TOOLBAR_HOME' ) , 'index.php?option=com_easyblog' );
		JToolbarHelper::divider();
		JToolbarHelper::addNew();
		JToolBarHelper::divider();
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'setDefault' , 'star' , '' , JText::_( 'COM_EASYBLOG_MAKE_DEFAULT' ) );
		JToolBarHelper::custom( 'unsetDefault' , 'star-empty' , '' , JText::_( 'COM_EASYBLOG_REMOVE_DEFAULT' ) );
		JToolBarHelper::divider();		
		JToolbarHelper::deleteList();

	}
}
