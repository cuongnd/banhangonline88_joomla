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

class EasyBlogViewCategory extends EasyBlogAdminView
{
	var $cat	= null;

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
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();
		$config		= EasyBlogHelper::getConfig();
		$acl		= EasyBlogACLHelper::getRuleSet();

		//Load pane behavior
		jimport('joomla.html.pane');

		$catId		= JRequest::getVar( 'catid' , '' );

		$cat		= EasyBlogHelper::getTable( 'Category' , 'Table' );

		$cat->load( $catId );

		$this->cat	= $cat;

		// Set default values for new entries.
		if( empty( $cat->created ) )
		{
			$date	= EasyBlogDateHelper::getDate();
			$now	= EasyBlogDateHelper::toFormat($date);

			$cat->created	= $now;
			$cat->published	= true;
		}

		$catRuleItems	= EasyBlogHelper::getTable( 'CategoryAclItem' , 'Table' );

		$categoryRules	= $catRuleItems->getAllRuleItems();
		$assignedACL	= $cat->getAssignedACL();

		$parentList		= EasyBlogHelper::populateCategories('', '', 'select', 'parent_id', $cat->parent_id , false , false , false , array( $cat->id ) );

		$editor			= JFactory::getEditor( $config->get( 'layout_editor' ) );

		$this->assignRef( 'editor'		, $editor );
		$this->assignRef( 'cat'			, $cat );
		$this->assignRef( 'config'		, $config );
		$this->assignRef( 'acl'			, $acl );
		$this->assignRef( 'parentList'	, $parentList );
		$this->assignRef( 'categoryRules'	, $categoryRules );
		$this->assignRef( 'assignedACL'	, $assignedACL );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		if( $this->cat->id != 0 )
		{
			JToolBarHelper::title( JText::sprintf( 'COM_EASYBLOG_CATEGORIES_EDIT_CATEGORY_TITLE' , $this->cat->title ), 'category' );
		}
		else
		{
			JToolBarHelper::title( JText::_( 'COM_EASYBLOG_CATEGORIES_EDIT_ADD_CATEGORY_TITLE' ), 'category' );
		}

		JToolBarHelper::apply( 'save' );
		JToolBarHelper::custom('saveNew','save.png','save_f2.png', JText::_( 'COM_EASYBLOG_SAVE_NEW_BUTTON' ) , false);
		JToolBarHelper::divider();
		JToolBarHelper::cancel();
	}

	function registerSubmenu()
	{
		return 'submenu.php';
	}
}
