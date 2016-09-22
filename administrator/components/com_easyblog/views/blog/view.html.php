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

require( EBLOG_ADMIN_ROOT . DIRECTORY_SEPARATOR . 'views.php');

class EasyBlogViewBlog extends EasyBlogAdminView
{
	function display($tpl = null)
	{
		// @rule: Test for user access if on 1.6 and above
		if( EasyBlogHelper::getJoomlaVersion() >= '1.6' )
		{
			if(!JFactory::getUser()->authorise('easyblog.manage.blog' , 'com_easyblog') )
			{
				JFactory::getApplication()->redirect( 'index.php' , JText::_( 'JERROR_ALERTNOAUTHOR' ) , 'error' );
				JFactory::getApplication()->close();
			}
		}

		// Load the front end language file.
		$lang		= JFactory::getLanguage();
		$lang->load( 'com_easyblog' , JPATH_ROOT );

		// Initial variables.
		$doc 		= JFactory::getDocument();
		$my 		= JFactory::getUser();
		$app 		= JFactory::getApplication();
		$acl 		= EasyBlogACLHelper::getRuleSet();
		$config 	= EasyBlogHelper::getConfig();

		// Load the JEditor object
		$editor = JFactory::getEditor( $config->get('layout_editor', 'tinymce') );


		// Enable datetime picker
		EasyBlogDateHelper::enableDateTimePicker();

		// required variable initiation.
		$meta   			= null;
		$blogContributed    = array();
		$tags               = null;
		$external           = '';
		$extGroupId         = '';

		// Event id state.
		$externalEventId	= '';

		//Load blog table
		$blogId		= JRequest::getVar( 'blogid' , '' );
		$blog		= EasyBlogHelper::getTable( 'Blog' , 'Table' );
		$blog->load( $blogId );

		$tmpBlogData		= EasyBlogHelper::getSession('tmpBlogData');
		$loadFromSession    = false;

		// Initialize default tags.
		$blog->tags = array();
		
		if(isset($tmpBlogData))
		{
			$loadFromSession    = true;
			$blog->bind($tmpBlogData);

			// reprocess the date offset here.
			$tzoffset       = EasyBlogDateHelper::getOffSet();

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


			if( isset( $tmpBlogData[ 'tags' ] ) )
			{
				$blog->tags		= $this->bindTags( $tmpBlogData[ 'tags' ] );
			}

			// metas
			$meta				= new stdClass();
			$meta->id			= '';
			$meta->keywords		= isset( $tmpBlogData['keywords'] ) ? $tmpBlogData['keywords'] : '';
			$meta->description 	= isset( $tmpBlogData['description'] ) ? $tmpBlogData['description'] : '';

			if(isset($tmpBlogData['blog_contribute']))
			{
				$blogContributed	= $this->bindContribute( $tmpBlogData[ 'blog_contribute' ] );
			}

			$contributionSource	= isset( $tmpBlogData['blog_contribute_source'] ) ? $tmpBlogData['blog_contribute_source'] : '';

			if( !empty( $contributionSource ) && $contributionSource != 'easyblog' )
			{
				$external           = true;
				$extGroupId         = $tmpBlogData[ 'blog_contribute' ];
				$externalEventId	= $tmpBlogData[ 'blog_contribute' ];
			}

			$blog->unsaveTrackbacks = '';
			if( !empty( $tmpBlogData['trackback'] ) )
			{
				$blog->unsaveTrackbacks	= $tmpBlogData['trackback'];
			}
		}


		$draft		= EasyBlogHelper::getTable( 'Draft' , 'Table' );
		$draft_id	= JRequest::getVar( 'draft_id' , '' );
		$isDraft    = false;

		$pending_approval   = JRequest::getVar( 'approval' , '' );

		if( !empty( $draft_id ) )
		{
			//first check if the logged in user have the required acl or not.
			if( empty($acl->rules->add_entry) || empty($acl->rules->publish_entry) || empty($acl->rules->manage_pending))
			{
				$message = JText::_('COM_EASYBLOG_BLOGS_BLOG_NO_PERMISSION_TO_CREATE_BLOG');
				$app->enqueueMessage( $message, 'error' );
				$app->redirect( JRoute::_('index.php?option=com_easyblog&view=blogs', false) );
			}

			$draft->load( $draft_id );
			$blog->load( $draft->entry_id );

			$blog->bind( $draft );

			$blog->tags		= $this->bindTags( explode( ',' , $draft->tags ) );
			$blog->newtags 	= $blog->tags;
			$tags           = $blog->tags;

			// metas
			$meta				= new stdClass();
			$meta->id			= '';
			$meta->keywords		= $draft->metakey;
			$meta->description	= $draft->metadesc;

			$blog->unsaveTrackbacks = '';
			if( !empty( $draft->trackbacks ) )
			{
				$blog->unsaveTrackbacks	= $draft->trackbacks;
			}

			if( $draft->blog_contribute )
			{
				$blogContributed	= $this->bindContribute( $draft->blog_contribute );
			}

			$blog->set( 'id' , $draft->entry_id );
			$blogId		= $blog->id;
			$isDraft	= true;
		}

		// set page title
		if ( !empty( $blogId ) )
		{
			$doc->setTitle( JText::_('COM_EASYBLOG_BLOGS_EDIT_POST') . ' - ' . $config->get('main_title') );
			$editorTitle 	= JText::_('COM_EASYBLOG_BLOGS_EDIT_POST');

			// check if previous status is not Draft
			if ( $blog->published != POST_ID_DRAFT )
			{
				$isEdit			= true;
			}
		}
		else
		{
			$doc->setTitle( JText::_('COM_EASYBLOG_BLOGS_NEW_POST') );
			$editorTitle 		= JText::_('COM_EASYBLOG_BLOGS_NEW_POST');

			if(!$loadFromSession && !$isDraft)
			{
				// set to 'publish' for new blog in backend.
				$blog->published 	= $config->get('main_blogpublishing', '1');
			}
		}

		$author = null;
		if(!empty($blog->created_by))
		{
			$creator	= JFactory::getUser($blog->created_by);
			$author		= EasyBlogHelper::getTable( 'Profile', 'Table' );
			$author->setUser( $creator );
			unset($creator);
		}
		else
		{
			$creator	= JFactory::getUser($my->id);
			$author		= EasyBlogHelper::getTable( 'Profile', 'Table' );
			$author->setUser( $creator );
			unset($creator);
		}

		//Get tag
		if( !$loadFromSession && !$isDraft )
		{
			$tagModel	= EasyBlogHelper::getModel( 'PostTag' , true );
			$tags		= $tagModel->getBlogTags($blogId);
		}

		$tagsArray = array();

		if ($tags)
		{
			foreach($tags as $data)
			{
				$tagsArray[] = $data->title;
			}
			$tagsString = implode(",", $tagsArray);
		}

		//prepare initial blog settings.
		$isPrivate    	= $config->get('main_blogprivacy', '0');
		$allowComment   = $config->get('main_comment', 1);
		$allowSubscribe	= $config->get('main_subscription', 1);
		$showFrontpage  = $config->get('main_newblogonfrontpage', 0);
		$sendEmails  	= $config->get('main_sendemailnotifications', 0);

		$isSiteWide		= (isset($blog->issitewide)) ? $blog->issitewide : '1';

		$tbModel 		= EasyBlogHelper::getModel( 'TeamBlogs', true );
		$teamBlogJoined = $tbModel->getTeamJoined($author->id);

		if(! empty($blog->id))
		{
			$isPrivate    	= $blog->private;
			$allowComment   = $blog->allowcomment;
			$allowSubscribe	= $blog->subscription;
			$showFrontpage	= $blog->frontpage;
			$sendEmails  	= $blog->send_notification_emails;

			//get user teamblog
			$teamBlogJoined 	= $tbModel->getTeamJoined($blog->created_by);

			if( !$isDraft )
				$blogContributed    = $tbModel->getBlogContributed($blog->id);
		}

		if( $loadFromSession || $isDraft)
		{
			$isPrivate    		= $blog->private;
			$allowComment   	= $blog->allowcomment;
			$allowSubscribe		= $blog->subscription;
			$showFrontpage		= $blog->frontpage;
			$sendEmails			= $blog->send_notification_emails;
		}

		if( count($blogContributed) > 0 && $blogContributed )
		{
			for($i = 0; $i < count($teamBlogJoined); $i++)
			{
				$joined = $teamBlogJoined[$i];

				if($joined->team_id == $blogContributed->team_id)
				{
					$joined->selected   = 1;
					continue;
				}
			}
		}

		//get all tags ever created.
		$newTagsModel 	= EasyBlogHelper::getModel( 'Tags' );

		if( isset( $blog->newtags ) )
		{
			$blog->newtags 	= array_merge( $blog->newtags , $newTagsModel->getTags() );
		}
		else
		{
			$blog->newtags 	= $newTagsModel->getTags();	
		}
		

		//get tags used in this blog post
		if( !$loadFromSession && !$isDraft && $blogId )
		{
			$tagsModel	= EasyBlogHelper::getModel( 'PostTag' );
			$blog->tags	= $tagsModel->getBlogTags( $blogId );
		}

		//@task: List all trackbacks
		$trackbacksModel	= EasyBlogHelper::getModel( 'TrackbackSent' );
		$trackbacks			= $trackbacksModel->getSentTrackbacks( $blogId );

		// get meta tags
		if( !$loadFromSession && !$isDraft )
		{
			$metaModel 		= EasyBlogHelper::getModel( 'Metas' );
			$meta 			= $metaModel->getPostMeta($blogId);
		}

		//perform some title string formatting
		$blog->title    = $this->escape($blog->title);

		$blogger_id = ( !isset($blog->created_by) ) ? $my->id   : $blog->created_by;

		$defaultCategory	= ( empty($blog->category_id) ) ? EasyBlogHelper::getDefaultCategoryId() : $blog->category_id;

		$category		= EasyBlogHelper::getTable( 'Category' );
		$category->load( $defaultCategory );

		$content	= $blog->intro;
		// Append the readmore if necessary
		if( !empty($blog->intro) && !empty( $blog->content ) )
		{
			$content	.=  '<hr id="system-readmore" />';
		}
		$content	.= $blog->content;

		//check if this is a external group contribution.
		$blog_contribute_source = 'easyblog';
		$external   			= false;
		$extGroupId 			= EasyBlogHelper::getHelper( 'Groups' )->getGroupContribution( $blog->id );
		$externalEventId		= EasyBlogHelper::getHelper( 'Event' )->getContribution( $blog->id );
		$extGroupName			= '';
		if( !empty($extGroupId) )
		{
			$external   			= $extGroupId;
			$blog_contribute_source = EasyBlogHelper::getHelper( 'Groups' )->getGroupSourceType();
			$extGroupName			= EasyBlogHelper::getHelper( 'Groups' )->getGroupContribution( $blog->id, $blog_contribute_source, 'name' );
		}

		if( !empty($externalEventId) )
		{
			$external   			= $externalEventId;
			$blog_contribute_source = EasyBlogHelper::getHelper( 'Event' )->getSourceType();
		}

		//site wide or team contribution
		$teamblogModel	= EasyBlogHelper::getModel( 'TeamBlogs' );
		$teams			= ( !empty($blog->created_by) ) ? $teamblogModel->getTeamJoined($blog->created_by) : $teamblogModel->getTeamJoined($my->id);

		$this->assignRef( 'teams'			, $teams );
		$this->assignRef( 'isDraft'			, $isDraft );

		$joomlaVersion	= EasyBlogHelper::getJoomlaVersion();
		$my				= JFactory::getUser();
		$blogger_id		= $my->id;

		$nestedCategories	= '';

		$categoryselecttype = ( $config->get( 'layout_dashboardcategoryselect') == 'multitier' ) ? 'select' : $config->get( 'layout_dashboardcategoryselect');
		if( $categoryselecttype == 'select' )
		{
			$nestedCategories	= EasyBlogHelper::populateCategories( '' , '' , 'select' , 'category_id', $blog->category_id , true , true , false );
		}

		// Load media manager and get info about the files.
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'mediamanager.php' );

