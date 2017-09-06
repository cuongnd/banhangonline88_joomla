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

class SocialPageAppPhotos extends SocialAppItem
{
	/**
	 * Renders the notification item
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function onNotificationLoad(SocialTableNotification &$item)
	{
		$allowed = array('comments.item', 'comments.involved', 'likes.item', 'likes.involved', 'photos.tagged',
							'likes.likes', 'comments.comment.add');

		if (!in_array($item->cmd, $allowed)) {
			return;
		}

		// When user comments a single photo
		$allowedContexts = array('photos.page.upload', 'stream.page.upload', 'albums.page.create', 'photos.page.uploadAvatar', 'photos.page.updateCover');
		if (($item->cmd == 'comments.item' || $item->cmd == 'comments.involved') && in_array($item->context_type, $allowedContexts)) {

			$hook = $this->getHook('notification', 'comments');
			$hook->execute($item);

			return;
		}

		// When user likes a single photo
		if (($item->cmd == 'likes.item' || $item->cmd == 'likes.involved') && in_array($item->context_type, $allowedContexts)) {

			$hook = $this->getHook('notification', 'likes');
			$hook->execute($item);

			return;
		}

		// When user is tagged in a photo
		if ($item->cmd == 'photos.tagged' && $item->context_type == 'tagging') {

			$hook = $this->getHook('notification', 'tagging');
			$hook->execute($item);
		}


		return;
	}

	/**
	 * Fixed legacy issues where the app is displayed on apps list of a page.
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function appListing($view, $id, $type)
	{
		return false;
	}


	/**
	 * Triggered to validate the stream item whether should put the item as valid count or not.
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function onStreamCountValidation(&$item, $includePrivacy = true)
	{
		// If this is not it's context, we don't want to do anything here.
		if ($item->context_type != 'photos') {
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
	 * Trigger for onPrepareStream
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function onPrepareStream(SocialStreamItem &$item)
	{
		// We only want to process related items
		if ($item->context != 'photos') {
			return;
		}

		$config	= ES::config();

		// Do not allow user to access photos if it's not enabled
		if (!$config->get('photos.enabled') && $item->verb != 'uploadAvatar' && $item->verb != 'updateCover') {
			return;
		}

		// page access checking
		$page = $item->getCluster();

		if (!$page || !$page->id) {
			return;
		}

		// Test if the viewer can really view the item
		if (!$page->canViewItem()) {
			return;
		}

		// check the page category allow photo acl permission
		if (!$page->getCategory()->getAcl()->get('photos.enabled', true) || !$page->getParams()->get('photo.albums', true)) {
			return;
		}

		// Get current logged in user.
		$my = ES::user();

		$element = $item->context;
		$uid = $item->contextId;

		$photoId = $item->contextId;
		$photo = $this->getPhotoFromParams($item);

		// Process actions on the stream
		$this->processActions($item, $page->id);

		// Get the app params.
		$params = $this->getParams();

		$item->display = SOCIAL_STREAM_DISPLAY_FULL;

		if ($item->verb == 'uploadAvatar' && $params->get('stream_avatar', true)) {
			$this->prepareUploadAvatarStream($item, $page);
		}

		if ($item->verb == 'updateCover' && $params->get('stream_cover', true)) {
			$this->prepareUpdateCoverStream($item, $page);
		}

		// Photo stream types. Uploaded via the story form
		if ($item->verb == 'share' && $params->get('stream_share', true)) {
			$this->prepareSharePhotoStream($item, $page);
		}

		if (($item->verb == 'add' || $item->verb == 'create') && $params->get('stream_upload', true)) {
			$this->preparePhotoStream($item, $page);
		}

		return true;
	}

	/**
	 * Processes the stream actions
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function processActions(SocialStreamItem &$item, $pageId)
	{
		// Whether the item is shared or uploaded via the photo albums, we need to bind the repost here
		$repost = ES::get('Repost', $item->uid, SOCIAL_TYPE_STREAM, SOCIAL_APPS_GROUP_PAGE);
		$item->repost = $repost;


		$photoStreams = array('add', 'create', 'share');

		// lets check how many photos in this stream
		if (count($item->contextIds) == 1 && in_array($item->verb, $photoStreams)) {
			$photo 		= ES::table('Photo');
			$photo->load($item->contextIds[0]);

			// if single photos, we reset the repost and use photo id instead. #5730
			$repost = ES::get('Repost', $photo->id, SOCIAL_TYPE_PHOTO, SOCIAL_APPS_GROUP_PAGE);
			$item->repost = $repost;
		}


		// For photo items that are shared on the stream
		if ($item->verb =='share') {

			// By default, we'll use the stream id as the object id
			$objectId = $item->uid;
			$objectType = SOCIAL_TYPE_STREAM;
			$commentUrl = FRoute::stream(array('layout' => 'item', 'id' => $item->uid));

			// When there is only 1 photo that is shared on the stream, we need to link to the photo item
			// We will only alter the id
			if (count($item->contextIds) == 1) {
				$photo = ES::table('Photo');
				$photo->load($item->contextIds[0]);

				$objectId = $photo->id;
				$objectType = SOCIAL_TYPE_PHOTO;
				$commentUrl = $photo->getPermalink();
			}

			// Append the likes action on the stream
			$likes = ES::likes();
			$likes->get($objectId, $objectType, 'upload', SOCIAL_APPS_GROUP_PAGE, $item->uid, array('clusterId' => $pageId));
			$item->likes = $likes;

			$commentParams = array('url' => $commentUrl);

			// Set the cluster id so that we know the comment is belong to this cluster
			$commentParams['clusterId'] = $pageId;

			// Append the comment action on the stream
			$comments = ES::comments($objectId, $objectType, 'upload', SOCIAL_APPS_GROUP_PAGE, $commentParams, $item->uid);
			$item->comments = $comments;

			return;
		}

		if ($item->verb == 'uploadAvatar' || $item->verb == 'updateCover') {

			$photo = $this->getPhotoFromParams($item);

			// Apply comments on the stream
			$commentParams = array('url' => $item->getPermalink());

			// Set the cluster id so that we know the comment is belong to this cluster
			$commentParams['clusterId'] = $pageId;

			$item->comments = ES::comments($photo->id, SOCIAL_TYPE_PHOTO, $item->verb, SOCIAL_APPS_GROUP_PAGE, $commentParams, $item->uid);
		}

		// Here onwards, we are assuming the user is uploading the photos via the albums area.
		// If there is more than 1 photo uploaded, we need to link the likes and comments on the album
		if (count($item->contextIds) > 1) {

			$photos = $this->getPhotoFromParams($item);
			$photo = isset($photos[0]) ? $photos[0] : false;

			// If we can't get anything, skip this
			if (!$photo) {
				return;
			}

			$element = SOCIAL_TYPE_ALBUM;
			$uid = $photo->album_id;

			// Get the album object
			$album = ES::table('Album');
			$album->load($photo->album_id);

			// Format the likes for the stream
			$likes = ES::likes();
			$likes->get($photo->album_id, 'albums', 'create', SOCIAL_APPS_GROUP_PAGE, null, array('clusterId' => $pageId));
			$item->likes = $likes;

			// Apply comments on the stream
			$commentParams = array('url' => $album->getPermalink());

			// Set the cluster id so that we know the comment is belong to this cluster
			$commentParams['clusterId'] = $pageId;

			$comments = ES::comments($photo->album_id, 'albums', 'create', SOCIAL_APPS_GROUP_PAGE, $commentParams);

			// Stream id must be 0 for albums. #4984
			$comments->stream_id = 0;
			$item->comments = $comments;

			return;
		}
	}

	/**
	 * Retrieve the table object from the stream item params
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function getPhotoFromParams(SocialStreamItem &$item, $privacy = null)
	{
		if (count($item->contextIds) > 0 && $item->verb != 'uploadAvatar' && $item->verb != 'updateCover')
		{
			$photos = array();

			// We only want to get a maximum of 5 photos if we have more than 1 photo to show.
			$ids = array_reverse($item->contextIds);
			$limit = 5;

			for ($i = 0; $i < count($ids) && $i < $limit; $i++) {
				$photo = ES::table('Photo');
				$photo->load($ids[$i]);

				$photos[] = $photo;
			}

			return $photos;
		}

		// Load up the photo object
		$photo = ES::table('Photo');

		// Get the context id.
		$id = $item->contextId;
		$photo->load($id);

		return $photo;
	}

	/**
	 * Prepares the stream items for photo uploads shared on the stream
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function prepareSharePhotoStream(SocialStreamItem &$item, SocialPage $page)
	{
		// Get the stream object.
		$stream = ES::table('Stream');
		$stream->load($item->uid);

		// Get photo objects
		$photos = $this->getPhotoFromParams($item);

		// Get the first photo's album id.
		$albumId = $photos[ 0 ]->album_id;
		$album = ES::table('Album');
		$album->load($albumId);

		// Get total number of items uploaded.
		$count = count($item->contextIds);

		// Get the actor
		$actor = $item->getPostActor($page);

		// Get params of the app
		$app = ES::table('app');
		$app->loadByElement('photos', 'page', 'apps');
		$params = $app->getParams();

		$this->set('content', $stream->content);
		$this->set('page', $page);
		$this->set('total', count($photos));
		$this->set('photos', $photos);
		$this->set('album', $album);
		$this->set('actor', $actor);
		$this->set('params', $params);
		$this->set('item', $item);

		// Set the display mode to be full.
		$item->title = parent::display('themes:/site/streams/photos/page/share.title');
		$item->preview = parent::display('themes:/site/streams/photos/preview');
	}

	/**
	 * Prepares the stream items for photo uploads that are uploaded through the photos area of the page
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function preparePhotoStream(SocialStreamItem &$item, SocialPage $page)
	{
		// Get photo objects
		$photos = $this->getPhotoFromParams($item);

		// Get the first photo's album id.
		$albumId = $photos[0]->album_id;
		$album = ES::table('Album');
		$album->load($albumId);

		$objectId = $album->id;
		$objectType = SOCIAL_TYPE_ALBUM;
		$commentUrl = $album->getPermalink();

		// When there is only 1 photo that is uploaded, we need to link to the photo item
		if (count($photos) == 1) {
			$photo = ES::table('Photo');
			$photo->load($photos[0]->id);

			$objectId = $photo->id;
			$objectType = SOCIAL_TYPE_PHOTO;
			$commentUrl = $photo->getPermalink();
		}

		// Get total number of items uploaded.
		$count = count($item->contextIds);

		// Get the actor
		$actor = $item->getPostActor($page);

		// Get params of the app
		// Get params
		$app = ES::table('app');
		$app->loadByElement('photos', 'page', 'apps');
		$params = $app->getParams();

		$this->set('count', $count);
		$this->set('page', $page);
		$this->set('totalPhotos', count($photos));
		$this->set('photos', $photos);
		$this->set('album', $album);
		$this->set('actor', $actor);
		$this->set('params', $params);
		$this->set('item', $item);

		// Set the display mode to be full.
		$item->title = parent::display('themes:/site/streams/photos/page/add.title');
		$item->preview = parent::display('themes:/site/streams/photos/preview');
	}

	/**
	 * Prepares the upload avatar stream
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function prepareUploadAvatarStream(SocialStreamItem &$item, SocialPage $page)
	{
		// Get the photo object
		$photo = $this->getPhotoFromParams($item);

		// Set an alias for actor
		$item->setActorAlias($page);

		$this->set('photo', $photo);
		$this->set('page', $page);
		$this->set('item', $item);

		$item->display = SOCIAL_STREAM_DISPLAY_FULL;

		$item->title = parent::display('themes:/site/streams/photos/page/avatar.title');
		$item->preview = parent::display('themes:/site/streams/photos/avatar.preview');
	}

	/**
	 * Prepares the stream item for group cover
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function prepareUpdateCoverStream(SocialStreamItem &$item, SocialPage $page)
	{
		$element = $item->context;
		$uid = $item->contextId;

		// Get the photo object
		$photo = $this->getPhotoFromParams($item);

		// Get the cover of the page
		$cover = $page->getCoverData();

		// Set an alias for actor
		$item->setActorAlias($page);

		$this->set('cover', $cover);
		$this->set('photo', $photo);
		$this->set('page', $page);
		$this->set('item', $item);

		$item->title = parent::display('themes:/site/streams/photos/page/cover.title');
        $item->preview = parent::display('themes:/site/streams/photos/cover.preview');
	}

	/**
	 * Processes a saved story.
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function onAfterStorySave(&$stream, $streamItem, &$template)
	{
		$photos = JRequest::getVar('photos');

		// If there's no data, we don't need to do anything here.
		if (empty($photos)) {
			return;
		}

		if (empty($template->content)) {
			$template->content 	.= '<br />';
		}

		// Now that we know the saving is successfull, we want to update the state of the photo table.
		// And also the actor of the uploader; Page or User
		foreach ($photos as $photoId) {
			$table = ES::table('Photo');
			$table->load($photoId);

			$album = ES::table('Album');
			$album->load($table->album_id);

			$table->state = SOCIAL_STATE_PUBLISHED;
			$table->post_as = $template->post_as ? $template->post_as : SOCIAL_TYPE_USER;
			$table->store();

			// Determine if there's a cover for this album.
			if (!$album->hasCover()) {
				$album->cover_id = $table->id;
				$album->store();
			}

			$template->content 	.= '<img src="' . $table->getSource('thumbnail') . '" width="128" />';
		}

		return true;
	}

	/**
	 * Save trigger which is called after really saving the object.
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function onAfterSave(&$data)
	{
	    // for now we only support the photo added by person. later on we will support
	    // for pages, events and etc.. the source will determine the type.
	    $source = isset($data->source) ? $data->source : 'people';
	    $actor = ($source == 'people') ? ES::get('People', $data->created_by) : '0';

	    // save into activity streams
	    $item = new StdClass();
	    $item->actor_id = $actor->get('node_id');
	    $item->source_type = $source;
	    $item->source_id = $actor->id;
	    $item->context_type = 'photos';
	    $item->context_id = $data->id;
	    $item->verb = 'upload';
	    $item->target_id = $data->album_id;

	    //$item   = get_object_vars($item);
        //ES::get('Stream')->addStream(array($item, $item, $item));
        ES::get('Stream')->addStream($item);
		return true;
	}

	/**
	 * Prepares the story panel for pages story
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function onPrepareStoryPanel($story)
	{
		$my = ES::user();

		if (!$my->getAccess()->allowed('photos.create')) {
			return;
		}

		if (!$this->config->get('photos.enabled') || !$my->getAccess()->allowed('albums.create')) {
			return;
		}

		// Get the page
		$page = ES::page($story->cluster);

		// check the page category allow photo acl permission
		if (!$page->getCategory()->getAcl()->get('photos.enabled', true) || !$page->getParams()->get('photo.albums', true)) {
			return;
		}

		// Get current logged in user.
		$access = $page->getAccess();

		// Create the story plugin
		$plugin = $story->createPlugin("photos", "panel");


		$theme = ES::themes();

		// Check for page's access
		if ($access->exceeded('photos.max', $page->getTotalPhotos())) {
			$theme->set('exceeded', JText::sprintf('COM_EASYSOCIAL_PHOTOS_EXCEEDED_MAX_UPLOAD', $access->get('photos.uploader.max')));
		}

		// check max photos upload daily here.
		if ($access->exceeded('photos.maxdaily', $page->getTotalPhotos(true))) {
			$theme->set('exceeded', JText::sprintf('COM_EASYSOCIAL_PHOTOS_EXCEEDED_DAILY_MAX_UPLOAD', $access->get('photos.uploader.maxdaily')));
		}

        $button = $theme->output('site/story/photos/button');
        $form = $theme->output('site/story/photos/form');

       	// Attach the script files
        $script = ES::script();
        $maxSize = $access->get('photos.maxsize', 5);

        $script->set('type', SOCIAL_TYPE_PAGE);
        $script->set('uid', $page->id);
		$script->set('maxFileSize', $maxSize . 'M');
		$scriptFile = $script->output('site/story/photos/plugin');

		$plugin->setHtml($button, $form);
		$plugin->setScript($scriptFile);

		return $plugin;
	}

	/**
	 * Triggers when unlike happens
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function onAfterLikeDelete(&$likes)
	{
		if (!$likes->type) {
			return;
		}

		// Set the default element.
		$element = $likes->type;
		$uid = $likes->uid;

		if (strpos($element, '.') !== false) {
			$data = explode('.', $element);
			$page = $data[1];
			$element = $data[0];
		}

		if ($element != SOCIAL_TYPE_PHOTO) {
			return;
		}

		// Get the photo object
		$photo = ES::table('Photo');
		$photo->load($uid);

		// @points: photos.unlike
		// Deduct points for the current user for unliking this item
		$photo->assignPoints('photos.unlike', ES::user()->id);
	}

	/**
	 * Triggers after a like is saved
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function onAfterLikeSave(&$likes)
	{
		$allowed = array('photos.page.upload', 'stream.page.upload', 'albums.page.create', 'photos.page.uploadAvatar', 'photos.page.updateCover');

		if (!in_array($likes->type, $allowed)) {
			return;
		}

		$type = explode(".", $likes->type);
		$verb = $type[2];
		$element = $type[0];

		// For likes on albums when user uploads multiple photos within an album
		if ($likes->type == 'albums.page.create') {

			// Since the uid is tied to the album we can get the album object
			$album = ES::table('Album');
			$album->load($likes->uid);

			// Get the actor of the likes
			$actor = ES::user($likes->created_by);

			// Load the page
			$page = ES::page($album->uid);

	        // Set the email options
	        $emailOptions = array(
	            'title' => 'APP_PAGE_PHOTOS_EMAILS_LIKE_ALBUM_ITEM_SUBJECT',
	            'template' => 'apps/page/photos/like.album.item',
	            'permalink' => $album->getPermalink(true, true),
	            'albumTitle' => $album->get('title'),
	            'albumPermalink' => $album->getPermalink(true, true),
	            'albumCover' => $album->getCover(),
	            'actor' => $actor->getName(),
	            'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
	            'actorLink' => $actor->getPermalink(true, true)
	       	);

	        $systemOptions = array(
	            'context_type' => $likes->type,
	            'context_ids' => $album->id,
	            'url' => $album->getPermalink(false, false, 'item', false),
	            'actor_id' => $likes->created_by,
	            'uid' => $likes->uid,
	            'aggregate' => true
	       	);


	        // Notify the owner of the photo first
	        if ($likes->created_by != $album->user_id) {
	        	ES::notify('likes.item', array($album->user_id), $emailOptions, $systemOptions, $page->notification);
	        }

	        // Get a list of recipients to be notified for this stream item
	        // We exclude the owner of the note and the actor of the like here
	        $recipients = $this->getStreamNotificationTargets($likes->uid, 'albums', 'page', 'create', array(), array($album->user_id, $likes->created_by));

	        $emailOptions['title'] = 'APP_PAGE_PHOTOS_EMAILS_LIKE_ALBUM_INVOLVED_SUBJECT';
	        $emailOptions['template'] = 'apps/page/photos/like.album.involved';

	        // Notify other participating users
	        ES::notify('likes.involved', $recipients, $emailOptions, $systemOptions, $page->notification);

			return;
		}

		// Get the actor of the likes
		$actor = ES::user($likes->created_by);

        // Set the email options
        $emailOptions = array(
            'template' => 'apps/page/photos/like.photo.item'
       	);

        $systemOptions = array(
            'context_type' => $likes->type,
            'actor_id' => $likes->created_by,
            'uid' => $likes->uid,
            'aggregate' => true
       	);

       	$involvedTitle = 'APP_PAGE_PHOTOS_EMAILS_LIKE_PHOTO_INVOLVED_SUBJECT';

		// If this item is multiple share on the stream, we need to get the photo id here.
		if ($likes->type == 'stream.page.upload') {

			// Since this item is tied to the stream, we need to load the stream object
			$stream = ES::table('Stream');
			$stream->load($likes->uid);

			// Get the photo object from the context id of the stream
			$model = ES::model('Stream');
			$origin = $model->getContextItem($likes->uid);

			$photo = ES::table('Photo');
			$photo->load($origin->context_id);

			// Standard email subject
			$ownerTitle = 'APP_PAGE_PHOTOS_EMAILS_LIKE_PHOTO_ITEM_SUBJECT_' . strtoupper($photo->post_as);

			$systemOptions['context_ids'] = $photo->id;
			$systemOptions['url'] = $stream->getPermalink(false, false, false);
			$emailOptions['permalink'] = $stream->getPermalink(true, true);
		}

		// For single photo items on the stream
		if ($likes->type == 'photos.page.upload' || $likes->type == 'photos.page.uploadAvatar' || $likes->type == 'photos.page.updateCover') {
			// Get the photo object
			$photo = ES::table('Photo');
			$photo->load($likes->uid);

			// Standard email subject
			$ownerTitle = 'APP_PAGE_PHOTOS_EMAILS_LIKE_PHOTO_ITEM_SUBJECT_' . strtoupper($photo->post_as);

	        $systemOptions['context_ids'] = $photo->id;
			$systemOptions['url'] = $photo->getPermalink(false, false, 'item', false);
			$emailOptions['permalink'] = $photo->getPermalink(true, true, 'item');

	        if ($likes->type == 'photos.page.uploadAvatar') {

	        	$ownerTitle = 'APP_PAGE_PHOTOS_EMAILS_LIKE_PROFILE_PICTURE_ITEM_SUBJECT';
	        	$involvedTitle = 'APP_PAGE_PHOTOS_EMAILS_LIKE_PROFILE_PICTURE_INVOLVED_SUBJECT';
	        }

	        if ($likes->type == 'photos.page.updateCover') {

	        	$ownerTitle = 'APP_PAGE_PHOTOS_EMAILS_LIKE_PROFILE_COVER_ITEM_SUBJECT';
	        	$involvedTitle = 'APP_PAGE_PHOTOS_EMAILS_LIKE_PROFILE_COVER_INVOLVED_SUBJECT';
	        }
		}

		// We try to modify the target
       	// If this photo was shared by Page, we need to notify all the page admins
       	// If was shared by user, we need to notify only the user who upload this photo
       	$targets = array($photo->user_id);

       	// Load the page
       	$page = ES::page($photo->uid);

		// For certain types, we need to modify the author
		if ($verb == 'upload') {

			// [Page Compatibility] - If the liker is the page admin, change the author
			if ($page->isAdmin($actor->id)) {
				$actor = $page;
			}

       	}

		// @points: photos.like
		// Assign points for the author for liking this item
		$photo->assignPoints('photos.like', $likes->created_by);

		// Set the email params
		$emailOptions['title'] = $ownerTitle;
		$emailOptions['actor'] = $actor->getName();
		$emailOptions['page'] = $page->getName();
		$emailOptions['actorAvatar'] = $actor->getAvatar(SOCIAL_AVATAR_SQUARE);
		$emailOptions['actorLink'] = $actor->getPermalink(true, true);

        // Notify the owner of the photo first
        if ($likes->created_by != $photo->user_id && $photo->post_as != SOCIAL_TYPE_PAGE) {
        	ES::notify('likes.item', $targets, $emailOptions, $systemOptions, $page->notification);
        }

        // If this photo is post as Page, notify all the page admin
        if ($photo->post_as == SOCIAL_TYPE_PAGE) {
        	ES::notify('comments.item', $page->getAdmins($likes->created_by), $emailOptions, $systemOptions, $page->notification);
        }

        // Get additional recipients since photos has tag
        $additionalRecipients = array();
        $this->getTagRecipients($additionalRecipients, $photo);

        // Get a list of recipients to be notified for this stream item
        // We exclude the owner of the note and the actor of the like here
        $recipients = $this->getStreamNotificationTargets($likes->uid, $element, 'page', $verb, $additionalRecipients, array($photo->user_id, $likes->created_by));

        $emailOptions['title'] = $involvedTitle;
        $emailOptions['template'] = 'apps/page/photos/like.photo.involved';

        // Notify other participating users
        ES::notify('likes.involved', $recipients, $emailOptions, $systemOptions, $page->notification);

		return;
	}

	/**
	 * Triggered when a comment save occurs
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function onAfterCommentSave(&$comment)
	{
		$allowed = array('photos.page.upload', 'stream.page.upload', 'albums.page.create', 'photos.page.uploadAvatar', 'photos.page.updateCover');

		if (!in_array($comment->element, $allowed)) {
			return;
		}

		$type = explode(".", $comment->element);
		$verb = $type[2];
		$element = $type[0];

		// For likes on albums when user uploads multiple photos within an album
		if ($comment->element == 'albums.page.create') {

			// Since the uid is tied to the album we can get the album object
			$album = ES::table('Album');
			$album->load($comment->uid);

			// Load the Page
			$page = ES::page($album->uid);

			// Get the actor of the likes
			$actor = ES::user($comment->created_by);

	        // Set the email options
	        $emailOptions = array(
	            'title' => 'APP_PAGE_PHOTOS_EMAILS_COMMENT_ALBUM_ITEM_SUBJECT',
	            'template' => 'apps/page/photos/comment.album.item',
	            'permalink' => $album->getPermalink(true, true),
				'comment' => $comment->comment,
	            'actor' => $actor->getName(),
	            'actorAvatar' => $actor->getAvatar(SOCIAL_AVATAR_SQUARE),
	            'actorLink' => $actor->getPermalink(true, true),
	            'page' => $page->getName()
	       );

	        $systemOptions = array(
	            'context_type' => $comment->element,
	            'context_ids' => $comment->uid,
	            'url' => $album->getPermalink(false, false, 'item', false),
	            'actor_id' => $comment->created_by,
	            'uid' => $comment->id,
	            'aggregate' => true
	       );


	        // Notify the owner of the photo first
	        if ($comment->created_by != $album->user_id) {
	        	ES::notify('comments.item', array($album->user_id), $emailOptions, $systemOptions, $page->notification);
	        }

	        // Get a list of recipients to be notified for this stream item
	        // We exclude the owner of the note and the actor of the like here
	        $recipients = $this->getStreamNotificationTargets($comment->uid, 'albums', 'page', 'create', array(), array($album->user_id, $comment->created_by));

	        $emailOptions['title'] = 'APP_PAGE_PHOTOS_EMAILS_COMMENT_ALBUM_INVOLVED_SUBJECT';
	        $emailOptions['template'] = 'apps/page/photos/comment.album.involved';

	        // Notify other participating users
	        ES::notify('comments.involved', $recipients, $emailOptions, $systemOptions, $page->notification);

			return;
		}

		// Get the actor of the likes
		$actor = ES::user($comment->created_by);

		// Set the email options
		$emailOptions = array(
		    'template' => 'apps/page/photos/comment.photo.item',
		    'comment' => $comment->comment
		);

		$systemOptions = array(
		    'context_type' => $comment->element,
		    'context_ids' => $comment->uid,
		    'actor_id' => $comment->created_by,
		    'uid' => $comment->id,
		    'aggregate' => true
		);

		// Standard email subject
		$involvedTitle = 'APP_PAGE_PHOTOS_EMAILS_COMMENT_PHOTO_INVOLVED_SUBJECT';

		// If this item is multiple share on the stream, we need to get the photo id here.
		if ($comment->element == 'stream.page.upload') {

			// Since this item is tied to the stream, we need to load the stream object
			$stream = ES::table('Stream');
			$stream->load($comment->uid);

			// Get the photo object from the context id of the stream
			$model = ES::model('Stream');
			$origin = $model->getContextItem($comment->uid);

			$photo = ES::table('Photo');
			$photo->load($origin->context_id);

			// If this photo is post as a Page, we need to change the title of the email
			$ownerTitle = 'APP_PAGE_PHOTOS_EMAILS_COMMENT_PHOTO_ITEM_SUBJECT_' . $photo->post_as;

			// Get the permalink to the photo
			$emailOptions['permalink'] = $stream->getPermalink(true, true);
			$systemOptions['url'] = $stream->getPermalink(false, false, false);
		}

		// For single photo items on the stream
		if ($comment->element == 'photos.page.upload' || $comment->element == 'photos.page.uploadAvatar' || $comment->element == 'photos.page.updateCover') {
			// Get the photo object
			$photo = ES::table('Photo');
			$photo->load($comment->uid);

			// If this photo is post as a Page, we need to change the title of the email
			$ownerTitle = 'APP_PAGE_PHOTOS_EMAILS_COMMENT_PHOTO_ITEM_SUBJECT_' . $photo->post_as;

	        // Get the permalink to the photo
			$emailOptions['permalink'] = $photo->getPermalink(true, true);
			$systemOptions['url'] = $photo->getPermalink(false, false, 'item', false);

			if ($comment->element == 'photos.page.uploadAvatar') {
				$ownerTitle	= 'APP_PAGE_PHOTOS_EMAILS_COMMENT_PROFILE_PICTURE_ITEM_SUBJECT';
				$involvedTitle = 'APP_PAGE_PHOTOS_EMAILS_COMMENT_PROFILE_PICTURE_INVOLVED_SUBJECT';
			}

			if ($comment->element == 'photos.page.updateCover') {
				$ownerTitle = 'APP_PAGE_PHOTOS_EMAILS_COMMENT_PROFILE_COVER_ITEM_SUBJECT';
				$involvedTitle = 'APP_PAGE_PHOTOS_EMAILS_COMMENT_PROFILE_COVER_INVOLVED_SUBJECT';
			}
		}

		// Load the page
		$page = ES::page($photo->uid);

		// For certain types, we need to modify the author
		if ($verb == 'upload') {

			// [Page Compatibility] - If the commentator is the page admin, change the author
			if ($page->isAdmin($actor->id)) {
				$actor = $page;
			}
       	}

		$emailOptions['title'] = $ownerTitle;
	    $emailOptions['actor'] = $actor->getName();
	    $emailOptions['actorAvatar'] = $actor->getAvatar(SOCIAL_AVATAR_SQUARE);
	    $emailOptions['actorLink'] = $actor->getPermalink(true, true);
	    $emailOptions['actorLink'] = $actor->getPermalink(true, true);
	    $emailOptions['page'] = $page->getName();
	    $emailOptions['postAs'] = strtoupper($photo->post_as);

		// @points: photos.like
		// Assign points for the author for liking this item
		$photo->assignPoints('photos.comment.add', $comment->created_by);

        // Notify the owner of the photo first
        if ($photo->user_id != $comment->created_by && $photo->post_as != SOCIAL_TYPE_PAGE) {
        	ES::notify('comments.item', array($photo->user_id), $emailOptions, $systemOptions, $page->notification);
        }

        // If this photo is post as Page, notify all the page admin
        if ($photo->post_as == SOCIAL_TYPE_PAGE) {
        	ES::notify('comments.item', $page->getAdmins($comment->created_by), $emailOptions, $systemOptions, $page->notification);
        }

        // Get additional recipients since photos has tag
        $additionalRecipients = array();
        $this->getTagRecipients($additionalRecipients, $photo);

        // Get a list of recipients to be notified for this stream item
        // We exclude the owner of the note and the actor of the like here
        $recipients = $this->getStreamNotificationTargets($comment->uid, $element, 'page', $verb, $additionalRecipients, array($photo->user_id, $comment->created_by));

        $emailOptions['title'] = $involvedTitle;
        $emailOptions['template'] = 'apps/page/photos/comment.photo.involved';

        // Notify other participating users
        ES::notify('comments.involved', $recipients, $emailOptions, $systemOptions, $page->notification);

		return;
	}

	/**
	 * Responsible to return the excluded verb from this app context
	 * @since	2.0
	 * @access	public
	 */
	public function onStreamVerbExclude(&$exclude)
	{
		// Get app params
		$params	= $this->getParams();

		$excludeVerb = false;

		if (! $params->get('stream_avatar', true)) {
			$excludeVerb[] = 'uploadAvatar';
		}

		if (! $params->get('stream_cover', true)) {
			$excludeVerb[] = 'updateCover';
		}

		if (! $params->get('stream_share', true)) {
			$excludeVerb[] = 'share';
		}

		if (! $params->get('stream_upload', true)) {
			$excludeVerb[] = 'add';
			$excludeVerb[] = 'create';
		}

		if ($excludeVerb !== false) {
			$exclude['photos'] = $excludeVerb;
		}
	}



