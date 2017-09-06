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

class SocialPageAppFilesHookNotificationLikes
{
    /**
     * Processes likes notifications
     *
     * @since   1.2
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

        // When someone likes on the photo that you have uploaded in a page
        if ($item->context_type == 'files.page.uploaded') {

            $stream = ES::table('Stream');
            $stream->load($item->context_ids);

            $streamItem = $stream->getItems();

            $params = ES::registry($streamItem[0]->params);
            $fileIds = $params->get('file');

            $file = ES::table('File');
            $file->load($fileIds[0]);

            // Get the page from the stream
            $page = ES::page($file->uid);

            // Set the content
            if ($file->hasPreview()) {
                $item->image = $file->getPreviewURI();
            }

            // We need to generate the notification message differently for the author of the item and the recipients of the item.
            if ($file->user_id == $item->target_id && $item->target_type == SOCIAL_TYPE_USER) {
                $langString = ES::string()->computeNoun('APP_PAGE_FILES_USER_LIKES_FILE', count($users));
                $item->title = JText::sprintf($langString, $names, $page->getName());

                return $item;
            }

            // This is for 3rd party viewers
            $langString = ES::string()->computeNoun('APP_PAGE_FILES_USER_LIKES_USERS_FILE', count($users));
            $item->title = JText::sprintf($langString, $names, ES::user($file->user_id)->getName(), $page->getName());

            return;
        }

        return;
    }

}
