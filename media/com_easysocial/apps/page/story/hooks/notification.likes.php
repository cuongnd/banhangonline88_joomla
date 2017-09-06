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

class SocialPageAppStoryHookNotificationLikes
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
        // Get likes participants
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

        if (!$users) {
            return false;
        }

        // Convert the names to stream-ish
        $names = ES::string()->namesToNotifications($users);

        // When someone likes on the photo that you have uploaded in a page
        if ($item->context_type == 'photos.page.share') {

            $this->notificationPhotos($names, $users, $item);

            return;
        }

        // When someone likes your post in a page
        if ($item->context_type == 'story.page.create') {

            // Get the owner of the stream item since we need to notify the person
            $stream = ES::table('Stream');
            $stream->load($item->uid);

            // Get the page from the stream
            $page = ES::page($stream->cluster_id);

            // Set the content
            if ($page) {
                $item->image = $page->getAvatar();
            }

            // We need to generate the notification message differently for the author of the item and the recipients of the item.
            if ($stream->actor_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $langString = ES::string()->computeNoun('APP_PAGE_STORY_USER_LIKES_YOUR_POST', count($users));
                $item->title = JText::sprintf($langString, $names, $page->getName());

                return $item;
            }

            // This is for 3rd party viewers
            $langString = ES::string()->computeNoun('APP_PAGE_STORY_USER_LIKES_USERS_POST', count($users));
            $item->title = JText::sprintf($langString, $names, ES::user($stream->actor_id)->getName(), $page->getName());

            return;
        }

        if ($item->context_type == 'links.create') {

            // Get the owner of the stream item since we need to notify the person
            $stream = ES::table('Stream');
            $stream->load($item->uid);

            // Get the page from the stream
            $page = ES::page($stream->cluster_id);

            // Set the content
            if ($page) {
                $item->image = $page->getAvatar();
            }

            // Get the link object
            $model = ES::model('Stream');
            $links = $model->getAssets($item->uid, SOCIAL_TYPE_LINKS);

            if ($links) {
                $link = ES::makeObject($links[0]->data);

                $item->content = $link->link;
                $item->image = $link->image;
            }

            // We need to generate the notification message differently for the author of the item and the recipients of the item.
            if ($stream->actor_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $langString = ES::string()->computeNoun('APP_PAGE_STORY_USER_LIKES_YOUR_LINK', count($users));
                $item->title = JText::sprintf($langString, $names, $page->getName());

                return $item;
            }

            // This is for 3rd party viewers
            $langString = ES::string()->computeNoun('APP_PAGE_STORY_USER_LIKES_USERS_LINK', count($users));
            $item->title = JText::sprintf($langString, $names, ES::user($stream->actor_id)->getName(), $page->getName());

            return;
        }

        return $item;
    }

    /**
     *
     *
     * @since   2.0
     * @access  public
     * @param   string
     * @return
     */
    private function notificationPhotos($names, $users, &$item)
    {
        // Get the stream object
        $stream = ES::table('Stream');
        $stream->load($item->uid);

        // Get the page
        $page = ES::page($item->context_ids);

        // Get all child stream items
        $streamItems = $stream->getItems();

        // Get the first photo since we can't get all photos
        if ($streamItems && isset($streamItems[0])) {

            $streamItem = $streamItems[0];

            $photo = ES::table('Photo');
            $photo->load($streamItem->context_id);

            $item->image = $photo->getSource();
        }

        // We need to generate the notification message differently for the author of the item and the recipients of the item.
        if ($stream->actor_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
            $langString = ES::string()->computeNoun('APP_PAGE_STORY_USER_LIKES_YOUR_SHARED_PHOTO', count($users));
            $item->title = JText::sprintf($langString, $names, $page->getName());

            return $item;
        }

        // This is for 3rd party viewers
        $langString = ES::string()->computeNoun('APP_PAGE_STORY_USER_LIKES_USERS_SHARED_PHOTO', count($users));
        $item->title = JText::sprintf($langString, $names, ES::user($stream->actor_id)->getName(), $page->getName());
    }
}
