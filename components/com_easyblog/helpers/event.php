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

require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR .'helper.php' );
jimport( 'joomla.filesystem.file' );

class EasyBlogEventHelper
{
	public function isEnabled()
	{
		// TODO: Some checking required here
		// Since we only support jomsocial now, load up their form
		if( JPluginHelper::isEnabled( 'system' , 'eventeasyblog' ) && $this->testExists( 'jomsocial' ) )
		{
			return true;
		}
		return false;
	}

	/**
	 * Returns the group form html
	 */
	public function getFormHTML( $uid = '0' , $blogSource = '')
	{
		$contents	= '';

		// TODO: Check whether to load groupjive,jomsocial or any other group collaboration tools here.

		// Since we only support jomsocial now, load up their form
		if( JPluginHelper::isEnabled( 'system' , 'eventeasyblog' ) && $this->testExists( 'jomsocial' ) )
		{
			$contents	= $this->jomsocialForm( $uid , $blogSource );
		}

		return $contents;
	}

	public function testExists( $source )
	{
		switch( $source )
		{
			case 'jomsocial':
				return JFile::exists( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php' );
			break;
		}
	}

	public function getSourceType()
	{
		$source = 'jomsocial';

		return $source;
	}

	/**
	 * This is triggered when a blog post is removed from the site.
	 *
	 * @param	int $postId
	 */
	public function deleteContribution( $postId )
	{
		$db		= EasyBlogHelper::db();

		$query	= 'DELETE FROM ' . $db->nameQuote( '#__easyblog_external' ) . ' '
				. 'WHERE ' . $db->nameQuote( 'post_id' ) . ' = ' . $db->Quote( $postId );
		$db->setQuery( $query );

		$db->Query();

		return true;
	}

	public function updateContribution( $postId , $ids , $source )
	{
		// Maybe post was being updated and the user changed the group contributions.
		// Delete any existing post relations.
		$this->deleteContribution( $postId );

		$table	= EasyBlogHelper::getTable( 'External' );

		if( !is_array( $ids ) )
		{
			$ids	= array( $ids );
		}

		foreach( $ids as $id )
		{
			$table->set( 'post_id' 	, $postId );
			$table->set( 'source'	, $source );
			$table->set( 'uid'		, $id );
		}

		return $table->store();
	}

	private function jomsocialForm( $uid = '0' , $blogSource )
	{
		$my		= JFactory::getUser();
		$file	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';


		if( !JFile::exists( $file ) )
		{
			return false;
		}

		require_once( $file );
		$model	= CFactory::getModel( 'Events' );

		$rows	= $model->getEvents( null , $my->id , null , null , false , false , null , null , CEventHelper::ALL_TYPES , 0 , 999999 );
		$events	= array();

		JTable::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'tables' );
		foreach( $rows as $row )
		{
			$event 		= JTable::getInstance( 'Event' , 'CTable' );
			$event->load( $row->id );

			$data			= new stdClass();
			$data->id		= $event->id;
			$data->title	= $event->title;
			$data->avatar	= $event->getAvatar();

			$events[]		= $data;
		}

		$theme				= new CodeThemes( 'dashboard' );
		$theme->set( 'blogSource' , $blogSource );
		$theme->set( 'external'		, $uid );
		$theme->set( 'selectedEvent', $uid );
		$theme->set( 'events'	, $events );

		return $theme->fetch( 'dashboard.write.events.jomsocial.php' );
	}

	public function addCommentStream( $blog , $comment , $external )
	{
		return $this->addCommentStreamJomsocial( $blog , $comment , $external );
	}

	/**
	 * Creates a stream item for the respective 3rd party plugin
	 *
	 * @param	TableBlog $blog
	 */
	public function addStream( $blog , $isNew , $key , $source )
	{
		// Since we only support jomsocial now, load up their form
		return $this->addStreamJomsocial( $blog , $isNew , $key , $source );
	}

	/**
	 * Sends a notification item for the respective 3rd party plugins
	 */
	public function sendNotifications( $blog , $isNew , $key , $source , $author )
	{
		return $this->sendNotificationsJomsocial( $blog , $isNew , $key , $source , $author );
	}

