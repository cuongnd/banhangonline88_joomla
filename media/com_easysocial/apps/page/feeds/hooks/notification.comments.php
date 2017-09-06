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

class SocialPageAppFeedsHookNotificationComments
{
    /**
     * Processes likes notifications
     *
     * @since   2.0
     * @access  public
     * @param   string
     * @return
     */
    public function execute(&$item)
    {
         // Get comment participants
        $model = ES::model('Comments');
        $users = $model->getParticipants($item->uid, $item->context_type);

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

        // Load the comment object since we have the context_ids
        $comment = ES::table('Comments');
        $comment->load($item->context_ids);

        // Load the stream object
        $stream = ES::table('Stream');
        $stream->load($item->context_ids);

        // Load the page
        $page = ES::page($stream->cluster_id);

        // We need to determine if the user is the owner
        if ($stream->actor_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
            $item->title = JText::sprintf('APP_PAGE_FEEDS_NOTIFICATIONS_USER_POSTED_A_COMMENT_ON_FEED', $names, $page->getName());

            return;
        }

        // When feed owner comment on thier own stream
        if ($item->actor_id == $stream->actor_id && count($users) == 1) {
            // We do not need to pluralize here since we know there's always only 1 recipient
            $item->title = JText::sprintf('APP_PAGE_FEEDS_NOTIFICATIONS_USER_POSTED_A_COMMENT_ON_USERS_FEED', $page->getName());
            return;
        }

        // For other users, we just post a generic message
        $item->title = JText::sprintf('APP_USER_FEEDS_NOTIFICATIONS_USER_POSTED_A_COMMENT_ON_USERS_FEED', $names, $page->getName());

        return;
    }

}
