<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

ES::import('admin:/includes/apps/apps');

class SocialPageAppFeeds extends SocialAppItem
{
	/**
	 * Triggered to validate the stream item whether should put the item as valid count or not.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	jos_social_stream, boolean
	 * @return  0 or 1
	 */
	public function onStreamCountValidation(&$item, $includePrivacy = true)
	{
		// If this is not it's context, we don't want to do anything here.
		if ($item->context_type != 'feeds') {
			return false;
		}

		$item->cnt = 1;

		if ($includePrivacy) {
			$uid = $item->id;
			$my = ES::user();
			$privacy = ES::privacy($my->id);

			$sModel = ES::model('Stream');
			$aItem = $sModel->getActivityItem($item->id, 'uid');

			if ($aItem) {
				$uid = $aItem[0]->id;

				if (!$privacy->validate('core.view', $uid, SOCIAL_TYPE_ACTIVITY, $item->actor_id)) {
					$item->cnt = 0;
				}
			}
		}

		return true;
	}

	/**
	 * Processes notifications for feeds
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onNotificationLoad(SocialTableNotification &$item)
	{
		$allowed = array('feeds.page.create');

		if (!in_array($item->context_type, $allowed)) {
			return;
		}

		if ($item->cmd == 'likes.item' || $item->cmd == 'likes.involved') {

			$hook = $this->getHook('notification', 'likes');
			$hook->execute($item);

			return;
		}

		if ($item->cmd == 'comments.item' || $item->cmd == 'comments.involved') {

			$hook = $this->getHook('notification', 'comments');
			$hook->execute($item);

			return;
		}
	}

	/**
	 * Notifies the owner when user likes their feed
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterLikeSave(&$likes)
	{
		$allowed = array('feeds.page.create');

		if (!in_array($likes->type, $allowed)) {
			return;
		}

		// For new feed items
		if ($likes->type == 'feeds.page.create') {

			// Get the RSS feed
			$feed = ES::table('Rss');
			$feed->load($likes->uid);

			// Get the stream since we want to link it to the stream
			$stream = ES::table('Stream');
			$stream->load($likes->stream_id);

			// Get the actor of the likes
			$actor = ES::user($likes->created_by);

			// Get the owner of the item
			$owner = ES::user($feed->user_id);

			// Load the Page
	        $page = ES::page($stream->cluster_id);

	        // Set the email options
	        $emailOptions = array(
	            'title' => 'APP_PAGE_FEEDS_EMAILS_LIKE_RSS_FEED_ITEM_SUBJECT',
	            'template' => 'apps/page/feeds/like.feed.item',
	            'permalink' => $stream->getPermalink(true, true),
	            'actor' => $actor->getName(),
	            'page' => $page->getName(),
	            'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
	            'actorLink' => $actor->getPermalink(true, true)
	       );

	        $systemOptions = array(
	            'context_type' => $likes->type,
	            'context_ids' => $stream->id,
	            'url' => $stream->getPermalink(false, false, false),
	            'actor_id' => $likes->created_by,
	            'uid' => $likes->uid,
	            'aggregate' => true
	       	);

	        // [Page Compatibility] Only notify if the liker is not a page admin
	        // Notify the owner of the feed item first
	        if (!$page->isAdmin($likes->created_by)) {
	        	ES::notify('likes.item', $page->getAdmins(), $emailOptions, $systemOptions, $page->notification);
	        }

	        // Get a list of recipients to be notified for this stream item
	        // We exclude the owner of the note and the actor of the like here
	        $recipients = $this->getStreamNotificationTargets($likes->uid, 'feeds', 'page', 'create', array(), array($feed->user_id, $likes->created_by));

	        $emailOptions['title'] = 'APP_USER_FEEDS_EMAILS_LIKE_RSS_FEED_INVOLVED_SUBJECT';
	        $emailOptions['template'] = 'apps/page/feeds/like.feed.involved';

	        // Notify other participating users
	        ES::notify('likes.involved', $recipients, $emailOptions, $systemOptions, $page->notification);

			return;
		}
	}

	/**
	 * Notifies the owner when user likes their feed
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterCommentSave(&$comment)
	{
		// @legacy
		// photos.user.add should just be photos.user.upload since they are pretty much the same
		$allowed = array('feeds.page.create');

		if (!in_array($comment->element, $allowed)) {
			return;
		}

		// For new feed items
		if ($comment->element == 'feeds.page.create') {

			// Get the RSS feed
			$feed = ES::table('Rss');
			$feed->load($comment->uid);

			// Get the stream since we want to link it to the stream
			$stream = ES::table('Stream');
			$stream->load($comment->stream_id);

			// Get the actor of the comment
			$actor = ES::user($comment->created_by);

			// Get the owner of the feed item
			$owner = ES::user($feed->user_id);

			// Load the page 
	    	$page = ES::page($stream->cluster_id);

	        // Set the email options
	        $emailOptions = array(
	            'title' => 'APP_PAGE_FEEDS_EMAILS_COMMENT_RSS_FEED_ITEM_SUBJECT',
	            'template' => 'apps/page/feeds/comment.feed.item',
	            'permalink' => $stream->getPermalink(true, true),
	            'page' => $page->getName(),
	            'actor' => $actor->getName(),
	            'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
	            'actorLink' => $actor->getPermalink(true, true),
	            'target' => $owner->getName(),
	            'comment' => $comment->comment
	       );

	        $systemOptions = array(
	            'context_type' => $comment->element,
	            'context_ids' => $stream->id,
	            'url' => $stream->getPermalink(false, false, false),
	            'actor_id' => $comment->created_by,
	            'uid' => $comment->uid,
	            'aggregate' => true
	       	);

	        // [Page Compatibility] Only notify if the commentator is not a page admin
	        if (!$page->isAdmin($comment->created_by)) {
	        	ES::notify('comments.item', $page->getAdmins(), $emailOptions, $systemOptions, $page->notification);
	        }

	        // Get a list of recipients to be notified for this stream item
	        // We exclude the owner of the note and the actor of the like here
	        $recipients = $this->getStreamNotificationTargets($comment->uid, 'feeds', 'user', 'create', array(), array($feed->user_id, $comment->created_by));

	        $emailOptions['title'] = 'APP_USER_FEEDS_EMAILS_COMMENT_RSS_FEED_INVOLVED_SUBJECT';
	        $emailOptions['template'] = 'apps/page/feeds/comment.feed.involved';

	        // Notify other participating users
	        ES::notify('comments.involved', $recipients, $emailOptions, $systemOptions, $page->notification);

			return;
		}
	}

	/**
	 * Responsible to return the excluded verb from this app context
	 * @since	2.0
	 * @access	public
	 * @param	array
	 */
	public function onStreamVerbExclude(&$exclude)
	{
		// Get app params
		$params	= $this->getParams();

		$excludeVerb = false;

		if (!$params->get('stream_create', true)) {
			$exclude['feeds'] = true;
		}
	}