	private function sendNotificationsJomsocial( $blog , $isNew , $uid , $source , $author )
	{
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

		$config 						= EasyBlogHelper::getConfig();

		// @rule: Send email notifications out to subscribers.
		$author 			= EasyBlogHelper::getTable( 'Profile' );
		$author->load( $blog->created_by );

		$data[ 'blogTitle']				= $blog->title;
		$data[ 'blogAuthor']			= $author->getName();
		$data[ 'blogAuthorAvatar' ]		= $author->getAvatar();
		$data[ 'blogAuthorLink' ]		= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $author->id , false , true );
		$data[ 'blogAuthorEmail' ]		= $author->user->email;
		$data[ 'blogIntro' ]			= $blog->intro;
		$data[ 'blogContent' ]			= $blog->content;
		$data[ 'blogLink' ]				= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id='. $blog->id, false, true);

		$date							= EasyBlogDateHelper::dateWithOffSet( $blog->created );
		$data[ 'blogDate' ]				= EasyBlogDateHelper::toFormat( $date , '%A, %B %e, %Y' );

		// If blog post is being posted from the back end and SH404 is installed, we should just use the raw urls.
		$sh404exists	= EasyBlogRouter::isSh404Enabled();

		if( JFactory::getApplication()->isAdmin() && $sh404exists )
		{
			$data[ 'blogLink' ]			= JURI::root() . 'index.php?option=com_easyblog&view=entry&id=' . $blog->id;
			$data[ 'blogAuthorLink' ]	= JURI::root() . 'index.php?option=com_easyblog&view=blogger&layout=listings&id=' . $author->id;
		}

		if( is_array($uid) )
		{
			$uid	= $uid[0];
		}

