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

class EasyBlogViewReports extends EasyBlogAdminView
{
	function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('easyblog.manage.report' , 'com_easyblog') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}

		//initialise variables
		$document	= JFactory::getDocument();
		$user		= JFactory::getUser();
		$mainframe	= JFactory::getApplication();

		$result		= $this->get( 'Data' );
		$pagination	= $this->get( 'Pagination' );

		$reports	= array();

		if( $result )
		{
			foreach( $result as $row )
			{
				$report	= EasyBlogHelper::getTable( 'Report' );
				$report->bind( $row );

				$reports[]	= $report;
			}
		}

		$this->assign( 'pagination'	, $pagination );
		$this->assign( 'reports' , $reports );

		parent::display($tpl);
	}

	public function getReportLink( $objId , $objType )
	{
		// @TODO: Configurable item links.
		switch( $objType )
		{
			case EBLOG_REPORTING_POST:
			default:
				$blog	= EasyBlogHelper::getTable( 'Blog' );
				$blog->load( $objId );

				$url	= JURI::root() . 'index.php?option=com_easyblog&view=entry&id=' . $objId;
				return '<a href="' . $url . '" target="_blank">' . $blog->title . '</a>';
			break;
		}
	}

	public function getType( $objType )
	{
		// @TODO: Configurable item links.
		switch( $objType )
		{
			case EBLOG_REPORTING_POST:
			default:
				return JText::_( 'COM_EASYBLOG_BLOG_POST' );
			break;
		}
	}

	function registerToolbar()
	{
		JToolBarHelper::title( JText::_( 'COM_EASYDISCUSS_REPORTS' ), 'reports' );

		JToolbarHelper::back( JText::_( 'COM_EASYBLOG_TOOLBAR_HOME' ) , 'index.php?option=com_easyblog' );
		JToolbarHelper::divider();
		JToolbarHelper::deleteList( JText::_( 'COM_EASYBLOG_CONFIRM_DISCARD_REPORTS' ) , 'discard' , JText::_( 'COM_EASYBLOG_DISCARD_REPORT_BUTTON' ) );
	}
}
