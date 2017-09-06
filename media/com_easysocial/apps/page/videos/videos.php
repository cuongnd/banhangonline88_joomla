<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

class SocialPageAppVideos extends SocialAppItem
{
	public $appListing = false;

	public function __construct( $options = array() )
	{
		parent::__construct($options);
	}

	/**
	 * Responsible to return the excluded verb from this app context
	 * @since   2.0
	 * @access  public
	 * @param   array
	 */
	public function onStreamVerbExclude(&$exclude)
	{
		// Get app params
		$params = $this->getParams();

		$excludeVerb = false;

		if (!$params->get('uploadVideos', true)) {
			$excludeVerb[] = 'create';
		}

		if (!$params->get('featuredVideos', true)) {
			$excludeVerb[] = 'featured';
		}

		if ($excludeVerb !== false) {
			$exclude['videos'] = $excludeVerb;
		}
	}


	/**
	 * Triggered to validate the stream item whether should put the item as valid count or not.
	 *
	 * @since   2.0
	 * @access  public
	 * @param   jos_social_stream, boolean
	 * @return  0 or 1
	 */
	public function onStreamCountValidation(&$item, $includePrivacy = true)
	{
		// If this is not it's context, we don't want to do anything here.
		if ($item->context_type != 'videos') {
			return false;
		}

		$params = ES::registry($item->params);
		$page = ES::page($params->get('page'));

		if (!$page) {
			return;
		}

		$item->cnt = 1;

		if (!$page->isOpen() && !$page->isMember($this->my->id)) {
			$item->cnt = 0;
		}

		return true;
	}

	/**
	 * Generates the stream item for videos
	 *
	 * @since   2.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function onPrepareStream(SocialStreamItem &$stream, $includePrivacy = true)
	{
		if ($stream->context != SOCIAL_TYPE_VIDEOS) {
			return;
		}

		// Determines if the viewer can view the stream item from this page
		$page = $stream->getCluster();

		if (!$page) {
			return;
		}

		if (!$page->canViewItem()) {
			return;
		}

		// Decorate the stream item with the neccessary design
		$stream->display = SOCIAL_STREAM_DISPLAY_FULL;

		// Get the video
		$video = ES::video($stream->cluster_id, SOCIAL_TYPE_PAGE, $stream->contextId);

		// Ensure that the video is really published
		if (!$video->isPublished()) {
			return;
		}

		// Set the actor alias
		$actor = $stream->getPostActor($page);

		$this->set('stream', $stream);
		$this->set('video', $video);
		$this->set('actor', $actor);
		$this->set('page', $page);

		// Update the stream title
		$stream->title = parent::display('themes:/site/streams/videos/page/title.' . $stream->verb);
		$stream->preview = parent::display('themes:/site/streams/videos/preview');

		// For Page, we need to pass the page id in order to use custom author for comment
		$stream->comments = $video->getComments($stream->verb, $stream->uid);
		$stream->likes = $video->getLikes($stream->verb, $stream->uid);

		// If the video has a thumbnail, add the opengraph tags
		$thumbnail = $video->getThumbnail();

		if ($thumbnail) {
			$stream->addOgImage($thumbnail);
		}

		// Append the opengraph tags
		if ($stream->content) {
			$stream->addOgDescription($stream->content);
		} else {
			$stream->addOgDescription($stream->title);
		}
	}

	/**
	 * Generates the story form for videos
	 *
	 * @since   2.0
	 * @access  public
	 * @param   string
	 */
	public function onPrepareStoryPanel(SocialStory $story)
	{
		// Get the page id
		$pageId = $story->cluster;

		// Get the video adapter
		$adapter = ES::video($pageId, SOCIAL_TYPE_PAGE);

		$page = ES::page($pageId);
		
		// Ensure that video creation is allowed
		if (!$page->getCategory()->getAcl()->get('videos.create', true)) {
			return;
		}

		// In story panel, we only allow page admin and page members to share video
		if (!$page->isAdmin() && !$page->isMember()) {
			return;
		}

		// Get a list of video categories
		$model = ES::model('Videos');
		$options = array('pagination' => false);

		if (!$this->my->isSiteAdmin()) {
			$options['respectAccess'] = true;
			$options['profileId'] = $this->my->getProfile()->id;
		}
		
		$categories = $model->getCategories($options);

		// Create a new plugin for this video
		$plugin = $story->createPlugin('videos', 'panel');

		// Get the maximum upload filesize allowed
		$uploadLimit = $adapter->getUploadLimit();

		$theme = ES::themes();
		$theme->set('categories', $categories);
		$theme->set('uploadLimit', $uploadLimit);
		$theme->set('video', $adapter);

		$button = $theme->output('site/story/videos/button');
		$form = $theme->output('site/story/videos/form');

		$script = ES::script();
		$script->set('uploadLimit', $uploadLimit);
		$script->set('type', SOCIAL_TYPE_PAGE);
		$script->set('uid', $pageId);

		$plugin->setHtml($button, $form);
		$plugin->setScript($script->output('site/story/videos/plugin'));

		return $plugin;
	}

