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
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'string.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'date.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'comment.php' );

class EasyBlogViewDashboard extends EasyBlogView
{
	var $err	= null;

	public function showVideoForm( $editorName )
	{
		$ajax	= new Ejax();
		$my		= JFactory::getUser();

		if( $my->id <= 0 )
		{
			$title		= JText::_('COM_EASYBLOG_INFO');
			$callback 	= JText::_('COM_EASYBLOG_NO_PERMISSION_TO_PUBLISH_OR_UNPUBLISH_COMMENT');
			$width		= '450';
			$height		= 'auto';
			$ajax->alert( $callback, $title, $width, $height );
			$ajax->send();
			return;
		}

		$theme		= new CodeThemes( true );
		$theme->set( 'editorName' , $editorName );
		$content	= $theme->fetch( 'ajax.dialog.videos.add.php' );

		$title	= JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_INSERT_VIDEO_DIALOG_TITLE' );

		$ajax->dialog( EasyBlogHelper::getHelper( 'DialogOptions' )->set( 'title' , $title )->set( 'content' , $content )->toObject() );

		return $ajax->send();
	}

	public function copyForm( $ids )
	{
		$ajax	= new Ejax();
		$my		= JFactory::getUser();

		if( $my->id <= 0 )
		{
			$title		= JText::_('COM_EASYBLOG_INFO');
			$callback 	= JText::_('COM_EASYBLOG_NOT_ALLOWED');
			$width		= '450';
			$height		= 'auto';
			$ajax->alert( $callback, $title, $width, $height );
			$ajax->send();
			return;
		}

		$categories	= EasyBlogHelper::populateCategories( '' , '' , 'select' , 'category_id', '' , true , true , true );

		$theme		= new CodeThemes( true );
		$theme->set( 'categories' , $categories );
		$theme->set( 'ids' , $ids );
		$content	= $theme->fetch( 'ajax.dialog.blog.copy.php' );

		$title	= JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_COPY_BLOG_DIALOG_TITLE' );

		$ajax->dialog( EasyBlogHelper::getHelper( 'DialogOptions' )->set( 'title' , $title )->set( 'content' , $content )->toObject() );

		return $ajax->send();
	}

	public function loadStream( $startdate )
	{
		$ajax	= new Ejax();
		$my		= JFactory::getUser();

		$data		= EasyBlogHelper::activityGet( $my->id, EBLOG_STREAM_NUM_ITEMS, $startdate );
		$activities     		= $data[0];
		$currentDateRange     	= $data[1];

		$nextStreamItem     	= EasyBlogHelper::activityHasNextItems( $my->id, EBLOG_STREAM_NUM_ITEMS, $currentDateRange['startdate']);

		if( empty( $nextStreamItem['startdate'] ) || empty( $currentDateRange['startdate'] ) )
		{
			$ajax->assign('stream-load', '');
		}
		else
		{
			$nextLoadLink   = '<a href="javascript:void(0);" onclick="eblog.stream.load(\'' . $currentDateRange['startdate'] . '\');">' . JText::_('COM_EASYBLOG_STREAM_LOAD_MORE') . '</a>';
			$ajax->assign('stream-load', $nextLoadLink);
		}

		$theme		= new CodeThemes( true );
		$theme->set( 'activities' , $activities );
		$content	= $theme->fetch( 'ajax.stream.items.php' );
		$ajax->append( 'stream-container', $content );

		return $ajax->send();
	}

	function imageBrowse()
	{
		$ajax	= new Ejax();
		$url = JURI::root() . '/index.php?option=com_media&view=images&tmpl=component&e_name=content';
		$ajax->loadUrl( $url );
		$ajax->send();
	}

	/**
	 * Generate proper permalink for a blog entry
	 **/
	function getPermalink( $value )
	{
		$ajax			= new Ejax();
		$permalink		= EasyBlogHelper::getPermalink( $value );

		$ajax->assign( 'permalink-url' , $permalink );
		$ajax->script( '$( "#edit-permalink" ).show();' );
		$ajax->script( '$( "#permalink-value" ).show();' );
		$ajax->value( 'permalink-data' , $permalink );

		$ajax->send();
	}

	/*
	 * AJAX method to publish or unpublish a comment item.
	 *
	 * @param	int	$id		The subject's id.
	 * @param	int	$status	The publishing status
	 * @return	string	JSON encoded string.
	 */
	public function publishComment( $ids , $status = '' )
	{
		$this->_publishComment( 'comment', $ids , $status);
	}

	function publishModerateComment( $ids , $status = '' )
	{
		$this->_publishComment( 'moderate', $ids , $status);
	}

