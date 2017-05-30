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

jimport('joomla.application.component.controller');

class EasyBlogControllerSpools extends EasyBlogController
{	
	function __construct()
	{
		parent::__construct();
	}
	
	public function purge()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'mail' );

		$db 	= EasyBlogHelper::db();
		$query	= 'DELETE FROM ' . $db->nameQuote( '#__easyblog_mailq' );
		
		$db->setQuery( $query );
		$db->Query();
		
		$this->setRedirect( 'index.php?option=com_easyblog&view=spools' , JText::_( 'COM_EASYBLOG_MAILS_PURGED' ) );
	}

	public function preview()
	{
		// Check for request forgeries
		JRequest::checkToken( 'get' ) or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'mail' );

		$mailq	= EasyBlogHelper::getTable( 'Mailqueue' );
		$mailq->load( JRequest::getInt( 'id' ) );

		echo $mailq->body;exit;
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'mail' );

		$mails		= JRequest::getVar( 'cid' , '' , 'POST' );
		
		$message	= '';
		$type		= 'info';
		
		if( empty( $mails ) )
		{
			$message	= JText::_('COM_EASYBLOG_NO_MAIL_ID_PROVIDED');
			$type		= 'error';
		}
		else
		{
			$table		= EasyBlogHelper::getTable( 'MailQueue' , 'Table' );
			
			foreach( $mails as $id )
			{
				$table->load( $id );

				if( !$table->delete() )
				{
					$message	= JText::_( 'COM_EASYBLOG_SPOOLS_DELETE_ERROR' );
					$type		= 'error';
					$this->setRedirect( 'index.php?option=com_easyblog&view=spools' , $message , $type );
					return;
				}
			}
			$message	= JText::_('COM_EASYBLOG_SPOOLS_DELETE_SUCCESS');
		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=spools' , $message );
	}
}