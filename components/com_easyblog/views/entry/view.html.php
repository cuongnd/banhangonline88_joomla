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

jimport( 'joomla.application.component.view');

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'helper.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'image.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'date.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'comment.php' );
require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'adsense.php' );

class EasyBlogViewEntry extends EasyBlogView
{
	function display( $tmpl = null )
	{
		JPluginHelper::importPlugin( 'easyblog' );
		$dispatcher = JDispatcher::getInstance();
		$mainframe	= JFactory::getApplication();
		$document	= JFactory::getDocument();
		$config 	= EasyBlogHelper::getConfig();
		$my			= JFactory::getUser();
		$notice		= '';

		//for trigger
		$params		= $mainframe->getParams('com_easyblog');
		$limitstart	= JRequest::getInt('limitstart', 0, '');
		$blogId		= JRequest::getVar('id');

		if( JRequest::getInt( 'print' ) == 1 )
		{
			// Add noindex for print view by default.
			$document->setMetadata( 'robots' , 'noindex,follow' );
		}

		if( empty($blogId) )
		{
			$mainframe->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=latest' , false ) , JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND') );
			$mainframe->close();
		}

		if( $my->id <= 0 && $config->get( 'main_login_read' ) )
		{
			$mainframe->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $blogId . '&layout=login' , false ) );
			$mainframe->close();
		}

		$team = JRequest::getVar('team','');

		if(empty($team))
		{
			//try get from session.
			$team	= EasyBlogHelper::getSession('EASYBLOG_TEAMBLOG_ID');
		}

		// set meta tags for post
		EasyBlogHelper::setMeta( $blogId, META_TYPE_POST );

		$print = JRequest::getBool('print');

		if ($print)
		{
			$document->setMetaData( 'robots' , 'noindex, nofollow' );
		}

		$my 	= JFactory::getUser();
		$blog	= EasyBlogHelper::getTable( 'Blog', 'Table' );

		if( !$blog->load($blogId) )
		{
			$mainframe->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=latest' , false ) , JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND') );
			$mainframe->close();
		}

		if( !empty( $blog->robots ) )
		{
			$document->setMetaData( 'robots' , $blog->robots );
		}

		if( !empty( $blog->copyrights ) )
		{
			$document->setMetaData( 'rights' , $blog->copyrights );
		}

		//assign the teamid here.
		$checkIsPrivate = false;

		//check if blog is password protected.
		if($config->get('main_password_protect', true) && !empty($blog->blogpassword))
		{
			if(!EasyBlogHelper::verifyBlogPassword($blog->blogpassword, $blog->id))
			{
				$errmsg = '';

				$jSession = JFactory::getSession();
				if($jSession->has( 'PROTECTEDBLOG_'.$blog->id, 'EASYBLOG'))
				{
					$errmsg = JText::_('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_INVALID_PASSWORD');
				}

				$theme = new CodeThemes();
				$theme->set('id', $blog->id);
				$theme->set('return', base64_encode(JURI::getInstance()->toString()));
				$theme->set('errmsg', $errmsg);
				echo $theme->fetch( 'blog.protected.php' );
				return false;
			}
		}

		//if team id provided, then we need to check if the user belong to the team or not.
		if($blog->issitewide)
		{
			$checkIsPrivate = true;
		}
		else
		{
			if(empty($team))
			{
				// blog post is not sitewide and teamid is empty? this is not so right. need to check this post contributed to which team one more time.
				$team   = $blog->getTeamContributed();
			}

			/*
			 * if teamblog access set to 'member only' | 'registered user', team blog will supersede blog permision
			 * if teamblog access set to 'everyone' then blog's permission will supersede teamblog access (logged user vs guest)
			 */

			if(! empty($team))
			{
				$teamblog	= EasyBlogHelper::getTable( 'TeamBlog', 'Table' );
				$teamblog->load($team);

				if($teamblog->access == '1')
				{
					if(! EasyBlogHelper::isTeamBlogJoined($my->id, $team))
					{
						//show error.
						EasyBlogHelper::showAccessDenied('teamblog', $teamblog->access);
						return;
					}
				}
				else if($teamblog->access == '2')
				{
					if(! EasyBlogHelper::isLoggedIn())
					{
						EasyBlogHelper::showLogin();
						return;
					}
				}
				else
				{
					// if teamblog the access set to 'everyone' then blog permission will supersede teamblog access
					$checkIsPrivate = true;
				}

			}
			else
			{
				$checkIsPrivate = true;
			}
		}

		$blog->team_id 	= $team;

		//check if the blog permission set to private or public. if private, we
		//need to check if the user has login or not.
		if($checkIsPrivate)
		{
			$privacy	= $blog->isAccessible();

			if( !$privacy->allowed )
			{
				echo $privacy->error;
				return;
			}
		}

		// added checking for other statuses
		switch ( $blog->published )
		{
			case 0:
			case 2:
			case 3:
				// Unpublished post
				// Only Admin and blog owner can view this post
				if ( $my->id == $blog->created_by )
				{
					$notice = JText::_('COM_EASYBLOG_ENTRY_BLOG_UNPUBLISHED_VISIBLE_TO_OWNER');
				}
				elseif ( EasyBlogHelper::isSiteAdmin() )
				{
					$notice = JText::_('COM_EASYBLOG_ENTRY_BLOG_UNPUBLISHED_VISIBLE_TO_ADMIN');
				}
				else
				{
					EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND') );
					$mainframe->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=latest', false) );
				}
				break;

			case 5:
				// Trashed posts.
				EasyBlogHelper::setMessageQueue( JText::_('COM_EASYBLOG_ENTRY_BLOG_NOT_FOUND') );
				$mainframe->redirect( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=latest', false) );

				break;
			case 1:
			default:
				break;
		}

		//update the hits
		$blog->hit();
		$acl = EasyBlogACLHelper::getRuleSet();



		$pageTitle	= EasyBlogHelper::getPageTitle( $config->get('main_title'));
		if( empty( $pageTitle  ))
		{
			$document->setTitle( $blog->title );
		}
		else
		{
			$document->setTitle( $blog->title . ' - ' . $pageTitle );
		}

		// There is a possibility that the intro is hidden in the entry view, so we need to get this data.
		$rawIntroText   = $blog->intro;

		// @rule: Process microblog post
		if( $blog->source )
		{
			EasyBlogHelper::formatMicroBlog( $blog );
		}

		// process the video here if nessary
		$blog->intro	= EasyBlogHelper::getHelper( 'Videos' )->processVideos( $blog->intro );
		$blog->content	= EasyBlogHelper::getHelper( 'Videos' )->processVideos( $blog->content );

		// @rule: Process audio files.
		$blog->intro		= EasyBlogHelper::getHelper( 'Audio' )->process( $blog->intro );
		$blog->content		= EasyBlogHelper::getHelper( 'Audio' )->process( $blog->content );

		// @rule: Process adsense codes.
		$blog->intro	= EasyBlogGoogleAdsense::processsAdsenseCode( $blog->intro, $blog->created_by );
		$blog->content	= EasyBlogGoogleAdsense::processsAdsenseCode( $blog->content, $blog->created_by );


		// @trigger: onEasyBlogPrepareContent
		EasyBlogHelper::triggerEvent( 'easyblog.prepareContent' , $blog , $params , $limitstart );

		// @rule: Hide introtext if necessary
		if( $config->get( 'main_hideintro_entryview' ) && !empty( $blog->content ) )
		{
			$blog->intro	= '';
		}


		//onPrepareContent trigger start
		$blog->introtext	= $blog->intro;
		$blog->text 		= $blog->intro . $blog->content;

		// @trigger: onEasyBlogPrepareContent
		EasyBlogHelper::triggerEvent( 'prepareContent' , $blog , $params , $limitstart );

		$blog->intro		= $blog->introtext;
		$blog->content		= $blog->text;

		// @legacy: since 3.5 has blog images, we can remove this in the future.
		// Remove first image for featured blogs
		if( $blog->isFeatured() )
		{
			$blog->content		= EasyBlogHelper::removeFeaturedImage( $blog->content );
		}

		$isFeatured    		= EasyBlogHelper::isFeatured('post', $blog->id);

		/* Post Tags */
		$modelPT	= $this->getModel( 'PostTag' );
		$tags		= $modelPT->getBlogTags($blog->id);

		//page setup
		$blogHtml		= '';
		$commentHtml	= '';
		$blogHeader		= '';
		$blogFooter		= '';
		$adsenseHtml	= '';
		$trackbackHtml  = '';

		$blogger		= null;
		if($blog->created_by != 0)
		{
			$blogger 	= EasyBlogHelper::getTable( 'Profile', 'Table' );
			$blogger->load( $blog->created_by );
		}

		// @rule: Set the author object into the table.
		$blog->author 	= $blogger;
		$blog->blogger 	= $blogger;

		// @rule: Before any trigger happens, try to replace the gallery first and append it at the bottom.
		$blog->intro	= EasyBlogHelper::getHelper( 'Gallery' )->process( $blog->intro , $blog->created_by );
		$blog->content	= EasyBlogHelper::getHelper( 'Gallery' )->process( $blog->content , $blog->created_by );

		$blog->intro	= EasyBlogHelper::getHelper( 'Album' )->process( $blog->intro , $blog->created_by );
		$blog->content	= EasyBlogHelper::getHelper( 'Album' )->process( $blog->content , $blog->created_by );

		//onAfterDisplayTitle, onBeforeDisplayContent, onAfterDisplayContent trigger start
		$blog->event		= new stdClass();
		$blog->introtext	= $blog->intro;
		$blog->text			= $blog->content;

		// @trigger: onAfterDisplayTitle / onContentAfterTitle
		$results	= EasyBlogHelper::triggerEvent( 'afterDisplayTitle' , $blog , $params , $limitstart );
		$blog->event->afterDisplayTitle = JString::trim(implode("\n", $results));

		// @trigger: onBeforeDisplayContent / onContentBeforeDisplay
		$results	= EasyBlogHelper::triggerEvent( 'beforeDisplayContent' , $blog , $params , $limitstart );
		$blog->event->beforeDisplayContent = JString::trim(implode("\n", $results));

		// @trigger: onAfterDisplayContent / onContentAfterDisplay
		$results	= EasyBlogHelper::triggerEvent( 'afterDisplayContent' , $blog , $params , $limitstart );
		$blog->event->afterDisplayContent	= JString::trim(implode("\n", $results));

		$blog->intro		= $blog->introtext;
		$blog->content		= $blog->text;

		unset($blog->introtext);
		unset($blog->text);

		if($print)
		{
			$theme		= new CodeThemes();

			$theme->set('blog'		, $blog );
			$theme->set('tags'		, $tags );
			$theme->set('config'	, $config );
			$theme->set('blogger'	, $blogger );

			echo $theme->fetch( 'blog.read.print.php' );
			return;
		}

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'blogger', $blogger->id ) && $config->get( 'layout_blogger_breadcrumb') )
		{
			$this->setPathway( $blogger->getName() , $blogger->getLink() );
		}

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'entry', $blog->id ) )
		{
			$this->setPathway( $blog->title , '' );
		}

		$blogModel		= $this->getModel( 'Blog' );
		$theme			= new CodeThemes();

		// add checking if comment system disabled by site owner
		if ( $config->get('main_comment') && $blog->allowcomment )
		{
			// getting blog comments
			$commentModel		= $this->getModel( 'Comment' );
			$blogComments		= EasyBlogHelper::getHelper( 'Comment' )->getBlogComment( $blogId );
			$commtPagination	= EasyBlogHelper::getHelper( 'Comment' )->pagination;
			$comments			= array();

			if(! empty( $blogComments ) )
			{
				foreach ($blogComments as $comment)
				{
					$row 				= $comment;

					$row->comment   	= EasyBlogCommentHelper::parseBBCode($row->comment);


					if($config->get('comment_likes'))
					{
						$row->likesAuthor   = EasyBlogHelper::getLikesAuthors($row->id, 'comment', $my->id);
						$row->isLike   		= $commentModel->isLikeComment($row->id, $my->id);
					}
					else
					{
						$row->likesAuthor   = '';
						$row->isLike   		= 0;
					}
					$comments[] 	= $row;
				}
			}

			// compliant with the #comments at blog.item.comment.php
			$commentHtml	= ( $config->get('comment_jcomments' ) ) ? '' : '<a id="comments"></a>';
			$commentHtml	.= EasyBlogCommentHelper::getCommentHTML( $blog , $comments , $commtPagination );
		}
		$blog->totalComments	= EasyBlogHelper::getHelper( 'Comment' )->getCommentCount( $blog );

		//get related blog post
		$blogRelatedPost    = '';
		if($config->get('main_relatedpost', true))
		{
			$blogRelatedPost    = $blogModel->getRelatedBlog($blogId);
		}


		//get author's recent posts.
		$authorRecentPosts = '';
		if( $config->get('main_showauthorinfo') && $config->get('main_showauthorposts') ) 
		{
			$authorPostLimit 	= $config->get('main_showauthorpostscount');
			$authorRecentPosts 	= $blogModel->getBlogsBy( 'blogger', $blog->created_by, 'latest', $authorPostLimit );
		}

		// Facebook Like integrations
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'facebook.php' );
		$facebookLike	= EasyBlogFacebookLikes::getLikeHTML( $blog , $rawIntroText );

		$url			= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id=' . $blog->id , false , true );

		// @rule: Add opengraph tags if required.
		if( $config->get( 'main_facebook_opengraph' ) )
		{
			EasyBlogFacebookLikes::addOpenGraphTags( $blog , $rawIntroText );
		}

		// Add Twitter card details on page.
		EasyBlogHelper::getHelper( 'Twitter' )->addCard( $blog , $rawIntroText );

		// @task: Add canonical URLs.
		if( $config->get( 'main_canonical_entry') )
		{
			$canonicalUrl   = EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id=' . $blog->id , false , true, true );
			$document->addCustomTag( '<link rel="canonical" href="' . $canonicalUrl . '"/>' );
		}

		// @task: Add rel="nofollow" if necessary.
		if( $config->get( 'main_anchor_nofollow' ) )
		{
			$blog->content 	= EasyBlogHelper::addNoFollow( $blog->content );
		}

		$prevLink	= array();
		$nextLink	= array();

		// construct prev & next link
		//get blog navigation object
		if( $config->get( 'layout_navigation') )
		{
			$blogNav    = EasyBlogHelper::getBlogNavigation($blogId, $blog->created, $team, 'team'); //$team

			$prevLink = array();
			if ( !empty($blogNav['prev'] ) )
			{
				$prevLink['id']		=  $blogNav['prev'][0]->id;
				$prevLink['title']	=  (JString::strlen($blogNav['prev'][0]->title) > 50) ? JString::substr($blogNav['prev'][0]->title, 0, 50) . '...' : $blogNav['prev'][0]->title;
			}

			$nextLink = array();
			if ( !empty($blogNav['next'] ) )
			{
				$nextLink['id']		=  $blogNav['next'][0]->id;
				$nextLink['title']	=  (JString::strlen($blogNav['next'][0]->title) > 50) ? JString::substr($blogNav['next'][0]->title, 0, 50) . '...' : $blogNav['next'][0]->title;
			}
		}

		// @rule: Mark notifications item in EasyDiscuss when the blog entry is viewed
		if( $config->get( 'integrations_easydiscuss_notification_blog' ) )
		{
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->readNotification( $blog->id , EBLOG_NOTIFICATIONS_TYPE_BLOG );
		}

		if( $config->get( 'integrations_easydiscuss_notification_comment' ) )
		{
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->readNotification( $blog->id , EBLOG_NOTIFICATIONS_TYPE_COMMENT );
		}

		if( $config->get( 'integrations_easydiscuss_notification_rating' ) )
		{
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->readNotification( $blog->id , EBLOG_NOTIFICATIONS_TYPE_RATING );
		}


		//get social bookmark provider.
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'bookmark.php' );
		$bookmark	= EasyBlogBookmark::getHTML();

		// @task: As we are standardizing the admin tools, we fix the necessary properties here.
		$blog->isFeatured	= $isFeatured;

		$theme->set( 'currentURL'		, EasyBlogRouter::_( 'index.php?option=com_easyblog&view=latest') );
		$theme->set( 'facebookLike' 	, $facebookLike );
		$theme->set( 'notice'			, $notice );
		$theme->set( 'team'				, $team );
		$theme->set('blog'				, $blog );
		$theme->set('tags'				, $tags );
		$theme->set('blogger'			, $blogger );
		$theme->set('prevLink'			, $prevLink);
		$theme->set('nextLink'			, $nextLink);
		$theme->set('blogRelatedPost'	, $blogRelatedPost );
		$theme->set('authorRecentPosts'	, $authorRecentPosts );
		$theme->set('isFeatured'		, $isFeatured );
		$theme->set('isMineBlog'		, EasyBlogHelper::isMineBlog($blog->created_by, $my->id) );
		$theme->set( 'acl'				, $acl );
		$theme->set( 'url'				, $url );
		$theme->set( 'commentHTML'		, $commentHtml );
		$theme->set( 'bookmark'			, $bookmark );
		$theme->set( 'pdfLinkProperties', EasyBlogHelper::getPDFlinkProperties() );
		$theme->set( 'ispreview', false );

		// @task: trackbacks
		$trackbacks		= $blogModel->getTrackback( $blogId );
		$theme->set( 'trackbackURL'		, EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=trackback&post_id=' . $blog->id  , true , true ) );
		$theme->set( 'trackbacks'		, $trackbacks );


		//google adsense
		$adsense	= EasyBlogGoogleAdsense::getHTML( $blogger->id );

		$blogHeader     = $adsense->header;
		$blogFooter     = $adsense->footer;

		$theme->set( 'adsenseHTML' , $adsense->beforecomments );
		$blogHtml	= $theme->fetch( 'blog.read' . EasyBlogHelper::getHelper( 'Sources' )->getTemplateFile( $blog->source ) . '.php' );

		echo $blogHeader;
		echo $blogHtml;
		echo $blogFooter;
	}

	/**
	 * Displays a single latest blog entry.
	 *
	 * @since	3.5
	 * @author	Mark Lee <mark@stackideas.com>
	 */
	public function latest()
	{
		// Fetch the latest blog entry
		$model 	= $this->getModel( 'Blog' );

		// Get the current active menu's properties.
		$app		= JFactory::getApplication();
		$menu 		= $app->getMenu()->getActive();
		$inclusion	= '';

		if( is_object( $menu ) )
		{
			$params 	= EasyBlogHelper::getRegistry( $menu->params );
			$inclusion	= EasyBlogHelper::getCategoryInclusion( $params->get( 'inclusion' ) );
		}

		$items 	= $model->getBlogsBy( 'latest' , 0 , '' , 1 , EBLOG_FILTER_PUBLISHED , null , true , array() , false , false , true , array() , $inclusion );

		if( is_array( $items ) && !empty( $items ))
		{
			JRequest::setVar( 'id' , $items[ 0 ]->id );
			return $this->display();
		}

		echo JText::_( 'COM_EASYBLOG_NO_BLOG_ENTRY' );
	}

	public function login()
	{
		$theme	= new CodeThemes();
		$id		= JRequest::getInt( 'id' );

		$theme->set( 'return' , base64_encode( EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $id , false ) ) );
		echo $theme->fetch( 'blog.read.login.php' );
	}

	function preview()
	{
		JPluginHelper::importPlugin( 'easyblog' );
		$dispatcher = JDispatcher::getInstance();
		$mainframe	= JFactory::getApplication();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$config 	= EasyBlogHelper::getConfig();
		$document	= JFactory::getDocument();
		$my			= JFactory::getUser();
		$params		= $mainframe->getParams('com_easyblog');

		if(! EasyBlogHelper::isLoggedIn())
		{
			EasyBlogHelper::showLogin();
			return;
		}

		$draftId    = JRequest::getVar( 'draftid', '');
		$draft		= EasyBlogHelper::getTable( 'Draft' , 'Table' );
		$draft->load( $draftId );


		$blog       = EasyBlogHelper::getTable( 'Blog' , 'Table' );
		$blog->bind( $draft );

		$blogger	= null;
		if($blog->created_by != 0)
		{
			$blogger 	= EasyBlogHelper::getTable( 'Profile', 'Table' );
			$blogger->load( $blog->created_by );
		}

		// @rule: Set the author object into the table.
		$blog->author 	= $blogger;
		$blog->blogger 	= $blogger;

		$blogId		= ( empty( $draft->entry_id ) ) ? $draft->id : $draft->entry_id;
		$limitstart = '0';
		$notice     = '';
		$team       = '';

		$blog->tags		= empty( $draft->tags ) ? array() : $this->bindTags( explode( ',' , $draft->tags ) );

		// metas
		$meta				= new stdClass();
		$meta->id			= '';
		$meta->keywords		= $draft->metakey;
		$meta->description	= $draft->metadesc;

		$pageTitle	= EasyBlogHelper::getPageTitle($config->get('main_title'));
		$document->setTitle( $blog->title . $pageTitle );

		// process the video here if nessary
		$blog->intro	= EasyBlogHelper::getHelper( 'Videos' )->processVideos( $blog->intro );
		$blog->content	= EasyBlogHelper::getHelper( 'Videos' )->processVideos( $blog->content );

		// @rule: Process audio files.
		$blog->intro		= EasyBlogHelper::getHelper( 'Audio' )->process( $blog->intro );
		$blog->content		= EasyBlogHelper::getHelper( 'Audio' )->process( $blog->content );

		// @rule: Before any trigger happens, try to replace the gallery first and append it at the bottom.
		$blog->intro	= EasyBlogHelper::getHelper( 'Gallery' )->process( $blog->intro , $blog->created_by );
		$blog->content	= EasyBlogHelper::getHelper( 'Gallery' )->process( $blog->content , $blog->created_by );

		// Process jomsocial album's.
		$blog->intro	= EasyBlogHelper::getHelper( 'Album' )->process( $blog->intro , $blog->created_by );
		$blog->content	= EasyBlogHelper::getHelper( 'Album' )->process( $blog->content , $blog->created_by );

		// @trigger: onEasyBlogPrepareContent
		EasyBlogHelper::triggerEvent( 'easyblog.prepareContent' , $blog , $params , $limitstart );

		//onPrepareContent trigger start
		$blog->introtext	= $blog->intro;
		$blog->text			= $blog->intro . $blog->content;

		// @trigger: onEasyBlogPrepareContent
		EasyBlogHelper::triggerEvent( 'prepareContent' , $blog , $params , $limitstart );

		$blog->intro		= $blog->introtext;
		$blog->content		= $blog->text;

		$isFeatured    		= false;

		//page setup
		$blogHtml		= '';
		$commentHtml	= '';
		$blogHeader		= '';
		$blogFooter		= '';
		$adsenseHtml	= '';
		$trackbackHtml  = '';


		$blogger	= null;
		if($blog->created_by != 0)
		{
			$blogger 	= EasyBlogHelper::getTable( 'Profile', 'Table' );
			$blogger->load( $blog->created_by );
		}

		//onAfterDisplayTitle, onBeforeDisplayContent, onAfterDisplayContent trigger start
		$blog->event = new stdClass();

		// @trigger: onAfterDisplayTitle / onContentAfterTitle
		$results	= EasyBlogHelper::triggerEvent( 'afterDisplayTitle' , $blog , $params , $limitstart );
		$blog->event->afterDisplayTitle = JString::trim(implode("\n", $results));

		// @trigger: onBeforeDisplayContent / onContentBeforeDisplay
		$results	= EasyBlogHelper::triggerEvent( 'beforeDisplayContent' , $blog , $params , $limitstart );
		$blog->event->beforeDisplayContent = JString::trim(implode("\n", $results));

		// @trigger: onAfterDisplayContent / onContentAfterDisplay
		EasyBlogHelper::triggerEvent( 'afterDisplayContent' , $blog , $params , $limitstart );
		$blog->event->afterDisplayContent	= JString::trim(implode("\n", $results));

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'blogger', $blogger->id ) )
			$this->setPathway( $blogger->getName() , $blogger->getLink() );

		if( ! EasyBlogRouter::isCurrentActiveMenu( 'entry', $blog->id ) )
			$this->setPathway( $blog->title , '' );

		$blog->totalComments	= 0;

		// Facebook Like integrations
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'facebook.php' );
		$facebookLike	= EasyBlogFacebookLikes::getLikeHTML( $blog );

		$url	= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id=' . $blog->id , false , true );

		//get blog navigation object
		$blogNav    = EasyBlogHelper::getBlogNavigation($blog->id, $blog->created, $team, 'team'); //$team

		$prevLink = array();
		if ( !empty($blogNav['prev'] ) )
		{
			$prevLink['id']		=  $blogNav['prev'][0]->id;
			$prevLink['title']	=  (JString::strlen($blogNav['prev'][0]->title) > 50) ? JString::substr($blogNav['prev'][0]->title, 0, 50) . '...' : $blogNav['prev'][0]->title;
		}

		$nextLink = array();
		if ( !empty($blogNav['next'] ) )
		{
			$nextLink['id']		=  $blogNav['next'][0]->id;
			$nextLink['title']	=  (JString::strlen($blogNav['next'][0]->title) > 50) ? JString::substr($blogNav['next'][0]->title, 0, 50) . '...' : $blogNav['next'][0]->title;
		}

		// @rule: Hide introtext if necessary
		if( $config->get( 'main_hideintro_entryview' ) )
		{
			$blog->intro	= '';
		}

		//get social bookmark provider.
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'bookmark.php' );
		$bookmark	= EasyBlogBookmark::getHTML();


		$theme			= new CodeThemes();
		$theme->set( 'facebookLike' 	, $facebookLike );
		$theme->set( 'notice'			, $notice );
		$theme->set( 'blog'				, $blog );
		$theme->set( 'tags'				, $blog->tags );
		$theme->set( 'blogger'			, $blogger );
		$theme->set( 'prevLink'			, $prevLink);
		$theme->set( 'nextLink'			, $nextLink);
		$theme->set( 'blogRelatedPost'	, '' );
		$theme->set( 'isFeatured'		, $isFeatured );
		$theme->set( 'isMineBlog'		, true );
		$theme->set( 'acl'				, $acl );
		$theme->set( 'url'				, $url );
		$theme->set( 'commentHTML'		, $commentHtml );
		$theme->set( 'bookmark'			, $bookmark );
		$theme->set( 'pdfLinkProperties', EasyBlogHelper::getPDFlinkProperties() );
		$theme->set( 'ispreview', true );

		// @task: trackbacks
		$trackbacks		= '';
		$theme->set( 'trackbackURL'		, EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=trackback&post_id=' . $blog->id  , true , true ) );
		$theme->set( 'trackbacks'		, $trackbacks );

		//google adsense
		require_once( EBLOG_CLASSES . DIRECTORY_SEPARATOR . 'adsense.php' );
		$adsense	= EasyBlogGoogleAdsense::getHTML( $blogger->id );

		$blogHeader     = $adsense->header;
		$blogFooter     = $adsense->footer;

		$theme->set( 'adsenseHTML' , $adsense->beforecomments );
		$blogHtml	= $theme->fetch( 'blog.read.php' );

		echo $blogHeader;
		echo $blogHtml;
		echo $blogFooter;
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
}