	function _publishComment( $type, $ids , $status = '')
	{
		$ajax		= new Ejax();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$ids		= explode( ',' , $ids );
		$my			= JFactory::getUser();

		if( $my->id == 0 )
		{
			$title		= JText::_('COM_EASYBLOG_INFO');
			$callback 	= JText::_('COM_EASYBLOG_NO_PERMISSION_TO_PUBLISH_OR_UNPUBLISH_COMMENT');
			$width		= '450';
			$height		= 'auto';
			$ajax->alert( $callback, $title, $width, $height );
			$ajax->send();
			return;
		}

		JTable::addIncludePath( EBLOG_TABLES );

		foreach( $ids as $id )
		{
			$comment	= EasyBlogHelper::getTable( 'Comment' , 'Table' );
			$comment->load( $id );

			$blog		= EasyBlogHelper::getTable( 'Blog' , 'Table' );
			$blog->load( $comment->post_id );

			// @rule: Test if the current browser is allowed to do this or not.
			// check if the comments belong to the blog post the browser or not.
			if( $blog->created_by != $my->id && !EasyBlogHelper::isSiteAdmin() && empty( $acl->rules->manage_comment ) )
			{
				echo JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
				exit;
			}

			$wasPending	= $comment->published == EBLOG_COMMENT_STATUS_MODERATED;

			$comment->published		= !$comment->published;

			if( !empty( $status ) )
			{
				$comment->published	= $status == 'publish' ? true : false;
			}

			$comment->store( $wasPending );

			// @task: Send notification if neccessary
			if( $comment->published && !$comment->sent && $wasPending )
			{
				$comment->comment   = EasyBlogCommentHelper::parseBBCode($comment->comment);
				$comment->comment   = nl2br($comment->comment);

				// @rule: Process emails
				$comment->processEmails();

				//update the sent flag to sent
				$comment->updateSent();
			}

			//display message
			$message	= $comment->published ? JText::_( 'COM_EASYBLOG_DASHBOARD_COMMENTS_COMMENT_PUBLISHED_SUCCESS' ) : JText::_( 'COM_EASYBLOG_DASHBOARD_COMMENTS_COMMENT_UNPUBLISHED_SUCCESS' );

			if( $type == 'comment' )
			{
				if( $comment->published )
				{
					$ajax->script( '$( "#publishing-' . $comment->id . ' a" ).removeClass( "icon-unpublished" ).addClass( "icon-published" ).html( "' . JText::_( 'COM_EASYBLOG_PUBLISHED' ) . '" )' );
				}
				else
				{
					$ajax->script( '$( "#publishing-' . $comment->id . ' a" ).removeClass( "icon-published" ).addClass( "icon-unpublished" ).html( "' . JText::_( 'COM_EASYBLOG_UNPUBLISHED' ) . '" )' );
				}


				$ajax->script( '$( "#eblog-comment-item' . $comment->id . ' .ui-inmsg").html( "' . $message . '" ).addClass( "success" );' );
			}
			else if(  $type == 'moderate' )
			{
			    $ajax->script( '$( "#moderate-publishing-' . $comment->id . '").removeClass( "ispending" ).addClass( "ispublish" ).html("'. $message .'")' );
			}
		}
		return $ajax->send();
	}

	/*
	 * AJAX method to edit a comment
	 *
	 * @param	int		$id		The comment subject.
	 * @return	string	JSON encoded stiring.
	 */
	public function editComment( $id )
	{
	    $config     = EasyBlogHelper::getConfig();
	    $my			= JFactory::getUser();
		$ajax		= new Ejax();
		$acl		= EasyBlogACLHelper::getRuleSet();

		if( empty( $acl->rules->manage_comment ) || $my->id == 0 )
		{
			$ajax->alert( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_EDIT_COMMENT'), JText::_('COM_EASYBLOG_INFO'), '450', 'auto' );
			return $ajax->send();
		}

		JTable::addIncludePath( EBLOG_TABLES );
		$comment	= EasyBlogHelper::getTable( 'Comment' , 'Table' );
		$comment->load( $id );

		$blog		= EasyBlogHelper::getTable( 'Blog' , 'Table' );
		$blog->load( $comment->post_id );

		// @rule: Test if the user really can edit the entry.
		if( $blog->created_by != $my->id && !EasyBlogHelper::isSiteAdmin() && empty($acl->rules->edit_comment) )
		{
			echo JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
			exit;
		}

		$tpl = new CodeThemes('dashboard');
		$tpl->set('comment'	, $comment );


		$options		  = new stdClass();
		$options->title   = JText::_('COM_EASYBLOG_DASHBOARD_EDIT_COMMENT');
		$options->content = $tpl->fetch( 'ajax.dialog.comments.edit.php' );

		$ajax->dialog( $options );
		$ajax->send();
	}

	function updateComment( $post )
	{
		$ajax		= new Ejax();
		$mainframe	= JFactory::getApplication();
		$my			= JFactory::getUser();
	    $acl		= EasyBlogACLHelper::getRuleSet();

		if(empty($acl->rules->manage_comment) || $my->id == 0)
		{
			$title		= JText::_('COM_EASYBLOG_INFO');
			$width		= '450';
			$height		= 'auto';
			$callback 	= JText::_('COM_EASYBLOG_NO_PERMISSION_TO_UPDATE_COMMENT');
			$ajax->alert( $callback, $title, $width, $height );
			$ajax->send();
			return;
		}

		$commentId	= $post['commentId'];

		array_walk($post, array($this, '_trim') );
		if(! $this->_validateFields($post))
		{
			$callBack = 'ejax.load(\'dashboard\',\'viewComment\', \''.$commentId.'\', \'edit\')';
			//$ajax->assign('name' , 'hahaha');

			//re open the comment edit pop up dialog
			$ajax->dialog( $this->err[0], $callBack, JText::_('COM_EASYBLOG_ERROR') , '450', 'auto');
			$ajax->send();
			return;
		}

		//add here so that other component with the same comment.php jtable file will not get reference.
		JTable::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'administrator' . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'tables');
		$comment = EasyBlogHelper::getTable( 'Comment', 'Table' );
		$comment->bindPost($post);

		$blog		= EasyBlogHelper::getTable( 'Blog' , 'Table' );
		$blog->load( $comment->post_id );

		// Test if the current browser is allowed to do this or not.
		if( $blog->created_by != $my->id )
		{
			echo JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
			exit;
		}

		$now	= EasyBlogHelper::getDate();
		$comment->modified	= $now->toMySql();

		if( $comment->store() )
		{
			//now we need to update the parent content.
			$title		= '<a href="javascript: ejax.load(\'dashboard\',\'viewComment\', \''.$comment->id.'\', \'readOnly\');">'.$comment->title.'</a>';

			$commt->comment	  = (JString::strlen($comment->comment) > 150) ? JString::substr($comment->comment, 0, 150) . '...' : $comment->comment;
			$commt->comment   = EasyBlogCommentHelper::parseBBCode($comment->comment);
			$commt->comment   = nl2br($comment->comment);

			$content	= '<p id="comment-content-' . $comment->id . '">';
			$content	.= $comment->comment;
			$content	.= '</p>';

			$ajax->assign('comment-title-'.$comment->id, $title);
			$ajax->assign('comment-content-'.$comment->id, $content);
			$ajax->assign('comment-name-'.$comment->id, $comment->name);

		}
		else
		{

			$ajax->alert( JText::_('COM_EASYBLOG_COMMENT_FAILED_TO_SAVE'),  JText::_('COM_EASYBLOG_ERROR') , '450', 'auto');
		}
		$ajax->send();
		return;
	}

