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

class EasyBlogViewUsers extends EasyBlogAdminView
{
	function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('easyblog.manage.user' , 'com_easyblog') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}

		//initialise variables
		$document		= JFactory::getDocument();
		$user			= JFactory::getUser();
		$mainframe		= JFactory::getApplication();

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easyblog.users.filter_state', 		'filter_state', 	'*', 'word' );
		$search			= $mainframe->getUserStateFromRequest( 'com_easyblog.users.search', 			'search', 			'', 'string' );

		$search			= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easyblog.users.filter_order', 		'filter_order', 	'a.id', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easyblog.users.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		//Get data from the model
		$isBrowse 		= JRequest::getVar('browse', '0');
		$model          = $this->getModel();
		$users			= $model->getUsers( $isBrowse );
		$pagination		= $this->get( 'Pagination' );


		if( ! $isBrowse )
		{
			// Filter to show users which have the ability to write post
			$acl = EasyBlogACLHelper::getRuleSet( );
			$bloggers = array();

			if( !empty($users) )
			{
				foreach( $users as $user )
				{
					$acl = EasyBlogACLHelper::getRuleSet( $user->id );

					if( !empty($acl->rules->add_entry) )
					{
						// We call them bloggers for user with the acl permission to write entry
						$bloggers[] = $user;
					}
				}
			}
			// Pass back lists of bloggers back to users
			$users = $bloggers;
		}

		if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
		{
			if(count($users) > 0)
			{
				for($i = 0; $i < count($users); $i++)
				{
					$row    			= $users[$i];
					$row->usergroups 	= $this->getGroupTitle( $row->id );
				}
			}
		}

		$browse			= JRequest::getInt( 'browse' , 0 );
		$browsefunction = JRequest::getVar('browsefunction', 'insertMember');
		$browseUID		= JRequest::getVar( 'uid' , '' );

		$this->assign( 'browseUID' , $browseUID );
		$this->assign( 'browse' , $browse );
		$this->assign( 'browsefunction' , $browsefunction );
		$this->assignRef( 'users' 		, $users );
		$this->assignRef( 'pagination'	, $pagination );
		$this->assign( 'state'			, JHTML::_('grid.state', $filter_state ) );
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		parent::display($tpl);
	}

	function getGroupTitle( $user_id )
	{
		$db = EasyBlogHelper::db();
		$sql = "SELECT title FROM ".EasyBlogHelper::getHelper( 'SQL' )->nameQuote('#__usergroups')." ug left join ".
				EasyBlogHelper::getHelper( 'SQL' )->nameQuote('#__user_usergroup_map')." map on (ug.id = map.group_id)".
				" WHERE map.user_id=". $db->Quote( $user_id );

		$db->setQuery($sql);
		$result = $db->loadResultArray();
		return nl2br( implode("\n", $result) );
	}

	function getPostCount( $id )
	{
		$db = EasyBlogHelper::db();

		$query	= 'SELECT COUNT(1) FROM ' . $db->nameQuote( '#__easyblog_post' ) . ' '
				. 'WHERE `created_by`=' . $db->Quote( $id );
		$db->setQuery( $query );

		return $db->loadResult();
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYBLOG_BLOGGERS_TITLE' ), 'users' );

		JToolbarHelper::back( JText::_( 'COM_EASYBLOG_TOOLBAR_HOME' ) , 'index.php?option=com_easyblog' );
		JToolbarHelper::divider();
		JToolbarHelper::divider();
		JToolbarHelper::addNew();
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'feature' , 'star' , '' , JText::_( 'COM_EASYBLOG_FEATURE_TOOLBAR' ) );
		JToolBarHelper::custom( 'unfeature' , 'star-empty' , '' , JText::_( 'COM_EASYBLOG_UNFEATURE_TOOLBAR' ) );
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();
	}
}