	/**
	 * Prepares the stream item
	 *
	 * @since	2.0
	 * @access	public
	 * @param	SocialStreamItem	The stream object.
	 * @param	bool				Determines if we should respect the privacy
	 */
	public function onPrepareStream(SocialStreamItem &$item, $includePrivacy = true)
	{
		if ($item->context !== 'feeds') {
			return;
		}

		// Get app params
		$params = $this->getParams();

		if (!$params->get('stream_create', true)) {
			return;
		}

		// Get the feed table
		$rss = ES::table('Rss');
		$rss->load($item->contextId);

		if (!$rss->id || !$item->contextId) {
			return;
		}

		$page = ES::page($item->cluster_id);

		// We know that only page admin able to add feeds on page
		$item->setActorAlias($page);

		$item->display = SOCIAL_STREAM_DISPLAY_FULL;

		$app = $this->getApp();

		// For Page, we need to manually ceate the likes and comments object
		$item->likes = ES::likes($item->contextId , $item->context, $item->verb, SOCIAL_APPS_GROUP_PAGE, $item->uid, array('clusterId' => $page->id));

		$commentParams = array('url' => ESR::stream(array('layout' => 'item', 'id' => $item->uid)), 'clusterId' => $page->id);
		$item->comments = ES::comments($item->contextId, $item->context, $item->verb, SOCIAL_APPS_GROUP_PAGE, $commentParams, $item->uid);
		
		$this->set('app', $app);
		$this->set('rss', $rss);
		$this->set('cluster', $page);

		$item->title = parent::display('themes:/site/streams/feeds/page/' . $item->verb . '.title');
		$item->preview = parent::display('themes:/site/streams/feeds/preview');
	}
}
