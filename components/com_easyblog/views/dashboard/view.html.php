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

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'date.php' );

class EasyBlogViewDashboard extends EasyBlogView
{
	function showToolbar( $current , $user )
	{
		$acl			= EasyBlogACLHelper::getRuleSet();
		$config			= EasyBlogHelper::getConfig();

		$joomlaVersion	= EasyBlogHelper::getJoomlaVersion();

		$homeItemId		= EasyBlogRouter::getItemid( 'latest' );
		$logoutURL		= base64_encode( EasyBlogRouter::_('index.php?option=com_easyblog&view=latest&Itemid=' . $homeItemId , false ) );

		$model			= $this->getModel('Blogs');
		$total			= $model->getTotalPending();

		$isTeamAdmin		= EasyBlogHelper::isTeamAdmin();
		$totalTeamRequest	= 0;

		if($isTeamAdmin)
		{
			$teamModel = $this->getModel('TeamBlogs');
			$totalTeamRequest	= $teamModel->getTotalRequest();
		}

		// @task: Get total draft entries
		$draftsModel	= $this->getModel( 'Drafts' );
		$totalDrafts	= $draftsModel->getTotal();

		//get the logout link
		$logoutActionLink = '';
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			$logoutActionLink = 'index.php?option=com_users&task=user.logout';
		}
		else
		{
			$logoutActionLink = 'index.php?option=com_user&task=logout';
		}

		// @task: Determine if the current user is a blogger or not.
		$isBlogger	= EasyBlogHelper::isSiteAdmin() || $acl->rules->add_entry;

		$tpl	= new CodeThemes('dashboard');

