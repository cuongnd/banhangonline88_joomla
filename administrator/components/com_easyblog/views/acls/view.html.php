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

class EasyBlogViewAcls extends EasyBlogAdminView
{
	function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('easyblog.manage.acl' , 'com_easyblog') )
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

		$model 		= EasyBlogHelper::getModel( 'Acl' , true );

		$config		= EasyBlogHelper::getConfig();

		$type = $mainframe->getUserStateFromRequest( 'com_easyblog.acls.filter_type', 'filter_type', 'group', 'word' );

		//filtering
		$filter = new stdClass();
		$filter->type 	= $this->getFilterType($type);
		$filter->search = $mainframe->getUserStateFromRequest( 'com_easyblog.acls.search', 'search', '', 'string' );

		//sorting
		$sort = new stdClass();
		$sort->order			= $mainframe->getUserStateFromRequest( 'com_easyblog.acls.filter_order', 'filter_order', 'a.`id`', 'cmd' );
		$sort->orderDirection	= $mainframe->getUserStateFromRequest( 'com_easyblog.acls.filter_order_Dir', 'filter_order_Dir', '', 'word' );

		$rulesets	= $model->getRuleSets($type);
		$pagination = $model->getPagination($type);

		if ( $type == 'assigned' )
		{
			$document->setTitle( JText::_("COM_EASYBLOG_ACL_ASSIGN_USER") );
			JToolBarHelper::title( JText::_( 'COM_EASYBLOG_ACL_ASSIGN_USER' ), 'acl' );
		}
		else
		{
			$document->setTitle( JText::_("COM_EASYBLOG_ACL_JOOMLA_USER_GROUP") );
			JToolBarHelper::title( JText::_( 'COM_EASYBLOG_ACL_JOOMLA_USER_GROUP' ), 'acl' );
		}

		$this->assignRef( 'config' , $config );
		$this->assignRef( 'rulesets' , $rulesets );
		$this->assignRef( 'filter', $filter );
		$this->assignRef( 'sort', $sort );
		$this->assignRef( 'type', $type );
		$this->assignRef( 'pagination'	, $pagination );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		$mainframe	= JFactory::getApplication();
		$type		= $mainframe->getUserStateFromRequest( 'com_easyblog.acls.filter_type', 'filter_type', 'group', 'word' );

		JToolbarHelper::back( JText::_( 'COM_EASYBLOG_TOOLBAR_HOME' ) , 'index.php?option=com_easyblog' );
		JToolbarHelper::divider();

		if($type=='assigned')
		{
			JToolbarHelper::addNew();
			JToolbarHelper::deleteList();
		}
	}

	function getFilterType( $filter_type='*', $group='COM_EASYBLOG_JOOMLA_GROUP', $assigned='COM_EASYBLOG_ASSIGNED' )
	{
		return $this->renderFilters(
						array( 'group' => $group, 'assigned' => $assigned ),
						$filter_type,
						'filter_type');
	}

}