		$mediamanager	= new EasyBlogMediaManager();
		$userFolders	= $mediamanager->getInfo( EasyBlogMediaManager::getAbsolutePath('' , 'user') , 'folders' );
		$userFiles		= $mediamanager->getInfo( EasyBlogMediaManager::getAbsolutePath('' , 'user') , 'files' );

		$sharedFolders	= $mediamanager->getInfo( EasyBlogMediaManager::getAbsolutePath( '' , 'shared' ) , 'folders' );
		$sharedFiles 	= $mediamanager->getInfo( EasyBlogMediaManager::getAbsolutePath( '' , 'shared' ) , 'files' );

		// @rule: Test if the user is already associated with Flickr
		$oauth		= EasyBlogHelper::getTable( 'Oauth' );
		$associated	= $oauth->loadByUser( $my->id , EBLOG_OAUTH_FLICKR );

		$jConfig 	= EasyBlogHelper::getJConfig();

		$this->set( 'flickrAssociated' , $associated );

		$this->assignRef( 'userFolders' , $userFolders );
		$this->assignRef( 'userFiles'	 , $userFiles );
		$this->assignRef( 'sharedFolders' , $sharedFolders );
		$this->assignRef( 'sharedFiles'	, $sharedFiles );
		$this->assignRef( 'jConfig'			, $jConfig );
		$this->assignRef( 'my'				, $my );
		$this->assignRef( 'content'			, $content );
		$this->assignRef( 'category'		, $category );
		$this->assignRef( 'blogger_id' 		, $blogger_id );
		$this->assignRef( 'joomlaversion' 	, $joomlaVersion );
		$this->assignRef( 'isEdit' 			, $isEdit );
		$this->assignRef( 'editorTitle' 	, $editorTitle );
		$this->assignRef( 'blog' 			, $blog );
		$this->assignRef( 'meta' 			, $meta );
		$this->assignRef( 'editor' 			, $editor );
		$this->assignRef( 'tagsString' 		, $tagsString );
		$this->assignRef( 'acl' 			, $acl );
		$this->assignRef( 'isPrivate' 		, $isPrivate );
		$this->assignRef( 'allowComment'	, $allowComment );
		$this->assignRef( 'subscription' 	, $allowSubscribe );
		$this->assignRef( 'frontpage' 		, $showFrontpage );
		$this->assignRef( 'trackbacks'		, $trackbacks );
		$this->assignRef( 'author'			, $author );
		$this->assignRef( 'nestedCategories'	, $nestedCategories );
		$this->assignRef( 'teamBlogJoined'		, $teamBlogJoined );
		$this->assignRef( 'isSiteWide'			, $isSiteWide );
		$this->assignRef( 'draft'				, $draft );
		$this->assignRef( 'config'			, $config );
		$this->assignRef( 'pending_approval'	, $pending_approval );
		$this->assignRef( 'external'	, $external );
		$this->assignRef( 'extGroupId'	, $extGroupId );
		$this->assignRef( 'externalEventId', $externalEventId );
		$this->assignRef( 'extGroupName', $extGroupName );
		$this->assignRef( 'blog_contribute_source', $blog_contribute_source );
		$this->assignRef( 'categoryselecttype', $categoryselecttype );
		$this->assignRef( 'send_notification_emails' 		, $sendEmails );