		$tpl->set( 'isBlogger' 			, $isBlogger );
		$tpl->set( 'totalPending'		, $total );
		$tpl->set( 'user'				, $user );
		$tpl->set( 'current' 			, $current );
		$tpl->set( 'acl' 				, $acl );
		$tpl->set( 'config' 			, $config );
		$tpl->set( 'logoutURL' 			, $logoutURL );
		$tpl->set( 'logoutActionLink'   , $logoutActionLink );
		$tpl->set( 'isTeamAdmin' 		, $isTeamAdmin );
		$tpl->set( 'totalTeamRequest'	, $totalTeamRequest );
		$tpl->set( 'totalDrafts'		, $totalDrafts );
		return $tpl->fetch( 'dashboard.toolbar.php' );
	}

	/**
	 * Main access to the dashboard here.
	 */
	function display( $tmpl = null )
	{
		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$my			= JFactory::getUser();
		$config		= EasyBlogHelper::getConfig();

		if(! EasyBlogHelper::isLoggedIn())
		{
			EasyBlogHelper::showLogin();
			return;
		}

		$user		= EasyBlogHelper::getTable( 'Profile' , 'Table' );
		$user->setUser($my);

		//setting pathway
		$pathway	= $mainframe->getPathway();

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'dashboard' ) )
		{
			$pathway->addItem( JText::_('COM_EASYBLOG_DASHBOARD_BREADCRUMB'), '' );
		}

		$title					= EasyBlogHelper::getPageTitle( JText::_( 'COM_EASYBLOG_DASHBOARD_PAGE_TITLE' ) );

		// @task: Set the page title
		parent::setPageTitle( $title , false , $config->get( 'main_pagetitle_autoappend' ) );


		//getting the blog statistic from various model file.
		$modelB				= $this->getModel( 'Blog' );
		$modelC				= $this->getModel( 'Category' );
		$modelT				= $this->getModel( 'Tags' );
		$modelCmmt			= $this->getModel( 'Comment' );
		$modelTB			= $this->getModel( 'Teamblogs' );
		$tagsModel			= $this->getModel( 'Tags' );
		$tags				= $tagsModel->getTags();

		$categoriesModel	= $this->getModel( 'Categories' );
		$categories			= EasyBlogHelper::populateCategories('', '', 'select', 'category_id', '' , true, true, true);

		$blogStat	= new StdClass();
		$blogStat->blog			= $modelB->getTotalBlogs( $my->id );
		$blogStat->category		= $modelC->getTotalCategory( $my->id );
		$blogStat->tag			= $modelT->getTotalTags( $my->id );
		$blogStat->comment		= $modelCmmt->getTotalComment( $my->id );
		$blogStat->subscriber	= $modelB->getTotalBlogSubscribers( $my->id );
		$blogStat->team			= $modelTB->getTotalTeamJoined( $my->id );
		$blogStat->totalHits 	= $modelB->getTotalHits( $my->id );

		echo $this->showToolbar( __FUNCTION__ , $user );

		$data			= EasyBlogHelper::activityGet( $my->id, EBLOG_STREAM_NUM_ITEMS, '');

		$activities			= $data[0];
		$currentDateRange	= $data[1];
		$nextStreamItem		= EasyBlogHelper::activityHasNextItems( $my->id, EBLOG_STREAM_NUM_ITEMS, $currentDateRange['startdate']);

		$theme		= new CodeThemes('dashboard');
		$theme->set( 'tags'			, $tags );
		$theme->set( 'categories'	, $categories );
		$theme->set( 'blogStat' 	, $blogStat );
		$theme->set( 'activities' 	, $activities );
		$theme->set( 'hasNextStream' 	, count( $nextStreamItem ) );
		$theme->set( 'currentDate' 	, $currentDateRange );

		echo $theme->fetch( 'dashboard.php' );
	}

	function bindTags( $arrayData )
	{
		$result	= array();

		if( count( $arrayData ) > 0 )
		{
			foreach( $arrayData as $tag )
			{
				$obj		= new stdClass();
				$obj->title	= $tag;
				$result[]	= $obj;
			}
		}
		return $result;
	}

	function bindContribute( $contribution = '' )
	{
		if( $contribution )
		{
			$contributed			= new stdClass();
			$contributed->team_id	= $contribution;
			$contributed->selected	= 1;

			return $contributed;
		}
		return false;
	}

	/*
	 * @since 1.0
	 * Responsible to display the write entry form.
	 *
	 * @param	null
	 * @return	null
	 */
	function write()
	{
		$document		= JFactory::getDocument();
		$config 		= EasyBlogHelper::getConfig();
		$mainframe		= JFactory::getApplication();
		$acl			= EasyBlogACLHelper::getRuleSet();
		$siteAdmin		= EasyBlogHelper::isSiteAdmin();
		$my 			= JFactory::getUser();

		// set the editor title based on operation
		$editorTitle 		= '';

		// just to inform the view that this is edit operation
		$showDraftStatus	= true;
		$isEdit				= false;

		if(! EasyBlogHelper::isLoggedIn())
		{
			EasyBlogHelper::showLogin();
			return;
		}

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'dashboard' ) )
		{
			$this->setPathway( JText::_('COM_EASYBLOG_DASHBOARD_BREADCRUMB'), EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard' ) );
		}

		$this->setPathway( JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_BREADCRUMB' ) );

		if( !$acl->rules->add_entry )
		{
			$mainframe->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard' , false ) , JText::_('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_BLOG') );
			$mainframe->close();
		}

		// enable datetime picker
		EasyBlogDateHelper::enableDateTimePicker();

		// Add the Calendar includes to the document <head> section
		JHTML::_('behavior.calendar');

		// Add modal behavior
		JHTML::_( 'behavior.modal' );

		// Load the JEditor object
		if( $config->get( 'layout_editor_author' ) )
		{
			// Get user's parameters
			$userParams 	= EasyBlogHelper::getRegistry( $my->params );

			$editorType 	= $userParams->get( 'editor' , $config->get( 'layout_editor' ) );

			$editor 		= JFactory::getEditor( $editorType );
		}
		else
		{
			$editor 	= JFactory::getEditor( $config->get('layout_editor' ) );
		}


		$user		= EasyBlogHelper::getTable( 'Profile' , 'Table' );
		$user->setUser($my);

		$model				= $this->getModel( 'Blog' );
		$categoriesModel	= $this->getModel( 'Categories' );
		$publishedOnly		= true;
		$categoryItems		= $categoriesModel->getParentCategories( '' , 'all' , $publishedOnly, true );
		$categories			= array();

		if( $categoryItems )
		{
			foreach( $categoryItems as $categoryItem )
			{
				$category	= EasyBlogHelper::getTable( 'Category' );
				$category->bind( $categoryItem );

				$categories[]	= $category;
			}
		}

		$trackbacksModel	= $this->getModel( 'TrackbackSent' );
		$blogContributed	= '';
		$trackbacks			= '';
		$external			= '';
		$extGroupId			= '';

		// @task: See if there's any uid in the query string.
		// @since 3.5
		$source				= JRequest::getVar( 'source' );
		$uid				= JRequest::getInt( 'uid' );

		// get blogid if exists
		$blogId			= JRequest::getVar( 'blogid' , '' );

		// Test if draft id exists
		$draftId		= JRequest::getVar( 'draft_id' , '' );

		// test if this is a under approval post or not.
		$underApproval	= JRequest::getVar( 'approval' , '');

		// Load blog table
		$blog		= EasyBlogHelper::getTable( 'Blog' , 'Table' );
		$blog->load( $blogId );

		// Test if this blog belongs to the team and the current browser is the team admin.
		$teamblogModel		= $this->getModel( 'TeamBlogs' );
		$teamContribution	= $teamblogModel->getBlogContributed( $blog->id );

		// Check if the person has access to create a new blog post
		if( $blog->id && $blog->created_by != $my->id && !$siteAdmin && empty($acl->rules->moderate_entry) && !$teamContribution )
		{
			$url  = 'index.php?option=com_easyblog&view=dashboard';
			$mainframe->redirect( EasyBlogRouter::_( $url , false ) , JText::_('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_BLOG') );
		}

		// This will be the team blog checking
		$isCurrentTeamAdmin	= false;
		if( $teamContribution && !$siteAdmin && empty($acl->rules->moderate_entry) )
		{
			$isCurrentTeamAdmin	= $teamblogModel->checkIsTeamAdmin( $my->id , $teamContribution->team_id );

			// Test if the user has access to this team posting.
			if( !$isCurrentTeamAdmin && $blog->created_by != $my->id )
			{
				$url  = 'index.php?option=com_easyblog&view=dashboard';
				$mainframe->redirect( EasyBlogRouter::_( $url , false ) , JText::_('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_BLOG') );
			}
		}

		$tmpBlogData		= EasyBlogHelper::getSession('tmpBlogData');
		$loadFromSession	= false;

		$blogSource 		= '';

		if(isset($tmpBlogData))
		{
			$loadFromSession	= true;
			$blog->bind($tmpBlogData);

			// reprocess the date offset here.
			$tzoffset			= EasyBlogDateHelper::getOffSet();

			if(!empty( $blog->created ))
			{
				$date 			= EasyBlogHelper::getDate( $blog->created,  $tzoffset);
				$blog->created	= $date->toMySQL();
			}

			if( !empty( $blog->publish_up ) && $blog->publish_up != '0000-00-00 00:00:00')
			{
				$date 				= EasyBlogHelper::getDate( $blog->publish_up,  $tzoffset);
				$blog->publish_up	= $date->toMySQL();
			}

			if( !empty( $blog->publish_down ) && $blog->publish_down != '0000-00-00 00:00:00')
			{
				$date 				= EasyBlogHelper::getDate( $blog->publish_down,  $tzoffset);
				$blog->publish_down	= $date->toMySQL();
			}


			//bind the content from previous form
			$blog->content  = $tmpBlogData[ 'write_content' ];

			$blog->tags = array();
			if( isset( $tmpBlogData[ 'tags' ] ) )
			{
				$blog->tags		= $this->bindTags( $tmpBlogData[ 'tags' ] );
			}

			// metas
			$meta				= new stdClass();
			$meta->id			= '';
			$meta->keywords		= isset( $tmpBlogData['keywords'] ) ? $tmpBlogData['keywords'] : '';
			$meta->description	= isset( $tmpBlogData['description'] ) ? $tmpBlogData['description'] : '';

			if(isset($tmpBlogData['blog_contribute']))
			{
				$blogContributed	= $this->bindContribute( $tmpBlogData[ 'blog_contribute' ] );
			}

			$contributionSource	= isset( $tmpBlogData['blog_contribute_source'] ) ? $tmpBlogData['blog_contribute_source'] : '';

			if( !empty( $contributionSource ) && $contributionSource != 'easyblog' && !$uid )
			{
				$external			= true;
				$extGroupId			= $tmpBlogData[ 'blog_contribute' ];
				$blogSource 		= 'group';
			}

			if( !empty( $contributionSource ) && $contributionSource != 'easyblog' && $uid && $source == 'jomsocial.event')
			{
				$external			= true;
				$uid				= $tmpBlogData[ 'blog_contribute' ];
				$blogSource			= 'event';
			}
		}

		// Check if this is an edited post and if it has draft.
		$draft			= EasyBlogHelper::getTable( 'Draft' , 'Table' );
		$isDraft		= false;

		if( !empty( $draftId ) )
		{
			$draft->load( $draftId );
			$blog->load( $draft->entry_id );
			$blog->bind( $draft );

			$blog->tags		= empty( $draft->tags ) ? array() : $this->bindTags( explode( ',' , $draft->tags ) );

			// metas
			$meta				= new stdClass();
			$meta->id			= '';
			$meta->keywords		= $draft->metakey;
			$meta->description	= $draft->metadesc;

			if( !empty( $draft->trackbacks ) )
			{
				$blog->unsaveTrackbacks	= $draft->trackbacks;
			}

			if( $draft->blog_contribute )
			{
				$blogContributed	= $this->bindContribute( $draft->blog_contribute );
			}
			$blog->tags 	= array();

			if( !empty( $draft->tags ) )
			{
				$blog->tags		= $this->bindTags( explode( ',' , $draft->tags ) );
			}

			$blog->set( 'id' , $draft->entry_id );
			$blogId		= $blog->id;
			$isDraft	= true;
		}
		else
		{
			// We only want to load drafts that has a blog id.
			if( $blog->id )
			{
				$draft->loadByEntry( $blog->id );
			}

		}

		// set page title
		if ( !empty( $blogId ) )
		{
			$title					= EasyBlogHelper::getPageTitle( JText::_('COM_EASYBLOG_DASHBOARD_EDIT_POST') );

			// @task: Set the page title
			parent::setPageTitle( $title , false , $config->get( 'main_pagetitle_autoappend' ) );

			$editorTitle 	= JText::_('COM_EASYBLOG_DASHBOARD_EDIT_POST');

			// check if previous status is not Draft
			if ( $blog->published == POST_ID_DRAFT ) {
				$showDraftStatus	= true;
			}

			$isEdit = true;

			//perform some title string formatting
			$blog->title	= $this->escape($blog->title);
		}
		else
		{
			$title					= EasyBlogHelper::getPageTitle( JText::_('COM_EASYBLOG_DASHBOARD_WRITE_POST') );

			// @task: Set the page title
			parent::setPageTitle( $title , false , $config->get( 'main_pagetitle_autoappend' ) );

			$editorTitle	= JText::_('COM_EASYBLOG_DASHBOARD_WRITE_POST');

			// set the default publishing status only if it is a brand new creation page.
			if(!$loadFromSession && !$isDraft )
			{
				// by default, all new post MUST BE set to draft
				$blog->published 	= $config->get('main_blogpublishing', '3');
			}
		}

		//get all tags ever created.
		$newTagsModel	= $this->getModel( 'Tags' );
		$blog->newtags	= $newTagsModel->getTags();

		//prepare initial blog settings.
		$isPrivate		= $config->get('main_blogprivacy', '0');
		$allowComment	= $config->get('main_comment', 1);
		$allowSubscribe	= $config->get('main_subscription', 1);
		$showFrontpage	= $config->get('main_newblogonfrontpage', 0);
		$sendEmails		= $config->get('main_sendemailnotifications', 1);

		$isSiteWide		= (isset($blog->issitewide)) ? $blog->issitewide : '1';

		$teamblogModel	= $this->getModel( 'TeamBlogs' );
		$teams			= ( !empty($blog->created_by) ) ? $teamblogModel->getTeamJoined($blog->created_by) : $teamblogModel->getTeamJoined($my->id);

		if(! empty($blog->id))
		{
			$isPrivate		= $blog->private;
			$allowComment	= $blog->allowcomment;
			$allowSubscribe	= ($config->get('main_subscription'))? $blog->subscription : 0;
			$showFrontpage	= $blog->frontpage;
			$sendEmails		= $blog->send_notification_emails;


			//get user teamblog
			$teams				= $teamblogModel->getTeamJoined( $blog->created_by );

			//@task: List all trackbacks
			$trackbacks			= $trackbacksModel->getSentTrackbacks( $blogId );
		}

		if( $loadFromSession || $isDraft )
		{
			$isPrivate			= $blog->private;
			$allowComment		= $blog->allowcomment;
			$allowSubscribe		= $blog->subscription;
			$showFrontpage		= $blog->frontpage;
			$sendEmails			= $blog->send_notification_emails;

		}

		$author = null;
		//if site admin then get get blog creator and include a javascript function to change author.
		if($siteAdmin || !empty($acl->rules->moderate_entry) || ( isset( $teamContribution ) && $isCurrentTeamAdmin ) )
		{
			if(!empty($blog->created_by))
			{
				$creator	= JFactory::getUser($blog->created_by);
				$author		= EasyBlogHelper::getTable( 'Profile', 'Table' );
				$author->setUser( $creator );
				unset($creator);
			}
		}

		//check if can upload image or not.
		$useImageManager = $config->get('main_media_manager', 1);

		if( !isset( $meta ) )
		{
			$meta 				= new stdClass();
			$meta->id 			= '';
			$meta->keywords		= '';
			$meta->description 	= '';
		}

		if( empty($blog->created_by) || $blog->created_by == $my->id || $siteAdmin || !empty($acl->rules->moderate_entry) || $teamContribution )
		{
			$blog->tags 	= isset( $blog->tags ) && !empty($blog->tags) ? $blog->tags : array();

			if(!$loadFromSession && !$isDraft )
			{
				// get the tag only if it is not loaded from the session value
				if( $blogId )
				{
					$tagsModel		= $this->getModel( 'PostTag' );
					$blog->tags		= $tagsModel->getBlogTags( $blogId );

					// get meta tags
					$metaModel		= $this->getModel('Metas');
					$meta			= $metaModel->getPostMeta($blogId);
				}
			}

			$onlyPublished		= ( empty( $blogId ) ) ? true : false;
			$isFrontendWrite	= true;
			$nestedCategories	= '';

			$defaultCategory		= JRequest::getInt( 'categoryId' );

			$menu				= JFactory::getApplication()->getMenu()->getActive();

			if( $menu && isset( $menu->params ) )
			{
				$param 	= EasyBlogHelper::getRegistry();
				$param->load( $menu->params );

				$catId	= $param->get( 'categoryId' );

				if( $catId )
				{
					$defaultCategory	= $catId;
				}
			}

			// @task: If blog is being edited, it should contain a category_id property.
			$defaultCategory		= ( empty( $blog->category_id ) ) ? $defaultCategory : $blog->category_id;

			if( $config->get( 'layout_dashboardcategoryselect') == 'select' )
			{
				$nestedCategories	= EasyBlogHelper::populateCategories( '' , '' , 'select' , 'category_id', $defaultCategory , true , $onlyPublished , $isFrontendWrite );
			}

			echo $this->showToolbar( __FUNCTION__ , $user );

			$tpl		= new CodeThemes('dashboard');
			$blogger_id = ( !isset($blog->created_by) ) ? $user->id   : $blog->created_by;


			$content	= $blog->intro;

			// Append the readmore if necessary
			if( !empty($blog->intro) && !empty( $blog->content ) )
			{
				$content	.=  '<hr id="system-readmore" />';
			}

			$content	.= $blog->content;

			$defaultCategoryName	= '';

			if( empty( $defaultCategory ) )
			{
				//get default category if configured.
				$defaultCategory	= EasyBlogHelper::getDefaultCategoryId();
			}

			if( !empty( $defaultCategory ) )
			{
				$categoryTbl		= EasyBlogHelper::getTable( 'Category' );
				$categoryTbl->load( $defaultCategory );
				$defaultCategoryName	= $categoryTbl->title;
			}

			if( $draft->id != 0 && $isDraft )
			{
				if( !empty( $draft->external_source ) )
				{
					$external   = true;
					$extGroupId = $draft->external_group_id;
				}
			}
			else if( !$loadFromSession )
			{
				// If writing is for an external source, we need to tell the editor to strip down some unwanted features
				$external	= JRequest::getVar( 'external' , false );

				//check if this is a external group contribution.
				$extGroupId =  EasyBlogHelper::getHelper( 'Groups' )->getGroupContribution( $blog->id );

				if( !empty($extGroupId) )
				{
					$external   = $extGroupId;
					$blogSource 		= 'group';
				}

				if( !empty( $uid ) )
				{
					$external	= $uid;
					$blogSource = 'event';
				}

				$externalEventId	= EasyBlogHelper::getHelper( 'Event' )->getContribution( $blog->id );

				if( !empty( $externalEventId ) )
				{
					$external		= $externalEventId;
					$blogSource 	= 'event';
				}
			}

			// If there's a tag (maybe from the draft area, we need to add the tags data back)
			if( $isDraft && !empty($blog->tags) )
			{
				$blog->newtags	= array_merge( $blog->newtags , $blog->tags );
			}



			// Add the breadcrumbs
			$breadcrumbs 	= array( $editorTitle => '' );

			$tpl->set( 'teamContribution'	, $teamContribution );
			$tpl->set( 'isCurrentTeamAdmin'	, $isCurrentTeamAdmin );
			$tpl->set( 'breadcrumbs'		, $breadcrumbs );
			$tpl->set( 'external'			, $external );
			$tpl->set( 'extGroupId'			, $extGroupId );
			$tpl->set( 'defaultCategory'	, $defaultCategory );
			$tpl->set( 'defaultCategoryName'	, $defaultCategoryName );
			$tpl->set( 'content'			, $content );
			$tpl->set( 'blogger_id'			, $blogger_id );
			$tpl->set( 'draft'				, $draft );
			$tpl->set( 'isDraft'			, $isDraft );
			$tpl->set( 'isPending'			, $underApproval );
			$tpl->set( 'isEdit'				, $isEdit );
			$tpl->set( 'showDraftStatus'	, $showDraftStatus );
			$tpl->set( 'editorTitle' 		, $editorTitle );
			$tpl->set( 'meta' 				, $meta );
			$tpl->set( 'editor' 			, $editor );
			$tpl->set( 'trackbacks'			, $trackbacks );
			$tpl->set( 'categories' 		, $categories );
			$tpl->set( 'blog' 				, $blog );
			$tpl->set( 'user'				, $user );
			$tpl->set( 'isPrivate' 			, $isPrivate);
			$tpl->set( 'allowComment' 		, $allowComment);
			$tpl->set( 'subscription' 		, $allowSubscribe);
			$tpl->set( 'trackbacks'			, $trackbacks );
			$tpl->set( 'frontpage'			, $showFrontpage );
			$tpl->set( 'author'				, $author );
			$tpl->set( 'useImageManager'	, $useImageManager );
			$tpl->set( 'nestedCategories'	, $nestedCategories );
			$tpl->set( 'teams'				, $teams );
			$tpl->set( 'isSiteWide'			, $isSiteWide );
			$tpl->set( 'send_notification_emails'			, $sendEmails );



			// @since: 3.5
			// The unique external source and id.
			$tpl->set( 'blogSource'			, $blogSource );
			$tpl->set( 'source'				, $source );
			$tpl->set( 'uid'				, $uid );

			// @since: 3.6
			// Media manager options
			$tpl->set( 'session'			, JFactory::getSession() );

			// Load media manager and get info about the files.
			require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'mediamanager.php' );

			$mediamanager	= new EasyBlogMediaManager();
			$userFolders	= $mediamanager->getInfo( EasyBlogMediaManager::getAbsolutePath('' , 'user') , 'folders' );
			$userFiles		= $mediamanager->getInfo( EasyBlogMediaManager::getAbsolutePath('' , 'user') , 'files' );

			$sharedFolders	= $mediamanager->getInfo( EasyBlogMediaManager::getAbsolutePath( '' , 'shared' ) , 'folders' );
			$sharedFiles 	= $mediamanager->getInfo( EasyBlogMediaManager::getAbsolutePath( '' , 'shared' ) , 'files' );

			$tpl->set( 'userFolders' , $userFolders );
			$tpl->set( 'userFiles'	 , $userFiles );
			$tpl->set( 'sharedFolders' , $sharedFolders );
			$tpl->set( 'sharedFiles'	, $sharedFiles );

			// @rule: Test if the user is already associated with Flickr
			$oauth		= EasyBlogHelper::getTable( 'Oauth' );
			$associated	= $oauth->loadByUser( $my->id , EBLOG_OAUTH_FLICKR );
			$tpl->set( 'flickrAssociated' , $associated );


			echo $tpl->fetch( 'dashboard.write.php' );
		}
		else
		{
			$mainframe->redirect(EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard', false), JText::_('COM_EASYBLOG_NO_PERMISSION_TO_EDIT_BLOG'), 'error');
		}
	}

	/**
	 * Function to show user profile
	 */
	function profile()
	{
		$mainframe	= JFactory::getApplication();
		$my			= JFactory::getUser();
		$config		= EasyBlogHelper::getConfig();

		if(! EasyBlogHelper::isLoggedIn())
		{
			EasyBlogHelper::showLogin();
			return;
		}

		JHTML::_('behavior.formvalidation');

		$document	= JFactory::getDocument();

		$title					= EasyBlogHelper::getPageTitle( JText::_('COM_EASYBLOG_DASHBOARD_SETTINGS_PAGE_TITLE') );

		// @task: Set the page title
		parent::setPageTitle( $title , false , $config->get( 'main_pagetitle_autoappend' ) );

		$pathway	= $mainframe->getPathway();
		if( ! EasyBlogRouter::isCurrentActiveMenu( 'dashboard' ) )
			$pathway->addItem(JText::_('COM_EASYBLOG_DASHBOARD_BREADCRUMB'), EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard'));

		$pathway->addItem(JText::_('COM_EASYBLOG_DASHBOARD_SETTINGS_BREADCRUMB'), '');

		$profile	= EasyBlogHelper::getTable( 'Profile', 'Table' );
		$profile->load($my->id);

		$editor 	= JFactory::getEditor( $config->get('layout_editor' ) );

		$avatarIntegration = $config->get( 'layout_avatarIntegration', 'default' );

		$user		= EasyBlogHelper::getTable( 'Profile' , 'Table' );
		$user->load( $my->id );

		//default blogger permalink to username if not found.
		if(empty($profile->permalink))
		{
			$profile->permalink = $my->username;
		}

		$feedburner	= EasyBlogHelper::getTable( 'Feedburner' , 'Table' );
		$feedburner->load( $my->id );

		$adsense	= EasyBlogHelper::getTable( 'Adsense' , 'Table' );
		$adsense->load( $my->id );

		//get meta info for this blogger
		$model		= $this->getModel( 'Metas' );
		$meta		= $model->getMetaInfo(META_TYPE_BLOGGER, $my->id);

		$twitter	= EasyBlogHelper::getTable( 'Oauth' , 'Table' );
		$twitter->loadByUser( $my->id , EBLOG_OAUTH_TWITTER );

		$linkedin	= EasyBlogHelper::getTable( 'Oauth' , 'Table' );
		$linkedin->loadByUser( $my->id , EBLOG_OAUTH_LINKEDIN );

		$facebook	= EasyBlogHelper::getTable( 'Oauth' , 'Table' );
		$facebook->loadByUser( $my->id , EBLOG_OAUTH_FACEBOOK );

		//multi blogger themes
		$userparams		= EasyBlogHelper::getRegistry($profile->get('params'));
		$multithemes	= new stdClass();
		$multithemes->enable = $config->get('layout_enablebloggertheme', true);

		if( !is_array($config->get('layout_availablebloggertheme' ) ) )
		{
			$multithemes->availableThemes	= explode('|', $config->get('layout_availablebloggertheme' ) );
		}

		$multithemes->selectedTheme = $userparams->get('theme', 'global');

		echo $this->showToolbar( __FUNCTION__ , $user );

		// Add the breadcrumbs
		$breadcrumbs	= array( JText::_( 'COM_EASYBLOG_DASHBOARD_BREADCRUMB_EDIT_PROFILE' ) => '' );

		$editor			= JFactory::getEditor( $config->get('layout_editor' ) );

		$tpl	= new CodeThemes('dashboard');
		$tpl->set( 'editor'		, $editor );
		$tpl->set( 'breadcrumbs'		, $breadcrumbs );
		$tpl->set( 'google_profile_url' , $userparams->get( 'google_profile_url' ) );
		$tpl->set( 'show_google_profile_url' , $userparams->get( 'show_google_profile_url' ) );
		$tpl->set( 'facebook'	, $facebook );
		$tpl->set( 'linkedin' 	, $linkedin );
		$tpl->set( 'my'			, $my );
		$tpl->set( 'feedburner'	, $feedburner );
		$tpl->set( 'editor'		, $editor );
		$tpl->set( 'twitter'	, $twitter );
		$tpl->set( 'adsense'	, $adsense );
		$tpl->set( 'profile'	, $profile );
		$tpl->set( 'config'		, $config );
		$tpl->set( 'avatarIntegration', $avatarIntegration );
		$tpl->set( 'meta'		, $meta );
		$tpl->set( 'multithemes', $multithemes );

		echo $tpl->fetch( 'dashboard.profile.php' );

	}

	/*
	 * Responsible to display draft entries from the site.
	 *
	 * @params	null
	 * @return	null
	 */
	function drafts()
	{
		$mainframe	= JFactory::getApplication();
		$my			= JFactory::getUser();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$config	= EasyBlogHelper::getConfig();
		$document	= JFactory::getDocument();

		if(! EasyBlogHelper::isLoggedIn())
		{
			EasyBlogHelper::showLogin();
			return;
		}

		$title					= EasyBlogHelper::getPageTitle( JText::_('COM_EASYBLOG_DASHBOARD_DRAFTS_PAGE_TITLE') );

		// @task: Set the page title
		parent::setPageTitle( $title , false , $config->get( 'main_pagetitle_autoappend' ) );

		$user		= EasyBlogHelper::getTable( 'Profile' , 'Table' );
		$user->load( $my->id );

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'dashboard' ) )
			$this->setPathway( JText::_('COM_EASYBLOG_DASHBOARD_BREADCRUMB') , EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard') );

		$this->setPathway( JText::_('COM_EASYBLOG_DASHBOARD_DRAFTS_BREADCRUMB') , '' );

		$model		= $this->getModel( 'Drafts' );

		$filter		= JRequest::getWord( 'filter' , 'all' , 'GET' );
		$search		= JRequest::getVar( 'post-search' , false , 'POST' );
		$data		= $model->getData( true , $my->id );
		$pagination	= $model->getPagination();

		$entries	= array();
		for( $i = 0; $i < count( $data ); $i++ )
		{
			$entry		=& $data[ $i ];

			$draft		= EasyBlogHelper::getTable( 'Draft' , 'Table' );
			$draft->bind( $entry );

			$category	= EasyBlogHelper::getTable( 'Category' , 'Table' );
			$category->load( $entry->category_id );

			$draft->category	= $category->title;

			if( empty( $draft->tags ) )
			{
				$draft->tags	= array();
			}
			else
			{
				$draft->tags	= explode( ',' , $draft->tags );

				$draft->_tags = array();
				foreach($draft->tags as $tag)
				{
					$_tag = new stdClass();
					$_tag->title = $tag;
					$draft->_tags[] = $_tag;
				}
			}

			$draft->content			= $draft->intro . $draft->content;

			$entries[]	= $draft;
		}
		echo $this->showToolbar( __FUNCTION__ , $user );

		// Add the breadcrumbs
		$breadcrumbs 	= array( JText::_( 'COM_EASYBLOG_DASHBOARD_BREADCRUMB_DRAFTS' ) => '' );


		$tpl	= new CodeThemes('dashboard');
		$tpl->set( 'breadcrumbs' 	, $breadcrumbs );
		$tpl->set( 'filter'	, $filter );
		$tpl->set( 'user'	, $user );
		$tpl->set( 'entries' , $entries );
		$tpl->set( 'pagination' , $pagination );
		$tpl->set( 'search' , $search );

		echo $tpl->fetch( 'dashboard.drafts.php' );
	}

	/*
	 * Display list of blog entries created.
	 *
	 * @param	null
	 * @return	null
	 */
	function entries()
	{
		$mainframe	= JFactory::getApplication();
		$my			= JFactory::getUser();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$config		= EasyBlogHelper::getConfig();
		$document	= JFactory::getDocument();

		// @rule: Test if the user is currently logged in.
		if(! EasyBlogHelper::isLoggedIn())
		{
			EasyBlogHelper::showLogin();
			return;
		}

		$title					= EasyBlogHelper::getPageTitle( JText::_('COM_EASYBLOG_DASHBOARD_ENTRIES_PAGE_TITLE') );

		// @task: Set the page title
		parent::setPageTitle( $title , false , $config->get( 'main_pagetitle_autoappend' ) );

		$user		= EasyBlogHelper::getTable( 'Profile' , 'Table' );
		$user->load( $my->id );

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'dashboard' ) )
		{
			$this->setPathway( JText::_('COM_EASYBLOG_DASHBOARD_BREADCRUMB') , EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard') );
		}
		$this->setPathway( JText::_('COM_EASYBLOG_DASHBOARD_ENTRIES_BREADCRUMB') , '' );

		$model		= $this->getModel( 'Blogs' );
		$blogModel	= $this->getModel( 'Blog' );
		$oauthModel	= $this->getModel( 'Oauth' );

		$filter		= JRequest::getWord( 'filter' , 'all' , 'REQUEST' );
		$search		= JRequest::getVar( 'post-search' , false);

		// determine whether this user should retrive all blog posts from other bloggers as well or not.
		$queryType	= 'blogger';
		$queryID	= $my->id;

		if( !empty( $acl->rules->moderate_entry ) )
		{
			$queryType	= '';
			$queryID	= '';
		}

		// Detect the current post type.
		$postType	= JRequest::getVar( 'postType' , 'posts' );

		$entries	= $blogModel->getBlogsBy( $queryType , $queryID , 'latest' , 0 , $filter , $search, '', '', '', true , true , array() , array() , $postType );

		$entries	= EasyBlogHelper::formatBlog( $entries );
		$pagination	= $blogModel->getPagination();


		// @rule: Retrieve total standard blog posts
		$postCount			= $blogModel->getBlogPostsCount( $my->id );

		// @rule: Retrieve total micro posts
		$microPostCount 	= $blogModel->getMicroPostsCount( $my->id );

		// Social sharing
		$consumers	= array();
		$users		= array( 'twitter' => $my->id , 'facebook' => $my->id , 'linkedin' => $my->id );

		JTable::addIncludePath( EBLOG_TABLES );
		foreach( $users as $type => $id )
		{
			$consumer	= EasyBlogHelper::getTable( 'Oauth' , 'Table' );
			$consumer->loadByUser( $id , $type );

			$consumers[]= $consumer;
		}

		echo $this->showToolbar( __FUNCTION__ , $user );

		$urlType	='';

		if( !is_null( $postType ) )
		{
			$urlType	= '&postType=' . $postType;
		}

		$tpl		= new CodeThemes('dashboard');

		// Add the breadcrumbs
		$breadcrumbs 	= array( JText::_( 'COM_EASYBLOG_DASHBOARD_BREADCRUMB_POSTS' ) => '' );

		$tpl->set( 'breadcrumbs' 	, $breadcrumbs );
		$tpl->set( 'postType'		, $postType );
		$tpl->set( 'urlType' 		, $urlType );
		$tpl->set( 'postCount'		, $postCount );
		$tpl->set( 'microPostCount'	, $microPostCount );
		$tpl->set( 'consumers'		, $consumers );
		$tpl->set( 'filter'			, $filter );
		$tpl->set( 'user'			, $user );
		$tpl->set( 'entries' 		, $entries );
		$tpl->set( 'pagination' 	, $pagination );
		$tpl->set( 'search' 		, $search );
		$tpl->set( 'config' 		, $config );

		echo $tpl->fetch( 'dashboard.entries.php' );
	}

	/*
	 * Display recent comments posted on the current logged in user's blog
	 * entries.
	 * @param	null
	 * @return	null
	 */
	function comments()
	{
		$mainframe	= JFactory::getApplication();

		if(! EasyBlogHelper::isLoggedIn())
		{
			EasyBlogHelper::showLogin();
			return;
		}

		$config		= EasyBlogHelper::getConfig();
		$document	= JFactory::getDocument();


		$title					= EasyBlogHelper::getPageTitle( JText::_('COM_EASYBLOG_DASHBOARD_COMMENTS_PAGE_TITLE') );

		// @task: Set the page title
		parent::setPageTitle( $title , false , $config->get( 'main_pagetitle_autoappend' ) );


		$pathway	= $mainframe->getPathway();

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'dashboard' ) )
		{
			$pathway->addItem( JText::_('COM_EASYBLOG_DASHBOARD_BREADCRUMB'), EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard') );
		}

		$pathway->addItem( JText::_( 'COM_EASYBLOG_DASHBOARD_COMMENTS_BREADCRUMB' ), '' );

		$my			= JFactory::getUser();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$model		= $this->getModel( 'Comment' );
		$user		= EasyBlogHelper::getTable( 'Profile' , 'Table' );
		$user->load( $my->id );

		$search		= JRequest::getVar('post-search', '');
		$filter		= JRequest::getWord( 'filter' , 'all' , 'REQUEST' );
		$sort		= 'latest';

		if( $acl->rules->manage_comment )
		{
			$comments	= $model->getComments(0, '' , $sort, '', $search, $filter);
		}
		else
		{
			$comments	= $model->getComments(0, $my->id, $sort, '', $search, $filter);
		}

		$pagination	= $model->getPagination();

		JTable::addIncludePath( EBLOG_TABLES );
		for($i = 0; $i < count( $comments ); $i++)
		{
			$row			=& $comments[$i];
			$row->comment	= (JString::strlen($row->comment) > 150) ? JString::substr($row->comment, 0, 150) . '...' : $row->comment;
			$row->comment	= EasyBlogCommentHelper::parseBBCode($row->comment);
			$row->comment	= strip_tags( $row->comment , '<img>' );
			$row->isOwner	= EasyBlogHelper::isMineBlog($my->id, $row->blog_owner);

			$profile		= EasyBlogHelper::getTable( 'Profile', 'Table' );
			$profile->load( $row->created_by );
			$row->author	= $profile;
		}

		echo $this->showToolbar( __FUNCTION__ , $user );

		// Add the breadcrumbs
		$breadcrumbs 	= array( JText::_( 'COM_EASYBLOG_DASHBOARD_BREADCRUMB_COMMENTS' ) => '' );

		$theme	= new CodeThemes('dashboard');
		$theme->set( 'breadcrumbs'	, $breadcrumbs );
		$theme->set( 'search'	, $search );
		$theme->set( 'filter'	, $filter );
		$theme->set( 'comments' , $comments );
		$theme->set( 'pagination' , $pagination );

		echo $theme->fetch( 'dashboard.comments.php' );
	}

	/*
	 * Display all categories created by the user
	 * @param	null
	 * @return	null
	 */
	function categories()
	{
		$document	= JFactory::getDocument();
		$config	= EasyBlogHelper::getConfig();

		$title					= EasyBlogHelper::getPageTitle( JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_PAGE_TITLE') );

		// @task: Set the page title
		parent::setPageTitle( $title , false , $config->get( 'main_pagetitle_autoappend' ) );


		if( ! EasyBlogRouter::isCurrentActiveMenu( 'dashboard' ) )
			$this->setPathway( JText::_('COM_EASYBLOG_DASHBOARD_BREADCRUMB') , EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard') );

		$this->setPathway( JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_BREADCRUMB') , '' );

		if(! EasyBlogHelper::isLoggedIn())
		{
			EasyBlogHelper::showLogin();
			return;
		}

		$my				= JFactory::getUser();
		$user			= EasyBlogHelper::getTable( 'Profile' , 'Table' );
		$user->load( $my->id );

		$order			= JRequest::getVar( 'order' , 'latest' );
		$model			= $this->getModel( 'Categories' );
		$rows			= $model->getCategoriesByBlogger( $my->id , $order );
		$pagination		= $model->getPaginationByBlogger( $my->id );
		$categories		= array();

		$catRuleItems	= EasyBlogHelper::getTable( 'CategoryAclItem' , 'Table' );
		$categoryRules	= $catRuleItems->getAllRuleItems();

		$category		= EasyBlogHelper::getTable( 'Category' , 'Table' );
		$assignedACL	= $category->getAssignedACL();

		if( count( $rows ) > 0 )
		{
			JTable::addIncludePath( EBLOG_TABLES );
			foreach( $rows as $row )
			{
				$category	= EasyBlogHelper::getTable( 'Category' , 'Table' );
				$category->bind( $row );

				$categories[]	= $category;
			}
		}

		$parentList = EasyBlogHelper::populateCategories('', '', 'select', 'parent_id', '0');

		$editor 	= JFactory::getEditor( $config->get('layout_editor' ) );

		echo $this->showToolbar( __FUNCTION__ , $user );

		// Add the breadcrumbs
		$breadcrumbs 	= array( JText::_( 'COM_EASYBLOG_DASHBOARD_BREADCRUMB_CATEGORIES' ) => '' );

		$theme	= new CodeThemes('dashboard');
		$theme->set( 'breadcrumbs'	, $breadcrumbs );
		$theme->set( 'editor'		, $editor );
		$theme->set( 'order'		, $order );
		$theme->set( 'user'			, $user );
		$theme->set( 'categories'	, $categories );
		$theme->set( 'pagination'	, $pagination );
		$theme->set( 'config'		, $config );
		$theme->set( 'parentList'	, $parentList );
		$theme->set( 'categoryRules', $categoryRules );
		$theme->set( 'assignedACL'	, $assignedACL );


		echo $theme->fetch( 'dashboard.categories.php' );
	}

	/*
	 * Display category creation / edit page
	 * @param	null
	 * @return	null
	 */
	function category()
	{
		$document	= JFactory::getDocument();
		$config	= EasyBlogHelper::getConfig();
		$document->setTitle( JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_PAGE_TITLE') . EasyBlogHelper::getPageTitle($config->get('main_title')) );

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'dashboard' ) )
			$this->setPathway( JText::_('COM_EASYBLOG_DASHBOARD_BREADCRUMB') , EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard') );

		$this->setPathway( JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_BREADCRUMB') , '' );

		if(! EasyBlogHelper::isLoggedIn())
		{
			EasyBlogHelper::showLogin();
			return;
		}

		$catId		= JRequest::getVar( 'id' , '' );
		$category	= EasyBlogHelper::getTable( 'Category' , 'Table' );
		$category->load( $catId );

		$my			= JFactory::getUser();
		$user		= EasyBlogHelper::getTable( 'Profile' , 'Table' );
		$user->load( $my->id );


		$catRuleItems	= EasyBlogHelper::getTable( 'CategoryAclItem' , 'Table' );
		$categoryRules	= $catRuleItems->getAllRuleItems();

		$assignedACL	= $category->getAssignedACL();

		$parentList		= EasyBlogHelper::populateCategories('', '', 'select', 'parent_id', $category->parent_id);
		$editor			= JFactory::getEditor( $config->get('layout_editor' ) );

		echo $this->showToolbar( __FUNCTION__ , $user );

		$theme	= new CodeThemes('dashboard');
		$theme->set( 'editor'		, $editor );
		$theme->set( 'user'			, $user );
		$theme->set( 'config'		, $config );
		$theme->set( 'parentList'	, $parentList );
		$theme->set( 'categoryRules', $categoryRules );
		$theme->set( 'assignedACL'	, $assignedACL );
		$theme->set( 'category'		, $category );


		echo $theme->fetch( 'dashboard.category.php' );
	}


	/*
	 * Display a list of tags created by the user.
	 *
	 * @param	null
	 * @return	null
	 */
	function tags()
	{
		$config	= EasyBlogHelper::getConfig();
		$my			= JFactory::getUser();
		$document	= JFactory::getDocument();

		$title					= EasyBlogHelper::getPageTitle( JText::_('COM_EASYBLOG_DASHBOARD_TAGS_PAGE_TITLE') );

		// @task: Set the page title
		parent::setPageTitle( $title , false , $config->get( 'main_pagetitle_autoappend' ) );


		if( ! EasyBlogRouter::isCurrentActiveMenu( 'dashboard' ) )
			$this->setPathway( JText::_('COM_EASYBLOG_DASHBOARD_BREADCRUMB'), EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard' ) );

		$this->setPathway( JText::_( 'COM_EASYBLOG_DASHBOARD_TAGS_BREADCRUMB' ) );

		if(! EasyBlogHelper::isLoggedIn())
		{
			EasyBlogHelper::showLogin();
			return;
		}

		$user		= EasyBlogHelper::getTable( 'Profile' , 'Table' );
		$user->setUser($my);

		echo $this->showToolbar( __FUNCTION__ , $user );

		$model		= $this->getModel( 'Tags' );

		$sort		= 'post';
		$tags		= $model->getTagsByBlogger($my->id, false, $sort);

		// we do no want pagination on this page.
		$pagination	= null;

		$config		= EasyBlogHelper::getConfig();


		// Add the breadcrumbs
		$breadcrumbs 	= array( JText::_( 'COM_EASYBLOG_DASHBOARD_BREADCRUMB_TAGS' ) => '' );

		$tpl	= new CodeThemes('dashboard');
		$tpl->set( 'breadcrumbs'	, $breadcrumbs );
		$tpl->set( 'user'	, $user );
		$tpl->set( 'tags'	, $tags );
		$tpl->set( 'pagination'	, $pagination );
		$tpl->set( 'config' , $config );
		$tpl->set( 'filter'		, 'all' );
		echo $tpl->fetch( 'dashboard.tags.php' );
	}

	function review()
	{
		$this->pending(true);
	}


	/**
	 * Responsible to output a list of pending blog posts.
	 *
	 */
	function pending( $isReview = false )
	{
		$app	= JFactory::getApplication();
		$my		= JFactory::getUser();
		$acl	= EasyBlogACLHelper::getRuleSet();
		$config	= EasyBlogHelper::getConfig();
		$doc	= JFactory::getDocument();

		if( !EasyBlogHelper::isLoggedIn() )
		{
			$uri		= JFactory::getURI();
			$return		= $uri->toString();

			$component	= ( EasyBlogHelper::getJoomlaVersion() >= '1.6' ) ? 'com_users' : 'com_user';

			$url	= 'index.php?option='.$userComponent.'&view=login';
			$url	.= '&return='.base64_encode($return);

			$app->redirect( EasyBlogRouter::_( $url , false ) , JText::_('COM_EASYBLOG_YOU_MUST_LOGIN_FIRST') );
		}

		if( !$isReview )
		{
			if( empty($acl->rules->manage_pending) || empty($acl->rules->publish_entry))
			{
				EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' )  , 'error');
				$app->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard' , false ) );
				return;
			}
		}

		// @task: Set the page title.
		self::setPageTitle( JText::_( 'COM_EASYBLOG_DASHBOARD_PENDING_PAGE_TITLE' ) , null , true );

		// @task: Add breadcrumbs
		if( ! EasyBlogRouter::isCurrentActiveMenu( 'dashboard' ) )
		{
			$this->setPathway( JText::_('COM_EASYBLOG_DASHBOARD_BREADCRUMB'), EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard') );
		}
		$this->setPathway( JText::_('COM_EASYBLOG_DASHBOARD_PENDING_BREADCRUMB'), '' );

		// @task: Add the internal breadcrumbs for EasyBlog
		$breadcrumbs 	= array( JText::_( 'COM_EASYBLOG_DASHBOARD_BREADCRUMB_PENDING_YOUR_REVIEW' ) => '' );
		if( $isReview )
		{
			$breadcrumbs 	= array( JText::_( 'COM_EASYBLOG_DASHBOARD_BREADCRUMB_POST_UNDER_REVIEWS' ) => '' );
		}

		// @task: Render user object
		$user		= EasyBlogHelper::getTable( 'Profile' , 'Table' );
		$user->load( $my->id );

		$typeId		= ( $isReview ) ? $my->id : '';

		// @task: Get the current search value
		$search		= JRequest::getString( 'post-search' , false , 'POST' );

		// @task: Retrieve the data
		$model		= $this->getModel( 'Blog' );

		$entries	= $model->getPending( $typeId , 'latest' , 0 , $search , false , '' , true );
		$pagination	= $model->getPagination();
		$entries	= EasyBlogHelper::formatDraftBlog( $entries );

		for($i = 0; $i < count($entries); $i++)
		{
			$row				=& $entries[$i];
			$row->isOwner		= EasyBlogHelper::isMineBlog($my->id, $row->created_by);

			$profile			= EasyBlogHelper::getTable( 'Profile', 'Table' );
			$profile->load( $row->created_by );

			$row->author		= $profile;
			$row->displayName	= $profile->getName();
			$row->avatar		= $profile->getAvatar();
		}

		$oauthModel	= $this->getModel( 'Oauth' );
		$data		= $oauthModel->getConsumers( $my->id );
		$consumers	= array();

		if( count( $data ) > 0 )
		{
			for( $i = 0; $i < count( $data ); $i++ )
			{
				$row		=& $data[ $i ];
				$consumer	= EasyBlogHelper::getTable( 'Oauth' , 'Table' );
				$consumer->bind( $row );

				$consumers[]= $consumer;
			}
		}

		echo $this->showToolbar( __FUNCTION__ , $user );


		$tpl	= new CodeThemes('dashboard');
		$tpl->set( 'breadcrumbs', $breadcrumbs );
		$tpl->set( 'entries'	, $entries );
		$tpl->set( 'pagination' , $pagination );
		$tpl->set( 'search' 	, $search );
		$tpl->set( 'consumers'	, $consumers );
		$tpl->set( 'isReview'	, $isReview );

		echo $tpl->fetch( 'dashboard.pending.php' );
	}

	function teamblogs()
	{
		$mainframe	= JFactory::getApplication();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$config		= EasyBlogHelper::getConfig();
		$document	= JFactory::getDocument();
		$my			= JFactory::getUser();

		$title	= EasyBlogHelper::getPageTitle( JText::_('COM_EASYBLOG_DASHBOARD_TEAMBLOG_PAGE_TITLE') );

		// @task: Set the page title
		parent::setPageTitle( $title , false , $config->get( 'main_pagetitle_autoappend' ) );

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'dashboard' ) )
		{
			$this->setPathway( JText::_('COM_EASYBLOG_DASHBOARD_BREADCRUMB'), EasyBlogRouter::_('index.php?option=com_easyblog&view=dashboard') );
		}


		$this->setPathway( JText::_('COM_EASYBLOG_DASHBOARD_TEAMBLOG_BREADCRUMB'), '' );

		if(! EasyBlogHelper::isLoggedIn())
		{
			EasyBlogHelper::showLogin();
			return;
		}

		if(! EasyBlogHelper::isTeamAdmin())
		{
			EasyBlogHelper::showAccessDenied();
			return;
		}

		// get all the team request that this user assigned as admin.
		$tbRequest	= $this->getModel( 'TeamBlogs' );

		$myId		= (EasyBlogHelper::isSiteAdmin()) ? '' : $my->id;
		$requests	= $tbRequest->getTeamBlogRequest( $myId );
		$pagination	= $tbRequest->getPagination();

		// Add the breadcrumbs
		$breadcrumbs 	= array( JText::_( 'COM_EASYBLOG_DASHBOARD_BREADCRUMB_TEAM_REQUEST' ) => '' );

		$user		= EasyBlogHelper::getTable( 'Profile' , 'Table' );
		$user->load( $my->id );
		echo $this->showToolbar( __FUNCTION__ , $user );

		$tpl	= new CodeThemes('dashboard');
		$tpl->set( 'user'	, $user );
		$tpl->set( 'requests' , $requests );
		$tpl->set( 'pagination' , $pagination );
		$tpl->set( 'breadcrumbs'	, $breadcrumbs );
		echo $tpl->fetch( 'dashboard.teamblog.request.php' );
	}

	/**
	 * Micro blogging layout
	 *
	 * @since	3.0.7706
	 * @access	public
	 * @param	null
	 * @return 	null
	 */
	public function microblog()
	{
		$mainframe	= JFactory::getApplication();
		$config		= EasyBlogHelper::getConfig();
		$acl		= EasyBlogACLHelper::getRuleSet();

		if (!EasyBlogHelper::isLoggedIn()) {

			if ($config->get( 'main_login_provider' ) == 'easysocial') {

				$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

				if ($easysocial->exists()) {

					JFactory::getApplication()->redirect(FRoute::login(array(), false));

					return;
				}
				
			}

			EasyBlogHelper::showLogin();
			return;
		}

		$my		= JFactory::getuser();
		$user	= EasyBlogHelper::getTable( 'Profile' , 'Table' );
		$user->load( $my->id );

		// @rule: Test if microblogging is allowed
		if( !$config->get( 'main_microblog' ) )
		{
			EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' )  , 'error');
			JFactory::getApplication()->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard' , false ) );
		}

		// @rule: Test ACL if add entry is allowed
		if( !$acl->rules->add_entry )
		{
			$mainframe->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=dashboard' , false ) , JText::_('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_BLOG') );
			$mainframe->close();
		}

		$document		= JFactory::getDocument();

		$title			= EasyBlogHelper::getPageTitle( JText::_( 'COM_EASYBLOG_DASHBOARD_SHARE_A_STORY_TITLE' ) );

		// @task: Set the page title
		parent::setPageTitle( $title , false , $config->get( 'main_pagetitle_autoappend' ) );

		// Add toolbar to the output
		echo $this->showToolbar( __FUNCTION__ , $user );

		// Get active tabs
		$activeType		= JRequest::getVar( 'type' , 'text' );

		// Add the breadcrumbs
		$breadcrumbs	= array( JText::_( 'COM_EASYBLOG_DASHBOARD_BREADCRUMB_SHARE_STORY' ) => '' );

		// @task: Retrieve existing categories
		$categoryModel	= $this->getModel( 'Categories' );
		$categories		= EasyBlogHelper::populateCategories( '' , '' , 'select' , 'category_id' , '' , true , true , true );

		// @task: Retrieve existing tags
		$tagsModel		= $this->getModel( 'Tags' );
		$tags			= $tagsModel->getTags();

		$template		= new CodeThemes( 'dashboard' );
		$template->set( 'activeType'	, $activeType );
		$template->set( 'categories'	, $categories );
		$template->set( 'breadcrumbs'	, $breadcrumbs );
		$template->set( 'tags'			, $tags );
		echo $template->fetch( 'dashboard.microblog.php' );
	}

	/*
	 * List down bloggers from the site for the admin to change authors.
	 *
	 * @param	null
	 * @return	null
	 */
	public function listCategories()
	{
		// Anyone with moderate_entry acl is also allowed to change author.
		$acl		= EasyBlogACLHelper::getRuleSet();

		$model		= $this->getModel( 'Categories' );
		$rows		= $model->getCategoriesHierarchy();
		$pagination	= $model->getPagination();

		JFactory::getDocument()->addStyleSheet( rtrim( JURI::root() , '/') . '/components/com_easyblog/assets/css/reset.css' );

		for($i = 0; $i < count($rows); $i++ )
		{
			$item   =& $rows[$i];

			$category   = EasyBlogHelper::getTable('Category');
			$category->load( $item->id );
			$item->avatar	= $category->getAvatar();
		}

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
		$tpl->set( 'categories' 	, $rows );
		$tpl->set( 'pagination'		, $pagination );
		$tpl->set( 'orderDir'		, $orderDir );
		$tpl->set( 'order'			, $order );
		$tpl->set( 'search'			, $search );
		$tpl->set( 'filter_state'	, $filter_state );

		echo $tpl->fetch( 'dashboard.list.categories.php' );
		return;
	}
}
