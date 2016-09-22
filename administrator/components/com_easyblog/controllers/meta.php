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

jimport('joomla.application.component.controller');

require_once( dirname(__FILE__ ) . DIRECTORY_SEPARATOR . 'controller.php' );

class EasyBlogControllerMeta extends EasyBlogController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask( 'apply'			, 'save' );
		$this->registerTask( 'addIndexing'		, 'saveIndexing' );
		$this->registerTask( 'removeIndexing'	, 'saveIndexing' );
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'meta' );

		$mainframe	= JFactory::getApplication();

		$message	= '';
		$type		= 'message';

		$url		= JRoute::_( 'index.php?option=com_easyblog&view=metas' , false );

		if( JRequest::getMethod() == 'POST' )
		{
			$post				= JRequest::get( 'post' );

			if(empty($post['id']))
			{
				$mainframe->enqueueMessage(JText::_('COM_EASYBLOG_INVALID_META_TAG_ID'), 'error');

				$url  = 'index.php?option=com_easyblog&view=metas';
				$mainframe->redirect(JRoute::_($url, false));
				return;
			}

			$meta		= EasyBlogHelper::getTable( 'meta', 'Table' );
			$user		= JFactory::getUser();
			$metaId		= JRequest::getVar( 'id' , '' );

			if( !empty( $metaId ) )
			{
				$meta->load( $metaId );
			}

			$meta->bind( $post );
			$meta->store();

			$message	= JText::_( 'COM_EASYBLOG_META_SAVED' );

			if( $this->getTask() == 'apply' )
			{
				$url 		= JRoute::_( 'index.php?option=com_easyblog&view=meta&id=' . $meta->id , false );
			}
		}
		else
		{
			$message	= JText::_('Invalid request method. This form needs to be submitted through a "POST" request.');
			$type		= 'error';
		}


		$mainframe->redirect( $url , $message , $type );
	}

	public function saveIndexing()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'meta' );

		$app 		= JFactory::getApplication();
		$task 		= $this->getTask();
		$cid 		= JRequest::getVar( 'cid' );

		$meta 		= EasyBlogHelper::getTable( 'Meta' );
		$meta->load( $cid[ 0 ] );

		if( empty( $cid ) || !$meta->id )
		{
			$app->redirect( 'index.php?option=com_easyblog&view=metas' , JText::_( 'COM_EASYBLOG_INVALID_ID_PROVIDED') , 'error' );
			$app->close();
		}

		$meta->indexing 	= $task == 'addIndexing' ? 1 : 0;
		$meta->store();

		$message 			= $task == 'addIndexing' ? JText::_( 'COM_EASYBLOG_META_ENABLED_INDEXING' ) : JText::_( 'COM_EASYBLOG_META_DISABLED_INDEXING' );

		$app->redirect( 'index.php?option=com_easyblog&view=metas' , $message );
	}

	/**
	* Cancels an edit operation
	*/
	function cancel()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'meta' );

		$mainframe = JFactory::getApplication();

		$mainframe->redirect('index.php?option=com_easyblog&view=metas');
	}
}