	/**
	 * Processes after a story is saved on the site. When the story is stored, we need to create the necessary video
	 *
	 * @since   2.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function onBeforeStorySave(SocialStreamTemplate &$template, SocialStream &$stream, $content)
	{
		if ($template->context_type != 'videos') {
			return;
		}

		// Check if user is really allowed to do this?
		$cluster = ES::cluster($template->cluster_type, $template->cluster_id);

		if (!$cluster->isMember() && !$this->my->isSiteAdmin()) {
			JError::raiseError(500, JText::_('COM_EASYSOCIAL_CLUSTER_NOT_ALLOWED_TO_POST_UPDATE'));
			return;
		}

		// Determine the type of the video
		$data = array();
		$data['source'] = $this->input->get('videos_type', '', 'word');
		$data['title'] = $this->input->get('videos_title', '', 'default');
		$data['description'] = $this->input->get('videos_description', '', 'default');
		$data['link'] = $this->input->get('videos_link', '', 'default');
		$data['category_id'] = $this->input->get('videos_category', 0, 'int');
		$data['uid'] = $template->cluster_id;
		$data['type'] = $template->cluster_type;

		// The video author will follow the stream Post As value
		$data['post_as'] = $template->post_as ? $template->post_as : SOCIAL_TYPE_USER;

		// Save options for the video library
		$saveOptions = array();

		// If this is a link source, we just load up a new video library
		if ($data['source'] == 'link') {
			$video = ES::video($template->cluster_id, SOCIAL_TYPE_PAGE);
		}

		// If this is a video upload, the id should be provided because videos are created first.
		if ($data['source'] == 'upload') {
			$id = $this->input->get('videos_id', 0, 'int');

			$video = ES::video($template->cluster_id, SOCIAL_TYPE_PAGE);
			$video->load($id);

			// Video library needs to know that we're storing this from the story
			$saveOptions['story'] = true;

			// We cannot publish the video if auto encoding is disabled
			if ($this->config->get('video.autoencode')) {
				$data['state'] = SOCIAL_VIDEO_PUBLISHED;
			}
		}

		// Check if user is really allowed to upload videos
		if ($video->id && !$video->canEdit()) {
			return JError::raiseError(500, JText::_('COM_EASYSOCIAL_VIDEOS_NOT_ALLOWED_EDITING'));
		}

		// Try to save the video
		$state = $video->save($data, array(), $saveOptions);

		// We should set this to hide the stream from being displayed.
		$stream->hidden = true;

		// We need to update the context
		$template->context_type = SOCIAL_TYPE_VIDEOS;
		$template->context_id = $video->id;

		$options = array();
		$options['userId'] = $this->my->id;
		$options['title'] = $video->title;
		$options['description'] = $video->getDescription();
		$options['permalink'] = $video->getPermalink();
		$options['id'] = $video->id;

		// Only notify followers if the updates is came from Page
		if ($template->post_as == SOCIAL_TYPE_PAGE) {
			$cluster->notifyMembers('video.create', $options);
		}
	}

	public function onAfterStorySave(&$stream, &$streamItem)
	{
		// Determine the type of the video
		$data = array();
		$data['source'] = $this->input->get('videos_type', '', 'word');

		// If this is a video upload, the id should be provided because videos are created first.
		if ($data['source'] == 'upload' && !$this->config->get('video.autoencode')) {
			$streamItem->hidden = true;
		}
	}

	/**
	 * Triggers when unlike happens
	 *
	 * @since   2.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function onAfterLikeDelete(&$likes)
	{
		if (!$likes->type) {
			return;
		}

		// Deduct points when the user unliked a video
		if ($likes->type == 'videos.page.create' || $likes->type == 'videos.page.featured') {
			 ES::points()->assign('video.unlike', 'com_easysocial', $this->my->id);
		}
	}


	/**
	 * Triggers after a like is saved
	 *
	 * @since   2.0
	 * @access  public
	 * @param   object  $params     A standard object with key / value binding.
	 *
	 * @return  none
	 */
	public function onAfterLikeSave(&$likes)
	{
		$allowed = array('videos.page.create', 'videos.page.featured');

		if (!in_array($likes->type, $allowed)) {
			return;
		}

		// Get the actor of the likes
		$actor = ES::user($likes->created_by);

		// Set the email options
		$emailOptions = array(
			'template' => 'apps/page/videos/like.video.item',
			'actor' => $actor->getName(),
			'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
			'actorLink' => $actor->getPermalink(true, true)
		);

		$systemOptions = array(
			'context_type' => $likes->type,
			'actor_id' => $likes->created_by,
			'uid' => $likes->uid,
			'aggregate' => true
		);

		// Standard email subject
		$ownerTitle = 'APP_PAGE_VIDEOS_EMAILS_LIKE_VIDEO_OWNER_SUBJECT';
		$involvedTitle = 'APP_PAGE_VIDEOS_EMAILS_LIKE_VIDEO_INVOLVED_SUBJECT';

		$videoTable = ES::table('Video');
		$videoTable->load($likes->uid);

		$video = ES::video($videoTable->uid, $videoTable->type, $videoTable);

		// Get the page
		$page = ES::page($video->uid);

		// Get the permalink to the video
		$systemOptions['context_ids'] = $video->id;
		$emailOptions['permalink'] = $video->getPermalink(true);
		$systemOptions['url'] = $video->getPermalink(false);

		// For single video items on the stream
		if ($likes->type == 'videos.user.create') {
			$verb = 'create';
		}

		if ($likes->type == 'videos.user.featured') {
			$verb = 'featured';
		}

		// Default title
		$emailOptions['title'] = $ownerTitle;

		// @points: photos.like
		// Assign points for the author for liking this item
		ES::points()->assign('video.like', 'com_easysocial', $likes->created_by);

		// Notify the owner of the video first
		if ($likes->created_by != $video->user_id && $video->post_as != SOCIAL_TYPE_PAGE) {
			ES::notify('likes.item', array($video->user_id), $emailOptions, $systemOptions, $page->notification);
		}

		// If this video is post as Page, notify all the page admin
		if ($video->post_as == SOCIAL_TYPE_PAGE) {
			ES::notify('likes.item', $page->getAdmins($likes->created_by), $emailOptions, $systemOptions, $page->notification);
		}

		$element = 'videos';
		$verb = 'create';
		// // Get additional recipients since photos has tag
		// $additionalRecipients = array();
		// $this->getTagRecipients($additionalRecipients, $video);

		// Get a list of recipients to be notified for this stream item
		// We exclude the owner of the note and the actor of the like here
		$recipients = $this->getStreamNotificationTargets($likes->uid, $element, 'page', $verb, array(), array($video->user_id, $likes->created_by));

		$emailOptions['title'] = $involvedTitle;
		$emailOptions['template'] = 'apps/page/videos/like.video.involved';

		// Notify other participating users
		ES::notify('likes.involved', $recipients, $emailOptions, $systemOptions, $page->notification);

		return;
	}

