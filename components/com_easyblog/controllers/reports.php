<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');

require_once( EBLOG_ROOT . DIRECTORY_SEPARATOR . 'controller.php' );

class EasyBlogControllerReports extends EasyBlogParentController
{
	/**
	 * Process report items.
	 *
	 * @access	public
	 * @param	null
	 **/
	public function submitReport()
	{
		JRequest::checkToken() or die( 'Invalid Token' );

		$my 		= JFactory::getUser();
		$config 	= EasyBlogHelper::getConfig();
		
		if( !$my->id && !$config->get( 'main_reporting_guests' ) )
		{
			echo JText::_( 'COM_EASYBLOG_CATEGORIES_FOR_REGISTERED_USERS_ONLY' );
			exit;
		}

		$objId		= JRequest::getInt( 'obj_id' );
		$objType	= JRequest::getCmd( 'obj_type' );
		$reason		= JRequest::getString( 'reason' );

		// @task: Ensure that the reason is never empty.
		if( empty( $reason ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_REPORT_PLEASE_SPECIFY_REASON' ) , 'error' );
			$this->setRedirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $objId , false ) );
			return;
		}

		$report		= EasyBlogHelper::getTable( 'Report' );
		$report->set( 'obj_id'		, $objId );
		$report->set( 'obj_type'	, $objType );
		$report->set( 'reason'		, $reason );
		$report->set( 'created'		, EasyBlogHelper::getDate()->toMySQL() );
		$report->set( 'created_by'	, $my->id );
		$report->set( 'ip'			, @$_SERVER['REMOTE_ADDR'] );
		
		if( !$report->store() )
		{
			$error 	= $report->getError();

			EasyBlogHelper::setMessageQueue( $error , 'error' );
			$this->setRedirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $objId , false ) );
			return;
		}

		// @TODO: Configurable report links
		switch( $objType )
		{
			case EBLOG_REPORTING_POST:
			default:
				$blog 		= EasyBlogHelper::getTable( 'Blog' );
				$blog->load( $objId );

				$report->notify( $blog );
				
				$message 	= JText::_( 'COM_EASYBLOG_THANKS_FOR_REPORTING' );
				$redirect 	= EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $objId , false );
			break;
		}
		EasyBlogHelper::setMessageQueue( $message );
		$this->setRedirect( $redirect );
	}
}