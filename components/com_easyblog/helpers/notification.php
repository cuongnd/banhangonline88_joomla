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


class EasyBlogNotificationHelper
{
	public function getAdminEmails( &$emails = array() )
	{
		$db		= EasyBlogHelper::db();

		$query	= 'SELECT ' . $db->nameQuote('name') . ', ' . $db->nameQuote('email');
		$query	.= ' FROM ' . $db->nameQuote('#__users');

		$emptyUserId = false;

		if(EasyBlogHelper::getJoomlaVersion() >= '1.6')
		{
			$saUsersIds	= EasyBlogHelper::getSAUsersIds();
			if( !$saUsersIds )
				$emptyUserId = true;
			$query	.= ' WHERE id IN (' . implode(',', $saUsersIds) . ')';
		}
		else
		{
			$query	.= ' WHERE LOWER( ' . $db->nameQuote('usertype') . ' ) = ' . $db->Quote('super administrator');
		}

		$query	.= ' AND `sendEmail` = ' . $db->Quote('1');

		if(!$emptyUserId)
		{
			$db->setQuery( $query );
			$result = $db->loadObjectList();
		}
		else
		{
			$result = "";
		}

		if( !$result )
		{
			return;
		}

		foreach( $result as $row )
		{
			$obj 				= new StdClass();
			$obj->unsubscribe	= false;
			$obj->email 		= $row->email;

			$emails[ $row->email ]	= $obj;
		}
	}

	public function getBlogSubscriberEmails( &$emails = array() , $blogId )
	{
		$blog 	= EasyBlogHelper::getTable( 'Blog' );
		$blog->load( $blogId );

		$subscribers 	= $blog->getSubscribers();

		if( !$subscribers )
		{
			return $subscribers;
		}

		foreach( $subscribers as $row )
		{
			$obj 				= new StdClass();

			if( !isset( $row->type ) )
			{
				$row->type	= 'subscription';
			}

			$obj->unsubscribe	= EasyBlogHelper::getUnsubscribeLink( $row , true );
			$obj->email 		= $row->email;

			$emails[ $row->email ]	= $obj;
		}
	}

	public function getTeamAdminEmails( &$emails = array() , $teamId )
	{
		$db 	= EasyBlogHelper::db();

		$query  = 'select `email` from `#__users` as a inner join `#__easyblog_team_users` as b on a.`id` = b.`user_id`';
		$query  .= ' where b.`team_id` = ' . $db->Quote( $teamId );
		$query  .= ' and b.isadmin = ' . $db->Quote('1');

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if( !$result )
		{
			return $result;
		}

		foreach( $result as $row )
		{
			$obj 				= new StdClass();
			$obj->unsubscribe	= false;
			$obj->email 		= $row->email;

			$emails[ $row->email ]	= $obj;
		}
	}

	public function getCustomEmails( &$emails = array() )
	{
		$config 	= EasyBlogHelper::getConfig();

		$customEmails	= $config->get( 'notification_email' );
		$customEmails 	= trim( $customEmails );

		if( !empty($customEmails ) )
		{
			$customEmails 	= explode( ',' , $customEmails );

			foreach( $customEmails as $email )
			{
				$obj 				= new StdClass();
				$obj->unsubscribe	= false;
				$obj->email 		= $email;

				$emails[ $email ]	= $obj;
			}
		}

	}

	public function getAllEmails( &$emails = array() )
	{
		$config 	= EasyBlogHelper::getConfig();

		// Get every email address of the users on the site.
		if( !class_exists( 'EasyBlogModelSubscription' ) )
		{
			JLoader::import( 'subscription' , EBLOG_ROOT . DIRECTORY_SEPARATOR . 'models' );
		}

		$model 			= EasyBlogHelper::getModel( 'Subscription' );
		$subscribers	= $model->getMembersAndSubscribers();

		foreach( $subscribers as $subscriber )
		{
			if( !array_key_exists( $subscriber->email , $emails ) )
			{
				$obj 				= new stdClass();
				$obj->unsubscribe	= false;
				$obj->email 		= $subscriber->email;

				if( $subscriber->type != 'member' )
				{
					$obj->unsubscribe 	= EasyBlogHelper::getUnsubscribeLink( $subscriber , true );
				}
				$emails[ $subscriber->email ]	= $obj;
			}
		}
	}

