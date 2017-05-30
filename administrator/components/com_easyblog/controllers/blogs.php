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

class EasyBlogControllerBlogs extends EasyBlogController
{
	function __construct()
	{
		parent::__construct();

		$this->registerTask( 'add' , 'edit' );
		$this->registerTask( 'unfeature' , 'toggleFeatured' );
		$this->registerTask( 'feature' , 'toggleFeatured' );
		$this->registerTask( 'saveApply' , 'savePublish' );

		// Need to explicitly define this in Joomla 3.0
		$this->registerTask( 'unpublish' , 'unpublish' );

		$this->registerTask( 'restore' , 'publish' );

		// Need to explicitly define trash
		$this->registerTask( 'trash' , 'trash' );
	}


	public function autopost()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'blog' );

		$type 		= JRequest::getWord( 'autopost_type' );
		$config		= EasyBlogHelper::getConfig();
		$my			= JFactory::getUser();
		$app 		= JFactory::getApplication();

		$oauth		= EasyBlogHelper::getTable( 'OAuth' );
		$oauth->loadSystemByType( $type );

		if( !$oauth->id )
		{
			$app->redirect( 'index.php?option=com_easyblog&view=blogs' , JText::_( 'COM_EASYBLOG_AUTOPOST_UNABLE_TO_LOAD_TYPE' ) , 'error');
			$app->close();
		}

		if( !$config->get( 'integrations_' . $oauth->type ) )
		{
			$app->redirect( 'index.php?option=com_easyblog&view=blogs' , JText::sprintf( 'COM_EASYBLOG_AUTOPOST_SITE_IS_NOT_ENABLED' , ucfirst( $type ) ) , 'error');
			$app->close();
		}

		$id		= JRequest::getInt( 'autopost_selected' );
		$blog 	= EasyBlogHelper::getTable( 'Blog' );
		$blog->load( $id );

		// @task: Test if the blog post is unpublished.
		if( $blog->published != POST_ID_PUBLISHED )
		{
			$app->redirect( 'index.php?option=com_easyblog&view=blogs' , JText::_( 'COM_EASYBLOG_AUTOPOST_PLEASE_PUBLISH_BLOG') , 'error');
			$app->close();
		}

		// @task: Test if the api key and secret is valid.
		$key	= $config->get( 'integrations_' . $oauth->type . '_api_key' );
		$secret	= $config->get( 'integrations_' . $oauth->type . '_secret_key' );
		if( empty( $key ) || empty( $secret ) )
		{
			$app->redirect( 'index.php?option=com_easyblog&view=blogs' , JText::sprintf( 'COM_EASYBLOG_AUTOPOST_KEYS_INVALID' , ucfirst( $type ) ) , 'error' );
			$app->close();
		}

		if( !EasyBlogSocialShareHelper::share( $blog , $oauth->type , true ) )
		{
			$app->redirect( 'index.php?option=com_easyblog&view=blogs' , JText::sprintf( 'COM_EASYBLOG_AUTOPOST_SUBMIT_ERROR' , ucfirst( $oauth->type ) ) , 'error' );
			$app->close();
		}

		$app->redirect( 'index.php?option=com_easyblog&view=blogs' , JText::sprintf( 'COM_EASYBLOG_AUTOPOST_SUBMIT_SUCCESS' , ucfirst( $oauth->type ) ) , 'success' );
	}

	function toggleFrontpage()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'blog' );

		$mainframe	= JFactory::getApplication();
		$db     	= EasyBlogHelper::db();

		$records	= JRequest::getVar( 'cid' );
		$msg		= '';

		foreach( $records as $record )
		{
			$blog		= EasyBlogHelper::getTable( 'Blog' , 'Table' );
			$blog->load( $record );

			$state	= !$blog->frontpage;

			$sql    = 'update `#__easyblog_post` set `frontpage` = ' . $db->Quote($state);
			$sql    .= ' where id = ' . $db->Quote( $blog->id );
			$db->setQuery( $sql );
			$db->query();

			$msg				= $state ? JText::sprintf( 'COM_EASYBLOG_BLOGS_SET_AS_FRONTPAGE_SUCCESS' , $blog->title ) : JText::sprintf( 'COM_EASYBLOG_BLOGS_REMOVED_FROM_FRONTPAGE_SUCCESS' , $blog->title );
		}


		$mainframe->enqueueMessage( $msg , 'message' );
		$mainframe->redirect( 'index.php?option=com_easyblog&view=blogs' );
	}

	function toggleFeatured()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'blog' );

		$mainframe	= JFactory::getApplication();
		$records	= JRequest::getVar( 'cid' , '' );
		$message	= '';
		$task		= JRequest::getVar( 'task' );

		if( empty( $records ) )
		{
			$mainframe->enqueueMessage( JText::_( 'COM_EASYBLOG_INVALID_BLOG_ID' ) , 'error' );
			$mainframe->redirect( 'index.php?option=com_easyblog&view=blogs' );
			$mainframe->close();
		}

		foreach( $records as $record )
		{
			if( $task == 'unfeature' )
			{
				EasyBlogHelper::removeFeatured( EBLOG_FEATURED_BLOG, $record );
				$message	= JText::_( 'COM_EASYBLOG_BLOGS_UNFEATURED_SUCCESSFULLY' );
			}
			else
			{
				EasyBlogHelper::makeFeatured( EBLOG_FEATURED_BLOG, $record );
				$message	= JText::_( 'COM_EASYBLOG_BLOGS_FEATURED_SUCCESSFULLY' );
			}
		}
		$mainframe->enqueueMessage( $message , 'message' );
		$mainframe->redirect( 'index.php?option=com_easyblog&view=blogs' );
		$mainframe->close();
	}

	function toggleNotify()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'blog' );

		$mainframe	= JFactory::getApplication();

		$records	= JRequest::getVar( 'cid' );
		$msg		= '';

		foreach( $records as $record )
		{
			$blog		= EasyBlogHelper::getTable( 'Blog' , 'Table' );
			$blog->load( $record );

			$blog->notify();

			$msg				= JText::sprintf( 'COM_EASYBLOG_BLOGS_NOTIFY_SUBSCRIBERS' , $blog->title );
		}

		$mainframe->enqueueMessage( $msg , 'message' );
		$mainframe->redirect( 'index.php?option=com_easyblog&view=blogs' );
	}

	/**
	 * This method is invoked when a blog post is approved.
	 *
	 * @access	public
	 * @param	null
	 * @return	null
	 */
	function approveBlog()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'blog' );

		$app 		= JFactory::getApplication();
		$config 	= EasyBlogHelper::getConfig();
		$message	= '';
		$type		= 'message';

		$my				= JFactory::getUser();
		$acl			= EasyBlogACLHelper::getRuleSet();
		$joomlaVersion	= EasyBlogHelper::getJoomlaVersion();
		$redirect       = 'index.php?option=com_easyblog&view=pending';

		// @task: This is the primary key for this particular draft.
		$id				= JRequest::getInt( 'draft_id' );

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

		$message	= JText::_('COM_EASYBLOG_BLOGS_BLOG_SAVE_APPROVED');

		$app->enqueueMessage( JText::_( 'COM_EASYBLOG_BLOGS_BLOG_SAVE_APPROVED' ), 'info' );
		$app->redirect( $redirect );
		$app->close();
	}

	/**
	 * When an admin does not have publishing privileges, automatically submit this as a pending review post.
	 */
	function _saveDraft( )
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'blog' );

		$mainframe		= JFactory::getApplication();
		$config 		= EasyBlogHelper::getConfig();
		$message		= '';
		$type			= 'message';
		$my				= JFactory::getUser();
		$acl			= EasyBlogACLHelper::getRuleSet();
		$joomlaVersion	= EasyBlogHelper::getJoomlaVersion();


		$redirect   = 'index.php?option=com_easyblog&view=blogs';

		$params		= JRequest::get( 'post' );

		// Try to load this draft to see if it exists
		$draft	= EasyBlogHelper::getTable( 'Draft' , 'Table' );
		$draft->load( $params[ 'draft_id' ] );

		if( isset( $params[ 'blogid' ] ) && !empty( $params[ 'blogid' ] ) )
		{
			$draft->entry_id	= $params[ 'blogid' ];
			unset( $params[ 'blogid' ] );
		}

		$content		= JRequest::getVar('write_content', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$intro			= JRequest::getVar('intro', '', 'post', 'string', JREQUEST_ALLOWRAW );
		$params[ 'content' ]	= $content;
		$params[ 'intro' ] 		= $intro;

		$authorId				= JRequest::getVar( 'authorId' );
		$params[ 'created_by' ] = $authorId;

		$trackbacks	= JRequest::getVar( 'trackback' , '' , 'POST' );
		$params[ 'trackback' ]  = $trackbacks;
		$draft->bind( $params, true );

		if( isset( $params[ 'draft_id'] ) && !empty( $params[ 'draft_id' ] ) )
		{
			$draft->id	= $params[ 'draft_id' ];
		}

		$draft->pending_approval    = '1';

		// @task: Cleanup any messages
		$postreject	= EasyBlogHelper::getTable( 'PostReject' , 'Table' );
		$postreject->clear( $draft->id );

		if( $draft->store() )
		{
			$message    = JText::_('COM_EASYBLOG_BLOGS_BLOG_SAVE_BUT_PENDING_FOR_APPROVAL');
			$mainframe->enqueueMessage( $message, 'info' );

			// Redirect to new form again if necessary
			$saveNew	= JRequest::getInt( 'savenew' , 0 );

			if( $saveNew )
			{
				$mainframe->redirect( 'index.php?option=com_easyblog&view=blog' );
				return;
			}

			$mainframe->redirect( $redirect );

		}
		else
		{
			$mainframe->enqueueMessage( $draft->getError(), 'error' );
			$mainframe->redirect( $redirect );
		}
	}

	function savePublish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'blog' );

		$mainframe	= JFactory::getApplication();
		$config 	= EasyBlogHelper::getConfig();
		$message	= '';
		$type		= 'message';
		$saveNew	= JRequest::getInt( 'savenew' , 0 );

		$authorId	= JRequest::getVar( 'authorId' );
		$user		= JFactory::getUser($authorId);
		$joomlaVersion = EasyBlogHelper::getJoomlaVersion();

		if( JRequest::getMethod() == 'GET' )
		{
			echo JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
			return;
		}

		$acl		= EasyBlogACLHelper::getRuleSet();

		if(empty($acl->rules->add_entry))
		{
			$message = JText::_('COM_EASYBLOG_BLOGS_BLOG_NO_PERMISSION_TO_CREATE_BLOG');
			$mainframe->enqueueMessage( $message, 'error' );
			$this->setRedirect( 'index.php?option=com_easyblog&view=blogs' );
		}

		// get data from form post.
		$post		= JRequest::get( 'post' );


		// @rule: Check for invalid title
		$postTitle = trim( $post['title'] );
		if ( empty( $postTitle ) )
		{
			$post['write_content']  = JRequest::getVar('write_content_hidden', '', 'post', 'string', JREQUEST_ALLOWRAW );
			EasyBlogHelper::storeSession( $post , 'tmpBlogData');

			$mainframe->enqueueMessage( JText::_( 'COM_EASYBLOG_DASHBOARD_SAVE_EMPTY_TITLE_ERROR' ) , 'error' );
			$mainframe->redirect( 'index.php?option=com_easyblog&view=blog' );
			$mainframe->close();
		}


		JPluginHelper::importPlugin( 'easyblog' );
		$dispatcher = JDispatcher::getInstance();
		$blog		= EasyBlogHelper::getTable( 'blog', 'Table' );

		$id			= JRequest::getInt( 'blogid' );
		$blog->load( $id );
		$isNew		= ( $blog->id == 0 ) ? true : false;


		$draftId			= JRequest::getInt( 'draft_id' );
		$under_approval		= JRequest::getInt( 'under_approval' );
		$contributionSource = isset( $post['blog_contribute_source'] ) ? $post['blog_contribute_source'] : '';

		// If contribution source is empty, we should assume that it's for EasyBlog but it shouldn't be empty in the first place.
		if( !$contributionSource )
		{
			$contributionSource		= 'easyblog';
		}

		//override the isnew variable
		if(! $isNew)
		{
			// if this is blog edit, then we should see the column isnew to determine
			// whether we should send any notification
			$isNew  = $blog->isnew;
		}

		// @task: Get the offset
		$txOffset		= EasyBlogDateHelper::getOffSet();

		// we do not proccess this on draft
		if ( $post['published'] != POST_ID_DRAFT )
		{
			// we check the publishing date here
			// if user set the future date then we will automatically change
			// the status to Schedule
			$today   		= EasyBlogHelper::getDate();

			if($post['published'] == POST_ID_PUBLISHED)
			{
				$publishing     = EasyBlogHelper::getDate( $post[ 'publish_up' ], $txOffset );
				if ( $publishing->toUnix() > $today->toUnix() )
				{
					$post['published'] = POST_ID_SCHEDULED;
				}

			}//end if
		}

		if ( empty($post['publish_down']) ) {
			$post['publish_down'] = '0000-00-00 00:00:00';
		}

		// set author
		$post['created_by']	= $authorId;

		// Some other elements might use name="language", so we need to remap this
		$language 				= JRequest::getVar( 'eb_language' );
		$post['language']		= $language;

		$blog->bind( $post , true );

		/**
		 * check here if user do not have the 'Publish Entry' acl, send this post for pending approval.
		 */
		if( empty( $draftId ) && empty($acl->rules->publish_entry) )
		{
			$this->_saveDraft();
			return;
		}

		//check if user have permission to enable privacy.
		$aclBlogPrivacy = $acl->rules->enable_privacy;
		$blog->private = empty($aclBlogPrivacy)? '0' : $blog->private;

		//check if user have permission to contribute the blog post to eblog frontpage
		$blog->frontpage 	= (empty($acl->rules->contribute_frontpage)) ? '0' : $blog->frontpage;
		$blog->isnew 		= ($isNew && ($blog->published != 1)) ? '1' : '0';

		//now we need to check the blog contribution
		$blogContribution   = array();
		$issitewide 		= '1';

		if(isset( $post['blog_contribute']))
		{
			$myContribution = $post['blog_contribute'];
			//reset the value
			$issitewide = '0';

			if($myContribution == '0')
			{
				$issitewide = '1';
			}
			else
			{
				$blogContribution[] = $myContribution;
			}
		}
		$blog->issitewide   = $issitewide;


		if( $under_approval )
		{
			$blog->under_approval   = true;
		}

		$autopost = JRequest::getVar('socialshare', '');
		$centralized = JRequest::getVar( 'centralized' , '' );

		// If the post is scheduled, we need to know if we should auto post for the user or not.
		if ($blog->published == POST_ID_SCHEDULED && $centralized) {
			$blog->autoposting = json_encode($centralized);
		}

		if (!$blog->store())
		{
			$post['write_content']  = JRequest::getVar('write_content_hidden', '', 'post', 'string', JREQUEST_ALLOWRAW );
			EasyBlogHelper::storeSession( $post , 'tmpBlogData');

			$mainframe->enqueueMessage( $blog->getError() , 'error' );
			$mainframe->redirect( 'index.php?option=com_easyblog&view=blog' );
			$mainframe->close();
		}

		// @task: now we update the blog contribution
		if( $contributionSource == 'easyblog' )
		{
			$blog->updateBlogContribution($blogContribution);
		}
		else
		{
			$blog->updateBlogContribution( $blogContribution , $contributionSource );
		}

		// @task: Notify the world that someone created a blog post.
		if( $blog->published == POST_ID_PUBLISHED && $isNew && !$blog->private )
		{
			$blog->notify( $under_approval );
		}

		if(($blog->published == POST_ID_PUBLISHED) && ($user->id == $authorId) && (! $blog->ispending) && ($contributionSource != 'easyblog') )
		{
			EasyBlogHelper::getHelper( 'Groups' )->addStream( $blog , $isNew , $blogContribution , $contributionSource );
		}

		$blogId = $blog->id;

		//meta post info
		$metaId		= JRequest::getVar( 'metaid' , '' );

		$metapost	= array();
		$metapost['keywords']		= JRequest::getVar('keywords', '');
		$metapost['description']	= JRequest::getVar('description', '');
		$metapost['content_id']		= $blogId;
		$metapost['type']			= META_TYPE_POST;

		// save meta tag for post
		$meta		= EasyBlogHelper::getTable( 'Meta', 'Table' );
		$meta->load($metaId);
		$meta->bind($metapost);
		$meta->store();

		$author		= EasyBlogHelper::getTable( 'Profile', 'Table' );
		$author->setUser( $user );

		// @task: Store trackbacks into the trackback table for tracking purposes.
		$trackbacks	= JRequest::getVar( 'trackback' , '' , 'POST' );
		if( !empty( $acl->rules->add_trackback) && !empty( $trackbacks ) )
		{
			$blog->storeTrackbacks( $trackbacks );
			$blog->processTrackbacks();
		}

		// @rule: Process the tags after the blog is stored.
		$tags			= JRequest::getVar( 'tags' , '' , 'POST' );
		$blog->processTags( $tags );

		if( !empty($draftId) )
		{
			// @task: Cleanup any messages
			$postreject	= EasyBlogHelper::getTable( 'PostReject' , 'Table' );
			$postreject->clear( $draftId );

			$draft = EasyBlogHelper::getTable( 'Draft' , 'Table' );
			$draft->load( $draftId );
			$draft->delete();
		}


		// @rule: Autoposting to social network sites.
		if ($blog->published == POST_ID_PUBLISHED) {
			$blog->autopost($autopost, $centralized);
		}

		//not ready.
		$mainframe->enqueueMessage( JText::_( 'COM_EASYBLOG_BLOGS_BLOG_SAVE_POST_SAVED') );

		// Redirect to new form again if necessary
		if( $saveNew )
		{
			$this->setRedirect( 'index.php?option=com_easyblog&view=blog' );
			return;
		}

		if( $this->getTask() == 'saveApply' )
		{
			$this->setRedirect( 'index.php?option=com_easyblog&view=blog&blogid=' . $blog->id );
			return;
		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=blogs' );
	}

	function cancel()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'blog' );

		$this->setRedirect( 'index.php?option=com_easyblog&view=blogs' );

		return;
	}

	function addNew()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'blog' );

		$this->setRedirect( 'index.php?option=com_easyblog&view=blog' );

		return;
	}

	function edit()
	{
		// @task: Check for acl rules.
		$this->checkAccess( 'blog' );

		JRequest::setVar( 'view', 'blog' );
		JRequest::setVar( 'blogid' , JRequest::getVar( 'blogid' , '' , 'REQUEST' ) );
		JRequest::setVar( 'draft_id' , JRequest::getVar( 'draft_id' , '' , 'REQUEST' ) );
		JRequest::setVar( 'approval' , JRequest::getVar( 'approval' , '' , 'REQUEST' ) );

		parent::display();
	}

	public function trash()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'blog' );

		$config = EasyBlogHelper::getConfig();
		$blogs	= JRequest::getVar( 'cid' , '' , 'POST' );

		$message	= '';
		$type		= 'message';

		if( empty( $blogs ) )
		{
			$this->setRedirect( 'index.php?option=com_easyblog&view=blogs' , JText::_( 'COM_EASYBLOG_BLOGS_INVALID_ID' ) , 'error' );
		}

		foreach( $blogs as $blog )
		{
			$table 	= EasyBlogHelper::getTable( 'Blog' );
			$table->load( $blog );

			$table->trash();
		}

		$total 		= count( $blogs );
		$message	= JText::sprintf( 'COM_EASYBLOG_BLOGS_TRASHED' , $total );

		$this->setRedirect( 'index.php?option=com_easyblog&view=blogs' , $message );
	}

	public function remove()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'blog' );

		$config = EasyBlogHelper::getConfig();
		$blogs	= JRequest::getVar( 'cid' , '' , 'POST' );

		$message	= '';
		$type		= 'message';

		JPluginHelper::importPlugin( 'easyblog' );
		$dispatcher = JDispatcher::getInstance();

		if( empty( $blogs ) )
		{
			$message	= JText::_('Invalid blog id');
			$type		= 'error';
		}
		else
		{
			$blogTbl		= EasyBlogHelper::getTable( 'Blog' , 'Table' );
			foreach( $blogs as $blog )
			{
				$blogTbl->load( $blog );

				if( !$blogTbl->delete() )
				{
					$message	= JText::_( 'Error removing Blog.' );
					$type		= 'error';
					$this->setRedirect( 'index.php?option=com_easyblog&view=blogs' , $message , $type );
					return;
				}
			}

			$message	= JText::_('Blog deleted');
		}

		// Filter_state is necessary since the only view that the user can delete a blog post is when viewing trashed items.
		$this->setRedirect( 'index.php?option=com_easyblog&view=blogs&filter_state=T' , $message , $type );
	}

	function publish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'blog' );

		$ids	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';
		$task 		= $this->getTask();

		if( count( $ids ) <= 0 )
		{
			$message	= JText::_('Invalid blog id');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Blogs' );

			if( $model->publish( $ids , 1 ) )
			{
				$message	= JText::_('Blog(s) published');

				if( $task == 'restore' )
				{
					$message 	= JText::_( 'COM_EASYBLOG_BLOGS_RESTORED_SUCCESSFULLY' );
				}
			}
			else
			{
				$message	= JText::_('Error publishing blog');
				$type		= 'error';
			}

		}

		foreach($ids as $id)
		{
			$blog = EasyBlogHelper::getTable('Blog');
			$blog->load($id);


			if( $blog->published == POST_ID_PUBLISHED && $blog->isnew && !$blog->private )
			{
				$blog->notify();
			}
		}


		$this->setRedirect( 'index.php?option=com_easyblog&view=blogs' , $message );
	}

	function unpublish()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'blog' );

		$blogs	= JRequest::getVar( 'cid' , array(0) , 'POST' );

		$message	= '';
		$type		= 'message';

		if( count( $blogs ) <= 0 )
		{
			$message	= JText::_('Invalid blog id');
			$type		= 'error';
		}
		else
		{
			$model		= $this->getModel( 'Blogs' );

			if( $model->publish( $blogs , 0 ) )
			{
				$message	= JText::_('Blog(s) unpublished');
			}
			else
			{
				$message	= JText::_('Error unpublishing blog');
				$type		= 'error';
			}

		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=blogs' , $message , $type );
	}

	public function moveCategory()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'blog' );

		$blogs			= JRequest::getVar( 'cid' , array(0) , 'POST' );
		$newCategory	= JRequest::getInt( 'move_category_id' );

		if( !$blogs || !$newCategory )
		{
			$this->setRedirect( 'index.php?option=com_easyblog&view=blogs' , JText::_( 'COM_EASYBLOG_BLOGS_MOVED_ERROR') , 'error' );
			return;
		}

		foreach( $blogs as $id )
		{
			$blog		= EasyBlogHelper::getTable( 'Blog' );
			$blog->load( $id );

			$blog->set( 'category_id' , $newCategory );

			$blog->store();
		}

		$this->setRedirect( 'index.php?option=com_easyblog&view=blogs' , JText::sprintf( 'COM_EASYBLOG_BLOGS_MOVED_SUCCESSFULLY' , count( $blogs ) ) , 'success' );
	}

	/**
	 * Duplicates blog post
	 */
	public function copy()
	{
		// Check for request forgeries
		JRequest::checkToken() or jexit( 'Invalid Token' );

		// @task: Check for acl rules.
		$this->checkAccess( 'blog' );

		$blogs			= JRequest::getVar( 'cid' , array(0) , 'POST' );

		if( !$blogs )
		{
			$this->setRedirect( 'index.php?option=com_easyblog&view=blogs' , JText::_( 'COM_EASYBLOG_BLOGS_COPY_ERROR') , 'error' );
			return;
		}

		foreach( $blogs as $id )
		{
			$blog		= EasyBlogHelper::getTable( 'Blog' );
			$blog->load( $id );

			$newBlog 	= clone( $blog );

			$newBlog->published	= POST_ID_UNPUBLISHED;

			// We don't want to use the old id.
			$newBlog->id	= '';
			$newBlog->title = JText::_( 'COM_EASYBLOG_COPY_OF' ) . ' ' . $blog->title;

			$newBlog->store();
		}
		$this->setRedirect( 'index.php?option=com_easyblog&view=blogs' , JText::sprintf( 'COM_EASYBLOG_BLOGS_COPIED_SUCCESSFULLY' , count( $blogs ) ) , 'success' );
	}
}
