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

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'oauth.php' );

class EasyBlogControllerFeeds extends EasyBlogController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask( 'add' , 'edit' );
		$this->registerTask( 'publish' , 'publish' );
		$this->registerTask( 'unpublish' , 'unpublish' );
	}

	function cancel()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'feeds' );

		$this->setRedirect( 'index.php?option=com_easyblog&view=feeds' );

		return;
	}

	function addNew()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'feeds' );

		$this->setRedirect( 'index.php?option=com_easyblog&view=feeds&layout=form' );

		return;
	}

	function edit()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'feeds' );

		$document	= JFactory::getDocument();

		$cid 	= JRequest::getVar( 'cid' , '' , 'REQUEST' );
		JFactory::getApplication()->redirect( 'index.php?option=com_easyblog&view=feeds&layout=form&cid=' . $cid );
	}

	function remove()
	{
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'feeds' );

		$feeds	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $feeds ) <= 0 )
		{
			$message	= JText::_('COM_EASYBLOG_BLOGS_FEEDS_ERROR_INVALID_ID');
			$type		= 'error';
		}
		else
		{

			for( $i = 0; $i < count($feeds); $i++)
			{
				$id     = $feeds[$i];

				$feed	= EasyBlogHelper::getTable( 'Feed' );
				$feed->load($id);

				if( ! $feed->delete() )
				{
					$this->setRedirect( 'index.php?option=com_easyblog&view=feeds' , JText::_('COM_EASYBLOG_BLOGS_FEEDS_ERROR_DELETE') , 'error' );
					return;
				}
			}

			// all passed.
			$message	= JText::_('COM_EASYBLOG_BLOGS_FEEDS_DELETE_SUCCESS');

		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=feeds' , $message , $type );


	}

	public function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'feeds' );

		$document	= JFactory::getDocument();
		$mainframe	= JFactory::getApplication();

		$post	= JRequest::get( 'POST' );
		$cid	= JRequest::getVar( 'cid' , '' , 'REQUEST' );

		$feed	= EasyBlogHelper::getTable( 'Feed', 'Table' );

		if( !empty($cid) )
		{
			$feed->load($cid);
		}

		$feed->bind( $post );

		if( empty( $feed->item_creator ) )
		{
			$mainframe->enqueueMessage(JText::_( 'COM_EASYBLOG_BLOGS_FEEDS_ERROR_AUTHOR' ), 'error');

			JRequest::set($post, 'POST');

			$view	= $this->getView('Feeds', $document->getType());
			$view->setLayout('form');

			$view->display();
			return;
		}

		if( empty( $feed->item_category ) )
		{
			$mainframe->enqueueMessage(JText::_( 'COM_EASYBLOG_BLOGS_FEEDS_ERROR_CATEGORY' ), 'error');

			JRequest::set($post, 'POST');
			JRequest::setVar('cid', $cid);

			$view	= $this->getView('Feeds', $document->getType());
			$view->setLayout('form');
			$view->display();
			return;

		}

		if( empty( $feed->url ) )
		{
			$mainframe->enqueueMessage(JText::_( 'COM_EASYBLOG_BLOGS_FEEDS_ERROR_URL' ), 'error');

			JRequest::set($post, 'POST');
			JRequest::setVar('cid', $cid);

			$view	= $this->getView('Feeds', $document->getType());
			$view->setLayout('form');
			$view->display();
			return;
		}

		if( empty( $feed->title ) )
		{
			$mainframe->enqueueMessage(JText::_( 'COM_EASYBLOG_BLOGS_FEEDS_ERROR_TITLE' ), 'error');

			JRequest::set($post, 'POST');
			JRequest::setVar('cid', $cid);

			$view	= $this->getView('Feeds', $document->getType());
			$view->setLayout('form');
			$view->display();
			return;
		}

		// Store the allowed tags here.
		$allowed		= JRequest::getVar( 'item_allowed_tags' , '' , 'REQUEST' , 'none' , JREQUEST_ALLOWRAW );
		$copyrights		= JRequest::getVar( 'copyrights' , '' );
		$sourceLinks	= JRequest::getVar( 'sourceLinks' , '0' );
		$feedamount		= JRequest::getVar( 'feedamount' , '0' );
		$autopost 		= JRequest::getVar( 'autopost' , 0 );

		$params			= EasyBlogHelper::getRegistry( '' );
		$params->set( 'allowed'		, $allowed );
		$params->set( 'copyrights'	, $copyrights );
		$params->set( 'sourceLinks' , $sourceLinks );
		$params->set( 'autopost'	, $autopost );
		$params->set( 'feedamount' , $feedamount );
		$feed->params	= $params->toString();

		if( !$feed->store() )
		{
			$mainframe->enqueueMessage(JText::_( 'COM_EASYBLOG_BLOGS_FEEDS_ERROR_SAVE' ), 'error');

			JRequest::set($post, 'POST');
			JRequest::setVar('cid', $cid);

			$view	= $this->getView('Feeds', $document->getType());
			$view->setLayout('form');
			$view->display();
			return;
		}

		$mainframe->redirect( 'index.php?option=com_easyblog&view=feeds' , JText::_( 'COM_EASYBLOG_BLOGS_FEEDS_SAVE_SUCCESS' ) , 'success' );
	}

	function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'feeds' );

		$feeds	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $feeds ) <= 0 )
		{
			$message	= JText::_('COM_EASYBLOG_BLOGS_FEEDS_ERROR_INVALID_ID');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Feeds' );

			if( $model->publish( $feeds , 1 ) )
			{
				$message	= JText::_('Feed(s) published');
			}
			else
			{
				$message	= JText::_('Error publishing feed');
				$type		= 'error';
			}

		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=feeds' , $message , $type );
	}

	function unpublish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'feeds' );

		$feeds		= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $feeds ) <= 0 )
		{
			$message	= JText::_('COM_EASYBLOG_BLOGS_FEEDS_ERROR_INVALID_ID');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Feeds' );

			if( $model->publish( $feeds , 0 ) )
			{
				$message	= JText::_('Feed(s) unpublished');
			}
			else
			{
				$message	= JText::_('Error unpublishing feed');
				$type		= 'error';
			}

		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=feeds' , $message , $type );
	}
}