	public function getSubscriberEmails( &$emails = array() , $blog )
	{
		$config 	= EasyBlogHelper::getConfig();

		// @rule: Send to subscribers that subscribe to the bloggers
		if( $config->get( 'notification_blogsubscriber' ) )
		{
			self::getBloggerSubscriberEmails( $emails , $blog );
		}

		// @rule: Send to subscribers that subscribed to the category
		if( $config->get( 'notification_categorysubscriber' ) )
		{
			self::getCategorySubscriberEmails( $emails , $blog );
		}

		// @rule: Send to subscribers that subscribed to a team
		if( $config->get( 'notification_teamsubscriber' ) )
		{
			self::getTeamSubscriberEmails( $emails , $blog );
		}

		// @rule: Send notification to all site's subscribers
		if($config->get('notification_sitesubscriber') )
		{
			self::getSiteSubscriberEmails( $emails , $blog );
		}
	}

	public function getBloggerSubscriberEmails( &$emails = array() , $blog )
	{
		if( !class_exists( 'EasyBlogModelBlogger' ) )
		{
			jimport( 'joomla.application.component.model' );
			JLoader::import( 'blogger' , JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'models' );
		}
		$model 			= EasyBlogHelper::getModel( 'Blogger' );

		// @TODO: Fix this
		$subscribers	= $model->getBlogggerSubscribers( $blog->created_by );

		if( !empty( $subscribers ) )
		{
			foreach( $subscribers as $subscriber )
			{
				if( !array_key_exists( $subscriber->email , $emails ) )
				{
					$obj 				= new stdClass();
					$obj->email 		= $subscriber->email;
					$obj->unsubscribe 	= EasyBlogHelper::getUnsubscribeLink( $subscriber , true );
					$emails[$subscriber->email]	= $obj;
				}
			}
		}
	}

	public function getCategorySubscriberEmails( &$emails = array() , $blog )
	{
		$model 			= EasyBlogHelper::getModel( 'Category' );
		$subscribers	= $model->getCategorySubscribers( $blog->category_id );

		if( !empty( $subscribers ) )
		{
			foreach( $subscribers as $subscriber )
			{
				if( !array_key_exists( $subscriber->email , $emails ) )
				{
					$obj 				= new stdClass();
					$obj->email 		= $subscriber->email;
					$obj->unsubscribe 	= EasyBlogHelper::getUnsubscribeLink( $subscriber , true );
					$emails[$subscriber->email]	= $obj;
				}
			}
		}
	}


	public function getTeamUserEmails( &$emails = array() , $teamId )
	{
		$db 	= EasyBlogHelper::db();

		$query  = 'select `email` from `#__users` as a inner join `#__easyblog_team_users` as b on a.`id` = b.`user_id`';
		$query  .= ' where b.`team_id` = ' . $db->Quote( $teamId );

		$db->setQuery($query);
		$result = $db->loadObjectList();

		if( !$result )
		{
			return $result;
		}

		foreach( $result as $row )
		{
			if( !array_key_exists( $row->email , $emails ) )
			{
				$obj 				= new StdClass();
				$obj->unsubscribe	= false;
				$obj->email 		= $row->email;

				$emails[ $row->email ]	= $obj;
			}
		}
	}

	public function getTeamSubscriberEmails( &$emails = array() , $blog )
	{
		$model 			= EasyBlogHelper::getModel( 'TeamBlogs' );

		// @rule: See if blog post is tied to any group
		$db 			= EasyBlogHelper::db();
		$query 			= 'SELECT ' . $db->nameQuote( 'team_id' ) . ' FROM ' . $db->nameQuote( '#__easyblog_team_post' ) . ' '
						. 'WHERE ' . $db->nameQuote( 'post_id' ) . '=' . $db->Quote( $blog->id );
		$db->setQuery( $query );
		$teamId			= $db->loadResult();

		$subscribers 	= $model->getTeamSubscribers( $teamId );

		if( !empty( $subscribers ) )
		{
			foreach( $subscribers as $subscriber )
			{
				if( !array_key_exists( $subscriber->email , $emails ) )
				{
					$obj 				= new stdClass();
					$obj->email 		= $subscriber->email;
					$obj->unsubscribe 	= EasyBlogHelper::getUnsubscribeLink( $subscriber , true );
					$emails[$subscriber->email]	= $obj;
				}
			}
		}
	}

