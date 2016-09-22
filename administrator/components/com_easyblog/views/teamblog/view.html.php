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

class EasyBlogViewTeamblog extends EasyBlogAdminView
{
	var $team	= null;

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
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		$id			= JRequest::getInt( 'id' );
		JHTML::_('behavior.modal' , 'a.modal' );

		$document	= JFactory::getDocument();
		$document->addStyleSheet( JURI::root() . 'components/com_easyblog/assets/css/common.css' );

		$team	= EasyBlogHelper::getTable( 'TeamBlog' , 'Table' );
		$team->load( $id );
		$this->team	= $team;

		$blogAccess		= array();
		$blogAccess[]	= JHTML::_('select.option', '1', JText::_( 'COM_EASYBLOG_TEAM_MEMBER_ONLY' ) );
		$blogAccess[]	= JHTML::_('select.option', '2', JText::_( 'COM_EASYBLOG_ALL_REGISTERED_USERS' ) );
		$blogAccess[]	= JHTML::_('select.option', '3', JText::_( 'COM_EASYBLOG_EVERYONE' ) );

		$blogAccessList = JHTML::_('select.genericlist', $blogAccess, 'access', 'size="1" class="inputbox"', 'value', 'text', $team->access );

		$config		= EasyBlogHelper::getConfig();
		$editor		= JFactory::getEditor( $config->get('layout_editor') );

		// get meta tags
		$metaModel 		= EasyBlogHelper::getModel( 'Metas' );
		$meta 			= $metaModel->getMetaInfo(META_TYPE_TEAM, $id);

		$joomlaVersion	= EasyBlogHelper::getJoomlaVersion();
		$this->assignRef( 'joomlaversion' , $joomlaVersion );
		$this->assignRef( 'editor'	, $editor );
		$this->assignRef( 'team' 	, $team );
		$this->assignRef( 'meta' 	, $meta );
		$this->assignRef( 'config'	, $config );
		$this->assignRef( 'blogAccessList' , $blogAccessList );

		parent::display($tpl);
	}

	function getMembers( $teamId )
	{
		if( $teamId == 0 )
			return;

		$db		= EasyBlogHelper::db();

		$query	= 'SELECT * FROM #__easyblog_team_users '
				. 'WHERE `team_id`=' . $db->Quote( $teamId );
		$db->setQuery( $query );
		$members	= $db->loadObjectList();

		return $members;
	}

	function getGroups( $teamId )
	{
		$db		= EasyBlogHelper::db();

		$query	= 'SELECT * FROM `#__easyblog_team_groups` '
				. 'WHERE `team_id`=' . $db->Quote( $teamId );
		$db->setQuery( $query );
		$rows	= $db->loadObjectList();
		$groups	= array();

		if( $rows )
		{
			foreach( $rows as $row )
			{
				$group	= EasyBlogHelper::getJoomlaUserGroups( $row->group_id );
				$groups[]	= $group[0];
			}
		}

		return $groups;
	}

	function getPostCount( $id )
	{
		$db	= EasyBlogHelper::db();

		$query	= 'SELECT COUNT(1) FROM #__easyblog_post '
				. 'WHERE `created_by`=' . $db->Quote( $id );
		$db->setQuery( $query );

		return $db->loadResult();
	}

	function registerToolbar()
	{
		if( $this->team->id != 0 )
			JToolBarHelper::title( JText::sprintf( 'COM_EASYBLOG_EDITING_TEAM' , $this->team->title ), 'teamblogs' );
		else
			JToolBarHelper::title( JText::_( 'COM_EASYBLOG_CREATE_NEW_TEAM' ), 'teamblogs' );

		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}

	function registerSubmenu()
	{
		return 'submenu.php';
	}
}
