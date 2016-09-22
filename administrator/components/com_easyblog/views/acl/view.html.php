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

class EasyBlogViewAcl extends EasyBlogAdminView
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

		$mainframe	= JFactory::getApplication();
		$model 		= $this->getModel( 'Acl' );
		$document	= JFactory::getDocument();

		$cid	= JRequest::getVar('cid', '', 'REQUEST');
		$type	= JRequest::getVar('type', '', 'REQUEST');
		$add	= JRequest::getVar('add', '', 'REQUEST');

		JHTML::_('behavior.modal' , 'a.modal' );
		JHTML::_('behavior.tooltip');

		if((empty($cid) || empty($type)) && empty($add))
		{
			$mainframe->redirect( 'index.php?option=com_easyblog&view=acls' , JText::_('Invalid Id or acl type. Please try again.') , 'error' );
		}

		$rulesets = $model->getRuleSet($type, $cid, $add);

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

		$joomlaVersion	= EasyBlogHelper::getJoomlaVersion();

		$filter 		= EasyBlogHelper::getTable( 'AclFilter' );
		$filter->load( $cid , $type );

		$this->assignRef( 'filter'			, $filter );
		$this->assignRef( 'joomlaversion'	, $joomlaVersion );
		$this->assignRef( 'rulesets' 		, $rulesets );
		$this->assignRef( 'type' 			, $type );
		$this->assignRef( 'add' 			, $add );

		parent::display($tpl);
	}

	public function getDescription( $rule )
	{
		$db 	= EasyBlogHelper::db();
		$query 	= 'SELECT ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'description' ) . ' '
				. 'FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__easyblog_acl' ) . ' '
				. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'action' ) . '=' . $db->Quote( $rule );
		$db->setQuery( $query );
		$description	= $db->loadResult();

		return $description;
	}

	function registerToolbar()
	{
		JToolbarHelper::back( JText::_( 'COM_EASYBLOG_TOOLBAR_HOME' ) , 'index.php?option=com_easyblog' );
		JToolbarHelper::divider();
		JToolBarHelper::apply();
		JToolBarHelper::save();
		JToolBarHelper::divider();
		JToolBarHelper::custom( 'enableall', 'plus', '', JText::_( 'COM_EASYBLOG_ENABLE_ALL' ), false );
		JToolBarHelper::custom( 'disableall', 'minus', '', JText::_( 'COM_EASYBLOG_DISABLE_ALL' ), false );
		JToolBarHelper::divider();
		JToolBarHelper::cancel();
	}

	function registerSubmenu()
	{
		return 'submenu.php';
	}
}
