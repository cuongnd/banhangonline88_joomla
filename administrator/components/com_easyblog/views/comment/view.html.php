<?php
/**
* @package  EasyBlog
* @copyright Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license  GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');

jimport( 'joomla.application.component.view');
require( EBLOG_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views.php');

class EasyBlogViewComment extends EasyBlogAdminView
{
	var $tag	= null;

	function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('easyblog.manage.comment' , 'com_easyblog') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}

		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		//Load pane behavior
		jimport('joomla.html.pane');

		$commentId		= JRequest::getVar( 'commentid' , '' );

		$comment		= EasyBlogHelper::getTable( 'Comment' , 'Table' );

		$comment->load( $commentId );

		$this->comment	= $comment;

		// Set default values for new entries.
		if( empty( $comment->created ) )
		{
			$date	= EasyBlogDateHelper::getDate();
			$now	= EasyBlogDateHelper::toFormat($date);

			$comment->created	= $now;
			$comment->published	= true;
		}

		$this->assignRef( 'comment'		, $comment );

		parent::display($tpl);
	}

	function registerToolbar()
	{
		if( $this->comment->id != 0 )
		{
			JToolBarHelper::title( JText::_('COM_EASYBLOG_COMMENTS_COMMENT_EDITING_COMMENT'), 'comments' );
		}

		JToolBarHelper::save();
		JToolBarHelper::cancel();
	}

	function registerSubmenu()
	{
		return 'submenu.php';
	}
}
