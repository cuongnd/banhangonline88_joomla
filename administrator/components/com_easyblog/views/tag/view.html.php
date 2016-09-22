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

class EasyBlogViewTag extends EasyBlogAdminView
{
	var $tag	= null;

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
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		// Render modal
		JHTML::_( 'behavior.modal' );

		$tagId		= JRequest::getVar( 'tagid' , '' );

		$tag		= EasyBlogHelper::getTable( 'Tag' , 'Table' );

		$tag->load( $tagId );

		$tag->title = JString::trim($tag->title);
		$tag->alias = JString::trim($tag->alias);

		$this->tag	= $tag;

		// Set default values for new entries.
		if( empty( $tag->created ) )
		{
			$date   = EasyBlogDateHelper::getDate();
			$now 	= EasyBlogDateHelper::toFormat($date);

			$tag->created	= $now;
			$tag->published	= true;
		}

		$this->assignRef( 'my'		, $user );
		$this->assignRef( 'tag'		, $tag );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		if( $this->tag->id != 0 )
		{
			JToolBarHelper::title( JText::_( 'COM_EASYBLOG_TAGS_EDIT_TAG_TITLE'  ), 'tags' );

		}
		else
		{
			JToolBarHelper::title( JText::_( 'COM_EASYBLOG_TAGS_NEW_TAG_TITLE' ), 'tags' );
		}

		JToolBarHelper::apply( 'save' );
		JToolBarHelper::custom( 'saveNew' , 'save.png' , 'save_f2.png' , JText::_( 'COM_EASYBLOG_SAVE_NEW_BUTTON') , false );
		JToolBarHelper::divider();
		JToolBarHelper::cancel();
	}

	function registerSubmenu()
	{
		return 'submenu.php';
	}
}
