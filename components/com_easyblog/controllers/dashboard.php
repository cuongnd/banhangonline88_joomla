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

require_once( EBLOG_ROOT . DIRECTORY_SEPARATOR . 'controller.php' );


class EasyBlogControllerDashboard extends EasyBlogParentController
{
	/**
	 * Constructor
	 *
	 * @since 0.1
	 */
	function __construct()
	{
		// Include the tables in path
		JTable::addIncludePath( EBLOG_TABLES );

		parent::__construct();
	}

	/**
	 * Display the view
	 *
	 * @since 0.1
	 */
	function display( $cachable = false, $urlparams = false )
	{
		$document	= JFactory::getDocument();
		$viewType	= $document->getType();
		$viewName	= JRequest::getCmd( 'view', $this->getName() );
		$view 		= $this->getView( $viewName,'',  $viewType);
		$view->display();
	}

	function toggleBlogStatus()
	{
		$mainframe	= JFactory::getApplication();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$url    	= 'index.php?option=com_easyblog&view=latest';
		$msgType    = 'info';
		$id 		= JRequest::getVar( 'blogId' , '' );
		$id 		= explode( "," , $id );
		$src    	= JRequest::getVar( 'from' , '' );
		$src    	= strtolower($src);
		$status		= JRequest::getInt('status', 0);
		$my			= JFactory::getUser();

		if( empty($acl->rules->publish_entry) )
		{
			$callback = JText::_('COM_EASYBLOG_NO_PERMISSION_TO_PUBLISH_OR_UNPUBLISH_BLOG');
			EasyBlogHelper::setMessageQueue( $callback , 'error');
			$mainframe->redirect(EasyBlogRouter::_($url, false));
			return;
		}

		JTable::addIncludePath( EBLOG_TABLES );
		foreach( $id as $data )
		{
			$blog		= EasyBlogHelper::getTable( 'Blog' , 'Table' );
			$blog->load( $data );

			if( $blog->created_by != $my->id && !EasyBlogHelper::isSiteAdmin() )
			{
				$callback	= JText::_('COM_EASYBLOG_NO_PERMISSION_TO_PUBLISH_OR_UNPUBLISH_BLOG');
				EasyBlogHelper::setMessageQueue( $callback , 'error');
				$mainframe->redirect( EasyBlogRouter::_( $url , false ) );
				return;
			}
		}

		$model		= $this->getModel( 'Blogs' );
		$result 	= $model->publish($id, $status);

		$msg		= '';
		if($result)
		{
			$callback 	= ($status) ? JText::_('COM_EASYBLOG_DASHBOARD_ENTRIES_PUBLISHED_SUCCESS') : JText::_('COM_EASYBLOG_DASHBOARD_ENTRIES_UNPUBLISHED_SUCCESS');
			$msgType    = 'info';
		}
		else
		{
			$callback 	= JText::_('COM_EASYBLOG_DASHBOARD_ENTRIES_STATUS_FAILED_TO_UPDATE');
			$msgType    = 'warning';
		}


		EasyBlogHelper::setMessageQueue( $callback , $msgType);
		switch($src)
		{
			case 'categories':
				$url    = 'index.php?option=com_easyblog&view=categories';
				break;
			case 'category':
				$catId  = JRequest::getInt('catId', '1'); // default show uncategories
				$url    = 'index.php?option=com_easyblog&view=categories&layout=listings&id='.$catId;
				break;
			case 'tag':
				$tagId  = JRequest::getInt('tagId', '0');
				$url    = 'index.php?option=com_easyblog&view=tags&layout=tag&id='.$tagId;
				break;
			case 'blogger':
				$bloggerId  = JRequest::getInt('bloggerId', '0');
				$extra      = (empty($bloggerId)) ? '' : '&layout=listings&id=' . $bloggerId;
				$url    = 'index.php?option=com_easyblog&view=blogger' . $extra;
				break;
			case 'archive':
				$archiveyear 	= JRequest::getInt('archiveyear', '0');
				$archivemonth 	= JRequest::getInt('archivemonth', '0');
				$url    = 'index.php?option=com_easyblog&view=archive&archiveyear='.$archiveyear.'&archivemonth='.$archivemonth;
				break;
			case 'eblog':
			default:
				$url    = 'index.php?option=com_easyblog&view=latest';
				break;
		}

		$mainframe->redirect(EasyBlogRouter::_($url, false));
	}

	/**
	 * Duplicates blog post
	 */
	public function copy()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$blogs	= JRequest::getVar( 'ids' , array(0) , 'POST' );
		$app 	= JFactory::getApplication();

		if( !$blogs )
		{
			$app->redirect( 'index.php?option=com_easyblog&view=dashboard&layout=entries' , JText::_( 'COM_EASYBLOG_DASHBOARD_COPY_ERROR') , 'error' );
			$app->close();
			return;
		}

		$blogs	= explode( ',' , $blogs );

		foreach( $blogs as $id )
		{
			$blog		= EasyBlogHelper::getTable( 'Blog' );
			$blog->load( $id );

			if( $blog->id )
			{
				$newBlog 	= clone( $blog );

				// We don't want to use the old id.
				$newBlog->id	= '';
				$newBlog->title = JText::_( 'COM_EASYBLOG_COPY_OF' ) . ' ' . $blog->title;
				$newBlog->category_id	= JRequest::getInt( 'category_id' );
				$newBlog->store();
			}
		}

