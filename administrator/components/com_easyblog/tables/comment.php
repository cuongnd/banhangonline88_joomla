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

require_once( dirname( __FILE__ ) . DIRECTORY_SEPARATOR . 'table.php' );

require_once( JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_easyblog' . DIRECTORY_SEPARATOR . 'constants.php' );
require_once( EBLOG_HELPERS . DIRECTORY_SEPARATOR . 'string.php' );

class EasyBlogTableComment extends EasyBlogTable
{
	var $id 			= null;
	var $post_id		= null;
	var $comment		= null;
	var $name			= null;
	var $title			= null;
	var $email			= null;
	var $url			= null;
	var $ip				= null;
	var $created_by		= null;
	var $created		= null;
	var $modified		= null;
	var $published		= null;
	var $publish_up		= null;
	var $publish_down	= null;
	var $ordering		= null;
	var $vote			= null;
	var $hits			= null;
	var $sent			= null;
	var $lft			= null;
	var $rgt			= null;
	var $parent_id		= null;

	/**
	 * Constructor for this class.
	 *
	 * @return
	 * @param object $db
	 */
	function __construct(& $db )
	{
		parent::__construct( '#__easyblog_comment' , 'id' , $db );
	}

	/***
	 * When executed, remove any 3rd party integration records.
	 */
	public function removeStream()
	{
		jimport( 'joomla.filesystem.file' );

		$config 	= EasyBlogHelper::getConfig();

		// @rule: Detect if jomsocial exists.
		$file 		= JPATH_ROOT . DIRECTORY_SEPARATOR . 'components' . DIRECTORY_SEPARATOR . 'com_community' . DIRECTORY_SEPARATOR . 'libraries' . DIRECTORY_SEPARATOR . 'core.php';

		if( JFile::exists( $file ) && $config->get( 'integrations_jomsocial_comment_new_activity' ) )
		{
			// @rule: Test if record exists first.
			$db 	= EasyBlogHelper::db();
			$query	= 'SELECT COUNT(1) FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__community_activities' ) . ' '
					. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'app' ) . '=' . $db->Quote( 'com_easyblog' ) . ' '
					. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'cid' ) . '=' . $db->Quote( $this->id ) . ' '
					. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'comment_type') . '=' . $db->Quote( 'com_easyblog.comments' );
			$db->setQuery( $query );

			$exists	= $db->loadResult();

			if( $exists )
			{
				$query	= 'DELETE FROM ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( '#__community_activities' ) . ' '
						. 'WHERE ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'app' ) . '=' . $db->Quote( 'com_easyblog' ) . ' '
						. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'cid' ) . '=' . $db->Quote( $this->id ) . ' '
						. 'AND ' . EasyBlogHelper::getHelper( 'SQL' )->nameQuote( 'comment_type') . '=' . $db->Quote( 'com_easyblog.comments' );

				$db->setQuery( $query );
				$db->Query();
			}
		}
	}

	/**
	 * Method to update ordering before a comment is saved.
	 **/
	public function updateOrdering()
	{
		$model			= EasyBlogHelper::getModel( 'Comment' );
		$latestComment 	= $model->getLatestComment( $this->post_id , $this->parent_id );

		// @rule: Processing child comments
		if( $this->parent_id != 0 )
		{
			$parentComment 	= EasyBlogHelper::getTable( 'Comment' );
			$parentComment->load( $this->parent_id );

			$left 		= $parentComment->lft + 1;
			$right 		= $parentComment->lft + 2;
			$nodeVal	= $parentComment->lft;

			if( !empty( $latestComment ) )
			{
				$left 		= $latestComment->rgt + 1;
				$right		= $latestComment->rgt + 2;
				$nodeVal	= $latestComment->rgt;
			}

			$model->updateCommentSibling( $this->post_id , $nodeVal );

			$this->lft 		= $left;
			$this->rgt 		= $right;

			return true;
		}


		// @rule: Processing new comments
		$left 	= 1;
		$right 	= 2;

		if( !empty( $latestComment ) )
		{
			$left 	= $latestComment->rgt + 1;
			$right	= $latestComment->rgt + 2;

			$model->updateCommentSibling( $this->post_id , $latestComment->rgt );
		}

		$this->lft 	= $left;
		$this->rgt 	= $right;

		return true;
	}

	/**
	 * Notifies the comment author when someone likes the comment
	 *
	 * @since	5.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function notifyLikes($likeAuthorId = null)
	{
		// Ensure that the comment author is a valid user
		if (!$this->created_by) {
			return false;
		}

		// Get the likes author
		$author = EasyBlogHelper::getTable('Profile');
		$author->load($likeAuthorId);

		$data = array();
		$data['authorName'] = $author->getName();
		$data['authorAvatar'] = $author->getAvatar();
		// $data['permalink'] = $this

		// Get the post object
		$post = EasyBlogHelper::getTable('Blog');
		$post->load($this->post_id);

		require_once(JPATH_ROOT . '/components/com_easyblog/router.php');
		$permalink = EasyBlogRouter::_('index.php?option=com_easyblog&view=entry&id=' . $post->id . '#comment-' . $this->id);

		$data['permalink'] = $permalink;

		// Get the comment author
		$commentAuthor = EasyBlogHelper::getTable('Profile');
		$commentAuthor->load($this->created_by);

		$email = new stdClass();
		$email->unsubscribe = false;
		$email->email = $commentAuthor->user->email;

		// Email subject
		$subject = JText::_('COM_EASYBLOG_EMAIL_SUBJECT_NEW_LIKES');

		// Send notification now
		$notification = EasyBlogHelper::getHelper('Notification');
		$notification->send(array($email), $subject, 'email.like.comment' , $data);

		// Add EasySocial notifications as well
		$easysocial = EasyBlogHelper::getHelper('EasySocial');
		$easysocial->notifySubscribers($post, 'comment.likes', $this);
	}

	public function processEmails( $isModerated = false , $blog )
	{
		$config 	= EasyBlogHelper::getConfig();

		// @task: Fix contents of comments.
		$content	= $this->comment;
		// $content 	= nl2br( $this->comment );
		// $content 	= EasyBlogCommentHelper::parseBBCode( $content );

		// Initialize what we need
		$commentAuthor			= $this->name;
		$commentAuthorEmail		= $this->email;
		$commentAuthorAvatar	= JURI::root() . 'components/com_easyblog/assets/images/default_blogger.png';
		$date					= EasyBlogDateHelper::dateWithOffSet( $this->created );
		$commentDate			= EasyBlogDateHelper::toFormat( $date , '%A, %B %e, %Y' );

		$teamLink 	= '';
		$emails 	= array();

		if( isset( $blog->team ) )
		{
			$teamLink	= '&team=' . $blog->team;
		}


		if( $this->created_by != 0 )
		{
			$user 	= EasyBlogHelper::getTable( 'Profile' );
			$user->load( $this->created_by );

			$commentAuthor			= $user->getName();
			$commentAuthorEmail		= $user->user->email;
			$commentAuthorAvatar	= $user->getAvatar();
		}

		$blogAuthor = EasyBlogHelper::getTable( 'Profile' );
		$blogAuthor->load( $blog->created_by );

		$data 		= array(
							'blogTitle'			=> $blog->title,
							'blogIntro'			=> $blog->intro,
							'blogContent'		=> $blog->content,
							'blogLink'			=> EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry'.$teamLink.'&id='. $blog->id, false, true),
							'commentTitle'		=> empty( $comment->title ) ? '-' : $comment->title,
							'commentContent'	=> $content,
							'commentAuthor'		=> $commentAuthor,
							'commentAuthorAvatar' => $commentAuthorAvatar,
							'commentDate'		=> $commentDate,
							'commentLink'		=> EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&view=entry'.$teamLink.'&id='. $blog->id, false, true) . '#comment-' . $this->id
						);

		$emails 		= array();
		$notification 	= EasyBlogHelper::getHelper( 'Notification' );

		if( $isModerated )
		{
			$hashkey		= EasyBlogHelper::getTable( 'HashKeys' );
			$hashkey->uid	= $this->id;
			$hashkey->type	= 'comments';
			$hashkey->store();

			$data[ 'approveLink' ]	= EasyBlogRouter::getRoutedURL( 'index.php?option=com_easyblog&controller=dashboard&task=approvecomment&key=' . $hashkey->key , false , true );

			// $notification->getCustomEmails( $emails );
			// $notification->getAdminEmails( $emails );
			//

			if( $config->get( 'custom_email_as_admin' ) )
				{
					$notification->getCustomEmails( $emails );
				}
				else
				{
					$notification->getAdminEmails( $emails );
				}

			$notification->send( $emails , JText::_( 'COM_EASYBLOG_NEW_COMMENT_ADDED_MODERATED_TITLE' ) , 'email.comment.moderate' , $data );

			return true;
		}

		if( !$isModerated )
		{
			if( $config->get( 'notification_commentadmin' ) )
			{
				if( $config->get( 'custom_email_as_admin' ) )
				{
					$notification->getCustomEmails( $emails );
				}
				else
				{
					$notification->getAdminEmails( $emails );
				}
			}

			// @rule: Send notification to blog authors.
			if( $config->get( 'notification_commentauthor' ) )
			{
				$obj 				= new stdClass();
				$obj->unsubscribe	= false;
				$obj->email 		= $blogAuthor->user->email;

				$emails[ $blogAuthor->user->email ]	= $obj;
			}

			// @rule: Send notifications to blog subscribers
			if( ($config->get('main_subscription') && $blog->subscription == '1') && $config->get('notification_commentsubscriber') )
			{
				$notification->getBlogSubscriberEmails( $emails , $blog->id );
			}

			// @rule: Do not send to the person that commented on the blog post.
			unset( $emails[ $commentAuthorEmail ] );

			// @rule: Send the emails now.
			if( !empty( $emails ) )
			{
				$emailTitle 	= JText::_( 'COM_EASYBLOG_NEW_COMMENT_ADDED' );

				if( $config->get( 'main_comment_email' ) )
				{
					$emailTitle	= '[#' . $this->id . ']: ' . $emailTitle;
				}
				$notification->send( $emails , $emailTitle , 'email.comment.new' , $data );
			}

			return true;
		}
	}

	/**
	 * Retrieve a list of user id's from the following:
	 *
	 * - Users who subscribed to the blog entry
	 */
	public function getSubscribers( $blog , $excludeUsers )
	{
		$result			= $blog->getSubscribers();
		$subscribers	= array();

		foreach( $result as $row )
		{
			if( !in_array( $row->user_id , $excludeUsers ) && $row->user_id )
			{
				$subscribers[]	= $row->user_id;
			}
		}

		return $subscribers;
	}

	public function store( $isModerated = false )
	{
		$isNew      = ( empty( $this->id ) ) ? true : false;

		// @rule: Update the ordering as long as this is a new comment. Regardless of the publishing status
		if( $isNew )
		{
			$this->updateOrdering();
		}

		// @rule: If this was moderated and it is published, we should notify the other extensions.
		if( $isModerated && $this->published )
		{
			$isNew 	= true;
		}

		// @rule: Store after the ordering is updated
		$state		= parent::store();

		// @rule: Run point integrations here.
		if( $isNew && $this->published == 1 && $state )
		{
			$my 		= JFactory::getUser();
			$blog		= EasyBlogHelper::getTable( 'Blog' );
			$blog->load( $this->post_id );

			JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );
			$config 	= EasyBlogHelper::getConfig();

			$author 			= EasyBlogHelper::getTable( 'Profile' );
			$author->load( $this->created_by );

			// @task: Blog external link.
			$link		= $blog->getExternalBlogLink( 'index.php?option=com_easyblog&view=entry&id='. $blog->id ) . '#comment-' . $this->id;

			// @rule: Send notifications to the author of the blog for EasyDiscuss
			if( $blog->created_by != $this->created_by && $this->created_by != 0 )
			{
				if( $config->get( 'integrations_easydiscuss_notification_comment' ) )
				{
					EasyBlogHelper::getHelper( 'EasyDiscuss' )
							->addNotification( $blog ,
										JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_NOTIFICATIONS_NEW_COMMENT_IN_YOUR_BLOG' , $blog->title) ,
										EBLOG_NOTIFICATIONS_TYPE_COMMENT ,
										array( $blog->created_by ) ,
										$this->created_by,
										$link );
				}

				// @rule: Add notifications for jomsocial 2.6
				if( $config->get( 'integrations_jomsocial_notification_comment' ) )
				{
					// Get list of users who subscribed to this blog.
					EasyBlogHelper::getHelper( 'JomSocial' )->addNotification( JText::sprintf( 'COM_EASYBLOG_JOMSOCIAL_NOTIFICATIONS_NEW_COMMENT_IN_YOUR_BLOG' , $link  , $blog->title ) , 'easyblog_comment_blog' , array( $blog->created_by ) , $author , $link );
				}

				$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );

				if( $config->get( 'integrations_easysocial_notifications_newcomment' ) && $easysocial->exists() )
				{
					$easysocial->notifySubscribers( $blog , 'new.comment' , $this );
				}
			}

			// @rule: Notify subscribers through notification system.
			if( $config->get( 'integrations_jomsocial_notification_comment_follower' ) && $this->created_by != 0 )
			{
				$target	= $this->getSubscribers( $blog , array( $this->created_by , $blog->created_by ) );

				if( !empty( $target ) )
				{
					// Get list of users who subscribed to this blog.
					EasyBlogHelper::getHelper( 'JomSocial' )->addNotification( JText::sprintf( 'COM_EASYBLOG_JOMSOCIAL_NOTIFICATIONS_NEW_COMMENT_POSTED' , $link   , $blog->title ) , 'easyblog_comment_blog_sub' , $target , $author , $link );
				}
			}

			// @rule: Notify subscribers through notification system.
			if( $config->get( 'integrations_easydiscuss_notification_comment_follower' ) && $this->created_by != 0 )
			{
				$target	= $this->getSubscribers( $blog , array( $this->created_by , $blog->created_by ) );

				if( !empty( $target ) )
				{
					EasyBlogHelper::getHelper( 'EasyDiscuss' )
							->addNotification( $blog ,
										JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_NOTIFICATIONS_NEW_COMMENT_POSTED' , $blog->title) ,
										EBLOG_NOTIFICATIONS_TYPE_COMMENT ,
										$target ,
										$this->created_by,
										$link );
				}
			}

			if( $this->created_by != 0 )
			{
				// @rule: Integrations with EasyDiscuss
				EasyBlogHelper::getHelper( 'EasyDiscuss' )->log( 'easyblog.new.comment' , $this->created_by , JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_HISTORY_NEW_COMMENT' , $blog->title ) );
				EasyBlogHelper::getHelper( 'EasyDiscuss' )->addPoint( 'easyblog.new.comment' , $this->created_by );
				EasyBlogHelper::getHelper( 'EasyDiscuss' )->addBadge( 'easyblog.new.comment' , $this->created_by );

				EasyBlogHelper::getHelper( 'EasySocial' )->assignPoints( 'comment.create' , $this->created_by );
				EasyBlogHelper::getHelper( 'EasySocial' )->assignPoints( 'comment.create.author' , $blog->created_by );

				EasyBlogHelper::getHelper( 'EasySocial' )->assignBadge( 'comment.create' , JText::_( 'COM_EASYBLOG_EASYSOCIAL_BADGE_CREATE_COMMENT' ) );
			}

			// AlphaUserPoints
			// since 1.2
			if ( EasyBlogHelper::isAUPEnabled() && $this->created_by != 0 )
			{
				$url 		= EasyBlogRouter::_( 'index.php?option=com_easyblog&view=entry&id=' . $this->post_id );
				AlphaUserPointsHelper::newpoints( 'plgaup_easyblog_add_comment', AlphaUserPointsHelper::getAnyUserReferreID( $this->created_by ) , '', JText::sprintf('COM_EASYBLOG_AUP_NEW_COMMENT_SUBMITTED', $url, $blog->title) );

				// @rule: Add comment for blog author
				if( $blog->created_by != $this->created_by )
				{
					AlphaUserPointsHelper::newpoints( 'plgaup_easyblog_add_comment_blogger', AlphaUserPointsHelper::getAnyUserReferreID( $blog->created_by ) , '', JText::sprintf('COM_EASYBLOG_AUP_NEW_COMMENT_SUBMITTED', $url, $blog->title) );
				}
			}

			// Determine whether or not to push this as a normal activity
			$external	= $blog->getBlogContribution();

			// @rule: Add activity integrations for group integrations.
			if( $external )
			{
				if( $external instanceof TableExternal )
				{
					EasyBlogHelper::getHelper( 'Event' )->addCommentStream( $blog , $this , $external );
				}
				else
				{
					// @task: Legacy support prior to 3.5
					EasyBlogHelper::getHelper( 'Groups' )->addCommentStream( $blog , $this , $external );
				}
			}
			else
			{
				EasyBlogHelper::addJomSocialActivityComment( $this , $blog->title);

				// Add EasySocial stream
				$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );
				$easysocial->createCommentStream( $this , $blog );

				// Assign EasySocial points
				$easysocial->assignPoints( 'comments.create' );


				// @rule: Give points to the comment author
				EasyBlogHelper::addJomsocialPoint( 'com_easyblog.comments.add' );

				// @rule: Give points to the blog author
				if( $my->id != $blog->created_by && $this->created_by != 0 )
				{
					// Assign EasySocial points
					$easysocial->assignPoints( 'comments.create.author' , $blog->created_by );

					EasyBlogHelper::addJomsocialPoint( 'com_easyblog.comments.addblogger' , $blog->created_by );
				}
			}

		}

		if( $isNew && $state )
		{
			$blog 			= EasyBlogHelper::getTable( 'Blog' );
			$blog->load( $this->post_id );

			$blogauthor   = EasyBlogHelper::getTable('Profile');
			$blogauthor->load( $blog->created_by );


			$obj    			= new stdClass();
			$obj->comment		= $this->comment;
			$obj->commentauthor	= $this->name;
			if ($this->created_by) {
				$author = EasyBlogHelper::getTable( 'Profile' );
				$author->load( $this->created_by );
				$obj->commentauthor = $author->getName();
			}


			$obj->blogtitle		= $blog->title;
			$obj->blogautor		= $blogauthor->getName();

			$activity   = new stdClass();
			$activity->actor_id		= ( empty( $this->created_by ) ) ? '0' : $this->created_by;
			$activity->target_id	= $blog->created_by;
			$activity->context_type	= 'comment';
			$activity->context_id	= $this->id;
			$activity->verb         = 'add';
			$activity->source_id    = $this->post_id;
			$activity->uuid         = serialize( $obj );
			EasyBlogHelper::activityLog( $activity );
		}

		return $state;
	}

	public function delete( $cid = null )
	{
		$state 	= parent::delete( $cid );
		$my 	= JFactory::getUser();

		// @rule: Remove comment's stream
		$this->removeStream();

		if( $this->created_by != 0 && $this->published == '1' )
		{
			$blog		= EasyBlogHelper::getTable( 'Blog' );
			$blog->load( $this->post_id );

			JFactory::getLanguage()->load( 'com_easyblog' , JPATH_ROOT );
			$config 	= EasyBlogHelper::getConfig();

			// @rule: Integrations with EasyDiscuss
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->log( 'easyblog.delete.comment' , $this->created_by , JText::sprintf( 'COM_EASYBLOG_EASYDISCUSS_HISTORY_DELETE_COMMENT' , $blog->title ) );
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->addPoint( 'easyblog.delete.comment' , $this->created_by );
			EasyBlogHelper::getHelper( 'EasyDiscuss' )->addBadge( 'easyblog.delete.comment' , $this->created_by );

			// @since 1.2
			// AlphaUserPoints
			if ( EasyBlogHelper::isAUPEnabled() )
			{
				AlphaUserPointsHelper::newpoints( 'plgaup_easyblog_delete_comment', AlphaUserPointsHelper::getAnyUserReferreID( $this->created_by ) , '', JText::_('COM_EASYBLOG_AUP_COMMENT_DELETED') );

				// @rule: Add comment for blog author
				if( $blog->created_by != $this->created_by )
				{
					AlphaUserPointsHelper::newpoints( 'plgaup_easyblog_delete_comment_blogger', AlphaUserPointsHelper::getAnyUserReferreID( $blog->created_by ) , '', JText::sprintf('COM_EASYBLOG_AUP_COMMENT_DELETED_BLOGGER', $url, $blog->title) );
				}
			}

			// Assign EasySocial points
			$easysocial 	= EasyBlogHelper::getHelper( 'EasySocial' );
			$easysocial->assignPoints( 'comments.remove' , $this->created_by );

			// @rule: Deduct points from the comment author
			EasyBlogHelper::addJomsocialPoint( 'com_easyblog.comments.remove' , $this->created_by );

			// @rule: Give points to the blog author
			if( $my->id != $blog->created_by )
			{
				// Assign EasySocial points
				$easysocial->assignPoints( 'comments.remove.author' , $blog->created_by );

				EasyBlogHelper::addJomsocialPoint( 'com_easyblog.comments.removeblogger' , $blog->created_by );
			}
		}

		return $state;
	}

	/**
	 * Responsible to bind values into itself.
	 *
	 * @access	public
	 * @param 	Array 	$post	An array of posted values.
	 */
	function bindPost($post)
	{
		$config 		= EasyBlogHelper::getConfig();

		if(! empty($post['commentId']))
		{
			$this->id	= $post['commentId'];
		}

		$this->post_id	= $post['id'];

		//replace a url to link
		$comment        = $post['comment'];

		$filter 		= JFilterInput::getInstance();
		$comment		= $filter->clean($comment);

		$this->comment	= $comment;

		if( isset( $post['name'] ) )
		{
			$this->name		= $filter->clean($post['name']);
		}

		if( isset( $post['title'] ) )
		{
			$this->title	= $filter->clean($post['title']);
		}

		if( isset( $post['email'] ) )
		{
			$this->email	= $filter->clean($post['email']);
		}

		if( isset( $post['url'] ) )
		{
			$this->url		= $filter->clean($post['url']);
		}
	}

	function updateSent()
	{
		$db = EasyBlogHelper::db();

		if(! empty($this->id))
		{
			$query  = 'UPDATE `#__easyblog_comment` SET `sent` = 1 WHERE `id` = ' . $db->Quote($this->id);

			$db->setQuery($query);
			$db->query();
		}

		return true;
	}


	public function isCreator( $id = '' )
	{
		if( empty( $id ) )
		{
			$id	= JFactory::getUser()->id;
		}

		return $this->created_by == $id;
	}

	public function validate( $type )
	{
		$config		= EasyBlogHelper::getConfig();


		if( $config->get( 'comment_requiretitle' ) && $type == 'title' )
		{
			return JString::strlen( $this->title ) != 0 ;
		}

		if( $type == 'name' )
		{
			return JString::strlen( $this->name ) != 0;
		}

		if( $type == 'email' )
		{
			return JString::strlen( $this->email ) != 0;
		}

		if( $type == 'comment' )
		{
			return JString::strlen( $this->comment ) != 0;
		}

		return true;
	}
}
