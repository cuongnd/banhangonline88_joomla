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
jimport( 'joomla.html.toolbar' );

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'date.php' );

class EasyBlogViewArchive extends EasyBlogView
{
	function calendar( $tmpl = null )
	{
		JPluginHelper::importPlugin( 'easyblog' );

		$dispatcher = JDispatcher::getInstance();
		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$config		= EasyBlogHelper::getConfig();
		$my         = JFactory::getUser();
		$acl		= EasyBlogACLHelper::getRuleSet();

		//setting pathway
	    $pathway	= $mainframe->getPathway();
		if( ! EasyBlogRouter::isCurrentActiveMenu( 'archive' ) )
		{
			$pathway->addItem( JText::_('COM_EASYBLOG_ARCHIVE_BREADCRUMB') , '' );
		}

		EasyBlogHelper::getHelper( 'Feeds' )->addHeaders( 'index.php?option=com_easyblog&view=archive' );

		$menuParams 	= $mainframe->getParams();
		$defaultYear	= $menuParams->get('es_archieve_year', 0);
		$defaultMonth	= $menuParams->get('es_archieve_month', 0);

		$archiveYear	= JRequest::getVar( 'archiveyear' , $defaultYear , 'REQUEST' );
		$archiveMonth	= JRequest::getVar( 'archivemonth' , $defaultMonth, 'REQUEST' );
		$archiveDay		= JRequest::getVar( 'archiveday' , 0 , 'REQUEST' );
		$itemId 		= JRequest::getInt('Itemid', 0);

		if(empty($archiveYear) || empty($archiveMonth))
		{
			// @task: Set the page title
			$title					= EasyBlogHelper::getPageTitle( JText::_( 'COM_EASYBLOG_ARCHIVE_PAGE_TITLE' ) );
			parent::setPageTitle( $title , false , $config->get( 'main_pagetitle_autoappend' ) );

			$tpl	= new CodeThemes();
			$tpl->set('itemId', $itemId );
			echo $tpl->fetch( 'calendar.php' );
			return;
		}

		$date           = EasyBlogHelper::getDate();
		$sort			= 'latest';
        $model			= $this->getModel( 'Archive' );
		$year			= $model->getArchiveMinMaxYear();
		$data			= $model->getArchive($archiveYear, $archiveMonth, $archiveDay);
		$pagination		= $model->getPagination();
		$params			= $mainframe->getParams('com_easyblog');
		$limitstart		= JRequest::getVar('limitstart', 0, '', 'int');
		$data			= EasyBlogHelper::formatBlog( $data );

		//if day is empty
		if(empty($archiveDay))
		{
			$archiveDay		= '01';
			$dateformat		= '%B %Y';
			$emptyPostMsg	= JText::_('COM_EASYBLOG_ARCHIVE_NO_ENTRIES_ON_MONTH');
		}
		else
		{
			$dateformat		= '%d %B %Y';
			$emptyPostMsg	= JText::_('COM_EASYBLOG_ARCHIVE_NO_ENTRIES_ON_DAY');
		}

		$archiveDay		= ( strlen( $archiveDay ) < 2 )? '0' . $archiveDay : $archiveDay;
		$viewDate		= EasyBlogHelper::getDate( $archiveYear . '-' . $archiveMonth . '-' . $archiveDay);
		$formatedDate	= $viewDate->toFormat( $dateformat );
		$archiveTitle	= JText::sprintf( 'COM_EASYBLOG_ARCHIVE_HEADING_TITLE' , $formatedDate );

		// @task: Set the page title
		$title					= EasyBlogHelper::getPageTitle( JText::sprintf( 'COM_EASYBLOG_ARCHIVE_HEADING_TITLE' , $formatedDate ) );
		parent::setPageTitle( $title , false , $config->get( 'main_pagetitle_autoappend' ) );


	   	// set meta tags for featured view
		EasyBlogHelper::setMeta( META_ID_ARCHIVE, META_TYPE_VIEW, JText::_( 'COM_EASYBLOG_ARCHIVE_PAGE_TITLE' ) . ' - ' . $formatedDate);

	   	// set meta tags for featured view
		EasyBlogHelper::setMeta( META_ID_ARCHIVE, META_TYPE_VIEW, JText::_( 'COM_EASYBLOG_ARCHIVE_PAGE_TITLE' ) . ' - ' . $formatedDate);

		$tpl	= new CodeThemes();
		$tpl->set('data', $data );
		$tpl->set('pagination', $pagination->getPagesLinks());
		$tpl->set('siteadmin', EasyBlogHelper::isSiteAdmin() );
		$tpl->set('archiveYear', $archiveYear);
		$tpl->set('archiveMonth', $archiveMonth);
		$tpl->set('archiveDay', $archiveDay);
		$tpl->set('config', $config);
		$tpl->set('my', $my );
		$tpl->set('acl', $acl );
		$tpl->set('archiveTitle', $archiveTitle );
		$tpl->set('emptyPostMsg', $emptyPostMsg );

		echo $tpl->fetch( 'blog.archive.php' );
	}

	public function display( $tpl = null )
	{
		$model			= $this->getModel( 'Blog' );
		$data			= $model->getBlogsBy( '' , 0 , 'latest' , 0 , EBLOG_FILTER_PUBLISHED , false , false , array() , false , false , true , array() , array() , null , 'archive' );
		$pagination		= $model->getPagination();
		$document = JFactory::getDocument();

		$limitstart = JRequest::getVar('limitstart', 0);

		if ($limitstart) {
			$document->setMetadata('robots', 'noindex,follow');
		}

		$tpl	= new CodeThemes();
		$tpl->set( 'data' 		, $data );
		$tpl->set( 'pagination'	, $pagination );
		$tpl->set( 'emptyPostMsg' , JText::_( 'COM_EASYBLOG_NO_ENTRIES_YET' ) );
		echo $tpl->fetch( 'blog.archive.list.php' );
	}
}
