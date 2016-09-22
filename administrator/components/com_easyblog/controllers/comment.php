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

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'string.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'comment.php' );

class EasyBlogControllerComment extends EasyBlogController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask( 'add' , 'edit' );
		$this->registerTask( 'unpublish' , 'unpublish' );
	}

	function cancel()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'comment' );

		$this->setRedirect( 'index.php?option=com_easyblog&view=comments' );

		return;
	}

	function edit()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'comment' );

		JRequest::setVar( 'view', 'comment' );
		JRequest::setVar( 'commentid' , JRequest::getVar( 'commentid' , '' , 'REQUEST' ) );

		parent::display();
	}

	function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'comment' );

		$comments	= JRequest::getVar( 'cid' , '' , 'POST' );
		$message	= '';
		$type		= 'message';

		if( empty( $comments ) )
		{
			$message	= JText::_('Invalid comment id');
			$type		= 'error';
		}
		else
		{
			$table		= EasyBlogHelper::getTable( 'Comment' , 'Table' );
			foreach( $comments as $comment )
			{
				$table->load( $comment );

				// AlphaUserPoints
				// since 1.2
				if ( !empty($table->created_by) && EasyBlogHelper::isAUPEnabled() )
				{
					$aupid = AlphaUserPointsHelper::getAnyUserReferreID( $table->created_by );
					AlphaUserPointsHelper::newpoints( 'plgaup_easyblog_delete_comment', $aupid, '', JText::_('COM_EASYBLOG_AUP_COMMENT_DELETED') );
				}

				if( !$table->delete() )
				{
					$message	= JText::_( 'COM_EASYBLOG_COMMENTS_COMMENT_REMOVE_ERROR' );
					$type		= 'error';
					$this->setRedirect( 'index.php?option=com_easyblog&view=comments' , $message , $type );
					return;
				}

				$message	= JText::_('COM_EASYBLOG_COMMENTS_COMMENT_REMOVED');
			}
		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=comments' , $message , $type );
	}

	function save()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'comment' );

		$mainframe	= JFactory::getApplication();

		$message	= '';
		$type		= 'message';

		if( JRequest::getMethod() == 'POST' )
		{
			$post				= JRequest::get( 'post' );
			$user				= JFactory::getUser();
			$post['created_by']	= $user->id;
			$commentId				= JRequest::getVar( 'commentid' , '' );
			$comment				= EasyBlogHelper::getTable( 'Comment', 'Table' );

			if( !empty( $commentId ) )
			{
				$comment->load( $commentId );
				$post['created_by']	= $comment->created_by;
			}

			$comment->bind( $post );
			//$comment->comment	= EasyBlogStringHelper::url2link( $comment->comment );

			if (!$comment->store())
			{
	        	JError::raiseError(500, $comment->getError() );
			}
			else
			{
			    if($comment->published && !$comment->sent)
			    {
					$comment->comment   = EasyBlogCommentHelper::parseBBCode($comment->comment);
					$comment->comment   = nl2br($comment->comment);

		    		$blog 		= EasyBlogHelper::getTable( 'Blog' );
		    		$blog->load( $comment->post_id );

		    		$comment->processEmails( false , $blog );

					//update the sent flag to sent
					$comment->updateSent();
				}


				$message	= JText::_( 'COM_EASYBLOG_COMMENTS_SAVED' );
			}
		}
		else
		{
			$message	= JText::_('Invalid request method. This form needs to be submitted through a "POST" request.');
			$type		= 'error';
		}

		$mainframe->redirect( 'index.php?option=com_easyblog&view=comments' , $message , $type );
	}

	function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'comment' );

		$comments	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $comments ) <= 0 )
		{
			$message	= JText::_('Invalid comment id');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Comments' );

			foreach( $comments as $id )
			{
				$comment 		= EasyBlogHelper::getTable( 'Comment' );
				$comment->load( $id );
				$isModerated	= $comment->published == EBLOG_COMMENT_MODERATE;

				$comment->set( 'published' , EBLOG_COMMENT_PUBLISHED );

				$comment->store( $isModerated );

				$blog 		= EasyBlogHelper::getTable( 'Blog' );
				$blog->load( $comment->post_id );

				$comment->processEmails( false , $blog );

				//update the sent flag to sent
				$comment->updateSent();
			}
			$message	= JText::_('COM_EASYBLOG_COMMENTS_COMMENT_PUBLISHED');
		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=comments' , $message , $type );
	}

	function unpublish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'comment' );

		$comments	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $comments ) <= 0 )
		{
			$message	= JText::_('Invalid comment id');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Comments' );

			if( $model->publish( $comments , 0 ) )
			{
				$message	= JText::_('COM_EASYBLOG_COMMENTS_COMMENT_UNPUBLISHED');
			}
			else
			{
				$message	= JText::_('COM_EASYBLOG_COMMENTS_COMMENT_UNPUBLISH_ERROR');
				$type		= 'error';
			}

		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=comments' , $message , $type );
	}
}
