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

class SocialPageAppFiles extends SocialAppItem
{
	/**
	 * Determines if the app should be displayed in the list
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function appListing($view, $id, $type)
	{
		$page = ES::page($id);

		// Determines if this page has access to files
		$access = $page->getAccess();

		if (!$access->get('files.enabled', true)) {
			return false;
		}

		if (!$page->isMember()) {
			return false;
		}

		return true;
	}

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
		if ($item->context_type != SOCIAL_TYPE_FILES) {
			return false;
		}

		// if this is a cluster stream, let check if user can view this stream or not.
		$params = ES::registry($item->params);
		$page = ES::page($params->get('page'));

		if (!$page) {
			return;
		}

		$item->cnt = 1;

		if ($page->type != SOCIAL_PAGES_PUBLIC_TYPE) {
			if (!$page->isMember(ES::user()->id)) {
				$item->cnt = 0;
			}
		}

		return true;
	}

	/**
	 * Processes notifications for files
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onNotificationLoad(SocialTableNotification &$item)
	{
		$allowed = array('files.page.uploaded');

		if (!in_array($item->context_type, $allowed)) {
			return;
		}

		if ($item->type == 'likes' && $item->context_type == 'files.page.uploaded') {

			$hook = $this->getHook('notification', 'likes');
			$hook->execute($item);
			return;
		}

		if ($item->type == 'comments' && $item->context_type == 'files.page.uploaded') {

			$hook = $this->getHook('notification', 'comments');
			$hook->execute($item);
			return;
		}
	}

	/**
	 * Processes when user likes a file
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterLikeSave(&$likes)
	{
		$allowed = array('files.page.uploaded');

		if (!in_array($likes->type, $allowed)) {
			return;
		}

        // Set the default element.
        $uid = $likes->uid;
        $data = explode('.', $likes->type);
        $element = $data[0];
        $verb = $data[2];

		if ($likes->type == 'files.page.uploaded') {

	        // Get the owner of the post.
	        $stream = ES::table('Stream');
	        $stream->load($likes->stream_id);

	        // Since we have the stream, we can get the page id
	        $page = ES::page($stream->cluster_id);

	        // Get the actor
	        $actor = ES::user($likes->created_by);

	        $emailOptions   = array(
	            'title' => 'APP_PAGE_FILES_EMAILS_LIKE_ITEM_SUBJECT',
	            'template' => 'apps/page/files/like.file.item',
	            'permalink' => $stream->getPermalink(true, true),
	            'actor' => $actor->getName(),
	            'page' => $page->getName(),
	            'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
	            'actorLink' => $actor->getPermalink(true, true)
	       );

	        $systemOptions  = array(
	            'context_type' => $likes->type,
	            'context_ids' => $stream->id,
	            'url' => $stream->getPermalink(false, false, false),
	            'actor_id' => $likes->created_by,
	            'uid' => $likes->uid,
	            'aggregate' => true
	       	);

	        // [Page Compatibility] Only notify if the liker is not a page admin
	        // Notify the owner first
	        if (!$page->isAdmin($likes->created_by)) {
	        	ES::notify('likes.item', $page->getAdmins(), $emailOptions, $systemOptions, $page->notification);
	        }

	        // Get a list of recipients to be notified for this stream item
	        // We exclude the owner of the note and the actor of the like here
	        $recipients = $this->getStreamNotificationTargets($likes->uid, $element, 'page', $verb, array(), array($stream->actor_id, $likes->created_by));

	        $emailOptions['title'] = 'APP_PAGE_FILES_EMAILS_LIKE_INVOLVED_SUBJECT';
	        $emailOptions['template'] = 'apps/page/files/like.file.involved';

	        // Notify other participating users
	        ES::notify('likes.involved', $recipients, $emailOptions, $systemOptions, $page->notification);

	        return;
		}
	}

	/**
	 * Processes when user comments on a file
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterCommentSave(&$comment)
	{
		$allowed = array('files.page.uploaded');

		if (!in_array($comment->element, $allowed)) {
			return;
		}

		if ($comment->element == 'files.page.uploaded') {

			// Get the stream object
			$stream = ES::table('Stream');
			$stream->load($comment->uid);


			$segments = explode('.', $comment->element);
			$element = $segments[0];
	        $verb = $segments[2];

			// Load up the stream object
			$stream = ES::table('Stream');
			$stream->load($comment->stream_id);

			// Get the page object
			$page = ES::page($stream->cluster_id);

	        // Get the comment actor
	        $actor = ES::user($comment->created_by);

	        $emailOptions = array(
	            'title' => 'APP_PAGE_FILES_EMAILS_COMMENT_ITEM_SUBJECT',
	            'template' => 'apps/page/files/comment.file.item',
	            'comment' => $comment->comment,
	            'page' => $page->getName(),
	            'permalink' => $stream->getPermalink(true, true),
	            'actor' => $actor->getName(),
	            'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
	            'actorLink' => $actor->getPermalink(true, true)
	       );

	        $systemOptions = array(
	            'content' => $comment->comment,
	            'context_type' => $comment->element,
	            'context_ids' => $stream->id,
	            'url' => $stream->getPermalink(false, false, false),
	            'actor_id' => $comment->created_by,
	            'uid' => $comment->uid,
	            'aggregate' => true
	       	);

	        // [Page Compatibility] Only notify if the commentator is not a page admin
	        // Notify the owner first
	        if (!$page->isAdmin($comment->created_by)) {
	        	ES::notify('comments.item', $page->getAdmins(), $emailOptions, $systemOptions, $page->notification);
	        }

	        // Get a list of recipients to be notified for this stream item.
	        // We exclude the owner of the note and the actor of the like here
	        $recipients = $this->getStreamNotificationTargets($comment->uid, $element, 'page', $verb, array(), array($stream->actor_id, $comment->created_by));

	        $emailOptions['title'] = 'APP_PAGE_FILES_EMAILS_COMMENT_INVOLVED_SUBJECT';
	        $emailOptions['template'] = 'apps/page/files/comment.file.involved';

	        // Notify participating users
	        ES::notify('comments.involved', $recipients, $emailOptions, $systemOptions, $page->notification);

	        return;
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
		if ($item->context != SOCIAL_TYPE_FILES) {
			return;
		}

		// page access checking
		$page = ES::page($item->cluster_id);

		if (!$page) {
			return;
		}

		if (!$page->canViewItem()) {
			return;
		}

		$params = ES::registry($item->contextParams[0]);

		// Do not allow user to repost files
		$item->repost = false;

        $items = $params->get('file');
        $total = count($items);

     	if (!$items) {
     		return;
     	}

     	$files = array();

        foreach ($items as $id) {
            $file = ES::table('File');
            $state = $file->load($id);

            if ($state) {
                $files[] = $file;
            }
        }

        // Only proceed if the file still exist
        if (!$files) {
            return;
        }

        $plurality = $total > 1 ? '_PLURAL' : '_SINGULAR';

		$item->setActorAlias($page);

		$this->set('files', $files);
		$this->set('cluster', $page);
		$this->set('plurality', $plurality);
		$this->set('total', $total);

		$options = array('url' => $item->getPermalink());

		// Set the cluster id so that we know the comment is belong to this cluster
		$options['clusterId'] = $page->id;

		// Load the comments
		$item->comments = ES::comments($item->uid, $item->context, $item->verb, SOCIAL_APPS_GROUP_PAGE, $options, $item->uid);

		// Load up the other contents now.
		$item->likes = ES::likes($item->uid, $item->context, $item->verb, SOCIAL_APPS_GROUP_PAGE, $item->uid, $options);
		$item->display = SOCIAL_STREAM_DISPLAY_FULL;
		$item->title = parent::display('themes:/site/streams/files/page/title');
		$item->preview = parent::display('themes:/site/streams/files/preview');
	}

    /**
     * Prepares what should appear in the story form.
     *
     * @since   2.0
     * @access  public
     * @param   string
     * @return
     */
    public function onPrepareStoryPanel($story)
    {
        $params = $this->getParams();

        // Determine if the user can use this feature
        if (!$params->get('enable_uploads', true)) {
            return;
        }

        // Get the event object
        $page = ES::page($story->cluster);

		// Determines if this page has access to files
		$access = $page->getAccess();

		if (!$access->get('files.enabled', true) || !$this->getApp()->hasAccess($page->category_id)) {
			return;
		}

		// Only Page admin can upload a file
		if (!$page->isAdmin()) {
			return;
		}

        // Create plugin object
        $plugin = $story->createPlugin('files', 'panel');

        // Get the allowed extensions
        $allowedExtensions = $params->get('allowed_extensions', 'zip,txt,pdf,gz,php,doc,docx,ppt,xls');
        $maxFileSize = $params->get('max_upload_size', 8) . 'M';

        // We need to attach the button to the story panel
        $theme  = ES::themes();

        $plugin->button->html = $theme->output('site/story/files/button');
        $plugin->content->html = $theme->output('site/story/files/form');

        // Attachment script
        $script = ES::script();
        $script->set('allowedExtensions', $allowedExtensions);
        $script->set('maxFileSize', $maxFileSize);
        $script->set('type', SOCIAL_TYPE_PAGE);
        $script->set('uid', $story->cluster);

        $plugin->script = $script->output('site/story/files/plugin');

        return $plugin;
    }

    /**
     * Processes after the story is saved so that we can generate a stream item for this
     *
     * @since   2.0
     * @access  public
     * @param   string
     * @return
     */
    public function onAfterStorySave(SocialStream &$stream , SocialTableStreamItem $streamItem, &$template)
    {
        $files = $this->input->get('files', array(), 'array');

        if (!$files) {
            return;
        }

        // We need to set the context id's for the files shared in this stream.
        $params = ES::registry();
        $params->set('file', $files);

        $streamItem->verb = 'uploaded';
        $streamItem->params = $params->toString();
        $streamItem->store();
    }
}