		$app->redirect( 'index.php?option=com_easyblog&view=dashboard&layout=entries' , JText::_( 'COM_EASYBLOG_DASHBOARD_BLOG_COPIED_SUCCESS' ) , 'success' );
		$app->close();
		return;
	}

	function deleteTag()
	{
		$mainframe	= JFactory::getApplication();
		$config     = EasyBlogHelper::getConfig();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$callback 	= "";
		$url    	= 'index.php?option=com_easyblog&view=dashboard&layout=tags';
		$msgType    = 'info';
		$tags       = JRequest::getVar('tagId','');
		$my			= JFactory::getUser();

		if($acl->rules->create_tag)
		{
			$id = explode(",", $tags);

			if( empty( $tags ) )
			{
				$callback	= JText::_('COM_EASYBLOG_TAG_INVALID_ID');
				$msgType    = 'error';
			}
			else
			{
				$table		= EasyBlogHelper::getTable( 'Tag' , 'Table' );
				foreach( $id as $data )
				{
					$isError    = false;
					$table->load( $data );

					if( $table->created_by != $my->id && !EasyBlogHelper::isSiteAdmin() )
					{
						EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , 'error');
						$mainframe->redirect( EasyBlogRouter::_( $url , false ) );
						return;
					}

					if( !$table->delete() )
					{
						$isError	= true;
					}

					if($isError)
					{
						$callback = JText::sprintf('ERROR REMOVING TAG POST', $table->id, $table->title).'<br />';
						EasyBlogHelper::setMessageQueue( $callback , 'error');
						$mainframe->redirect(EasyBlogRouter::_($url, false));
					}

				}

				$callback = JText::_('COM_EASYBLOG_TAG_DELETED');
			}
		}
		else
		{
			$callback	= JText::_('COM_EASYBLOG_NO_PERMISSION_TO_DELETE_TAG');
			$msgType    = 'warning';
		}

		//returned message
		EasyBlogHelper::setMessageQueue( $callback , $msgType);
		$mainframe->redirect(EasyBlogRouter::_($url, false));
	}

	/*
	 * Responsible to delete comments
	 * @param	null
	 * @return	null
	 */
	public function deleteComments()
	{
		$mainframe  = JFactory::getApplication();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$my			= JFactory::getUser();
		$redirect	= base64_decode( JRequest::getVar( 'redirect' , '' ) );
		$redirect	= empty( $redirect ) ? EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=categories' , false ) : EasyBlogRouter::_( $redirect , false );
		$my			= JFactory::getUser();
		$ids		= JRequest::getVar( 'id' , '' );
		$ids		= explode( ',' , $ids );

		if( empty($acl->rules->delete_comment) || $my->id == 0 )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_DELETE_COMMENT') , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		JTable::addIncludePath( EBLOG_TABLES );
		foreach ($ids as $id)
		{
			$comment	= EasyBlogHelper::getTable( 'Comment' , 'Table' );
			$comment->load( $id );

			// @rule: Check if the current browser is the author of the blog.
			$blog		= EasyBlogHelper::getTable( 'Blog' , 'Table' );
			$blog->load( $comment->post_id );

			if( $blog->created_by != $my->id && !EasyBlogHelper::isSiteAdmin() && empty( $acl->rules->delete_comment) )
			{
				EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_NOT_ALLOWED') , 'error' );
				$mainframe->redirect( $redirect );
				$mainframe->close();
			}

			$comment->delete($cid);
		}

		EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_DASHBOARD_COMMENTS_DELETED_SUCCESS') , 'success');
		$mainframe->redirect( $redirect );
		$mainframe->close();
	}

	/*
	 * Responsible to delete category
	 * @param	null
	 * @return	null
	 */
	function deleteCategory()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mainframe  = JFactory::getApplication();
		$config     = EasyBlogHelper::getConfig();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$redirect	= base64_decode( JRequest::getVar( 'redirect' , '' ) );
		$redirect	= empty( $redirect ) ? EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=categories' , false ) : EasyBlogRouter::_( $redirect , false );
		$my			= JFactory::getUser();
		$id			= JRequest::getVar('categoryId','');

		if( !$acl->rules->delete_category || $my->id == 0 )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_DELETE_CATEGORY') , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		if( empty( $id ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_DASHBOARD_CATEGORIES_ID_IS_EMPTY_ERROR' ) , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		JTable::addIncludePath( EBLOG_TABLES );
		$category	= EasyBlogHelper::getTable( 'Category' , 'Table' );
		$category->load( $id );

		if( $category->created_by != $my->id && !EasyBlogHelper::isSiteAdmin() )
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , 'error');
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		if( $category->getPostCount() > 0 )
		{
			EasyBlogHelper::setMessageQueue(  JText::_( 'COM_EASYBLOG_DASHBOARD_CATEGORIES_POST_NOT_EMPTY_ERROR'  ) , 'error');
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		if( $category->getChildCount() > 0 )
		{
			EasyBlogHelper::setMessageQueue(  JText::_( 'COM_EASYBLOG_DASHBOARD_CATEGORIES_HAS_CHILD_ERROR'  ) , 'error');
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		if( !$category->delete() )
		{
			EasyBlogHelper::setMessageQueue(  JText::sprintf( 'COM_EASYBLOG_DASHBOARD_CATEGORIES_DELETE_ERROR' , $category->title ) , 'error');
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_DASHBOARD_CATEGORIES_DELETED_SUCCESSFULLY' ) , 'success' );
		$mainframe->redirect( $redirect );
		$mainframe->close();
	}

	/*
	 * Allows anyone to approve comments provided that they get the correct key
	 *
	 * @param	null
	 * @return	null
	 */
	public function approveComment()
	{
		$mainframe	= JFactory::getApplication();
		$key		= JRequest::getVar( 'key' , '' );
		$redirect	= EasyBlogRouter::_( 'index.php?option=com_easyblog' , false );

		if( empty( $key ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , 'error');
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		JTable::addIncludePath( EBLOG_TABLES );
		$hashkey	= EasyBlogHelper::getTable( 'HashKeys' , 'Table' );

		if( !$hashkey->loadByKey( $key ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , 'error');
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		$comment	= EasyBlogHelper::getTable( 'Comment' , 'Table');
		$comment->load( $hashkey->uid );
		$isModerated	= $comment->published == EBLOG_COMMENT_MODERATE;
		$comment->set( 'published' , EBLOG_COMMENT_PUBLISHED );
		$comment->store( $isModerated );

		$blog 		= EasyBlogHelper::getTable( 'Blog' );
		$blog->load( $comment->post_id );
		$comment->processEmails( false , $blog );
		//update the sent flag to sent
		$comment->updateSent();

		// Delete the unused hashkey now.
		$hashkey->delete();

		EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_MODERATE_COMMENT_PUBLISHED_SUCCESS' ) , 'success');
		$mainframe->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $comment->post_id , false ) );
	}

	/*
	 * Allows users with privileges to approve pending blog entries
	 *
	 * @param	null
	 * @return	null
	 */
	function approveBlog()
	{
		$mainframe	= JFactory::getApplication();
		$config	= EasyBlogHelper::getConfig();
		$message	= '';
		$type		= 'message';

		$my			= JFactory::getUser();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$joomlaVersion = EasyBlogHelper::getJoomlaVersion();

		// @rule: Test if the current logged in user is really logged in.
		if( $my->id == 0 )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_YOU_ARE_NOT_LOGIN') , 'error' );
			$this->setRedirect( EasyBlogRouter::_( 'index.php?option=com_easyblog' , false ) );
		}

		// @rule: Test if the current logged in user really has the access to manage pending entries and also has the rules to create new blog entry.
		if( empty( $acl->rules->add_entry ) && empty( $acl->rules->manage_pending ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_BLOG') , 'warning');
			$mainframe->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard' , false ) );
			return;
		}

		// @rule: Request should be a POST only.
		if( JRequest::getMethod() != 'POST' )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_DASHBOARD_SAVE_INVALID_METHOD') , 'error' );
			$this->setRedirect( EasyBlogRouter::_( 'index.php?option=com_easyblog' , false ) );
		}

		$draft		= EasyBlogHelper::getTable( 'Draft', 'Table' );
		$id			= JRequest::getInt( 'ids' );
		$draft->load( $id );

		$blog		= EasyBlogHelper::getTable( 'blog', 'Table' );
		$isNew		= ( $blog->load( $draft->entry_id ) ) ? false : true;

		if(! $isNew)
		{
			// if this is blog edit, then we should see the column isnew to determine
			// whether we should send any notification
			$isNew  = $blog->isnew;
		}
		else
		{
			// this mean the entry is coming from moderation approval.
			$isNew = true;
		}

		// we need so that when bind into blog, the isnew is correct
		$draft->isnew = $isNew;

		// @rule: Do not bring over the hit count as the draft copy doesn't containt the correct value
		unset($draft->hits);

		// author acl
		$authorAcl		= EasyBlogACLHelper::getRuleSet( $blog->created_by );

		// @rule: If it is new post, check if user have permission to post to front page
		// @rule: If it is not a new post, then carry over the front page value from original post
		if( $isNew && !empty($authorAcl->rules->contribute_frontpage) )
		{
			$draft->frontpage = $authorAcl->rules->contribute_frontpage;
		}
		else
		{
			$draft->frontpage = $blog->frontpage;
		}

		// @task: Map the data from draft table.
		$blog->bind( $draft );
		$blog->set( 'id' , $draft->entry_id );

		//check if user have permission to enable privacy.
		$blog->private	= empty( $authorAcl->rules->enable_privacy ) ? 0 : $blog->private;



		$blog->isnew 		= ($isNew && ($blog->published != 1)) ? '1' : '0';

		//now we need to check the blog contribution
		$blog->issitewide	= isset( $draft->blog_contribute) && $draft->blog_contribute != 0 ? false : true;

		$blogContribution   = array();
		$issitewide 		= '1';

		if( isset( $draft->blog_contribute) && $draft->blog_contribute == '0' )
		{
			$blog->issitewide	= true;
		}
		else
		{
			$blog->issitewide	= false;
			$blogContribution[]	= $draft->blog_contribute;
		}

		// default set this variable to true.
		$blog->under_approval   = true;


		// @task: Try to save the blog post.
		if (!$blog->store())
		{
			EasyBlogHelper::setMessageQueue( $blog->getError() , 'error');
			$mainframe->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=write' , false) );
		}

		// @task: Update the contribution linkage between the blog post and the respective team
		$blog->updateBlogContribution($blogContribution);

		// @task: Notify the world that someone created a blog post.
		//$blog->notify( $blog->under_approval );
		$blog->notify( true, $blog->published );

		// @rule: Since this is a new entry, we need to initialize the meta tag
		$blog->createMeta( $draft->metakey , $draft->metadesc );

		// @rule: Process the tags after the blog is stored.
		if( !empty( $draft->tags ) )
		{
			$blog->processTags( explode( ',' , $draft->tags ) );
		}

		$autopost 		= !empty( $draft->autopost ) ? explode( ',' , $draft->autopost ) : $draft->autopost;
		$centralized	= !empty( $draft->autopost_centralized ) ? explode( ',' , $draft->autopost_centralized ) : $draft->autopost_centralized;

		// @rule: Autoposting to social network sites.
		$blog->autopost( $autopost , $centralized );

		// @task: Set the success message / notice.
		$message	= JText::_('COM_EASYBLOG_DASHBOARD_ENTRIES_SAVED');

		// @task: Cleanup any messages
		$postreject	= EasyBlogHelper::getTable( 'PostReject' , 'Table' );
		$postreject->clear( $draft->id );

		// now blog updated, we need to remove the draft
		$draft->delete();

		EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_DASHBOARD_SAVE_SUCCESS')  , 'success');
		$this->setRedirect( EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=pending', false) );
	}

	/*
	 * Responsible to reject a blog post
	 *
	 * @param	null
	 */
	function rejectBlog()
	{
		$app		= JFactory::getApplication();
		$redirect	= base64_decode( JRequest::getVar( 'redirect' , '' ) );
		$redirect	= empty( $redirect ) ? EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=pending' , false ) : EasyBlogRouter::_( $redirect , false );
		$my			= JFactory::getUser();
		$config     = EasyBlogHelper::getConfig();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$ids  		= JRequest::getVar('ids', '');
		$ids		= explode( ',' , $ids );
		$message	= JRequest::getVar( 'message' , '' );

		// @task: Ensure that there's really an id to work on.
		if( empty( $ids ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , 'error');
			$app->redirect( $redirect );
			$app->close();
		}

		// @task: Ensure that the user really has permissions to reject the blog post.
		if( !EasyBlogHelper::isSiteAdmin() && empty( $acl->rules->manage_pending ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , 'error');
			$app->redirect( $redirect );
			$app->close();
		}

		$model 	= EasyBlogHelper::getModel( 'TeamBlogs' );

		foreach( $ids as $id )
		{
			$teamid = $model->getPostTeamId( $id );

			// check if is post's team's admin
			if( ! ( $model->checkIsTeamAdmin( $my->id, $teamid ) || EasyBlogHelper::isSiteAdmin() ) && empty( $acl->rules->manage_pending ) )
			{
				EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , 'error');
				$app->redirect( $redirect );
				$app->close();
			}

			$draft 	= EasyBlogHelper::getTable( 'Draft' );
			$draft->load( $id );

			if( $draft->pending_approval != 1 )
			{
				EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , 'error');
				$app->redirect( $redirect );
				$app->close();
			}

			// If the draft is rejected, revert draft status and create a note for the rejected draft.
			$draft->set( 'pending_approval' , 0 );
			$draft->store();

			// Create the message
			$postreject				= EasyBlogHelper::getTable( 'PostReject' );
			$postreject->draft_id	= $draft->id;
			$postreject->message	= $message;
			$postreject->created_by	= $my->id;
			$postreject->created	= EasyBlogHelper::getDate()->toMySQL();
			$postreject->store();
		}

		EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_DASHBOARD_BLOG_REJECTED') , 'info');
		$app->redirect( $redirect );
		$app->close();
	}

	/**
	 * Delete a blog entry from the site given the id's.
	 **/
	public function deleteBlog()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mainframe  = JFactory::getApplication();
		$config     = EasyBlogHelper::getConfig();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$my			= JFactory::getUser();
		$message	= '';
		$type		= '';

		$ids		= JRequest::getVar( 'blogId' , '' );
		$ids		= explode( ',' , $ids );
		$redirect	= JRequest::getVar( 'redirect' , '' );
		$redirect	= empty( $redirect ) ? 'index.php?option=com_easyblog' : base64_decode( $redirect );

		$sh404exists	= EasyBlogRouter::isSh404Enabled();

		if( empty( $ids ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_DASHBOARD_DELETE_NO_ID_ERROR' ) , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		if( (empty($acl->rules->delete_entry) || $my->id == 0 ) && !EasyBlogHelper::isSiteAdmin() && empty($acl->rules->moderate_entry))
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NO_PERMISSION_TO_DELETE_BLOG' ) , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}


		foreach( $ids as $id )
		{
			$blog	= EasyBlogHelper::getTable( 'Blog' , 'Table' );
			$blog->load( $id );

			if( $blog->created_by != $my->id && !EasyBlogHelper::isSiteAdmin() && empty($acl->rules->moderate_entry))
			{
				EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , 'error');
				$mainframe->redirect( $redirect );
				$mainframe->close();
			}

			if( !$blog->delete() )
			{
				EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_DASHBOARD_DELETE_ERROR' ) , 'error' );
				$mainframe->redirect( $redirect );
				$mainframe->close();
			}
		}

		EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_DASHBOARD_DELETE_SUCCESS' , 'success' ) );
		$mainframe->redirect( $redirect );
		$mainframe->close();
	}

	/**
	 * Key method to save blog post's as drafts
	 *
	 * @access	public
	 * @param	string 	$redirect			By specifing a redirect, this method will be smart enough to redirect to the desired URL.
	 * @param	boolean	$pendingApproval	Whether or not the current request is for pending posts.
	 *
	 */
	function _saveDraft( $redirect, $pendingApproval = false )
	{
		$app		= JFactory::getApplication();
		$config 	= EasyBlogHelper::getConfig();
		$my			= JFactory::getUser();
		$acl		= EasyBlogACLHelper::getRuleSet();

		// Status messages
		$message	= '';
		$type		= 'message';

		if( $my->id == 0 )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_YOU_ARE_NOT_LOGIN') , 'error' );
			$app->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog' , false ) );
			$app->close();
		}

		if( JRequest::getMethod() != 'POST' )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_DASHBOARD_SAVE_INVALID_METHOD') , 'error' );
			$app->redirect( EasyBlogRouter::_( $redirect , false ) );
			$app->close();
		}

		if( empty( $acl->rules->add_entry ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_DRAFT') , 'warning');
			$app->redirect( EasyBlogRouter::_( $redirect , false ) );
			$app->close();
		}

		$params				= JRequest::get( 'post' );
		$contributionSource	= JRequest::getVar( 'blog_contribute_source' , 'easyblog' );

		// Try to load this draft to see if it exists
		$draft	= EasyBlogHelper::getTable( 'Draft' );
		$draft->load( $params[ 'draft_id' ] );

		if( isset( $params[ 'id' ] ) && !empty( $params[ 'id' ] ) )
		{
			$draft->entry_id	= $params[ 'id' ];
			unset( $params[ 'id' ] );
		}

		$content				= JRequest::getVar('write_content_hidden', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$params[ 'content' ]	= $content;

		$trackbacks	= JRequest::getVar( 'trackback' , '' , 'POST' );
		$params[ 'trackback' ]  = $trackbacks;

		// Some other elements might use name="language", so we need to remap this
		$language 				= JRequest::getVar( 'eb_language' );
		$params['language']		= $language;

		$draft->bind( $params , true );

		// Set the creator / owner of the draft.
		$draft->created_by 		= $my->id;

		if( isset( $params[ 'draft_id'] ) && !empty( $params[ 'draft_id' ] ) )
		{
			$draft->id	= $params[ 'draft_id' ];
		}

		if( $draft->id && $draft->created_by != $my->id )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_NOT_ALLOWED') , 'warning');
			$app->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard' , false ) );
			$app->close();
		}

		// @rule: Set the approval state if this is a pending approval post.
		if( $pendingApproval )
		{
			$draft->set( 'pending_approval' , 1 );
		}

		// @rule: Save the draft
		if( !$draft->store() )
		{
			EasyBlogHelper::setMessageQueue( $draft->getError() , 'error');
			$app->redirect( EasyBlogRouter::_( $redirect , false) );
			$app->close();
		}

		$message    = JText::_('COM_EASYBLOG_DASHBOARD_DRAFTS_SAVE_SUCCESS');

		if( $pendingApproval )
		{
			$message    = JText::_('COM_EASYBLOG_DASHBOARD_SAVE_BLOG_BUT_PENDING_FOR_APPROVAL');
		}

		EasyBlogHelper::setMessageQueue( $message  , 'success');
		$app->redirect( EasyBlogRouter::_( $redirect, false) );
	}

	/**
	 * This method is invoked when user wants to save a blog post as draft.
	 *
	 * @access	public
	 * @param	null
	 * @return	null
	 **/
	function savedraft()
	{
		$mainframe		= JFactory::getApplication();
		$redirect   = 'index.php?option=com_easyblog&view=dashboard&layout=drafts';
		$this->_saveDraft( $redirect );
		return;
	}

	/**
	 * This method is invoked when user does not have privileges to publish blog post
	 *
	 * @access	public
	 * @param	null
	 * @return	null
	 **/
	function savepending()
	{
		$mainframe		= JFactory::getApplication();
		$redirect   = 'index.php?option=com_easyblog&view=dashboard&layout=entries';

		$this->_saveDraft( $redirect, true );
		return;
	}

	/*
	 * Saving a blog entry
	 *
	 * @param	null
	 * @return	null
	 */
	function save()
	{
		$mainframe		= JFactory::getApplication();
		$config			= EasyBlogHelper::getConfig();
		$message		= '';
		$type			= 'message';
		$my				= JFactory::getUser();
		$acl			= EasyBlogACLHelper::getRuleSet();
		$joomlaVersion	= EasyBlogHelper::getJoomlaVersion();

		$returnRaw	= JRequest::getVar( 'return' );
		$return		= '';

		if( !empty( $returnRaw ) )
		{
			$return			= base64_decode( $returnRaw );

			// Set the raw return value in case there's any errors
			$external	= JRequest::getInt( 'external' );
			$uid		= JRequest::getInt( 'uid' );
			$source		= JRequest::getVar( 'source' );

			$returnRaw	= '&return=' . $returnRaw;

			if( $external )
			{
				$returnRaw	.= '&external=' . $external;
			}

			if( $uid )
			{
				$returnRaw	.= '&uid=' . $uid;
			}

			if( $source )
			{
				$returnRaw	.= '&source=' . $source;
			}
		}
		else
		{
			// We don't want to have any values for built in writing
			$returnRaw		= '';
		}

		if( empty( $acl->rules->add_entry ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_BLOG') , 'warning');
			$mainframe->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard' . $returnRaw , false ) );
			return;
		}

		if( $my->id == 0 )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_YOU_ARE_NOT_LOGIN') , 'error' );
			$this->setRedirect( EasyBlogRouter::_( 'index.php?option=com_easyblog' . $returnRaw , false ) );
		}

		if( JRequest::getMethod() != 'POST' )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_DASHBOARD_SAVE_INVALID_METHOD') , 'error' );
			$this->setRedirect( EasyBlogRouter::_( 'index.php?option=com_easyblog' . $returnRaw , false ) );
		}

		JPluginHelper::importPlugin( 'easyblog' );
		$dispatcher	= JDispatcher::getInstance();
		$blog		= EasyBlogHelper::getTable( 'blog', 'Table' );
		$post		= JRequest::get( 'post' );
		$id			= JRequest::getInt( 'id' );
		$contributionSource	= JRequest::getVar( 'blog_contribute_source' , 'easyblog' );

		$isNew		= ( $blog->load( $id ) ) ? false : true;

		// There seems to be a bug in Joomla 1.6 where the load always return true
		// when invalid id is submitted
		if( $blog->id == 0 )
		{
			$isNew	= true;
		}

		if(! $isNew)
		{
			// if this is blog edit, then we should see the column isnew to determine
			// whether we should send any notification
			$isNew  = $blog->isnew;
		}

		$under_approval = false;
		if( isset($post['under_approval']) )
		{
			$under_approval = true;
		}

		// we do not proccess this on draft
		if ( $post['published'] != POST_ID_DRAFT )
		{
			$txOffset	= EasyBlogDateHelper::getOffSet();
			$today		= EasyBlogHelper::getDate();

			if($post['published'] == POST_ID_PUBLISHED)
			{
				// we check the publishing date here
				// if user set the future date then we will automatically change
				// the status to Schedule
				$publishing 	= EasyBlogHelper::getDate( !isset($post['publish_up']) ? '' : $post[ 'publish_up' ] , $txOffset);

				if ( $publishing->toUnix() > $today->toUnix() )
				{
					$post['published'] = POST_ID_SCHEDULED;
				}
			}
		}

		// Initialize some variables here.
		$post[ 'publish_down' ] = JRequest::getVar( 'publish_down' , '0000-00-00 00:00:00' );


		// Some other elements might use name="language", so we need to remap this
		$language 				= JRequest::getVar( 'eb_language' );
		$post['language']		= $language;

		// @task: Map the request.
		$blog->bind( $post , true );


		// @rule: If user does not have permissions to publish entry, then we need to submit this for approvals.
		if( empty($acl->rules->publish_entry) )
		{
			$this->savepending();
			return;
		}

		// If privacy overrides are disabled, do not let them to force their way in
		if( !$config->get('main_blogprivacy_override') )
		{
			$blog->private	= $config->get( 'main_blogprivacy' );
		}

		//check if user have permission to enable privacy.
		$blog->private		= empty( $acl->rules->enable_privacy ) ? 0 : $blog->private;

		//check if user have permission to contribute the blog post to eblog frontpage
		$blog->frontpage	= (empty($acl->rules->contribute_frontpage)) ? '0' : $blog->frontpage;
		$blog->isnew		= ($isNew && ($blog->published != 1)) ? '1' : '0';

		//now we need to check the blog contribution
		$blog->issitewide	= isset($post['blog_contribute']) && $post['blog_contribute'] != 0 ? false : true;

		$blogContribution	= array();
		$issitewide			= '1';

		if (isset($post['blog_contribute']) && $post['blog_contribute'] == '0') {
			$blog->issitewide = true;
		} else {
			$blog->issitewide = false;
			$blogContribution[] = $post['blog_contribute'];
		}
		
		// @task: When a new post is saved, we need to clear the drafts first.
		if( isset( $post[ 'draft_id' ] ) && !empty( $post[ 'draft_id' ] ) )
		{
			$draft		= EasyBlogHelper::getTable( 'Draft' , 'Table' );
			$draft->load( $post[ 'draft_id' ] );

			// @rule: Only delete the draft when the owner really owns the draft.
			if( $draft->created_by == $my->id || EasyBlogHelper::isSiteAdmin() || $under_approval )
			{
				if( $under_approval )
				{
					// @task: Cleanup any messages
					$postreject	= EasyBlogHelper::getTable( 'PostReject' , 'Table' );
					$postreject->clear( $draft->id );
				}

				$draft->delete();
			}
		}

		if( $under_approval )
		{
			$blog->under_approval   = true;
		}



		$centralized	= JRequest::getVar( 'centralized' , '' );
		$autopost		= JRequest::getVar( 'socialshare' , '' );

		// If the post is scheduled, we need to know if we should auto post for the user or not.
		if ($blog->published == POST_ID_SCHEDULED && $centralized) {
			$blog->autoposting = json_encode($centralized);
		}

		if (!$blog->store())
		{
			$post['write_content']  = JRequest::getVar('write_content_hidden', '', 'post', 'string', JREQUEST_ALLOWRAW );

			// Restore the contents
			EasyBlogHelper::storeSession( $post , 'tmpBlogData');
			EasyBlogHelper::setMessageQueue( $blog->getError() , 'error');

			$mainframe->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=write' . $returnRaw , false) );
		}

		// @task: Update the contribution linkage between the blog post and the respective team
		$blog->updateBlogContribution( $blogContribution , $contributionSource );

		// @task: Notify the world that someone created a blog post.
		//if( $blog->published == POST_ID_PUBLISHED && $isNew && !$blog->private )


		// Do not send out emails when the post is scheduled to post in future
		if( $isNew && !$blog->private && $blog->published != POST_ID_SCHEDULED )
		{
			$blog->notify( $under_approval, $blog->published );
		}

		$message	= JText::_('COM_EASYBLOG_DASHBOARD_ENTRIES_SAVED');

		JTable::addIncludePath( EBLOG_TABLES );
		$author		= EasyBlogHelper::getTable( 'Profile', 'Table' );
		$author->load( $blog->created_by );

		if(($blog->published == POST_ID_PUBLISHED) && ($my->id == $blog->created_by ) && (! $blog->ispending) && $contributionSource != 'easyblog' )
		{
			// @since 3.5
			// Event integrations.
			switch( $contributionSource )
			{
				case 'jomsocial.event':
					EasyBlogHelper::getHelper( 'Event' )->addStream( $blog , $isNew , $blogContribution , $contributionSource );
					EasyBlogHelper::getHelper( 'Event' )->sendNotifications( $blog , $isNew , $blogContribution , $contributionSource , $author );
				break;
				default:
					EasyBlogHelper::getHelper( 'Groups' )->addStream( $blog , $isNew , $blogContribution , $contributionSource );
					EasyBlogHelper::getHelper( 'Groups' )->sendNotifications( $blog , $isNew , $blogContribution , $contributionSource , $author );
				break;
			}
		}

		// @task: Update or initialize meta table.
		$blog->createMeta( JRequest::getVar('keywords', '') , JRequest::getVar('description', '') );

		// @task: Store trackbacks into the trackback table for tracking purposes.
		$trackbacks	= JRequest::getVar( 'trackback' , '' , 'POST' );

		// Store trackbacks if necessary
		if( !empty( $acl->rules->add_trackback) && !empty( $trackbacks ) )
		{
			$blog->storeTrackbacks( $trackbacks );
		}

		// @task: Save any tags associated with the blog entry.
		$tags			= JRequest::getVar( 'tags' , '' , 'POST' );
		$date			= EasyBlogHelper::getDate();

		// @rule: Process the tags after the blog is stored.
		$blog->processTags( $tags , $isNew );

		// @rule: Autoposting to social network sites.
		if ($blog->published == POST_ID_PUBLISHED) {
			$blog->autopost($autopost, $centralized);
		}


		// @rule: Process trackbacks
		$blog->processTrackbacks();


		$message		= JText::_('COM_EASYBLOG_DASHBOARD_SAVE_SUCCESS');


		if( !empty( $return ) )
		{
			$this->setRedirect( JRoute::_( $return , false ) , $message );
			return;
		}

		$isApply		= JRequest::getBool( 'apply' );

		if( $isApply )
		{
			$message	= JText::_('COM_EASYBLOG_DASHBOARD_APPLY_SUCCESS');
			EasyBlogHelper::setMessageQueue( $message , 'success');
			$this->setRedirect( EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=write&blogid=' . $blog->id, false) );
			return;
		}

		EasyBlogHelper::setMessageQueue( $message , 'success');
		$this->setRedirect( EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=entries', false) );
	}

	function saveAdsenseUserParams()
	{
		$acl		= EasyBlogACLHelper::getRuleSet();
		$config     = EasyBlogHelper::getConfig();

		$this->checkLogin();

		if( $config->get( 'integration_google_adsense_enable' ) )
		{
			if(! empty($acl->rules->add_adsense))
			{
				$mainframe	= JFactory::getApplication();
				$my			= JFactory::getUser();
				$post		= JRequest::get( 'post' );

				array_walk($post, array($this, '_trim') );

				$adsense = EasyBlogHelper::getTable( 'Adsense', 'Table' );
				$adsense->load($my->id);

				// Prevent Joomla from acting funny as on some site's it automatically adds the quote character at the end.
				$adsense->code 		= rtrim( $post['adsense_code'] , '"' );
				$adsense->display 	= $post['adsense_display'];
				$adsense->published = $post['adsense_published'];

				if(!$adsense->store())
				{
					EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_ADSENSE_FAILED_TO_UPDATE_INFO') , 'error');
				}
			}
		}
	}

	function _trim(&$text)
	{
		$text = JString::trim($text);
	}


	/**
	 * validate the required data from form submit
	 * param - array - forms
	 *       - array - required keys + message
	 * return boolean
	 */
	function _validateAppsFields($post, $requiredKeys)
	{
		$mainframe	= JFactory::getApplication();
		$valid		= true;

		if(is_array($requiredKeys))
		{
			foreach($requiredKeys as $key => $val)
			{
				if(JString::strlen($post[$key]) == 0)
				{
					EasyBlogHelper::setMessageQueue( JText::_($val)  , 'warning');
					$valid	= false;
					break;
				}
			}
		}
		else
		{
			$valid	= false;
			EasyBlogHelper::setMessageQueue( JText::_('MISSING REQUIRED KEY FOR VALIDATION') , 'error');
		}
		return $valid;
	}

	function _upload( $profile, $type = 'profile' )
	{
		$newAvatar	= $type == 'category' ? EasyBlogHelper::uploadCategoryAvatar( $profile ) : EasyBlogHelper::uploadAvatar( $profile );

		return $newAvatar;
	}

	function saveProfile()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mainframe	= JFactory::getApplication();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$post		= JRequest::get( 'post' );
		$config		= EasyBlogHelper::getConfig();
		$my 		= JFactory::getUser();
		$this->checkLogin();

		if( EasyBlogHelper::isSiteAdmin() || $config->get( 'layout_dashboard_biography_editor' ) )
		{
			$post['description']	= JRequest::getVar( 'description' , '' , 'POST' , '' , JREQUEST_ALLOWRAW );
			$post['biography']		= JRequest::getVar( 'biography' , '' , 'POST' , '' , JREQUEST_ALLOWRAW );

			// Filter / strip contents that are not allowed
			$filterTags 		= EasyBlogHelper::getHelper( 'Acl' )->getFilterTags();
			$filterAttributes	= EasyBlogHelper::getHelper( 'Acl' )->getFilterAttributes();

			// @rule: Apply filtering on contents
			jimport('joomla.filter.filterinput');
			$inputFilter 					= JFilterInput::getInstance( $filterTags , $filterAttributes , 1 , 1 , 0 );
			$inputFilter->tagBlacklist		= $filterTags;
			$inputFilter->attrBlacklist		= $filterAttributes;

			if( ( count($filterTags) > 0 && !empty($filterTags[0]) ) || ( count($filterAttributes) > 0 && !empty($filterAttributes[0]) ) )
			{
				$post['description']	= $inputFilter->clean( $post['description'] );
				$post['biography']		= $inputFilter->clean( $post['biography'] );
			}
		}

		array_walk($post, array($this, '_trim') );

		if( $config->get( 'main_dashboard_editaccount' ) )
		{
			if(! $this->_validateProfileFields($post))
			{
				$this->setRedirect( EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=profile' , false ) );
				return;
			}
			$my->name	= $post['fullname'];
			$my->save();
		}

		if( $config->get( 'main_joomlauserparams' ) )
		{
			$email		= $post[ 'email' ];
			$password	= $post[ 'password' ];
			$password2	= $post[ 'password2' ];

			if( JString::strlen( $password ) || JString::strlen( $password2 ) )
			{
				if( $password != $password2 )
				{
					EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_PASSWORD_ERROR')  , 'error' );
					$this->setRedirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=profile' , false ) );

					return false;
				}
			}

			// Store basic Joomla information
			$user		= JFactory::getUser();
			$data		= array( 'email' => $email , 'password' => $password , 'password2' => $password2 );
			$user->bind( $data );

			if (!$user->save())
			{
				EasyBlogHelper::setMessageQueue( $user->getError() , 'error' );
				$this->setRedirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=profile' , false ) );
				return false;
			}

			$session = JFactory::getSession();
			$session->set('user', $user);

			$table = JTable::getInstance( 'Session' );
			$table->load($session->getId());
			$table->username = $user->get('username');
			$table->store();
		}

		$post['permalink']  = $post['user_permalink'];
		unset( $post['user_permalink'] );

		// Check if permalink exists.
		$model 	= EasyBlogHelper::getModel( 'Users' );

		if( $model->permalinkExists( $post[ 'permalink' ] , $my->id ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_DASHBOARD_ACCOUNT_PERMALINK_EXISTS') , 'error' );
			$this->setRedirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=profile' , false ) );
			return false;
		}

		$profile	= EasyBlogHelper::getTable( 'Profile', 'Table' );
		$profile->load( $my->id );
		$profile->bind( $post );

		if( $config->get('main_feedburner') && $config->get('main_feedburnerblogger') && !empty($acl->rules->allow_feedburner))
		{
			$feedburner	= EasyBlogHelper::getTable( 'Feedburner' , 'Table' );
			$feedburner->load( $my->id );
			$feedburner->url	= $post['feedburner_url'];

			$feedburner->store();
		}

		JTable::addIncludePath( EBLOG_TABLES );
		if(! empty($acl->rules->update_twitter))
		{
			$mainframe	= JFactory::getApplication();

			$twitter	= EasyBlogHelper::getTable( 'Oauth' , 'Table' );
			$twitter->loadByUser( $my->id , EBLOG_OAUTH_TWITTER );

			$twitter->auto		= JRequest::getVar( 'integrations_twitter_auto' );
			$twitter->message	= JRequest::getVar( 'integrations_twitter_message' );

			if( !$twitter->store() )
			{
				EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_INTEGRATIONS_TWITTER_ERROR') , 'error');
			}
		}

		// Map linkedin items
		if(!empty($acl->rules->update_linkedin))
		{
			$mainframe	= JFactory::getApplication();

			$linkedin	= EasyBlogHelper::getTable( 'Oauth' , 'Table' );
			$linkedin->loadByUser( $my->id , EBLOG_OAUTH_LINKEDIN );

			$linkedin->auto		= JRequest::getVar( 'integrations_linkedin_auto' );
			$linkedin->message	= JRequest::getVar( 'integrations_linkedin_message' );
			$linkedin->private	= JRequest::getVar( 'integrations_linkedin_private' );

			if( !$linkedin->store() )
			{
				EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_INTEGRATIONS_LINKEDIN_ERROR') , 'error');
			}
		}

		if(!empty($acl->rules->update_facebook))
		{
			$mainframe	= JFactory::getApplication();

			$facebook	= EasyBlogHelper::getTable( 'Oauth' , 'Table' );
			$facebook->loadByUser( $my->id , EBLOG_OAUTH_FACEBOOK );

			$facebook->auto		= JRequest::getVar( 'integrations_facebook_auto' );
			$facebook->message	= '';

			if( !$facebook->store() )
			{
				EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_INTEGRATIONS_FACEBOOK_FAILED_UPDATE_INFO_ERROR') , 'error');
			}
		}

		$this->saveAdsenseUserParams();

		//save avatar
		if(! empty($acl->rules->upload_avatar))
		{
			$file 				= JRequest::getVar( 'Filedata', '', 'files', 'array' );
			if(! empty($file['name']))
			{
				$newAvatar			= $this->_upload( $profile );
				$profile->avatar    = $newAvatar;

				// AlphaUserPoints
				// since 1.2
				if ( EasyBlogHelper::isAUPEnabled() )
				{
					AlphaUserPointsHelper::newpoints( 'plgaup_easyblog_upload_avatar', '', 'easyblog_upload_avatar_' . $my->id, JText::_('COM_EASYBLOG_AUP_UPLOADED_AVATAR') );
				}
			}
		}


		//save meta
		if(! empty($acl->rules->add_entry))
		{
			//meta post info
			$metaId     = JRequest::getInt('metaid', 0);
			$metapost	= array();

			$metapost['keywords']		= JRequest::getVar('metakeywords', '');
			$metapost['description']	= JRequest::getVar('metadescription', '');
			$metapost['content_id']		= $my->id;
			$metapost['type']			= META_TYPE_BLOGGER;

			$meta		= EasyBlogHelper::getTable( 'Meta', 'Table' );
			$meta->load($metaId);
			$meta->bind($metapost);
			$meta->store();
		}

		//save params
		$userparams	= EasyBlogHelper::getRegistry('');
		$userparams->set( 'theme', $post['theme'] );

		// @rule: Save google profile url
		if( isset( $post[ 'google_profile_url' ] ) )
		{
			$userparams->set( 'google_profile_url' , $post[ 'google_profile_url'] );
		}

		if( isset( $post[ 'show_google_profile_url' ] ) )
		{
			$userparams->set( 'show_google_profile_url' , $post['show_google_profile_url'] );
		}

		$profile->params = $userparams->toString();


		if( $config->get('main_dashboard_editaccount') && $config->get( 'main_joomlauserparams') )
		{
			$my->save( true );
		}

		if( $profile->store() )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_DASHBOARD_PROFILE_UPDATE_SUCCESS')  , 'info');
		}
		else
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_DASHBOARD_PROFILE_UPDATE_FAILED')  , 'error');
		}

		$this->setRedirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=profile' , false ) );
	}

	function _validateProfileFields($post)
	{
		$mainframe	= JFactory::getApplication();
		$valid		= true;

		$message    = '<ul>';

		if(JString::strlen($post['fullname']) == 0)
		{
			$message    .= '<li>' . JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_REALNAME_EMPTY') . '</li>';
			$valid	= false;
		}

		if(JString::strlen($post['nickname']) == 0)
		{
			$message    .= '<li>' . JText::_('COM_EASYBLOG_DASHBOARD_ACCOUNT_NICKNAME_EMPTY') . '</li>';
			$valid	= false;
		}

		$message    .= '<ul>';

		EasyBlogHelper::setMessageQueue( $message , 'warning');

		return $valid;
	}

	public function checkLogin()
	{
		$mainframe	= JFactory::getApplication();

		if(! EasyBlogHelper::isLoggedIn())
		{
			$uri		= JFactory::getURI();
			$return		= $uri->toString();

			$userComponent  = ( EasyBlogHelper::getJoomlaVersion() >= '1.6' ) ? 'com_users' : 'com_user';

			$url  = 'index.php?option='.$userComponent.'&view=login';
			$url .= '&return='.base64_encode($return);
			$mainframe->redirect( EasyBlogRouter::_( $url , false ) , JText::_('COM_EASYBLOG_YOU_MUST_LOGIN_FIRST') );
		}
	}

	/*
	 * Adds new category
	 * @param	null
	 * @return	null
	 */
	function addCategory()
	{
		$mainframe	= JFactory::getApplication();
		$my         = JFactory::getUser();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$config     = EasyBlogHelper::getConfig();

		$this->checkLogin();

		if(empty($acl->rules->create_category))
		{
			$mainframe->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=categories' , false), JText::_('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_CATEGORY') );
			$mainframe->close();
		}

		$catName = JRequest::getVar( 'title', '' );

		if(empty($catName))
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_EMPTY_CATEGORY_TITLE_ERROR') , 'error');
			$mainframe->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=categories' , false) );
			$mainframe->close();
		}

		//check whether the category already created.
		$model  = $this->getModel('Category');

		if($model->isExist($catName))
		{
			EasyBlogHelper::setMessageQueue(JText::sprintf('COM_EASYBLOG_DASHBOARD_CATEGORIES_ALREADY_EXISTS_ERROR', $catName), 'error');
			$mainframe->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=categories' , false) );
			$mainframe->close();
		}

		$post				= JRequest::get( 'post' );
		$post['title'] 		= $catName;
		$post['created_by'] = $my->id;
		$post['parent_id'] 	= JRequest::getInt( 'parent_id', '0' );
		$post['private'] 	= JRequest::getInt( 'private', '0' );
		$post['description']	= JRequest::getVar( 'description' , '' , 'REQUEST' , 'none' , JREQUEST_ALLOWHTML );

		$category	= EasyBlogHelper::getTable( 'Category', 'Table' );
		$category->bind($post);
		$category->published    = 1;

		//save the cat 1st so that the id get updated
		$category->store();

		$category->deleteACL();
		if($category->private == CATEGORY_PRIVACY_ACL)
		{
			$category->saveACL( $post );
		}

		$file = JRequest::getVar( 'Filedata', '', 'files', 'array' );

		if(! empty($file['name']))
		{
			$newAvatar			= $this->_upload( $category, 'category' );
			$category->avatar   = $newAvatar;

			//now update the avatar.
			$category->store();
		}

		EasyBlogHelper::setMessageQueue( JText::sprintf('COM_EASYBLOG_DASHBOARD_CATEGORIES_ADDED_SUCCESSFULLY' , $catName ) , 'success' );
		$mainframe->redirect( EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=categories', false) );
		$mainframe->close();
	}

	/*
	 * Responsible to store an edited category.
	 * @param	null
	 * @return	null
	 */
	function saveCategory()
	{
		$id			= JRequest::getVar( 'id' , '' );
		$acl		= EasyBlogACLHelper::getRuleSet();
		$my			= JFactory::getUser();
		$redirect	= EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=categories' , false );
		$mainframe	= JFactory::getApplication();

		// @rule: Sanity checks
		if( empty( $id ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_DASHBOARD_CATEGORIES_ID_IS_EMPTY_ERROR' ) , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		// @rule: Check if the user is really allowed to create category.
		if( !$acl->rules->create_category)
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		// @rule: Check if the user is really allowed to edit this category
		$category	= EasyBlogHelper::getTable( 'Category' , 'Table' );
		$category->load( $id );

		if( $category->id && $category->created_by != $my->id && !EasyBlogHelper::isSiteAdmin() )
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		$post		= JRequest::get( 'POST' );

		$post['description']	= JRequest::getVar( 'description' , '' , 'REQUEST' , 'none' , JREQUEST_ALLOWHTML );

		$category->bind( $post );

		$model		= $this->getModel('Category');

		if( $model->isExist( $category->title , $category->id ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_DASHBOARD_CATEGORIES_ALREADY_EXISTS_ERROR' ) , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		$avatar	= JRequest::getVar( 'Filedata', '', 'files', 'array' );

		if( isset( $avatar[ 'name' ] ) && !empty( $avatar['name'] ) )
		{
			$category->avatar   = EasyBlogHelper::uploadCategoryAvatar( $category );
		}
		$category->store();

		//save acl
		$category->deleteACL();
		if($category->private == CATEGORY_PRIVACY_ACL)
		{
			$category->saveACL( $post );
		}

		EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_DASHBOARD_CATEGORIES_UPDATED_SUCCESSFULLY' ) , 'success' );
		$mainframe->redirect( $redirect );
		$mainframe->close();
	}

	function addTag()
	{
		$mainframe	= JFactory::getApplication();
		$my         = JFactory::getUser();
		$config     = EasyBlogHelper::getConfig();

		$acl		= EasyBlogACLHelper::getRuleSet();

		$this->checkLogin();

		if( empty( $acl->rules->create_tag ) )
		{
			$url  = 'index.php?option=com_easyblog&view=dashboard&layout=tags';
			$mainframe->redirect(EasyBlogRouter::_($url, false), JText::_('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_TAG') );
		}

		$tagName		= JRequest::getVar( 'tag', '' );

		if(empty($tagName))
		{
			EasyBlogHelper::setMessageQueue(JText::_('COM_EASYBLOG_DASHBOARD_TAG_INVALID'), 'error');
			$url  = 'index.php?option=com_easyblog&view=dashboard&layout=tags';
			$mainframe->redirect(EasyBlogRouter::_($url, false));
			return;
		}

		$arrTag = explode( ',', $tagName );

		$count  	= 0;
		$tagExists  = array();
		$tagAdded   = array();
		foreach( $arrTag as $item )
		{
			if( empty( $item ) )
			{
				continue;
			}

			$table	= EasyBlogHelper::getTable( 'Tag' , 'Table' );
			//@task: Only add tags if it doesn't exist.
			if( !$table->exists( $item ) )
			{
				$post['title'] 		= JString::trim( $item );
				$post['created_by'] = $my->id;

				$table->bind($post);
				$table->published	= 1;

				$table->store();

				$tagAdded[] = $item;
				$count++;
			}
			else
			{
				$tagExists[]    = $item;
			}
		}

		if( $count > 0 )
		{
			$tagName    = implode( ', ', $tagAdded);

			EasyBlogHelper::setMessageQueue(JText::sprintf('COM_EASYBLOG_DASHBOARD_TAG_NAME_ADDED', $tagName), 'info');
			$mainframe->redirect(EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=tags', false));

		}
		else if( count( $tagExists ) > 0 )
		{
			$tagName    = implode( ', ', $tagExists);
			EasyBlogHelper::setMessageQueue(JText::sprintf('COM_EASYBLOG_DASHBOARD_TAG_NAME_ALREADY_EXISTS', $tagName), 'error');
			$mainframe->redirect(EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=tags', false));
			return;
		}
		else
		{
			EasyBlogHelper::setMessageQueue(JText::_('COM_EASYBLOG_DASHBOARD_TAG_INVALID'), 'error');
			$mainframe->redirect(EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=tags', false));
			return;
		}
	}

	/*
	 * List down bloggers from the site for the admin to change authors.
	 *
	 * @param	null
	 * @return	null
	 */
	function listBloggers()
	{
		// Anyone with moderate_entry acl is also allowed to change author.
		$acl		= EasyBlogACLHelper::getRuleSet();
		if( !EasyBlogHelper::isSiteAdmin() && !$acl->rules->moderate_entry )
		{
			echo JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
			return;
		}

		// $model 		= $this->getModel( 'Users' );
		$model      = EasyBlogHelper::getModel( 'Users' );
		$rows		= $model->getUsers( true );
		$users		= array();

		JTable::addIncludePath( EBLOG_TABLES );
		foreach( $rows as $row )
		{
			$user		= EasyBlogHelper::getTable( 'Profile' , 'Table' );
			$user->load( $row->id );
			$users[]	= $user;
		}

		$pagination = $model->getPagination( true );

		$orderDir	= JRequest::getVar('filter_order_Dir', '', 'REQUEST');
		switch($orderDir)
		{
			case 'asc':
				$orderDir = 'desc';
				break;
			case 'desc':
			default:
				$orderDir = 'asc';
		}

		$order			= JRequest::getVar('filter_order', 'name', 'REQUEST');
		$search			= JRequest::getVar('search', '', 'REQUEST');
		$filter_state	= JRequest::getVar('filter_state', 'P', 'REQUEST');

		$tpl = new CodeThemes('dashboard');
		$tpl->set( 'users' 			, $users );
		$tpl->set( 'pagination'		, $pagination );
		$tpl->set( 'orderDir'		, $orderDir );
		$tpl->set( 'order'			, $order );
		$tpl->set( 'search'			, $search );
		$tpl->set( 'filter_state'	, $filter_state );

		echo $tpl->fetch( 'dashboard.users.php' );
		return;
	}

	public function teamApproval()
	{
		$mainframe	= JFactory::getApplication();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$config	= EasyBlogHelper::getConfig();
		$document	= JFactory::getDocument();
		$my			= JFactory::getUser();

		if(! EasyBlogHelper::isLoggedIn())
		{
			$uri		= JFactory::getURI();
			$return		= $uri->toString();

			$userComponent  = ( EasyBlogHelper::getJoomlaVersion() >= '1.6' ) ? 'com_users' : 'com_user';

			$url  = 'index.php?option='.$userComponent.'&view=login';
			$url .= '&return='.base64_encode($return);
			$mainframe->redirect(EasyBlogRouter::_($url, false), JText::_('COM_EASYBLOG_YOU_MUST_LOGIN_FIRST') );
		}

		$teamId 	= JRequest::getInt('team', 0);
		$approval	= JRequest::getInt('approve');
		$requestId	= JRequest::getInt('id', 0);

		//check if the current user have the right to approve this team request or not.
		$teamModel 		= $this->getModel( 'TeamBlogs' );

		if(! EasyBlogHelper::isSiteAdmin())
		{
			if(! $teamModel->checkIsTeamAdmin($my->id, $teamId))
			{
				EasyBlogHelper::showAccessDenied();
				return;
			}
		}


		$request 		= EasyBlogHelper::getTable( 'TeamBlogRequest' );
		$request->load( $requestId );

		if($approval)
		{
			$teamUsers    = EasyBlogHelper::getTable( 'TeamBlogUsers','Table' );

			$teamUsers->user_id    = $request->user_id;
			$teamUsers->team_id    = $request->team_id;

			if(!$teamUsers->addMember())
			{
				EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_TEAMBLOG_APPROVAL_FAILED')  , 'error');
				$this->setRedirect( EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogs', false) );
			}

			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_TEAMBLOG_APPROVAL_APPROVED')  , 'info');
		}
		else
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_TEAMBLOG_APPROVAL_REJECTED')  , 'info');
		}

		$request->ispending 	= 0;
		$request->store();

		$team 	= EasyBlogHelper::getTable( 'TeamBlog' );
		$team->load( $request->team_id );

		// @rule: Send notifications to the user that he's been approved.
		$request->sendApprovalEmail( $approval );

		$this->setRedirect( EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard&layout=teamblogs', false) );
	}

	/*
	 * @since 2.0
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
		$redirect	= EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=comments' , false );


		$id			= JRequest::getInt( 'id' );
		$post		= JRequest::get( 'POST' );
		array_walk( $post, array( $this, '_trim') );

		//add here so that other component with the same comment.php jtable file will not get reference.
		JTable::addIncludePath( EBLOG_TABLES );
		$comment = EasyBlogHelper::getTable( 'Comment', 'Table' );
		$comment->bindPost( $post );

		$blog		= EasyBlogHelper::getTable( 'Blog' , 'Table' );
		$blog->load( $comment->post_id );

		// @rule: Test if the current browser is allowed to do this or not.
		if( $blog->created_by != $my->id && !EasyBloghelper::isSiteAdmin() && empty($acl->rules->edit_comment) )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_UPDATE_COMMENT') , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		if( !$comment->validate( 'title' ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_COMMENT_TITLE_IS_EMPTY') , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		if( !$comment->validate( 'name' ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_COMMENT_NAME_IS_EMPTY') , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		if( !$comment->validate( 'email' ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_COMMENT_EMAIL_IS_EMPTY') , 'error' );
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

		EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_DASHBOARD_BOMMENTS_COMMENT_UPDATED_SUCCESS') , 'success' );
		$mainframe->redirect( $redirect );
		$mainframe->close();
	}

	/*
	 * @since 2.0
	 * Responsible to delete drafts
	 *
	 * @param	null
	 * @return	null
	 */
	public function deleteAllDrafts()
	{
		$mainframe	= JFactory::getApplication();
		$my			= JFactory::getUser();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$redirect	= EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=drafts' , false );

		if( ( $my->id == 0 ) && !EasyBlogHelper::isSiteAdmin() )
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NO_PERMISSION_TO_DELETE_DRAFTS' ) , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		$model		= $this->getModel( 'Drafts' );

		if( !$model->discard( $my->id ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_DASHBOARD_DRAFTS_DISCARDED_ERROR' ) , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_DASHBOARD_DRAFTS_DISCARDED_SUCCESSFULLY' ) , 'info');
		$mainframe->redirect( $redirect );
		$mainframe->close();
	}

	/*
	 * @since 2.0
	 * Responsible to delete drafts
	 *
	 * @param	null
	 * @return	null
	 */
	public function deleteDrafts()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$mainframe	= JFactory::getApplication();
		$ids		= JRequest::getVar( 'id' );
		$ids		= explode( ',' , $ids );
		$my			= JFactory::getUser();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$redirect	= EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=drafts' , false );

		if( empty( $ids ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_DASHBOARD_DRAFTS_NO_ID_ERROR' ) , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		if( $my->id == 0 )
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NO_PERMISSION_TO_DELETE_DRAFTS' ) , 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		JTable::addIncludePath( EBLOG_TABLES );

		foreach( $ids as $id )
		{
			$draft	= EasyBlogHelper::getTable( 'Draft' , 'Table' );
			$draft->load( $id );

			if( $draft->created_by != $my->id )
			{
				EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NO_PERMISSION_TO_DELETE_DRAFTS' ) , 'error' );
				$mainframe->redirect( $redirect );
				$mainframe->close();
			}
			$draft->delete();
		}

		EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_DASHBOARD_DRAFTS_DISCARDED_SUCCESSFULLY' ) , 'info');
		$mainframe->redirect( $redirect );
		$mainframe->close();
	}

	/**
	 * Allow current user to remove their own profile picture.
	 *
	 */
	public function removePicture()
	{
		$mainframe	= JFactory::getApplication();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$my			= JFactory::getUser();
		$config		= EasyBlogHelper::getConfig();

		if( !$config->get( 'layout_avatar' ) || !$acl->rules->upload_avatar )
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NO_PERMISSION_TO_DELETE_PROFILE_PICTURE' ) , 'error' );
			$mainframe->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=profile' , false ) );
			$mainframe->close();
		}

		JTable::addIncludePath( EBLOG_TABLES );
		$profile	= EasyBlogHelper::getTable( 'Profile' , 'Table' );
		$profile->load( $my->id );

		$avatar_config_path = $config->get('main_avatarpath');
		$avatar_config_path = rtrim($avatar_config_path, '/');
		$avatar_config_path = str_replace('/', DIRECTORY_SEPARATOR, $avatar_config_path);
		$path				= JPATH_ROOT . DIRECTORY_SEPARATOR . $avatar_config_path . DIRECTORY_SEPARATOR . $profile->avatar;

		if( !JFile::delete( $path ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NO_PERMISSION_TO_DELETE_PROFILE_PICTURE' ) , 'error' );
			$mainframe->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=profile' , false ) );
			$mainframe->close();
		}

		// @rule: Update avatar in database
		$profile->avatar	= '';
		$profile->store();

		EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_PROFILE_PICTURE_REMOVED' ) );
		$mainframe->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard&layout=profile' , false ) );
		$mainframe->close();
	}
}
