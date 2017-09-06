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

/**
 * Hook for likes
 *
 * @since	2.0
 */
class SocialUserAppPagesHookNotificationLikes
{
	/**
	 *
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function execute($item, $verb)
    {
        // Get the owner of the stream item since we need to notify the person
        $stream = ES::table('Stream');
        $stream->load($item->uid);

        // Get comment participants
        $model = ES::model('Likes');
        $users = $model->getLikerIds($item->uid, $item->context_type);

        // Include the actor of the stream item as the recipient
        $users = array_merge(array($item->actor_id), $users);

        // Ensure that the values are unique
        $users = array_unique($users);
        $users = array_values($users);

        // Exclude myself from the list of users.
        $index = array_search(ES::user()->id, $users);

        // If the skipExcludeUser is true, we don't unset myself from the list
        if (isset($item->skipExcludeUser) && $item->skipExcludeUser) {
            $index = false;
        }

        if ($index !== false) {
            unset($users[$index]);
            $users = array_values($users);
        }

        // Convert the names to stream-ish
        $names = ES::string()->namesToNotifications($users);

        // Get the page from the stream
        $page = ES::page($stream->cluster_id);

        // Set the content
        if ($page) {
            $item->content = $page->getName();
            $item->image = $page->getAvatar();
        }

        // We need to generate the notification message differently for the author of the item and the recipients of the item.
        if ($stream->actor_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
            $item->title = JText::sprintf('APP_USER_PAGES_NOTIFICATIONS_USER_LIKES_YOUR_PAGE_' . strtoupper($verb), $names);

            return $item;
        }

        // This is for 3rd party viewers
        $item->title = JText::sprintf('APP_USER_PAGES_NOTIFICATIONS_USER_LIKES_USERS_PAGE_' . strtoupper($verb), $names, ES::user($stream->actor_id)->getName());

        return $item;
    }

}
