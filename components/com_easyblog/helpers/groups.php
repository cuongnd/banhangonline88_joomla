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

class EasyBlogGroupsHelper
{
	public function useGroups()
	{
		// TODO: Some checking required here
		// Since we only support jomsocial now, load up their form
		if( JPluginHelper::isEnabled( 'system' , 'groupeasyblog' ) && $this->testExists( 'jomsocial' ) )
		{
			return true;
		}
		return false;
	}

	/**
	 * Returns the group form html
	 */
	public function getFormHTML( $external, $groupId = '0' , $blogSource = '', $isPending = 0 )
	{
		$contents	= '';

		// TODO: Check whether to load groupjive,jomsocial or any other group collaboration tools here.

		// Since we only support jomsocial now, load up their form
		if( JPluginHelper::isEnabled( 'system' , 'groupeasyblog' ) && $this->testExists( 'jomsocial' ) )
		{
			$contents	= $this->jomsocialForm( $external, $groupId , $blogSource, $isPending );
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

	public function getGroupSourceType()
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

		$query	= 'DELETE FROM ' . $db->nameQuote( '#__easyblog_external_groups' ) . ' '
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

		$table	= EasyBlogHelper::getTable( 'ExternalGroup' );

		if( !is_array( $ids ) )
		{
			$ids	= array( $ids );
		}

		foreach( $ids as $id )
		{
			$table->set( 'post_id' 	, $postId );
			$table->set( 'source'	, $source );
			$table->set( 'group_id'	, $id );
		}

		return $table->store();
	}

	private function jomsocialForm( $external, $groupId = '0' , $blogSource = '', $isPending = 0 )
	{
		$my		= JFactory::getUser();
		$file	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';

		//extra checking incase somewhere still passing empty blogSource.
		$blogSource = ( empty( $blogSource ) ) ? 'group' : $blogSource;

		if( !JFile::exists( $file ) )
		{
			return false;
		}

		require_once( $file );
		$model	= CFactory::getModel( 'groups' );
		if( EasyBlogHelper::isSiteAdmin() && $isPending)
		{
			$rows	= $model->getAllGroups();
		}
		else
		{
			$rows	= $model->getGroups( $my->id , null , false );
		}

		$groups	= array();

		JTable::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'tables' );
		foreach( $rows as $row )
		{
			$group		= JTable::getInstance( 'Group' , 'CTable' );
			$group->load( $row->id );

			$data			= new stdClass();
			$data->id		= $group->id;
			$data->title	= $group->name;
			$data->avatar	= $group->getAvatar();

			$groups[]		= $data;
		}

		$theme		= new CodeThemes( 'dashboard' );

		$theme->set( 'blogSource' , $blogSource );
		$theme->set( 'external', $external );
		$theme->set( 'groups' , $groups );
		$theme->set( 'groupId' , $groupId );

		return $theme->fetch( 'dashboard.write.groups.jomsocial.php' );
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

	private function sendNotificationsJomsocial( $blog , $isNew , $key , $source , $author )
	{
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

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

		// Get group members emails
		if( !class_exists( 'CommunityModelGroups' ) )
		{
			jimport( 'joomla.application.component.model' );
			JLoader::import( 'groups' , JPATH_ROOT . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'models' );
		}

		$model		= JModelLegacy::getInstance( 'Groups' , 'CommunityModel' );

		if( is_array($key) )
		{
			$key	= $key[0];
		}

		if( !method_exists( $model , 'getAllMember' ) )
		{
			// Snippet taken from getAllMember
			$db		= EasyBlogHelper::db();
			$query	= 'SELECT a.'.$db->nameQuote('memberid').' AS id, a.'.$db->nameQuote('approved').' , b.'.$db->nameQuote('name').' as name , a.'. $db->nameQuote('permissions') .' as permission FROM '
					. $db->nameQuote('#__community_groups_members') . ' AS a '
					. ' INNER JOIN ' . $db->nameQuote('#__users') . ' AS b '
					. ' WHERE b.'.$db->nameQuote('id').'=a.'.$db->nameQuote('memberid')
					. ' AND a.'.$db->nameQuote('groupid').'=' . $db->Quote( $key )
					. ' AND b.'.$db->nameQuote('block').'=' . $db->Quote( '0' ) . ' '
					. ' AND a.'.$db->nameQuote('permissions').' !=' . $db->quote( -1 );
			$db->setQuery( $query );

			$members	= $db->loadObjectList();
		}
		else
		{
			$members	= $model->getAllMember( $key );
		}

		$emails		= array();

		$jsCoreFile	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';
		foreach( $members as $member )
		{
			$user		= CFactory::getUser( $member->id );
			$userParam  = $user->getParams();
			$enabled    = $userParam->get( 'etype_groups_sendmail', 0 );
			if(! $enabled )
				continue;

			if( $author->user->email != $user->email )
			{
				$obj 				= new stdClass();
				$obj->email 		= $user->email;
				$emails[]			= $obj;
			}
		}

		$config 		= EasyBlogHelper::getConfig();
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
		$group				= JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $external->group_id );

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
		$obj->group_access	= $group->approvals;
		$obj->groupid		= $group->id;

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

	private function addStreamJomsocial( $blog , $isNew , $key , $source )
	{
		$jsCoreFile	= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';
		$config		= EasyBlogHelper::getConfig();

		// Somehow the blog contribution is in an array.
		$key		= $key[0];

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

		$blogContent	= $blog->intro . $blog->content;

		// Get blog's image and use it as the cover photo.
		$image 			= '';

		if( $blog->getImage() )
		{
			$imageSource	= $blog->getImage()->getSource( 'frontpage' );

			if( $imageSource )
			{
				$image	= '<a href="' . $blogLink . '"><img src="' . $imageSource . '" style="margin: 0 5px 5px 0;float: left; height: auto; width: 120px !important;"/></a>';
			}
		}
		else
		{
			// Try to find for an image in the content.
			$pattern		= '#<img[^>]*>#i';
			preg_match( $pattern , $blogContent , $matches );

			if( $matches )
			{
				$matches[0]		= JString::str_ireplace( 'img ' , 'img style="margin: 0 5px 5px 0;float: left; height: auto; width: 120px !important;"' , $matches[0 ] );

				$image 			= '<a href="' . $blogLink . '">' . $matches[0] . '</a>';
			}
		}

		// Strip unwanted data.
		$blogContent	= EasyBlogHelper::getHelper( 'Videos' )->strip( $blogContent );
		$blogContent	= EasyBlogGoogleAdsense::stripAdsenseCode( $blogContent );
		$blogContent	= JString::substr($blogContent, 0, $config->get( 'integrations_jomsocial_blogs_length', 250 ) ) . ' ...';

		// Remove all html tags from the content as we want to chop it down.
		$blogContent	= strip_tags( $blogContent );

		if( !empty( $image ) )
		{
			$blogContent 	= $image . $blogContent . '<div style="clear: both;"></div>';
		}

		$blogContent	.= '<div style="text-align: right;"><a href="' . $blogLink . '">' . JText::_( 'COM_EASYBLOG_CONTINUE_READING' ) . '</a></div>';

		$groupLink			= CRoute::_( 'index.php?option=com_community&view=groups&task=viewgroup&groupid=' . $key );
		JTable::addIncludePath( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'tables' );
		$group				= JTable::getInstance( 'Group' , 'CTable' );
		$group->load( $key );

		$title				= JText::sprintf( 'COM_EASYBLOG_JS_ACTIVITY_GROUP_BLOG_ADDED' , $blogLink , $title , $groupLink , $group->name );
		$obj				= new stdClass();
		$obj->title			= $title;
		$obj->content		= $blogContent;
		$obj->cmd 			= 'group.blog.added';

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

		$obj->group_access	= $group->approvals;
		$obj->actor   		= $my->id;
		$obj->target  		= $key;
		$obj->app			= 'easyblog';
		$obj->cid			= $key;
		$obj->groupid		= $key;

		// add JomSocial activities
		CFactory::load ( 'libraries', 'activities' );
		CActivityStream::add($obj);
	}

	public function getGroupContribution( $postId, $sourcetype = 'jomsocial', $type = 'id')
	{
		$db = EasyBlogHelper::db();

		$externalTblName    = '';


		if( $sourcetype == 'jomsocial' )
		{
			$externalTblName    = '#__community_groups';
		}

		$query  = '';

		if( $type == 'name' || $type == 'title' )
		{
			$query  = 'SELECT b.`name` FROM `#__easyblog_external_groups` as a ';
			$query  .= ' INNER JOIN ' . $db->NameQuote( $externalTblName ) . ' as b ON a.`group_id` = b.`id`';
		}
		else
		{
			$query  = 'SELECT `group_id` FROM `#__easyblog_external_groups` as a';
		}

		$query  .= ' WHERE a.`source` = ' . $db->Quote($sourcetype);
		$query  .= ' AND a.`post_id` = ' . $db->Quote($postId);


		$db->setQuery( $query );
		$result = $db->loadResult();

		return $result;
	}
}
