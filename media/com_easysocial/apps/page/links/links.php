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

require_once(__DIR__ . '/helper.php');

class SocialPageAppLinks extends SocialAppItem
{
    public function onNotificationLoad(SocialTableNotification &$item)
    {
        // there is nothing to process.
        return false;
    }

	/**
	 * Fixed legacy issues where the app is displayed on apps list of a page.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function appListing($view, $id, $type)
	{
		return false;
	}

	/**
	 * Processes a saved story.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onAfterStorySave(&$stream, &$streamItem, &$template)
	{
		// Get the link information from the request
		$link = JRequest::getVar('links_url', '');
		$title = JRequest::getVar('links_title', '');
		$content = JRequest::getVar('links_description', '');
		$image = JRequest::getVar('links_image', '');
		$video = JRequest::getVar('links_video', '');

		// If there's no data, we don't need to store in the assets table.
		if (empty($title) && empty($content) && empty($image)) {
			return;
		}

		$registry = ES::registry();
		$registry->set('title', $title);
		$registry->set('content', $content);
		$registry->set('image', $image);
		$registry->set('link', $link);

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
		if ($item->context_type != 'links') {
			return false;
		}

		// if this is a cluster stream, let check if user can view this stream or not.
		$params = ES::registry($item->params);
		$page = ES::page($params->get('page'));

		if (!$page) {
			return;
		}

		$item->cnt = 1;

		$my = ES::user();

		if ($page->type != SOCIAL_PAGES_PUBLIC_TYPE && !$page->isMember($my->id)) {
			$item->cnt = 0;
		}

		return true;
	}


	/**
	 * Generates the stream title of page.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	object	$params		A standard object with key / value binding.
	 *
	 * @return	none
	 */
	public function onPrepareStream(SocialStreamItem &$stream, $includePrivacy = true)
	{
		if ($stream->context != 'links') {
			return;
		}

		// Page access
		$page = $stream->getCluster();

		if (!$page) {
			return;
		}

		if (!$page->canViewItem()) {
			return;
		}

		//get links object, in this case, is the stream_item
		$uid = $stream->uid;

		$options = array('url' => $stream->getPermalink());

		// Set the cluster id so that we know the comment is belong to this cluster
		$options['clusterId'] = $page->id;

		// Load the comments
		$stream->comments = ES::comments($stream->uid, $stream->context, $stream->verb, SOCIAL_APPS_GROUP_PAGE, $options, $stream->uid);

		// Apply likes on the stream
		$stream->likes = ES::likes($stream->uid, $stream->context, $stream->verb, SOCIAL_APPS_GROUP_PAGE, $stream->uid, $options);
		$stream->repost = ES::repost($stream->uid, SOCIAL_TYPE_STREAM, SOCIAL_APPS_GROUP_PAGE);
		$stream->display = SOCIAL_STREAM_DISPLAY_FULL;

		$assets = $stream->getAssets();

		if (empty($assets)) {
			return;
		}

		// Get the assets
		$assets = $assets[0];

		// Load the link object
		$link = ES::table('Link');
		$link->loadByLink($assets->get('link'));

		$oembed = $link->getOembed();
		$image = $link->getImage($assets);

		// Get app params
		$params = $this->getParams();

		// if necessary, feed in our proxy to avoid http over https issue
		$uri = JURI::getInstance();

		if ($params->get('stream_link_proxy', false) && ($oembed || $asset->get('image')) && $uri->getScheme() == 'https') {

			// Check if there are any http links
			if (isset($oembed->thumbnail) && $oembed->thumbnail && stristr($oembed->thumbnail, 'http://') !== false) {
				$oembed->thumbnail = ES::proxy($oembed->thumbnail);
			}

			if ($image && stristr($image, 'http://') !== false) {
				$image = ES::proxy($image);
			}
		}

		// Get the content and truncate accordingly
		$content = $assets->get('content', '');

		if ($params->get('stream_link_truncate')) {
			$content = JString::substr(strip_tags($content), 0, $params->get('stream_link_truncate_length', 250)) . JText::_('COM_EASYSOCIAL_ELLIPSES');
		}

		// Append the OpenGraph tags
		if ($image) {
			$stream->addOgImage($image);
		}

		// Always add the Opengraph contents
		$stream->addOgDescription($content);

		// If the content is empty, try to get the stream content
		if (!$content && $stream->content) {
			$stream->addOgDescription($stream->content);
		}

		// Fall back to use title as the opengraph description
		if (!$content && !$stream->content) {
			$stream->addOgDescription($stream->title);
		}

		// Get the actor
		$actor = $stream->getPostActor($page);

		$this->set('image', $image);
		$this->set('content', $content);
		$this->set('params', $params);
		$this->set('oembed', $oembed);
		$this->set('assets', $assets);
		$this->set('actor', $actor);
		$this->set('stream', $stream);
		$this->set('page', $page);
		$this->set('link', $link);

		$stream->title = parent::display('themes:/site/streams/links/page/title');
		$stream->preview = parent::display('themes:/site/streams/links/preview');
	}


	/**
	 * Responsible to generate the activity logs.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	object	$params		A standard object with key / value binding.
	 *
	 * @return	none
	 */
	public function onPrepareActivityLog(SocialStreamItem &$item, $includePrivacy = true)
	{
		if ($item->context != 'links') {
			return;
		}

		//get story object, in this case, is the stream_item
		$tbl = ES::table('StreamItem');
		$tbl->load($item->uid); // item->uid is now streamitem.id

		$uid = $tbl->uid;

		//get story object, in this case, is the stream_item
		$my = ES::user();
		$privacy = ES::privacy($my->id);

		$actor = $item->actor;
		$target = count($item->targets) > 0 ? $item->targets[0] : '';

		$assets = $item->getAssets($uid);

		if (empty($assets)) {
			return;
		}

		$assets = $assets[0];

		$this->set('assets', $assets);
		$this->set('actor', $actor);
		$this->set('target', $target);
		$this->set('stream', $item);


		$item->display = SOCIAL_STREAM_DISPLAY_MINI;
		$item->title = parent::display('logs/' . $item->verb);

		return true;

	}

	/**
	 * Prepares what should appear in the story form.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onPrepareStoryPanel($story)
	{
		$params = $this->getParams();

		$page = ES::page($story->cluster);

		// Determine if we should attach ourselves here.
		if (!$params->get('story_links', true) || !$this->getApp()->hasAccess($page->category_id)) {
			return;
		}

		// Create plugin object
		$plugin = $story->createPlugin('links', 'panel');

		// We need to attach the button to the story panel
		$theme = ES::themes();

        $button = $theme->output('site/story/links/button');
        $form = $theme->output('site/story/links/form');

		// Attach the scripts
		$script = ES::script();
		$scriptFile = $script->output('site/story/links/plugin');

		$plugin->setHtml($button, $form);
		$plugin->setScript($scriptFile);

		return $plugin;
	}
}
