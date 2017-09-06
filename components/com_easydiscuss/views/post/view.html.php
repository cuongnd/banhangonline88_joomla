<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */
defined('_JEXEC') or die('Restricted access');

require_once DISCUSS_ROOT . '/views.php';
require_once DISCUSS_CLASSES . '/composer.php';

class EasyDiscussViewPost extends EasyDiscussView
{
	function display( $tpl = null )
	{
		$app 	= JFactory::getApplication();
		$doc 	= JFactory::getDocument();
		$config	= DiscussHelper::getConfig();

		// Sorting and filters.
		$sort			= JRequest::getString('sort', DiscussHelper::getDefaultRepliesSorting() );
		$filteractive	= JRequest::getString('filter', 'allposts');
		$id				= JRequest::getInt( 'id' );
		$acl			= DiscussHelper::getHelper( 'ACL' );

		// Add noindex for print view by default.
		if( JRequest::getInt( 'print' ) == 1 )
		{
			$doc->setMetadata( 'robots' , 'noindex,follow' );
		}

		// Get current logged in user.
		$my 		= JFactory::getUser();

		// Determine if the logged in user is an admin.
		$isAdmin	= DiscussHelper::isSiteAdmin();

		// Load the post table out.
		$post	= DiscussHelper::getTable( 'Post' );
		$state	= $post->load( $id );

		// Need raw content for later use
		$post->content_raw = $post->content;

		// If id is not found, we need to redirect gracefully.
		if( !$state || !$post->published || !$id )
		{
			DiscussHelper::setMessageQueue( JText::_( 'COM_EASYDISCUSS_SYSTEM_POST_NOT_FOUND' ) , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index' , false ) );
			$app->close();
		}

		// Check whether this is a valid discussion
		if( $post->parent_id != 0 || ($post->published == DISCUSS_ID_PENDING && ( !$isAdmin && $post->user_id != $my->id )) )
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_SYSTEM_INVALID_ID')  , DISCUSS_QUEUE_ERROR );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index' , false ) );
			$app->close();
		}

		// Load the category.
		$category	= DiscussHelper::getTable( 'Category' );
		$category->load( (int) $post->category_id );


		if( $post->category_id )
		{
			if( !$category->canAccess() )
			{
				DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_NO_PERMISSION_TO_VIEW_POST')  , 'error' );
				$app->redirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=index', false)) ;
			}
		}

		// Add pathway for category here.
		DiscussHelper::getHelper( 'Pathway' )->setCategoryPathway( $category );

		// Set breadcrumbs for this discussion.
		$this->setPathway( $this->escape( $post->title ) );

		// Mark as viewed for notifications.
		$this->logView();

		// Update hit count for this discussion.
		$post->hit();

		// Set page title.
		DiscussHelper::setPageTitle( $post->getTitle() );

		$doc->setMetadata('keywords', $post->title );
		$doc->setMetadata( 'description', preg_replace( '/\s+/', ' ', (substr( strip_tags( EasyDiscussParser::bbcode($post->content) ), 0, 160 ) ) ) );

		// Set canonical link to avoid URL duplication.
		$doc->addHeadLink( DISCUSS_JURIROOT . DiscussRouter::getPostRoute( $post->id ) , 'canonical' , 'rel' );

		// Add syntax highlighted css codes.
		if( $config->get( 'main_syntax_highlighter') )
		{
			$doc->addStylesheet( DISCUSS_MEDIA_URI . '/styles/syntaxhighlighter/' . $config->get( 'sh_theme' ) . '.css');
		}

		// Before sending the title and content to be parsed, we need to store this temporarily in case it needs to be accessed.
		$post->title_clear 	= $post->title;

		// Filter badwords
		$post->title		= DiscussHelper::wordFilter( $post->title );
		$post->content		= DiscussHelper::wordFilter( $post->content );


		// Get the tags for this discussion
		$postsTagsModel	= $this->getModel('PostsTags');
		$tags 			= $postsTagsModel->getPostTags( $id );

		// Get adsense codes here.
		$adsense 		= DiscussHelper::getAdsense();


		$postsModel 	= DiscussHelper::getModel( 'Posts' );

		// Get the answer for this discussion.
		$answer		= $postsModel->getAcceptedReply( $post->id );


		// Get a list of replies for this discussion
		$replies 		= array();
		$hasMoreReplies	= false;
		$totalReplies 	= 0;
		$readMoreURI	= '';

		if( $category->canViewReplies() )
		{
			$repliesLimit	= $config->get('layout_replies_list_limit');
			$totalReplies	= $postsModel->getTotalReplies( $post->id );

			$hasMoreReplies	= false;

			$limitstart		= null;
			$limit			= null;

			if( $repliesLimit && !JRequest::getBool('viewallreplies') )
			{
				$limit		= $repliesLimit;

				$hasMoreReplies = ( $totalReplies - $repliesLimit ) > 0;
			}

			$replies 		= $postsModel->getReplies( $post->id, $sort, $limitstart, $limit );

			if( count( $replies ) > 0 )
			{
				$repliesIds = array();
				$authorIds  = array();

				foreach( $replies as $reply )
				{
					$repliesIds[]	= $reply->id;
					$authorIds[]    = $reply->user_id;
				}

				if( $answer )
				{
					$repliesIds[]   = $answer[0]->id;
					$authorIds[]    = $answer[0]->user_id;
				}

				$post->loadBatch( $repliesIds );
				$post->setAttachmentsData( 'replies', $repliesIds);

				// here we include the discussion id into the array as well.
				$repliesIds[]   = $post->id;
				$authorIds[]    = $post->user_id;

				$post->setLikeAuthorsBatch( $repliesIds );
				DiscussHelper::getHelper( 'Post' )->setIsLikedBatch( $repliesIds );

				$post->setPollQuestionsBatch( $repliesIds );
				$post->setPollsBatch( $repliesIds );

				$post->setLikedByBatch( $repliesIds, $my->id );
				$post->setVoterBatch( $repliesIds );
				$post->setHasVotedBatch( $repliesIds );

				$post->setTotalCommentsBatch( $repliesIds );
				$commentLimit	= $config->get( 'main_comment_pagination' ) ? $config->get( 'main_comment_pagination_count' ) : null;
				$post->setCommentsBatch( $repliesIds, $commentLimit );

				// Reduce SQL queries by pre-loading all author object.
				$authorIds  = array_unique($authorIds);
				$profile	= DiscussHelper::getTable( 'Profile' );
				$profile->init( $authorIds );
			}

			$readMoreURI	= JURI::getInstance()->toString();
			$delimiteter	= JString::strpos($readMoreURI, '&') ? '&' : '?';
			$readMoreURI	= $hasMoreReplies ? $readMoreURI . $delimiteter . 'viewallreplies=1' : $readMoreURI;

			// Format the reply items.
			$replies		= DiscussHelper::formatReplies( $replies , $category );
		}

		// Format the answer object.
		if( $answer )
		{
			$answer 	= DiscussHelper::formatReplies( $answer , $category );
			$answer 	= $answer[0];
		}

		// Get comments for the post
		$commentLimit			= $config->get( 'main_comment_pagination' ) ? $config->get( 'main_comment_pagination_count' ) : null;
		$post->comments 		= false;

		if( $config->get( 'main_commentpost' ) )
		{
			$comments				= $post->getComments( $commentLimit );
			$post->comments 		= DiscussHelper::formatComments( $comments );
		}


		// get reply comments count
		$post->commentsCount	= $post->getTotalComments();

		// Get the post access object here.
		$access	= $post->getAccess( $category );
		$post->access = $access;

		// Add custom values.
		$postOwner = $post->getOwner();
		$profileTable = DiscussHelper::getTable( 'Profile' );
		$profileTable->load( $postOwner->id );
		$post->user 	= $profileTable;

		// update user's post read flag
		if( $my->id != 0 )
		{
			$profile	= DiscussHelper::getTable( 'Profile' );
			$profile->load( $my->id );
			$profile->read( $post->id );
		}

		$badgesTable	= DiscussHelper::getTable( 'Profile' );
		$badgesTable->load( $post->user->id );
		$postBadges = $badgesTable->getBadges();

		// Get Likes model here.
		$post->likesAuthor	= DiscussHelper::getHelper( 'Likes' )->getLikesHTML( $post->id , $my->id , 'post' );

		$post->isVoted		= DiscussHelper::getHelper( 'Post' )->isVoted( $post->id );

		// Test if trigger is necessary here.
		if ( $config->get( 'main_content_trigger_posts' ) )
		{
			$post->event = new stdClass();

			// Triger onContentPrepare here. Since it doesn't have any return value, just ignore this.
			DiscussHelper::triggerPlugins( 'content' , 'onContentPrepare' , $post );

			$post->event->afterDisplayTtle		= DiscussHelper::triggerPlugins( 'content' , 'onContentAfterTitle' , $post , true );
			$post->event->beforeDisplayContent	= DiscussHelper::triggerPlugins( 'content' , 'onContentBeforeDisplay' , $post , true );
			$post->event->afterDisplayContent 	= DiscussHelper::triggerPlugins( 'content' , 'onContentAfterDisplay' , $post , true );
		}


		$postStatus = $post->post_status;

		switch( $postStatus )
		{
			case '0':
				$postStatusClass = '';
				$postStatus = '';
				break;
			case '1':
				$postStatusClass = '-on-hold';
				$postStatus = JText::_( 'COM_EASYDISCUSS_POST_STATUS_ON_HOLD' );
				break;
			case '2':
				$postStatusClass = '-accepted';
				$postStatus = JText::_( 'COM_EASYDISCUSS_POST_STATUS_ACCEPTED' );
				break;
			case '3':
				$postStatusClass = '-working-on';
				$postStatus = JText::_( 'COM_EASYDISCUSS_POST_STATUS_WORKING_ON' );
				break;
			case '4':
				$postStatusClass = '-reject';
				$postStatus = JText::_( 'COM_EASYDISCUSS_POST_STATUS_REJECT' );
				break;
			default:
				$postStatusClass = '';
				$postStatus = '0';
				break;
		}

		$alias = $post->post_type;
		$model = DiscussHelper::getModel( 'Post_types' );

		// Get each post's post status title
		$title = $model->getTitle( $alias );
		$post->post_type = $title;

		// Get post type css suffix
		$suffix = $model->getSuffix( $alias );

		$theme 	= new DiscussThemes();

		$isQuestion = $post->isQuestion();
		$isReply	= $post->isReply();

		$moderators = array();
		$composer = new DiscussComposer("replying", $post);

		// Set the discussion object.
		$theme->set( 'post'					, $post );
		$theme->set( 'composer'             , $composer );

		// Set the replies for this discussion.
		$theme->set( 'replies'				, $replies );

		// This is the DiscussPost object for the accepted answer in this discussion.
		$theme->set( 'answer'				, $answer );
		$theme->set( 'sort'					, $sort );
		$theme->set( 'adsense'				, $adsense );
		$theme->set( 'tags'					, $tags );
		$theme->set( 'totalReplies'			, $totalReplies );
		$theme->set( 'hasMoreReplies'		, $hasMoreReplies );
		$theme->set( 'access'				, $access );
		$theme->set( 'category'				, $category );
		$theme->set( 'isQuestion'			, $isQuestion );
		$theme->set( 'isReply'				, $isReply );
		$theme->set( 'moderators'			, $moderators );
		$theme->set( 'readMoreURI'			, $readMoreURI );
		$theme->set( 'postStatus'			, $postStatus );
		$theme->set( 'suffix'				, $suffix );
		$theme->set( 'postStatusClass'		, $postStatusClass );
		$theme->set( 'postBadges'			, $postBadges );

		echo $theme->fetch( 'post.php' );

	}

	public function edit($tpl = null)
	{
		$app	= JFactory::getApplication();
		$doc	= JFactory::getDocument();
		$my		= JFactory::getUser();
		$acl	= DiscussHelper::getHelper( 'ACL' );
		$config	= DiscussHelper::getConfig();

		// Load post item
		$id		= JRequest::getInt( 'id' , 0 );

		if( empty($id) )
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_INVALID_POST_ID') );
		 	return;
		}

		$post	= DiscussHelper::getTable( 'Post' );
		$post->load( $id );

		$post->content_raw = $post->content;

		$editing	= (bool) $post->id;

		if( ! $editing )
		{
			// try to get from session if there are any.
			$this->getSessionData( $post );
		}


		$categoryId	= JRequest::getInt( 'category' , $post->category_id );

		// Load category item.
		$category 	= DiscussHelper::getTable( 'Category' );
		$category->load( $categoryId );

		// Check if user is allowed to post a discussion, we also need to check against the category acl
		if( empty($my->id) && !$acl->allowed('add_question', 0 ) )
		{
			DiscussHelper::setMessageQueue( JText::_('COM_EASYDISCUSS_PLEASE_KINDLY_LOGIN_TO_CREATE_A_POST') );
			$app->redirect( DiscussRouter::_( 'index.php?option=com_easydiscuss&view=index' , false ) );

			$app->close();
			return;
		}

		if( $my->id != 0 && !$acl->allowed('add_question', '0') && !$category->canPost() )
		{
			$app->redirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=index' , false ) , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
			$app->close();
			return;
		}

		// Set the breadcrumbs.
		$this->setPathway( JText::_( 'COM_EASYDISCUSS_BREADCRUMBS_ASK') );

		// Set the page title.
		$title	= JText::_( 'COM_EASYDISCUSS_TITLE_ASK' );

		if( $id && $post->id )
		{
			$title 	= JText::sprintf( 'COM_EASYDISCUSS_TITLE_EDIT_QUESTION' , $post->getTitle() );
		}

		// Set the page title
		DiscussHelper::setPageTitle( $title );

		if( $editing )
		{
			$isModerator = DiscussHelper::getHelper( 'Moderator' )->isModerator( $post->category_id );
			if( !DiscussHelper::isMine( $post->user_id ) && !DiscussHelper::isSiteAdmin() && !$acl->allowed( 'edit_question' ) && !$isModerator)
			{
				$app->redirect( DiscussRouter::_('index.php?option=com_easydiscuss&view=post&id='.$postid , false ) , JText::_('COM_EASYDISCUSS_SYSTEM_INSUFFICIENT_PERMISSIONS') );
				$app->close();
			}

			$tagsModel	= DiscussHelper::getModel( 'PostsTags' );
			$post->tags	= $tagsModel->getPostTags( $post->id );
		}
		else
		{
			if( $categoryId )
			{
				// set the default category
				$post->category_id = $categoryId;
			}
		}

		$attachments = $post->getAttachments();

		if( isset( $post->sessiondata ) )
		{
			$attachments    = '';
		}


		$model		= DiscussHelper::getModel( 'Posts' );
		$postCount	= count( $model->getPostsBy( 'user' , $my->id ) );


		$onlyPublished = ( empty( $post->id ) ) ? true : false;

		// @rule: If there is a category id passed through the query, respect it first.
		$showPrivateCat		= ( empty($post->id) && $my->id == 0 ) ? false : true;

		$categoryModel		= $this->getModel( 'Category' );
		$defaultCategory	= $categoryModel->getDefaultCategory();

		if( $categoryId == 0 && $defaultCategory !== false )
		{
			$categoryId 		= $defaultCategory->id;
		}

		$nestedCategories	= '';
		$categories			= '';

		if( $config->get( 'layout_category_selection' ) == 'multitier' )
		{
			$categoriesModel	= $this->getModel( 'Categories');
			$categories			= $categoriesModel->getCategories( array('acl_type' => DISCUSS_CATEGORY_ACL_ACTION_SELECT) );
		}
		else
		{
			$nestedCategories	= DiscussHelper::populateCategories('', '', 'select', 'category_id', $categoryId , true, $onlyPublished, $showPrivateCat , true );
		}

		if( $config->get( 'layout_reply_editor' ) == 'bbcode' )
		{
			// Legacy fix when switching from WYSIWYG editor to bbcode.
			$post->content	= EasyDiscussParser::html2bbcode( $post->content );
		}

		$editor = '';
		if( $config->get('layout_editor' ) != 'bbcode' )
		{
			$editor	= JFactory::getEditor( $config->get('layout_editor' ) );
		}

		// Get list of moderators from the site.
		$moderatorList = array();
		if( $config->get('main_assign_user') )
		{
			$moderatorList	= DiscussHelper::getHelper( 'Moderator' )->getSelectOptions( $post->category_id );
		}

		$composer = new DiscussComposer("editing", $post);

		// Set the discussion object.
		$access		= $post->getAccess( $category );

		$theme 		= new DiscussThemes();

		// Test if reference is passed in query string.
		$reference 		= JRequest::getWord( 'reference' );
		$referenceId	= JRequest::getInt( 'reference_id' , 0 );
		$redirect		= JRequest::getVar( 'redirect' , '' );

		$theme->set( 'redirect'			, $redirect );
		$theme->set( 'reference'		, $reference );
		$theme->set( 'referenceId'		, $referenceId );


		$theme->set( 'isEditMode'		, $editing );
		$theme->set( 'post'				, $post );
		$theme->set( 'composer'			, $composer );
		$theme->set( 'parent'			, $composer->parent );
		$theme->set( 'nestedCategories'	, $nestedCategories );
		$theme->set( 'attachments'		, $attachments );
		$theme->set( 'editor'			, $editor );
		$theme->set( 'moderatorList'	, $moderatorList );
		$theme->set( 'categories'		, $categories );
		$theme->set( 'access'			, $access );

		// Deprecated since 3.0. Will be removed in 4.0
		$theme->set( 'config'			, $config );

		echo $theme->fetch( 'form.reply.wysiwyg.php' );
	}



}
