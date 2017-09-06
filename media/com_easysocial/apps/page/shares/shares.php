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

ES::import('admin:/includes/themes/themes');
ES::import('admin:/includes/apps/apps');

class SocialPageAppShares extends SocialAppItem
{
	/**
	 * Process notifications
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function onNotificationLoad(SocialTableNotification &$item)
	{
        // Processes notifications when someone repost another person's item
        $allowed = array('add.stream');

        if (!in_array($item->context_type, $allowed)) {
            return;
        }

        // We should only process items from page here.
        $share = ES::table('Share');
        $share->load($item->context_ids);

        if ($share->element != 'stream.page') {
            return;
        }

        if ($item->type == 'repost') {

            $hook = $this->getHook('notification', 'repost');
            $hook->execute($item);

            return;
        }
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
		if($item->context_type != 'shares')
		{
			return false;
		}

		$item->cnt = 1;

		if($includePrivacy)
		{
			$uid = $item->id;
			$my = ES::user();
			$privacy = ES::privacy($my->id);

			$sModel = ES::model('Stream');
			$aItem = $sModel->getActivityItem($item->id, 'uid');

			if($aItem)
			{
				$uid = $aItem[0]->id;

				if(!$privacy->validate('core.view', $uid , SOCIAL_TYPE_ACTIVITY , $item->actor_id))
				{
					$item->cnt = 0;
				}
			}
		}

		return true;
	}

    /**
     * Notify the owner of the stream
     *
     * @since   1.2
     * @access  public
     * @param   string
     * @return
     */
    public function onAfterStreamSave(SocialStreamTemplate &$streamTemplate)
    {
        // We only want to process shares
        if ($streamTemplate->context_type != SOCIAL_TYPE_SHARE || !$streamTemplate->cluster_type) {
            return;
        }

        $allowed = array('add.stream');

        if (!in_array($streamTemplate->verb, $allowed)) {
            return;
        }

        // Because the verb is segmented with a ., we need to split this up
        $namespace = explode('.', $streamTemplate->verb);
        $verb = $namespace[0];
        $type = $namespace[1];

        // Add a notification to the owner of the stream
        $stream = ES::table('Stream');
        $stream->load($streamTemplate->target_id);

        // If the person that is reposting this is the same as the actor of the stream, skip this altogether.
        if ($streamTemplate->actor_id == $stream->actor_id) {
            return;
        }

        // Get the page
        $page = ES::page($streamTemplate->cluster_id);

        // Get the actor
        $actor = ES::user($streamTemplate->actor_id);

        // Get the share object
        $share = ES::table('Share');
        $share->load($streamTemplate->context_id);

        $mailTitle = 'APP_PAGE_SHARES_EMAILS_USER_REPOSTED_USER_POST_SUBJECT';
        $targets = array($stream->actor_id);
        $originalOwner = 'USER';

        // [Page Compatibility]
        if ($stream->post_as == SOCIAL_TYPE_PAGE) {

        	// If the original post belongs to the page, we need to notify all page admins
        	$mailTitle = 'APP_PAGE_SHARES_EMAILS_USER_REPOSTED_PAGE_POST_SUBJECT';
        	$targets = $page->getAdmins();
        	$originalOwner = 'PAGE';
        }

        // Prepare the email params
        $mailParams = array();
        $mailParams['actor'] = $actor->getName();
        $mailParams['actorLink'] = $actor->getPermalink(true, true);
        $mailParams['actorAvatar'] = $actor->getAvatar(SOCIAL_AVATAR_SQUARE);
        $mailParams['page'] = $page->getName();
        $mailParams['pageLink'] = $page->getPermalink(true, true);
        $mailParams['permalink'] = ESR::stream(array('layout' => 'item', 'id' => $share->uid, 'external' => true), true);
        $mailParams['title'] = $mailTitle;
        $mailParams['template'] = 'apps/page/shares/stream.repost';
        $mailParams['originalOwner'] = $originalOwner;

        // Prepare the system notification params
        $systemParams = array();
        $systemParams['context_type'] = $streamTemplate->verb;
        $systemParams['url'] = ESR::stream(array('layout' => 'item', 'id' => $share->uid, 'sef' => false));
        $systemParams['actor_id'] = $actor->id;
        $systemParams['uid'] = $page->id;
        $systemParams['context_ids'] = $share->id;

        ES::notify('repost.item', $targets, $mailParams, $systemParams, $page->notification);
    }

	/**
	 *
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	private function getHelper(SocialStreamItem $item , SocialTableShare $share)
	{
		$source = explode('.', $share->element);
		$element = $source[0];

		$file = dirname(__FILE__) . '/helpers/' . $element .'.php';
		require_once($file);

		// Get class name.
		$className = 'SocialPageSharesHelper' . ucfirst($element);

		// Instantiate the helper object.
		$helper = new $className($item, $share);

		return $helper;
	}

	/**
	 * Responsible to generate the stream contents.
	 *
	 * @since	1.0
	 * @access	public
	 * @param	object	$params		A standard object with key / value binding.
	 *
	 * @return	none
	 */
	public function onPrepareStream(SocialStreamItem &$item, $includePrivacy = true)
	{
		// Only process this if the stream type is shares
		if ($item->context != 'shares' || !$item->cluster_type) {
			return;
		}

		// Get the single context id
		$id = $item->contextId;

		// We only need the single actor.
		// Load the profiles table.
		$share = ES::table('Share');
		$share->load($id);

		// If shared item no longer exist, exit here.
		if (!$share->id) {
			return;
		}

		// Get the current logged in user
		$my = ES::user();

		// Break down the shared element
		$segments = explode('.', $share->element);
		$element = $segments[0];
		
		$page = $item->getCluster();

		// We only want to process items from albums, photos and stream
		$allowed = array('albums', 'photos', 'stream');

		if (!in_array($element, $allowed)) {
			return;
		}

		// Get the repost helper
		$helper = $this->getHelper($item , $share);

		// We want the likes and comments to be associated with the "stream" rather than the shared item
		$uid = $item->uid;
		$element = 'story';
		$verb = 'create';

		// Load up custom likes
		$likes = ES::likes();
		$likes->get($uid, $element, $verb, SOCIAL_APPS_GROUP_PAGE, $item->uid, array('clusterId' => $page->id));
		$item->likes = $likes;

		// Attach comments to the stream
		$commentParams = array('url' => $helper->getLink(), 'clusterId' => $page->id);
		$comments = ES::comments($uid, $element, $verb, SOCIAL_APPS_GROUP_PAGE, $commentParams, $item->uid);
		$item->comments = $comments;

		// Share app does not allow reposting itself.
		$item->repost = false;

		// Get the content of the repost
		$item->title = $helper->getStreamTitle();

        $contents = $helper->getContent();
		// If the content is a false, there could be privacy restrictions.
		if ($contents === false) {
			return;
		}

		// Set stream display mode.
		$item->display = SOCIAL_STREAM_DISPLAY_FULL;
        $item->preview = $contents;

	}

}
