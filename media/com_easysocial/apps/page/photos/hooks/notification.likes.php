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

class SocialPageAppPhotosHookNotificationLikes
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

        // Convert the names to stream-ish
        $names = ES::string()->namesToNotifications($users);

        // When user likes on an album or a page of photos from an album on the stream
        if ($item->context_type == 'albums.page.create') {

            $album = ES::table('Album');
            $album->load($item->uid);

            $item->content = $album->get('title');
            $item->image = $album->getCover();

            // Load the page for this album
            $page = ES::page($album->uid);

            // We need to determine if the user is the owner
            if ($album->user_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $langString = ES::string()->computeNoun('APP_PAGE_PHOTOS_NOTIFICATIONS_LIKES_ALBUMS', count($users));
                $item->title = JText::sprintf($langString, $names, $page->getName());

                return;
            }

            // For other users, we just post a generic message
            $langString = ES::string()->computeNoun('APP_PAGE_PHOTOS_NOTIFICATIONS_LIKES_USERS_ALBUMS', count($users));
            $item->title = JText::sprintf($langString, $names, $page->getName());

            return;
        }

       if ($item->context_type == 'photos.page.updateCover') {

            // Get the photo object
            $photo = ES::table('Photo');
            $photo->load($item->uid);

            // Set the photo image
            $item->image = $photo->getSource();
            $item->content = '';

            // Load the page for this photo
            $page = ES::page($photo->uid);

            // We need to determine if the user is the owner
            if ($photo->user_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $langString = ES::string()->computeNoun('APP_PAGE_PHOTOS_NOTIFICATIONS_LIKES_PROFILE_COVER', count($users));
                $item->title = JText::sprintf($langString, $names, $page->getName());

                return;
            }

            // For other users, we just post a generic message
            $langString = ES::string()->computeNoun('APP_PAGE_PHOTOS_NOTIFICATIONS_LIKES_USERS_PROFILE_COVER', count($users));
            $item->title = JText::sprintf($langString, $names, $page->getName());

            return;
        }

        if ($item->context_type == 'photos.page.uploadAvatar') {

            // Get the photo object
            $photo = ES::table('Photo');
            $photo->load($item->uid);

            // Set the photo image
            $item->image = $photo->getSource();
            $item->content = '';

            // Load the page for this photo
            $page = ES::page($photo->uid);

            // We need to determine if the user is the owner
            if ($photo->user_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $langString = ES::string()->computeNoun('APP_PAGE_PHOTOS_NOTIFICATIONS_LIKES_PAGE_PICTURE', count($users));
                $item->title = JText::sprintf($langString, $names, $page->getName());

                return;
            }

            // For other users, we just post a generic message
            $langString = ES::string()->computeNoun('APP_PAGE_PHOTOS_NOTIFICATIONS_LIKES_USERS_PROFILE_PHOTO', count($users));
            $item->title = JText::sprintf($langString, $names, $page->getName());

            return;
        }

        // If user uploads multiple photos on the stream
        if ($item->context_type == 'stream.page.upload') {
            // Get the photo object
            $photo = ES::table('Photo');
            $photo->load($item->context_ids);

            // Load the page
            $page = ES::page($photo->uid);

            $item->content = '';

            // We could also set an image preview
            $item->image = $photo->getSource();

            // Because we know that this is coming from a stream, we can display a nicer message
            if ($photo->user_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $langString = ES::string()->computeNoun('APP_PAGE_PHOTOS_NOTIFICATIONS_LIKES_PHOTO_SHARED_ON_THE_STREAM_' . strtoupper($photo->post_as), count($users));
                $item->title = JText::sprintf($langString, $names, $page->getName());

                return;
            }

            // We need to identify the owner of the photo.
            $owner = ES::user($photo->user_id)->getName();

            if ($photo->post_as == SOCIAL_TYPE_PAGE) {
                $owner = $page->getName();
            }

            // For other users, we just post a generic message
            $langString = ES::string()->computeNoun('APP_PAGE_PHOTOS_NOTIFICATIONS_LIKES_USERS_PHOTO_SHARED_ON_THE_STREAM', count($users));
            $item->title = JText::sprintf($langString, $names, $owner);

            return;
        }

        // When user likes on a single photo item
        if ($item->context_type == 'photos.page.upload' || $item->context_type == 'photos.page.add') {

            // Get the photo object
            $photo = ES::table('Photo');
            $photo->load($item->uid);

            // Load the page
            $page = ES::page($photo->uid);

            // Set the photo image
            $item->image = $photo->getSource();
            $item->content = '';

            // We need to identify the owner of the photo.
            $owner = strtoupper($photo->post_as);

            // We need to determine if the user is the owner
            if ($photo->user_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $langString = ES::string()->computeNoun('APP_PAGE_PHOTOS_NOTIFICATIONS_LIKES_PHOTO_' . strtoupper($photo->post_as), count($users));
                $item->title = JText::sprintf($langString, $names, $page->getName());

                return;
            }

            // We need to identify the owner of the photo.
            $owner = ES::user($photo->user_id)->getName();

            if ($photo->post_as == SOCIAL_TYPE_PAGE) {
                $owner = $page->getName();
            }

            // For other users, we just post a generic message
            $langString = ES::string()->computeNoun('APP_PAGE_PHOTOS_NOTIFICATIONS_LIKES_USERS_PHOTO', count($users));
            $item->title = JText::sprintf($langString, $names, $owner);

            return;
        }


        return;
    }

}
