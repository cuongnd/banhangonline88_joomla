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

require_once( EBLOG_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views.php' );

class EasyBlogViewTeamblogs extends EasyBlogAdminView
{
	function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('easyblog.manage.teamblog' , 'com_easyblog') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}

		//initialise variables
		$document		= JFactory::getDocument();
		$user			= JFactory::getUser();
		$mainframe		= JFactory::getApplication();

		$filter_state	= $mainframe->getUserStateFromRequest( 'com_easyblog.teamblogs.filter_state', 		'filter_state', 	'*', 'word' );
		$search			= $mainframe->getUserStateFromRequest( 'com_easyblog.teamblogs.search', 			'search', 			'', 'string' );

		$search			= trim(JString::strtolower( $search ) );
		$order			= $mainframe->getUserStateFromRequest( 'com_easyblog.teamblogs.filter_order', 		'filter_order', 	'a.id', 'cmd' );
		$orderDirection	= $mainframe->getUserStateFromRequest( 'com_easyblog.teamblogs.filter_order_Dir',	'filter_order_Dir',	'', 'word' );

		//Get data from the model
		$teams			= $this->get( 'Teams' );
		$pagination		= $this->get( 'Pagination' );


		$browse			= JRequest::getInt( 'browse' , 0 );
		$browsefunction = JRequest::getVar('browsefunction', 'insertTag');
		$this->assign( 'browse' , $browse );
		$this->assign( 'browsefunction' , $browsefunction );

		$this->assignRef( 'teams' 		, $teams );
		$this->assignRef( 'pagination'	, $pagination );
		$this->assign( 'state'			, JHTML::_('grid.state', $filter_state ) );
		$this->assign( 'search'			, $search );
		$this->assign( 'order'			, $order );
		$this->assign( 'orderDirection'	, $orderDirection );

		parent::display($tpl);
	}

	function getMembersCount( $teamId )
	{
		$db	= EasyBlogHelper::db();

		$query	= 'SELECT COUNT(1) FROM `#__easyblog_team_users` '
				. 'WHERE `team_id`=' . $db->Quote( $teamId );
		$db->setQuery( $query );

		$total 		= $db->loadResult();



		// Now we need to calculate the group members.
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			$query	= 'SELECT COUNT(1) '
					. 'FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_team_groups' ) . ' AS a '
					. 'INNER JOIN ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__user_usergroup_map' ) . ' AS b '
					. 'ON a.`group_id` = b.`group_id` '
					. 'WHERE a.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'team_id' ) . ' = ' . $db->Quote( $teamId );
		}
		else
		{
			$query	= 'SELECT COUNT(c.`value`) '
					. 'FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_team_groups' ) . ' AS a '
					. 'LEFT JOIN ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__core_acl_groups_aro_map' ) . ' AS b '
					. 'ON a.`group_id` = b.`group_id` '
					. 'INNER JOIN ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__core_acl_aro' ) . ' AS c '
					. 'ON b.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'aro_id' ) . ' = c.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'id' )
					. 'WHERE a.' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'team_id' ) . ' = ' . $db->Quote( $teamId );
		}

		$db->setQuery( $query );
		$groupsTotal	= $db->loadResult();

		if( !$groupsTotal )
		{
			return $total;
		}

		return $total + $groupsTotal;
	}

	function getPostCount( $id )
	{
		$db	= EasyBlogHelper::db();

		$query	= 'SELECT COUNT(1) FROM #__easyblog_post '
				. 'WHERE `created_by`=' . $db->Quote( $id );
		$db->setQuery( $query );

		return $db->loadResult();
	}

	public function getAccessHTML( $access )
	{
		if( $access == '1' )
		{
			return JText::_('COM_EASYBLOG_TEAM_MEMBER_ONLY');
		}

		if( $access == '2')
		{
			return JText::_('COM_EASYBLOG_ALL_REGISTERED_USERS');
		}

		return JText::_('COM_EASYBLOG_EVERYONE');
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYBLOG_TEAMBLOGS_TITLE' ), 'teamblogs' );

		JToolbarHelper::back( JText::_( 'COM_EASYBLOG_TOOLBAR_HOME' ) , 'index.php?option=com_easyblog' );
		JToolbarHelper::divider();
		JToolbarHelper::addNew();
		JToolBarHelper::divider();
		JToolbarHelper::publishList();
		JToolbarHelper::unpublishList();
		JToolBarHelper::divider();
		JToolbarHelper::deleteList();
	}
}