	function _trim(&$text)
	{
		$text = JString::trim($text);
	}

	function _validateFields($post)
	{
	    $config = EasyBlogHelper::getConfig();

	    if($config->get('comment_requiretitle', 0))
	    {
			if(JString::strlen($post['title']) == 0)
			{
				$this->err[0]	= JText::_('COM_EASYBLOG_COMMENT_TITLE_IS_EMPTY');
				$this->err[1]	= 'title';
				return false;
			}
		}

		if(JString::strlen($post['name']) == 0)
		{
			$this->err[0]	= JText::_('COM_EASYBLOG_COMMENT_NAME_IS_EMPTY');
			$this->err[1]	= 'name';
			return false;
		}

		if(JString::strlen($post['email']) == 0)
		{
			$this->err[0]	= JText::_('COM_EASYBLOG_COMMENT_EMAIL_IS_EMPTY');
			$this->err[1]	= 'email';
			return false;
		}

		if(JString::strlen($post['comment']) == 0)
		{
			$this->err[0]	= JText::_('COM_EASYBLOG_COMMENT_IS_EMPTY');
			$this->err[1]	= 'comment';
			return false;
		}

		return true;
	}

	public function togglePublish( $ids , $action = '' )
	{
		// Id may come in a comma separated values.
		$ids			= explode( ',' , $ids );

		$ajax		= new Ejax();
		$mainframe	= JFactory::getApplication();
	    $acl		= EasyBlogACLHelper::getRuleSet();
		$my			= JFactory::getUser();

		if( empty( $acl->rules->publish_entry ) || $my->id == 0 )
		{
			$ajax->alert( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_PUBLISH_OR_UNPUBLISH_BLOG'), JText::_('COM_EASYBLOG_ERROR') , '450', 'auto');
			$ajax->send();
			return;
		}

		JTable::addIncludePath( EBLOG_TABLES );

		// Need to ensure that whatever id passed in is owned by the current browser
		foreach( $ids as $id )
		{
			$blog	= EasyBlogHelper::getTable( 'Blog' , 'Table' );
			$blog->load( $id );

			if( $blog->created_by != $my->id && empty($acl->rules->moderate_entry))
			{
				echo JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
				exit;
			}
			$action	= empty( $action ) ? !$blog->published : $action;
			$ajax	= $this->_toggle( $blog->id , $ajax , $action );

			if( $blog->isnew && !$blog->private )
			{
				$blog->notify();
			}
		}

		return $ajax->send();
	}

	private function _toggle( $id , $ajax , $action = '' )
	{
		$my			= JFactory::getUser();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$blog		= EasyBlogHelper::getTable( 'Blog' , 'Table' );
		$blog->load( $id );

		if( ( $blog->created_by != $my->id && empty($acl->rules->moderate_entry) ) || $id == 0 )
		{
			$ajax->alert( JText::_('COM_EASYBLOG_NOT_ALLOWED'), JText::_('COM_EASYBLOG_ERROR') , '450', 'auto');
			$ajax->send();
			return;
		}

		// @rule: Only super admin can publish a scheduled , draft or pending post
		if( ( $blog->published == POST_ID_SCHEDULED || $blog->published == POST_ID_DRAFT || $blog->published == POST_ID_PENDING ) && !EasyBlogHelper::isSiteAdmin() )
		{
			$ajax->alert( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_PUBLISH_OR_UNPUBLISH_BLOG'), JText::_('COM_EASYBLOG_ERROR') , '450', 'auto');
			$ajax->send();
			return;
		}

		$blog->published	= !$blog->published;

		if( !empty( $action ) )
		{
			$newPublishStatus = '';

			if( $action == 'publish' )
			{
				$today		= EasyBlogHelper::getDate();

				// we check the publishing date here
				// if user set the future date then we will automatically change
				// the status to Schedule
				$publishing 	= EasyBlogHelper::getDate( $blog->publish_up);

				if ( $publishing->toUnix() > $today->toUnix() )
				{
					$blog->published = POST_ID_SCHEDULED;
				}
				else
				{
					$blog->published	= 1;
				}
			}
			else
			{
				$blog->published	= $action == 'publish' ? 1 : 0;
			}	
		}

		$blog->store();

		if( $blog->published == POST_ID_SCHEDULED)
		{
			// If new status is published, previously it was unpublished. Show unpublish stuffs now.
			//$ajax->script( '$( "#publishing-' . $blog->id . ' a" ).removeClass( "icon-unpublished" ).addClass( "icon-published" ).html( "' . JText::_( 'COM_EASYBLOG_DASHBOARD_ENTRIES_POST_IS_SCHEDULED' ) . '" )' );
			$ajax->script( '$( "#publishing-' . $blog->id . ' a" ).hide()' );
		}
		else if( $blog->published )
		{
			// If new status is published, previously it was unpublished. Show unpublish stuffs now.
			$ajax->script( '$( "#publishing-' . $blog->id . ' a" ).removeClass( "icon-unpublished" ).addClass( "icon-published" ).html( "' . JText::_( 'COM_EASYBLOG_PUBLISHED' ) . '" )' );
		}
		else
		{
			// If new status is unpublished, previously it was published. Show published stuffs now.
			$ajax->script( '$( "#publishing-' . $blog->id . ' a" ).removeClass( "icon-published" ).addClass( "icon-unpublished" ).html( "' . JText::_( 'COM_EASYBLOG_UNPUBLISHED' ) . '" )' );
		}

		$message	= '';

		if( $blog->published == POST_ID_SCHEDULED )
		{

			$message = JText::_( 'COM_EASYBLOG_DASHBOARD_ENTRIES_POST_IS_UNDER_SCHEDULED' );
		}
		else
		{
			$messsage = $blog->published ? JText::_( 'COM_EASYBLOG_DASHBOARD_ENTRIES_PUBLISHED_SUCCESS' ) : JText::_( 'COM_EASYBLOG_DASHBOARD_ENTRIES_UNPUBLISHED_SUCCESS' );
		}

		$ajax->script( '$( "#eb-entry-' . $blog->id . ' .ui-inmsg").html( "' . $message . '" ).addClass( "success" );' );
		return $ajax;
	}

	/**
	 * Ajax method to share content to social networks
	 *
	 * @param	int		$blogId		The blog's id.
	 * @param	int		$oauthId	The oauth id.
	 **/
	function ajaxSocialShare( $blogId , $type )
	{
		$ajax		= new Ejax();
		$config	= EasyBlogHelper::getConfig();
		$my			= JFactory::getUser();
		$userId     = $my->id;

		$oauth		= EasyBlogHelper::getTable( 'Oauth' , 'Table' );
		$oauth->loadByUser( $userId , $type );

		if( !$oauth->id )
		{
			$ajax->script('eblog.spinner.publish(\''.$blogId.'\', 0);');
			$ajax->alert( JText::_( 'COM_EASYBLOG_OAUTH_INVALID_ID' ) , JText::_('COM_EASYBLOG_INFO') , 450 , 'auto' );
			$ajax->send();
			return;
		}

		if( !$config->get( 'integrations_' . $oauth->type ) )
		{
			$ajax->script('eblog.spinner.publish(\''.$blogId.'\', 0);');
			$ajax->alert( JText::sprintf( 'COM_EASYBLOG_OAUTH_TYPE_DISABLED' , ucfirst( $oauth->type ) ) , JText::_('COM_EASYBLOG_INFO') , 450 , 'auto' );
			$ajax->send();
			return;
		}

	    $blog = EasyBlogHelper::getTable( 'blog', 'Table' );
		$blog->load( $blogId );

		if( $blog->published != POST_ID_PUBLISHED )
		{
			$ajax->script('eblog.spinner.publish(\''.$blogId.'\', 0);');
			$ajax->alert( JText::_( 'COM_EASYBLOG_DASHBOARD_ENTRIES_NOT_ABLE_TO_SOCIAL_SHARE' ) , JText::_('COM_EASYBLOG_INFO') , 450 , 'auto' );
			$ajax->send();
			return;
		}

		$key	= $config->get( 'integrations_' . $oauth->type . '_api_key' );
		$secret	= $config->get( 'integrations_' . $oauth->type . '_secret_key' );

		if( empty( $key ) || empty( $secret ) )
		{
			$ajax->script('eblog.spinner.publish("'.$blogId.'", 0);');
			$ajax->alert( JText::_( 'COM_EASYBLOG_OAUTH_KEY_INVALID' ) , JText::_('COM_EASYBLOG_INFO') , 450 , 'auto' );
			$ajax->send();
			return;
		}

		$callback	= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&controller=oauth&task=grant&type=' . $oauth->type , false , true );
		require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'oauth.php' );
		$consumer	= EasyBlogOauthHelper::getConsumer( $oauth->type , $key , $secret , $callback );
		$consumer->setAccess( $oauth->access_token );

		if(!EasyBlogSocialShareHelper::share( $blog , $oauth->type ) )
		{
			$ajax->script('eblog.spinner.publish("'.$blogId.'", 0);');
			$ajax->alert( JText::_( 'COM_EASYBLOG_OAUTH_ERROR_POSTING' ) , JText::_('COM_EASYBLOG_INFO') , 450 , 'auto' );
			$ajax->send();
			return;
		}

		// @todo: mark this as sent!
		$oauthPost	= EasyBlogHelper::getTable( 'OauthPost' , 'Table' );
		$oauthPost->loadByOauthId( $blog->id , $oauth->id );
		$date		= EasyBlogHelper::getDate();
		$oauthPost->post_id		= $blog->id;
		$oauthPost->oauth_id	= $oauth->id;
		$oauthPost->created		= $date->toMySQL();
		$oauthPost->modified		= $date->toMySQL();
		$oauthPost->sent		= $date->toMySQL();
		$oauthPost->store();

		// Update message
		$img 		= JURI::root() . '/components/com_easyblog/assets/icons/socialshare/' . JString::strtolower( $oauth->type ) . '.png';
		$ajax->script('$("#oauth_img_' . $oauth->type . '_' . $blog->id.'").attr("src", "'.$img.'");');
		$ajax->script('eblog.spinner.publish(\''.$blog->id.'\', 0);');
		$ajax->alert( JText::sprintf( 'COM_EASYBLOG_OAUTH_POST_SUCCESS' , ucfirst( $oauth->type ) ) , JText::_('COM_EASYBLOG_INFO') , 450 , 'auto' );
		$ajax->send();
	}

	/*
	 * Ajax method to edit a category item.
	 * @param	int	$categoryId		The category subject
	 * @return	string	JSON encoded string.
	 */
    function editCategory( $id )
    {
        $my     = JFactory::getUser();
		$ajax	= new Ejax();
		$acl	= EasyBlogACLHelper::getRuleSet();
		$config = EasyBlogHelper::getConfig();

		if( !$acl->rules->create_category || $my->id == 0 )
		{
			$ajax->alert( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_EDIT_CATEGORY'), JText::_('COM_EASYBLOG_INFO'), '450', 'auto' );
			return $ajax->send();;
		}

		$category	= EasyBlogHelper::getTable( 'Category' , 'Table' );
        $category->load( $id );

		// @rule: Make sure the category belongs to the user or if he is a super admin.
        if( $category->id && $category->created_by != $my->id && !EasyBlogHelper::isSiteAdmin() )
        {
	    	$ajax->alert( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , '' , '450' );
	    	return $ajax->send();
		}

        // #26 do not restrict the parent category creator.
		$parentList = EasyBlogHelper::populateCategories('', '', 'select', 'parent_id', $category->parent_id);

		$catRuleItems	= EasyBlogHelper::getTable( 'CategoryAclItem' , 'Table' );
		$categoryRules  = $catRuleItems->getAllRuleItems();

		// assigned acl
		$assignedACL    = $category->getAssignedACL();

		$tpl = new CodeThemes('dashboard');
		$tpl->set('category'	, $category );
		$tpl->set('config'		, $config );
		$tpl->set('parentList'	, $parentList );
		$tpl->set('categoryRules'	, $categoryRules );
		$tpl->set('assignedACL'	, $assignedACL );

		$options		  = new stdClass();
		$options->title   = JText::_( 'COM_EASYBLOG_DASHBOARD_CATEGORIES_DIALOG_EDIT_CATEGORY_TITLE' );
		$options->content = $tpl->fetch( 'ajax.dialog.category.edit.php' );

    	$ajax->dialog( $options );
		return $ajax->send();
    }

	/*
	 * Ajax method to edit a category item.
	 * @param	int	$categoryId		The category subject
	 * @return	string	JSON encoded string.
	 */
    function quickSaveCategory( $name )
    {
        $my     = JFactory::getUser();
		$ajax	= new Ejax();
		$acl	= EasyBlogACLHelper::getRuleSet();
		$config = EasyBlogHelper::getConfig();

		if( !$acl->rules->create_category || $my->id == 0 )
		{
			$ajax->alert( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_EDIT_CATEGORY'), JText::_('COM_EASYBLOG_INFO'), '450', 'auto' );
			return $ajax->send();
		}

		$catName    = $name;

	    if( empty($catName) )
	    {
			$ajax->alert( JText::_('COM_EASYBLOG_DASHBOARD_CATEGORIES_EMPTY_CATEGORY_TITLE_ERROR') , JText::_('COM_EASYBLOG_INFO'), '450', 'auto' );
			return $ajax->send();
	    }

	    $model  	= $this->getModel('Category');

	    if($model->isExist($catName))
	    {
			$ajax->alert( JText::sprintf('COM_EASYBLOG_DASHBOARD_CATEGORIES_ALREADY_EXISTS_ERROR', $catName) , JText::_('COM_EASYBLOG_INFO'), '450', 'auto' );
			return $ajax->send();
	    }

		$post					= array();
	    $post['title'] 			= $catName;
	    $post['created_by'] 	= $my->id;
	    $post['parent_id'] 		= '0';
	    $post['private'] 		= '0';
	    $post['description']	= '';

		$category	= EasyBlogHelper::getTable( 'Category' , 'Table' );
        $category->bind( $post );
        $category->published    = 1;

        //save the cat 1st so that the id get updated
        $category->store();

		$ajax->script('changeCategory("' . $category->id . '"," ' . $category->title . '")');

		return $ajax->send();
    }

    function saveTag( $post )
    {
		$ajax	= new Ejax();
		$acl	= EasyBlogACLHelper::getRuleSet();
		$my		= JFactory::getUser();

		if($acl->rules->create_tag && $my->id != 0)
		{
			$callback 	= "";

			$tagId		= $post['id'];
			$tagTitle	= $post['title'];

	        //check if category already exists
		    $model  = $this->getModel('Tags');

		    if($model->isExist($tagTitle, $tagId))
		    {
				$callback   = JText::_('COM_EASYBLOG_TAG_ALREADY_EXISTS');
		    }
		    else
		    {
				$tag	= EasyBlogHelper::getTable( 'Tag' , 'Table' );
		        $tag->load($tagId);

				if( $tag->created_by != $my->id )
				{
			    	$ajax->alert( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , '' , '450' );
			    	$ajax->send();
			    	return;
				}

		        $tag->bind($post);

		        $tag->store();
		        //update html label to new category title
		        $callback   = JText::_('COM_EASYBLOG_TAG_UPDATED_SUCCESSFULLY');
		        $ajax->assign("lbl-".$tagId, $tag->title);
		    }
		}
		else
		{
			$callback = JText::_('COM_EASYBLOG_NO_PERMISSION_TO_CREATE_TAG');
		}

		//returned message
		$title		= JText::_('COM_EASYBLOG_INFO');
		$width		= '450';
		$height		= 'auto';

		$ajax->alert( $callback, $title, $width, $height );
    	$ajax->send();
    }

    /*
     * Ajax method to edit a tag.
     * @param	int	$tagId	The tag subject.
     * @return	string	JSON encoded string.
     */
    function editTagDialog($tagId)
    {
        $my     = JFactory::getUser();
		$ajax	= new Ejax();
		$acl	= EasyBlogACLHelper::getRuleSet();

		if( !$acl->rules->create_tag )
		{
			$ajax->alert( JText::_('COM_EASYBLOG_NO_PERMISSION_TO_EDIT_TAG') , JText::_('COM_EASYBLOG_INFO'), '450', 'auto' );
			return $ajax->send();
		}

		$tag	= EasyBlogHelper::getTable( 'Tag' , 'Table' );
        $tag->load($tagId);

		if( $tag->created_by != $my->id )
		{
	    	$ajax->alert( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , '' , '450' );
	    	return $ajax->send();
		}

		$tpl = new CodeThemes( 'dashboard' );
		$tpl->set('tag', $tag );

		$options		  = new stdClass();
		$options->title   = JText::_('COM_EASYBLOG_DASHBOARD_EDIT_TAG');
		$options->content = $tpl->fetch( 'ajax.dialog.tags.edit.php' );

    	$ajax->dialog( $options );
    	return $ajax->send();
    }

    function checkPublishStatus($status, $unpublishDate)
    {
        $ajax			= new Ejax();
		$txOffset		= EasyBlogDateHelper::getOffSet();

		$today   		= EasyBlogHelper::getDate();

		$unpublishing 	= EasyBlogHelper::getDate( empty($unpublishDate) ? '' : $unpublishDate , $txOffset);

		if ( ($unpublishing->toUnix() <= $today->toUnix()) &&  ($status == POST_ID_PUBLISHED))
		{
			$options		  = new stdClass();
			$options->title   = JText::_('COM_EASYBLOG_CONFIRMATION');

			ob_start();
			?>

			<?php echo JText::_('COM_EASYBLOG_DASHBOARD_SAVE_BLOG_EXPIRED'); ?>

			<div class="dialog-actions">
				<input type="button" value="<?php echo JText::_('COM_EASYBLOG_CANCEL');?>" class="button" id="edialog-cancel" name="edialog-cancel" onclick="eblog.editor.cancelSubmit();" />
				<input type="button" value="<?php echo JText::_('COM_EASYBLOG_YES');?>" class="button" id="edialog-submit" name="edialog-submit" onclick="eblog.editor.postSubmit();" />
			</div>

			<?php
			$options->content = ob_get_contents();
	        ob_end_clean();

	        $ajax->dialog($options);
		}
		else
		{
		    $ajax->script("eblog.editor.postSubmit();");
		}

		$ajax->send();
		return;
    }

	function saveDraft( $params , $content , $intro )
	{
		$ajax	= new Ejax();
		$config = EasyBlogHelper::getConfig();

		if(! EasyBlogHelper::isLoggedIn())
		{
			echo JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
			exit;
		}

		$acl	= EasyBlogACLHelper::getRuleSet();

		if(empty($acl->rules->add_entry))
		{
			echo JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
			exit;
		}

		// Try to load this draft to see if it exists
		$draft	= EasyBlogHelper::getTable( 'Draft' , 'Table' );
		$draft->load( $params[ 'draft_id' ] );

		if( isset( $params[ 'id' ] ) && !empty( $params[ 'id' ] ) )
		{
			$draft->entry_id	= $params[ 'id' ];
			unset( $params[ 'id' ] );
		}

		$draft->intro	= $intro;
		$draft->content	= $content;

		$draft->bind( $params , true );

		if( isset( $params[ 'draft_id'] ) && !empty( $params[ 'draft_id' ] ) )
		{
			$draft->id	= $params[ 'draft_id' ];
		}
		$my		= JFactory::getUser();

		if( $draft->id && $draft->created_by != $my->id && !EasyBlogHelper::isSiteAdmin() )
		{
			echo JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
			exit;
		}

		$draft->store();


		$date	= EasyBlogDateHelper::dateWithOffSet( EasyBlogHelper::getDate()->toMySQL() );
		$date	= EasyBlogDateHelper::toFormat( $date , $config->get( 'layout_timeformat', '%I:%M:%S %p' ) );

		if( isset( $date[0] ) && $date[0] == 0 )
		{
			$date	= JString::substr( $date , 1 , JString::strlen( $date ) );
		}

		$ajax->assign( 'draft_status span' , JText::sprintf( 'COM_EASYBLOG_DRAFT_SAVED_TIME' , $date ) );
		$ajax->script( '$( "#draft_status" ).show();' );
		$ajax->value( 'draft_id' , $draft->id );
		$ajax->callback('');
		$ajax->send();
		return;

	}

	/*
	 * Called by the quick post form to quickly save a draft post.
	 *
	 * @param	array	An array of html input key/value pair.
	 * @param	string	The content of the item
	 * @return	string	JSON encoded string
	 */
	function quickSaveDraft( $params , $content  )
	{
		$ajax	= new Ejax();
		$my		= JFactory::getUser();
		$acl	= EasyBlogACLHelper::getRuleSet();

		if( empty( $acl->rules->add_entry ) || $my->id == 0 )
		{
			$ajax->script( 'eblog.dashboard.quickpost.notify("error" , "' . JText::_( 'COM_EASYBLOG_NO_PERMISSION_TO_CREATE_BLOG' ) . '");' );
			$ajax->script( 'eblog.loader.doneLoading( "quickpost-loading" );' );
			return $ajax->send();
		}

		if( empty( $params[ 'title' ] ) || $params[ 'title' ] == JText::_( 'COM_EASYBLOG_DASHBOARD_QUICKPOST_TITLE_INSTRUCTIONS' ) )
		{
			$ajax->script( 'eblog.dashboard.quickpost.notify("error" , "' . JText::_( 'COM_EASYBLOG_DASHBOARD_QUICKPOST_NO_TITLE_ERROR' ) . '");' );
			$ajax->script( 'eblog.loader.doneLoading( "quickpost-loading" );' );
			return $ajax->send();
		}

		if( empty( $params[ 'content' ] ) )
		{
			$ajax->script( 'eblog.dashboard.quickpost.notify("error" , "' . JText::_( 'COM_EASYBLOG_DASHBOARD_QUICKPOST_NO_CONTENT_ERROR' ) . '");' );
			$ajax->script( 'eblog.loader.doneLoading( "quickpost-loading" );' );
			return $ajax->send();
		}

		// Try to load this draft to see if it exists
		$draft	= EasyBlogHelper::getTable( 'Draft' , 'Table' );
		$draft->bind( $params , true );
		$draft->content		= $content;
		$draft->created_by	= $my->id;

		$my		= JFactory::getUser();
		$draft->store();

		$ajax->script( 'eblog.dashboard.quickpost.notify("success" , "' . JText::sprintf( 'COM_EASYBLOG_DASHBOARD_QUICKPOST_SAVED_DRAFT_SUCCESS' ) . '");' );
		$ajax->script( '$( "#reset-form" ).click();' );
		$ajax->script( 'eblog.editor.tag.clear("' . JText::_('COM_EASYBLOG_TAG_NO_SELECTED_TAGS') . '");' );
		$ajax->script( 'eblog.loader.doneLoading( "quickpost-loading" );' );
		return $ajax->send();

	}

	public function confirmDelete( $ids , $url )
	{
		$my			= JFactory::getUser();
		$ajax		= new Ejax();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$config		= EasyBlogHelper::getConfig();
		$ids		= explode( ',' , $ids );

		if( $my->id == 0 || empty( $acl->rules->delete_entry ) )
		{
			$options			= new stdClass();
			$options->content	= JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
			$ajax->dialog( $options );
			return $ajax->send();
		}

		// Need to ensure that whatever id passed in is owned by the current browser
		foreach( $ids as $id )
		{
			$blog	= EasyBlogHelper::getTable( 'Blog' , 'Table' );
			$blog->load( $id );

			if( $blog->created_by != $my->id && !EasyBlogHelper::isSiteAdmin() && empty( $acl->rules->moderate_entry ) )
			{
				echo JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
				exit;
			}
		}

		$themes		= new CodeThemes( 'dashboard' );
		$themes->set( 'ids' 		, implode( ',' , $ids ) );
		$themes->set( 'redirect' 	, base64_encode( $url ) );

		$options			= new stdClass();
		$options->title		= JText::_( 'COM_EASYBLOG_DASHBOARD_ENTRIES_DIALOG_CONFIRM_DELETE_TITLE' );
		$options->content	= $themes->fetch( 'ajax.dialog.entries.delete.php' );
		$ajax->dialog( $options );
		return $ajax->send();
	}

	/*
	 * Confirmation to delete tags dialog
	 *
	 * @param   Array   $ids    An array of tag id's.
	 * @param   string  $redirect   Where the form should be redirected to.
	 *
	 */
	public function confirmDeleteTag( $ids , $url )
	{
		$my			= JFactory::getUser();
		$ajax		= new Ejax();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$config		= EasyBlogHelper::getConfig();
		$ids		= explode( ',' , $ids );

		if( $my->id == 0 || empty( $acl->rules->create_tag ) )
		{
			$options			= new stdClass();
			$options->content	= JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
			$ajax->dialog( $options );
			return $ajax->send();
		}

		// Need to ensure that whatever id passed in is owned by the current browser
		foreach( $ids as $id )
		{
			$tag	= EasyBlogHelper::getTable( 'Tag' , 'Table' );
			$tag->load( $id );

			if( $tag->created_by != $my->id && !EasyBlogHelper::isSiteAdmin() )
			{
				EasyBlogHelper::setMessageQueue( JText::_( 'COM_EASYBLOG_NOT_ALLOWED' ) , 'error');
				$mainframe->redirect( EasyBlogRouter::_( $url , false ) );
				return;
			}
		}

		$themes		= new CodeThemes( 'dashboard' );
		$themes->set( 'id' 			, implode( ',' , $ids ) );
		$themes->set( 'redirect' 	, base64_encode( $url ) );

		$options			= new stdClass();
		$options->title		= JText::_( 'COM_EASYBLOG_DASHBOARD_TAGS_DIALOG_CONFIRM_DELETE_TITLE' );
		$options->content	= $themes->fetch( 'ajax.dialog.tag.delete.php' );
		$ajax->dialog( $options );
		return $ajax->send();
	}

	/*
	 * Ajax method to confirm the deletion of the category.
	 * @param	int	$id		The category subject
	 * @pram	string	$url	The url that we should redirect to once the deletion is completed.
	 * @return	string	JSON encoded string
	 */
	public function confirmDeleteCategory( $id , $url )
	{
		$my			= JFactory::getUser();
		$ajax		= new Ejax();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$config		= EasyBlogHelper::getConfig();

		if( $my->id == 0 || empty( $acl->rules->delete_category ) )
		{
			$options			= new stdClass();
			$options->content	= JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
			$ajax->dialog( $options );
			return $ajax->send();
		}

		JTable::addIncludePath( EBLOG_TABLES );
		$category	= EasyBlogHelper::getTable( 'Category' , 'Table' );
		$category->load( $id );

		if( $category->created_by != $my->id && !EasyBlogHelper::isSiteAdmin() )
		{
			$options			= new stdClass();
			$options->content	= JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
			$ajax->dialog( $options );
			return $ajax->send();
		}

		$themes		= new CodeThemes( 'dashboard' );
		$themes->set( 'id' , $id );
		$themes->set( 'redirect' , base64_encode( $url ) );

		$options			= new stdClass();
		$options->title		= JText::_( 'COM_EASYBLOG_DASHBOARD_CATEGORIES_DIALOG_CONFIRM_DELETE_TITLE' );
		$options->content	= $themes->fetch( 'ajax.dialog.category.delete.php' );
		$ajax->dialog( $options );
		return $ajax->send();
	}

	/*
	 * Ajax method to confirm the deletion of the comment.
	 * @param	int	$id		The comment subject
	 * @pram	string	$url	The url that we should redirect to once the deletion is completed.
	 * @return	string	JSON encoded string
	 */
	public function confirmDeleteComment( $id , $url )
	{
		$my			= JFactory::getUser();
		$ajax		= new Ejax();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$config		= EasyBlogHelper::getConfig();
		$ids		= explode( ',' , $id );

		if( $my->id == 0 || empty( $acl->rules->delete_comment ) )
		{
			$options			= new stdClass();
			$options->content	= JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
			$ajax->dialog( $options );
			return $ajax->send();
		}

		JTable::addIncludePath( EBLOG_TABLES );
		foreach( $ids as $id )
		{
			$comment	= EasyBlogHelper::getTable( 'Comment' , 'Table' );
			$comment->load( $id );

			// @rule: Check if the current browser is the author of the entry.
			if( $comment->created_by != $my->id && !EasyBlogHelper::isSiteAdmin() && empty( $acl->rules->delete_comment) )
			{
				$options			= new stdClass();
				$options->content	= JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
				$ajax->dialog( $options );
				return $ajax->send();
			}
		}

		$themes		= new CodeThemes( 'dashboard' );
		$themes->set( 'ids' 		, implode( ',' , $ids ) );
		$themes->set( 'redirect'		, base64_encode( $url ) );
		$options			= new stdClass();
		$options->title		= JText::_( 'COM_EASYBLOG_DASHBOARD_COMMENTS_DIALOG_CONFIRM_DELETE_TITLE' );
		$options->content	= $themes->fetch( 'ajax.dialog.comments.delete.php' );
		$ajax->dialog( $options );
		return $ajax->send();
	}

	/*
	 * Ajax method to confirm the deletion of the drafts.
	 * @param	int	$id		The comment subject
	 * @pram	string	$url	The url that we should redirect to once the deletion is completed.
	 * @return	string	JSON encoded string
	 */
	public function confirmDeleteDraft( $id )
	{
		$my			= JFactory::getUser();
		$ajax		= new Ejax();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$config		= EasyBlogHelper::getConfig();
		$ids		= explode( ',' , $id );

		if( $my->id == 0 )
		{
			$options			= new stdClass();
			$options->content	= JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
			$ajax->dialog( $options );
			return $ajax->send();
		}

		JTable::addIncludePath( EBLOG_TABLES );
		foreach( $ids as $id )
		{
			$draft		= EasyBlogHelper::getTable( 'Draft' , 'Table' );
			$draft->load( $id );

			// @rule: Check if the current browser is the author of the entry.
			if( $draft->created_by != $my->id )
			{
				$options			= new stdClass();
				$options->content	= JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
				$ajax->dialog( $options );
				return $ajax->send();
			}
		}

		$themes		= new CodeThemes( 'dashboard' );
		$themes->set( 'ids' 		, implode( ',' , $ids ) );

		$options			= new stdClass();
		$options->title		= JText::_( 'COM_EASYBLOG_DASHBOARD_DRAFTS_DIALOG_CONFIRM_DELETE_TITLE' );
		$options->content	= $themes->fetch( 'ajax.dialog.drafts.delete.php' );
		$ajax->dialog( $options );
		return $ajax->send();
	}

	/*
	 * Ajax method to confirm the deletion of the drafts.
	 * @param	int	$id		The comment subject
	 * @pram	string	$url	The url that we should redirect to once the deletion is completed.
	 * @return	string	JSON encoded string
	 */
	public function confirmDeleteAllDraft()
	{
		$my			= JFactory::getUser();
		$ajax		= new Ejax();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$config		= EasyBlogHelper::getConfig();

		if( $my->id == 0 )
		{
			$options			= new stdClass();
			$options->content	= JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
			$ajax->dialog( $options );
			return $ajax->send();
		}

		$themes		= new CodeThemes( 'dashboard' );

		$options			= new stdClass();
		$options->title		= JText::_( 'COM_EASYBLOG_DASHBOARD_DRAFTS_DIALOG_CONFIRM_DELETE_TITLE' );
		$options->content	= $themes->fetch( 'ajax.dialog.drafts.deleteall.php' );
		$ajax->dialog( $options );
		return $ajax->send();
	}

	/*
	 * Display a dialog confirm the blog entry approvals.
	 *
	 * @param	int		$ids		The specific blog id.
	 * @param	string	$url		The redirect url.
	 * @return	string	JSON encoded strings.
	 */
	public function confirmApproveBlog( $ids , $url )
	{
		$my			= JFactory::getUser();
		$ajax		= new Ejax();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$config		= EasyBlogHelper::getConfig();
		$ids		= explode( ',' , $ids );

		if( $my->id == 0 || empty( $acl->rules->manage_pending ) && !EasyBlogHelper::isSiteAdmin() )
		{
			$options			= new stdClass();
			$options->content	= JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
			$ajax->dialog( $options );
			return $ajax->send();
		}

		// if( $this->acl->rules->manage_pending && $entry->ispending == 1 && $entry->published != 3 )

		JTable::addIncludePath( EBLOG_TABLES );
		foreach( $ids as $id )
		{
			$draft		= EasyBlogHelper::getTable( 'Draft' , 'Table' );
			$draft->load( $id );

			// @rule: Check if the blog is really under pending
			if( $draft->pending_approval != 1 )
			{
				$options			= new stdClass();
				$options->content	= JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
				$ajax->dialog( $options );
				return $ajax->send();
			}
		}

		$themes		= new CodeThemes( 'dashboard' );
		$themes->set( 'ids' 		, implode( ',' , $ids ) );
		$themes->set( 'redirect'	, base64_encode( $url ) );
		$options			= new stdClass();
		$options->title		= JText::_( 'COM_EASYBLOG_DASHBOARD_BLOG_DIALOG_CONFIRM_APRROVE_TITLE' );
		$options->content	= $themes->fetch( 'ajax.dialog.pending.approve.php' );
		$ajax->dialog( $options );
		return $ajax->send();
	}

	function confirmRejectBlog( $ids )
	{
		$my			= JFactory::getUser();
		$ajax		= new Ejax();
		$acl		= EasyBlogACLHelper::getRuleSet();
		$config		= EasyBlogHelper::getConfig();
		$ids		= explode( ',' , $ids );

		if( $my->id == 0 || (empty( $acl->rules->manage_pending ) && empty( $acl->rules->publish_entry ) && !EasyBlogHelper::isSiteAdmin() ) )
		{
			$options			= new stdClass();
			$options->content	= JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
			$ajax->dialog( $options );
			return $ajax->send();
		}

		JTable::addIncludePath( EBLOG_TABLES );

		foreach( $ids as $id )
		{
			$blog		= EasyBlogHelper::getTable( 'Draft' , 'Table' );
			$blog->load( $id );

			// @rule: Check if the blog is really under pending
			if( $blog->pending_approval != 1 )
			{
				$options			= new stdClass();
				$options->content	= JText::_( 'COM_EASYBLOG_NOT_ALLOWED' );
				$ajax->dialog( $options );
				return $ajax->send();
			}
		}

		$themes		= new CodeThemes( 'dashboard' );
		$themes->set( 'ids' 		, implode( ',' , $ids ) );
		$themes->set( 'redirect'	, base64_encode( 'index.php?option=com_easyblog&view=dashboard&layout=pending' ) );
		$options			= new stdClass();
		$options->title		= JText::_( 'COM_EASYBLOG_DASHBOARD_BLOG_DIALOG_CONFIRM_REJECT_TITLE' );
		$options->content	= $themes->fetch( 'ajax.dialog.blog.reject.php' );
		$ajax->dialog( $options );
		return $ajax->send();
	}

	function updateDisplayDate( $eleId, $dateString)
	{
	    $ajax		= new Ejax();
	    $config = EasyBlogHelper::getConfig();

		$date 			= new JDate( $dateString );
		$now 			= EasyBlogDateHelper::toFormat( $date, $config->get( 'layout_dateformat' ) );

	    $ajax->assign( 'datetime_' . $eleId . ' .datetime_caption',  $now);
	    return $ajax->send();
	}
}
