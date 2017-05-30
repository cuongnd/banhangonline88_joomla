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

class EasyBlogControllerEntry extends EasyBlogParentController
{
	var $err	= null;

	function display()
	{
        parent::display();
	}

	function _trim(&$text)
	{
		$text = JString::trim($text);
	}

	function setProtectedCredentials()
	{
		$id = JRequest::getVar('id', '');

		if(!empty($id))
		{
			$password = JRequest::getVar('blogpassword_'.$id, '');

			$jSession 	= JFactory::getSession();
			$jSession->set('PROTECTEDBLOG_'.$id, $password, 'EASYBLOG');
		}

		$return = JRequest::getVar('return');

		$this->setRedirect(base64_decode($return));
	}

	/*
	 * @since 2.0.3300
	 * Responsible to update an existing comment
	 *
	 * @param	null
	 * @return	null
	 */
	public function updateComment()
	{
		$mainframe	= JFactory::getApplication();
		$my			= JFactory::getUser();
	    $acl		= EasyBlogACLHelper::getRuleSet();
		$id			= JRequest::getInt( 'commentId' );
		$post		= JRequest::get( 'POST' );

		//add here so that other component with the same comment.php jtable file will not get reference.
		JTable::addIncludePath( EBLOG_TABLES );
		$comment	= EasyBlogHelper::getTable( 'Comment', 'Table' );
		$comment->load( $id );
		$redirect	= EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $comment->post_id , false );

		if( ($my->id != $comment->created_by || !$acl->rules->delete_comment ) && !EasyBlogHelper::isSiteAdmin() && !$acl->rules->manage_comment|| $my->id == 0 )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_UPDATE_COMMENT') , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}
		$comment->bindPost( $post );

		if( !$comment->validate( 'title' ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_COMMENT_TITLE_IS_EMPTY') , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		if( !$comment->validate( 'comment' ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_COMMENT_IS_EMPTY') , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		$comment->modified	= EasyBlogHelper::getDate()->toMySQL();

		if( !$comment->store() )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_COMMENT_FAILED_TO_SAVE') , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_COMMENT_UPDATED_SUCCESS') , 'success' );
		$mainframe->redirect( $redirect );
		$mainframe->close();
	}

	/*
	 * @since 2.0.3300
	 * Responsible to update an existing comment
	 *
	 * @param	null
	 * @return	null
	 */
	public function deleteComment()
	{
		$mainframe	= JFactory::getApplication();
		$my			= JFactory::getUser();
	    $acl		= EasyBlogACLHelper::getRuleSet();
		$id			= JRequest::getInt( 'commentId' );
		$post		= JRequest::get( 'POST' );

		//add here so that other component with the same comment.php jtable file will not get reference.
		JTable::addIncludePath( EBLOG_TABLES );
		$comment	= EasyBlogHelper::getTable( 'Comment', 'Table' );
		$comment->load( $id );
		$redirect	= EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $comment->post_id , false );

		if( ( $my->id == 0 || $my->id != $comment->created_by || !$acl->rules->delete_comment ) && !EasyBlogHelper::isSiteAdmin() )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_UPDATE_COMMENT') , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		if( !$comment->delete() )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_COMMENT_FAILED_TO_SAVE') , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_COMMENT_DELETED_SUCCESS') , 'success' );
		$mainframe->redirect( $redirect );
		$mainframe->close();
	}

	/**
	 * @since 3.0
	 * Unsubscribe a user with email to a blog post
	 *
	 * @param	int		Subscription ID
	 * @param	int		Blog post ID
	 *
	 * @return	bool	True on success
	 */
	public function unsubscribe()
	{
		$subscriptionId	= JRequest::getInt('subscription_id');
		$blogId			= JRequest::getInt('blog_id');
		$my				= JFactory::getUser();
		$mainframe		= JFactory::getApplication();
		$redirect		= EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $blogId , false );

		// Check variables
		if( $my->id == 0 || !$subscriptionId || !$blogId )
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , 'error');
			$mainframe->redirect( $redirect );
		}

		// Need to ensure that whatever id passed in is owned by the current browser
		$blogModel	= EasyblogHelper::getModel('Blog');
		$sid		= $blogModel->isBlogSubscribedUser( $blogId , $my->id , $my->email );

		if($subscriptionId != $sid)
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , 'error');
			$mainframe->redirect( $redirect );
		}

		// Proceed to unsubscribe
		$table	= EasyBlogHelper::getTable('Subscription', 'Table');
		$table->load( $subscriptionId );

		if (!$table->delete())
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_UNSUBSCRIBE_BLOG_FAILED' ) , 'error');
			$mainframe->redirect( $redirect );
		}

		EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_UNSUBSCRIBE_BLOG_SUCCESS') , 'success' );
		$mainframe->redirect( $redirect );
	}
}