	/**
	 * Renders the notification item
	 *
	 * @since   1.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function onNotificationLoad(SocialTableNotification &$item)
	{
		$allowed = array('page.video.create', 'comments.item', 'comments.involved', 'likes.item');

		if (!in_array($item->cmd, $allowed)) {
			return;
		}

		if ($item->cmd == 'page.video.create') {
			$hook = $this->getHook('notification', 'updates');
			$hook->execute($item);

			return;
		}

		// Someone posted a comment on the video
		if ($item->cmd == 'comments.item' || $item->cmd == 'comments.involved') {
			$hook = $this->getHook('notification', 'comments');
			$hook->execute($item);

			return;
		}

		// Someone likes a video
		if ($item->cmd == 'likes.item') {
			$hook = $this->getHook('notification', 'likes');
			$hook->execute($item);

			return;
		}

		return;
	}

	/**
	 * Triggered after a comment is deleted
	 *
	 * @since   2.0
	 * @access  public
	 * @param   string
	 * @return
	 */
	public function onAfterDeleteComment(SocialTableComments &$comment)
	{
		$allowed = array('videos.page.create', 'videos.page.featured');

		if (!in_array($comment->element, $allowed)) {
			return;
		}

		// Assign points when a comment is deleted for a video
		ES::points()->assign('video.comment.remove', 'com_easysocial', $comment->created_by);
	}

