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

class EasyBlogControllerPending extends EasyBlogController
{	
	function __construct()
	{
		parent::__construct();
	}

	public function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		$ids 	= JRequest::getVar( 'cid' );

		foreach( $ids as $id )
		{
			$pending 	= EasyBlogHelper::getTable( 'Draft' );

			$pending->load( (int) $id );

			$pending->delete();
		}

		$message 	= JText::_( 'COM_EASYBLOG_PENDING_POSTS_DELETED_SUCCESSFULLY' );
		
		$this->setRedirect( 'index.php?option=com_easyblog&view=pending' , $message , 'success' );
	}

	public function approveItem( $id )
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'pending' );

		$app 		= JFactory::getApplication();
		$config 	= EasyBlogHelper::getConfig();
		$message	= '';
		$type		= 'message';

		$my				= JFactory::getUser();
		$acl			= EasyBlogACLHelper::getRuleSet();
		$joomlaVersion	= EasyBlogHelper::getJoomlaVersion();
		$redirect       = 'index.php?option=com_easyblog&view=pending';

		if( empty( $id ) )
		{
			$app->enqueueMessage( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ), 'error' );
			$app->redirect( $redirect );
			$app->close();
		}

		if( empty( $acl->rules->add_entry ) && empty( $acl->rules->manage_pending ) )
		{
			$app->enqueueMessage( JText::_( 'COM_EASYBLOG_BLOGS_BLOG_NO_PERMISSION_TO_CREATE_BLOG' ), 'error' );
			$app->redirect( $redirect );
			$app->close();
			return;
		}

		if( $my->id == 0 )
		{
			$app->enqueueMessage( JText::_( 'COM_EASYBLOG_YOU_ARE_NOT_LOGIN' ), 'error' );
			$app->redirect( $redirect );
			$app->close();
		}

		$draft		= EasyBlogHelper::getTable( 'Draft' );
		$draft->load( $id );

		$blog		= EasyBlogHelper::getTable( 'blog' );

		// @task: Try to determine the newness of this draft because this could be an edit.
		$blog->load( $draft->entry_id );
		$isNew 		= $blog->id ? false : true;

		// @task: If the blog post is not new, try to see if the isnew column is really new.
		if(! $isNew)
		{
			$isNew  = $blog->isnew;
		}

		// @rule: Do not copy the hit count over as the draft copy may not contain the correct values.
		unset($draft->hits);

		// @task: Retrieve author's acl
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

		$blog->isnew 		= $isNew;

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

		$blog->under_approval   = true;
		if( !$blog->store() )
		{
			$app->redirect( 'index.php?option=com_easyblog&view=pending' , $blog->getError() , 'error' );
			$app->close();
		}

		// this variable will be use when sending notification. This is bcos
		// the currently login use might be the admin and editing other blogger post.
		$authorId   = $blog->created_by;

		//now we update the blog contribution
		$blog->updateBlogContribution($blogContribution);

		$blogId		= $blog->id;

		//meta post info
		$metaId		= $blog->getMetaId();

		$metapost	= array();
		$metapost['keywords']		= $draft->metakey;
		$metapost['description']	= $draft->metadesc;
		$metapost['content_id']		= $blog->id;
		$metapost['type']			= META_TYPE_POST;

		// @rule: Save meta tags for this entry.
		$meta		= EasyBlogHelper::getTable( 'Meta', 'Table' );
		$meta->load( $metaId );
		$meta->bind( $metapost );
		$meta->store();

		$author		= EasyBlogHelper::getTable( 'Profile', 'Table' );
		$author->load( $blog->created_by );

		// @task: Store trackbacks into the trackback table for tracking purposes.
		$trackbacks	= $draft->trackbacks;

		// Store trackbacks if necessary
		if( !empty( $acl->rules->add_trackback) && !empty( $trackbacks ) )
		{
			$blog->storeTrackbacks( $trackbacks );
			$blog->processTrackbacks();
		}

		// @rule: Process the tags after the blog is stored.
		$tags           = explode(',', $draft->tags);
		$blog->processTags( $tags );

		// @rule: Autoposting to social network sites.
		if( $blog->published == POST_ID_PUBLISHED )
		{
			if( !empty($draft->autopost) || !empty( $draft->autopost_centralized ) )
			{
				$autopost		= empty( $draft->autopost ) ? array() : explode( ',' , $draft->autopost );
				$centralized	= empty( $draft->autopost_centralized ) ? array() : explode( ',' , $draft->autopost_centralized );

				$blog->autopost( $autopost , $centralized );
			}
		}

		// @task: Notify the world that someone created a blog post.
		$blog->notify( true, $blog->published );
		
		// @task: Cleanup any messages
		$postreject	= EasyBlogHelper::getTable( 'PostReject' , 'Table' );
		$postreject->clear( $draft->id );

		// now blog updated, we need to remove the draft
		$draft->delete();
	}

	function rejectItem( $id )
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'blog' );

		$mainframe  = JFactory::getApplication();
		$redirect	= base64_decode( JRequest::getVar( 'redirect' , '' ) );
		$redirect	= empty( $redirect ) ? EasyBlogRouter::_( 'index.php?option=com_easyblog&view=pending' , false ) : EasyBlogRouter::_( $redirect , false );
		$my			= JFactory::getUser();
		$config     = EasyBlogHelper::getConfig();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$message	= JRequest::getVar( 'message' , '' );

		if( empty( $id ) )
		{
			$mainframe->enqueueMessage( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ), 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		if( !EasyBlogHelper::isSiteAdmin() && empty( $acl->rules->manage_pending ) )
		{
			$mainframe->enqueueMessage( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ), 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		JTable::addIncludePath( EBLOG_TABLES );

		$draft		= EasyBlogHelper::getTable( 'Draft', 'Table' );
		$draft->load( $id );

		if( $draft->pending_approval != 1 )
		{
			$mainframe->enqueueMessage( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ), 'error' );
			$mainframe->redirect( $redirect );
			$mainframe->close();
		}

		// If the draft is rejected, revert draft status and create a note for the rejected draft.
		$draft->set( 'pending_approval' , 0 );
		$draft->store();

		// Create the message
		$postreject				= EasyBlogHelper::getTable( 'PostReject' , 'Table' );
		$postreject->draft_id	= $draft->id;
		$postreject->message	= $message;
		$postreject->created_by	= $my->id;
		$postreject->created	= EasyBlogHelper::getDate()->toMySQL();
		$postreject->store();
	}

	function reject()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );
	
		// @task: Check for acl rules.
		$this->checkAccess( 'pending' );

		$ids		= JRequest::getVar( 'draft_id' );

		foreach( $ids as $id )
		{
			$this->rejectItem( (int) $id );
		}

		$message    = JText::_('COM_EASYBLOG_BLOGS_BLOG_SAVE_REJECTED');

		$this->setRedirect( 'index.php?option=com_easyblog&view=pending' , $message );
	}

	function approve()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'pending' );

		$ids		= JRequest::getVar( 'cid' );

		foreach( $ids as $id )
		{
			$this->approveItem( (int) $id );
		}

		$message	= JText::_('COM_EASYBLOG_BLOGS_BLOG_SAVE_APPROVED');

		$this->setRedirect( 'index.php?option=com_easyblog&view=pending' , $message , 'success' );
	}

}