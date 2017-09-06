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

class SocialPageAppNewsHookNotificationComments
{
    /**
     * Processes comment notifications
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

        // When someone comment on the news that you have created in a page
        if ($item->context_type == 'news.page.create') {

            // Get the news object
            $news = ES::table('ClusterNews');
            $news->load($item->uid);

            // Get the page from the stream
            $page = ES::page($news->cluster_id);

            // Set the content
            if ($page) {
                $item->image = $page->getAvatar();
            }

            // We need to generate the notification message differently for the author of the item and the recipients of the item.
            if ($news->created_by == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $item->title = JText::sprintf('APP_PAGE_NEWS_USER_COMMENTED_ON_ANNOUNCEMENT', $names, $page->getName());

                return $item;
            }

            // This is for 3rd party viewers
            $item->title = JText::sprintf('APP_PAGE_NEWS_USER_COMMENTED_ON_USERS_ANNOUNCEMENT', $names, $page->getName());

            return;
        }

        return;
    }

}