		parent::display($tpl);
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

	function registerToolbar()
	{
		if( !empty( $this->pending_approval ) )
		{
			JToolBarHelper::title( JText::sprintf( 'COM_EASYBLOG_PENDING_EDIT_PAGE_HEADING' ), 'blogs' );
			JToolBarHelper::custom('rejectBlog','save.png','save_f2.png', 'COM_EASYBLOG_REJECT_BUTTON', false);
			JToolBarHelper::custom('savePublish','save.png','save_f2.png', 'COM_EASYBLOG_APPROVE_BUTTON', false);
		}
		else
		{
			if( $this->blog->id != 0 )
			{
				JToolBarHelper::title( JText::sprintf( 'COM_EASYBLOG_BLOGS_EDITING_BLOG_TITLE' , $this->blog->title ), 'blogs' );
				JToolBarHelper::apply('saveApply');
				//JToolBarHelper::custom('savePublish','save.png','save_f2.png', 'COM_EASYBLOG_UPDATE_BUTTON', false);
				JToolBarHelper::save('savePublish');
			}
			else
			{
				JToolBarHelper::title( JText::_( 'COM_EASYBLOG_BLOGS_NEW_POST_TITLE' ), 'blogs' );
				JToolBarHelper::apply('saveApply');
				JToolBarHelper::save('savePublish');

				if( EasyBlogHelper::getJoomlaVersion() > '1.6' )
				{
					JToolBarHelper::save2new( 'savePublishNew' );
				}
				else
				{
					JToolBarHelper::save( 'savePublishNew' , JText::_( 'COM_EASYBLOG_SAVE_AND_NEW' ) );
				}
			}
		}
		JToolBarHelper::cancel();
	}

	function registerSubmenu()
	{
		return 'submenu.php';
	}
}