		JTable::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'tables' );
		$event 		= JTable::getInstance( 'Event' , 'CTable' );
		$event->load( $uid );

		$members	= $event->getMembers( 1 , 99999 );
		$emails		= array();

		$jsCoreFile	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';
		foreach( $members as $member )
		{
			$user 			= CFactory::getUser( $member->id );

			// Do not send email to the author.
			if( $author->user->email != $user->email )
			{
				$obj 				= new stdClass();
				$obj->email 		= $user->email;
				$emails[]			= $obj;
			}
		}

		$notification	= EasyBlogHelper::getHelper( 'Notification' );
		$emailBlogTitle = JString::substr( $blog->title , 0 , $config->get( 'main_mailtitle_length' ) );
		$emailTitle 	= JText::sprintf( 'COM_EASYBLOG_EMAIL_TITLE_NEW_BLOG_ADDED_WITH_TITLE' ,  $emailBlogTitle ) . ' ...';
		$notification->send( $emails , $emailTitle , 'email.blog.new' , $data );
	}

	private function addCommentStreamJomsocial( $blog , $comment , $external )
	{
		$jsCoreFile	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';
		$config		= EasyBlogHelper::getConfig();

		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

		// We do not want to add activities if new blog activity is disabled.
		if( !$config->get( 'integrations_jomsocial_comment_new_activity' ) )
		{
			return false;
		}

		if( !JFile::exists( $jsCoreFile ) )
		{
			return false;
		}

		require_once( $jsCoreFile );

		JTable::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'tables' );
		$event				= JTable::getInstance( 'Group' , 'CTable' );
		$event->load( $external->uid );

		$config				= EasyBlogHelper::getConfig();
		$command			= 'easyblog.comment.add';

		$blogTitle			= JString::substr( $blog->title , 0 , 30 ) . '...';
		$blogLink			= EasyBlogRouter::getRoutedURL('index.php?option=com_easyblog&view=entry&id='. $comment->post_id, false, true);

		$content        = '';
		if($config->get('integrations_jomsocial_submit_content'))
		{
			$content		= $comment->comment;
			$content		= EasyBlogCommentHelper::parseBBCode( $content );
			$content		= nl2br( $content );
			$content		= strip_tags( $content );
			$content		= JString::substr( $content, 0 , $config->get( 'integrations_jomsocial_comments_length' ) );
		}

		$obj			= new stdClass();
		$obj->title		= JText::sprintf('COM_EASYBLOG_JS_ACTIVITY_COMMENT_ADDED' , $blogLink , $blogTitle );
		$obj->content	= ($config->get('integrations_jomsocial_submit_content')) ? $content : '';
		$obj->cmd 		= $command;
		$obj->actor   	= $comment->created_by;
		$obj->target  	= 0;
		$obj->app		= 'easyblog';
		$obj->cid		= $comment->id;
		$obj->eventid	= $event->id;

		if( $config->get( 'integrations_jomsocial_activity_likes' ) )
		{
			$obj->like_id   = $comment->id;
			$obj->like_type = 'com_easyblog.comments';
		}

		if( $config->get( 'integrations_jomsocial_activity_comments' ) )
		{
			$obj->comment_id    = $comment->id;
			$obj->comment_type  = 'com_easyblog.comments';
		}
		// add JomSocial activities
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add($obj);
	}

	private function addStreamJomsocial( $blog , $isNew , $uid , $source )
	{
		$jsCoreFile	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';
		$config		= EasyBlogHelper::getConfig();

		// Somehow the blog contribution is in an array.
		$uid		= $uid[0];

		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

		if( !JFile::exists( $jsCoreFile ) )
		{
			return false;
		}
		require_once( $jsCoreFile );

		$app	= JFactory::getApplication();
		$title	= JString::substr( $blog->title , 0 , 30 ) . '...';

		$easyBlogItemid	= '';

		if( $app->isAdmin() )
		{
			$easyBlogItemid	= EasyBlogRouter::getItemId('latest');
			$easyBlogItemid = '&Itemid=' . $easyBlogItemid;
		}

		$blogLink		= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry&id='. $blog->id . $easyBlogItemid, false, true);
		$my				= JFactory::getUser();

		$blogContent    = $blog->intro . $blog->content;
		$blogContent	= EasyBlogHelper::getHelper( 'Videos' )->strip( $blogContent );
		$pattern		= '#<img[^>]*>#i';
		preg_match( $pattern , $blogContent , $matches );

		// Remove all html tags from the content as we want to chop it down.
		$blogContent	= strip_tags( $blogContent );

		if( JString::strlen( $blogContent ) > $config->get( 'integrations_jomsocial_blogs_length', 250 ) )
		{
			$blogContent = JString::substr($blogContent, 0, $config->get( 'integrations_jomsocial_blogs_length', 250 ) ) . ' ...';
		}

		if( $matches )
		{
			$matches[0]		= JString::str_ireplace( 'img ' , 'img style="margin: 0 5px 5px 0;float: left; height: auto; width: 120px !important;"' , $matches[0 ] );
			$blogContent	= $matches[0] . $blogContent . '<div style="clear: both;"></div>';
		}
		$blogContent	.= '<div style="text-align: right;"><a href="' . $blogLink . '">' . JText::_( 'COM_EASYBLOG_CONTINUE_READING' ) . '</a></div>';

		$eventLink			= CRoute::_( 'index.php?option=com_community&view=events&task=viewevent&eventid=' . $uid );

		JTable::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'tables' );

		$event				= JTable::getInstance( 'Event' , 'CTable' );
		$event->load( $uid );

		$title				= JText::sprintf( 'COM_EASYBLOG_JS_ACTIVITY_EVENT_BLOG_ADDED' , $blogLink , $title , $eventLink , $event->title );
		$obj				= new stdClass();
		$obj->title			= $title;
		$obj->content		= $blogContent;
		$obj->cmd 			= 'event.blog.added';

		if( $config->get( 'integrations_jomsocial_activity_likes' ) )
		{
			$obj->like_id   	= $blog->id;
			$obj->like_type 	= 'com_easyblog';
		}

		if( $config->get( 'integrations_jomsocial_activity_comments' ) )
		{
			$obj->comment_id    = $blog->id;
			$obj->comment_type  = 'com_easyblog';
		}

		$obj->actor   		= $my->id;
		$obj->target  		= $uid;
		$obj->app			= 'easyblog';
		$obj->cid			= $uid;
		$obj->eventid		= $uid;

		// add JomSocial activities
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add($obj);
	}

	public function getContribution( $postId, $sourcetype = 'jomsocial', $type = 'id')
	{
		$db		= EasyBlogHelper::db();

		$externalTblName    = '';

		if( $sourcetype == 'jomsocial' )
		{
			$externalTblName    = '#__community_events';
		}

		$query  = '';

		if( $type == 'name' || $type == 'title' )
		{
			$query  = 'SELECT b.`title` FROM `#__easyblog_external` as a ';
			$query  .= ' INNER JOIN ' . $db->NameQuote( $externalTblName ) . ' as b ON a.`uid` = b.`id`';
		}
		else
		{
			$query  = 'SELECT `uid` FROM `#__easyblog_external` as a';
		}

		$query		.= ' WHERE ' . $db->nameQuote( 'source' ) . ' = ' . $db->Quote( $sourcetype . '.event' );
		$query		.= ' AND ' . $db->nameQuote( 'post_id' ) . ' = ' . $db->Quote( $postId );

		$db->setQuery( $query );
		$result = $db->loadResult();

		return $result;
	}
}