	/**
	 * Triggered when a comment save occurs
	 *
	 * @since   2.0
	 * @access  public
	 * @param   SocialTableComments The comment object
	 * @return
	 */
	public function onAfterCommentSave(&$comment)
	{
		$allowed = array('videos.page.create', 'videos.page.featured');

		if (!in_array($comment->element, $allowed)) {
			return;
		}

		// Get the actor of the likes
		$actor = ES::user($comment->created_by);

		// Set the email options
		$emailOptions   = array(
			'template' => 'apps/page/videos/comment.video.item',
			'actor' => $actor->getName(),
			'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
			'actorLink' => $actor->getPermalink(true, true),
			'comment' => $comment->comment
		);

		$systemOptions  = array(
			'context_type' => $comment->element,
			'context_ids' => $comment->uid,
			'actor_id' => $comment->created_by,
			'uid' => $comment->id,
			'aggregate' => true
		);

		// Standard email subject
		$ownerTitle = 'APP_PAGE_VIDEOS_EMAILS_COMMENT_VIDEO_OWNER_SUBJECT';
		$involvedTitle = 'APP_PAGE_VIDEOS_EMAILS_COMMENT_VIDEO_INVOLVED_SUBJECT';

		$videoTable = ES::table('Video');
		$videoTable->load($comment->uid);

		$video = ES::video($videoTable->uid, $videoTable->type, $videoTable);

		// Get the page
		$page = ES::page($video->uid);

		$emailOptions['permalink'] = $video->getPermalink(true, true);
		$systemOptions['url'] = $video->getPermalink(false, false, 'item', false);

		$element = 'videos';
		$verb = 'create';

		// Default email title should be for the owner
		$emailOptions['title'] = $ownerTitle;

		// Assign points for the author for posting a comment
		ES::points()->assign('videos.comment.add', 'com_easysocial', $comment->created_by);

		// Notify the owner of the video first
		if ($video->user_id != $comment->created_by && $video->post_as != SOCIAL_TYPE_PAGE) {
			ES::notify('comments.item', array($video->user_id), $emailOptions, $systemOptions, $page->notification);
		}

		// If this video is post as Page, notify all the page admin
		if ($video->post_as == SOCIAL_TYPE_PAGE) {
			ES::notify('comments.item', $page->getAdmins($comment->created_by), $emailOptions, $systemOptions, $page->notification);
		}

		// Get a list of recipients to be notified for this stream item
		// We exclude the owner of the note and the actor of the like here
		$recipients = $this->getStreamNotificationTargets($comment->uid, $element, 'page', $verb, array(), array($video->user_id, $comment->created_by));

		$emailOptions['title'] = $involvedTitle;
		$emailOptions['template'] = 'apps/page/videos/comment.video.involved';

		// Notify other participating users
		ES::notify('comments.involved', $recipients, $emailOptions, $systemOptions, $page->notification);

		return;
	}

}