	public function getSiteSubscriberEmails( &$emails = array() )
	{
		$model 			= EasyBlogHelper::getModel( 'Subscription' );
		$subscribers	= $model->getSiteSubscribers();

		if( !empty( $subscribers ) )
		{
			foreach( $subscribers as $subscriber )
			{
				if( !array_key_exists( $subscriber->email , $emails ) )
				{
					$obj 				= new stdClass();
					$obj->email 		= $subscriber->email;
					$obj->unsubscribe 	= EasyBlogHelper::getUnsubscribeLink( $subscriber , true );
					$emails[$subscriber->email]	= $obj;
				}
			}
		}
	}

	/**
	 * Sends an email out.
	 **/
	public function send( $emails , $emailTitle , $template , $data )
	{
		$config		= EasyBlogHelper::getConfig();
		$app 		= JFactory::getApplication();
		$jConfig 	= EasyBlogHelper::getJConfig();

		$defaultEmailFrom  	= ( EasyBlogHelper::getJoomlaVersion() >= '1.6' ) ? $jConfig->get( 'mailfrom') : $jConfig->get( 'mailfrom');
		$defaultFromName  	= ( EasyBlogHelper::getJoomlaVersion() >= '1.6' ) ? $jConfig->get( 'fromname') : $jConfig->get( 'fromname');

		$fromEmail 	= $config->get( 'notification_from_email' 	, $defaultEmailFrom );
		$fromName	= $config->get( 'notification_from_name'	, $defaultFromName );

		if( empty( $fromEmail ) )
		{
			$fromEmail 	= $defaultEmailFrom;
		}

		if( empty( $fromName ) )
		{
			$fromName 	= $defaultFromName;
		}

		// @rule: Make sure there are only unique emails so we don't send duplicates.
		foreach( $emails as $email => $obj )
		{
			if( $obj->unsubscribe )
			{
				$data['unsubscribeLink' ]	= $obj->unsubscribe;
			}

			// Retrieve the template's contents.
			$output 	= $this->getTemplateContents( $template , $data );

			$mailq 				= EasyBlogHelper::getTable( 'MailQueue' );
			$mailq->mailfrom	= $fromEmail;
			$mailq->fromname 	= $fromName;
			$mailq->recipient	= $obj->email;
			$mailq->subject 	= $emailTitle;
			$mailq->body		= $output;
			$mailq->created		= EasyBlogHelper::getDate()->toMySQL();
			$mailq->store();
		}
	}

	/**
	 * Retrieves the template contents.
	 *
	 **/
	function getTemplateContents( $template, $data )
	{
		// Load front end's language file.
		JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );

		// We only want to show unsubscribe link when the user is really subscribed to the blogs
		if( !isset( $data[ 'unsubscribeLink' ] ) )
		{
			$data[ 'unsubscribeLink' ]		= '';
		}

		$config = EasyBlogHelper::getConfig();

		// @rule: Detect what type of emails that we should process.
		$type 	= $config->get( 'main_mailqueuehtmlformat' ) ? 'html' : 'text';
		$theme	= new CodeThemes();

		// Fetch the child theme first.
		foreach( $data as $key => $val )
		{
			$theme->set( $key , $val );
		}

		$file 		=  $template . '.' . $type . '.php';

		$contents	= $theme->fetch( $file );

		// @rule: Now we need to process the main template holder.
		$title	= $config->get( 'notifications_title' );

		$theme 	= new CodeThemes();
		$theme->set( 'unsubscribe'	, $data['unsubscribeLink'] );
		$theme->set( 'emailTitle'	, $title );
		$theme->set( 'contents'		, $contents );
		$output = $theme->fetch( 'email.template.' . $type . '.php' );

		return $output;
	}
}