	/**
	 * Retrieves a list of tag recipients on a photo
	 *
	 * @since	2.0
	 * @access	public
	 */
	private function getTagRecipients(&$recipients, SocialTablePhoto &$photo, $exclusion = array())
	{
		// Get a list of tagged users
		$tags = $photo->getTags(true);

		if (!$tags) {
			return;
		}

		foreach ($tags as $tag) {

			if (!in_array($tag->uid, $exclusion)) {
				$recipients[] = $tag->uid;
			}

		}
	}


	/**
	 * Responsible to generate the activity log
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function onPrepareActivityLog(SocialStreamItem &$item, $includePrivacy = false)
	{
	    if ($item->context != 'photos') {
	        return;
	    }

	    // Get the context id
	    $id = $item->contextId;

	    $page = ES::page($item->cluster_id);

	    // Load the photo table
	    $photo = ES::table('Photo');
	    $state = $photo->load($id);

	    $album = ES::table('Album');
	    $album->load($photo->album_id);

	    // Get the actor
	    $actor = $item->actor;
	    $target = false;

	    $this->set('actor', $actor);
	    $this->set('target', $target);
	    $this->set('album', $album);
	    $this->set('photo', $photo);
	    $this->set('page', $page);

	    $count = count($item->contextIds);
	    $this->set('count', $count);

	    if ($item->verb == 'uploadAvatar') {
	        $file = 'avatar.title';
	    }

	    if ($item->verb == 'uploadCover') {
	        $file = 'cover.title';
	    }

	    if ($item->verb == 'create' || $item->verb == 'add') {
	        $file = 'add.title';
	    }

	    $item->display = SOCIAL_STREAM_DISPLAY_MINI;
	    $item->title = parent::display('logs/' . $file);
	    $item->content = parent::display('logs/content');
	}
}
