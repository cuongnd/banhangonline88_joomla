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

class DiscussionsViewCreate extends SocialAppsView
{
    /**
     * Renders the create discussion form
     *
     * @since   2.0
     * @access  public
     */
    public function display($uid = null , $docType = null)
    {
        $page = ES::page($uid);

        // Set the page title
        $this->page->title('APP_PAGE_DISCUSSIONS_PAGE_TITLE_CREATE');

        // Get the discussion item
        $discussion = ES::table('Discussion');

        // Determines if we should allow file sharing
        $access = $page->getAccess();
        $files = $access->get('files.enabled', true);

        $params = $this->getParams();
        $editor = $params->get('editor', 'bbcode');

        $this->set('cluster', $page);
        $this->set('files', $files);
        $this->set('discussion', $discussion);
        $this->set('editor', $editor);

        echo parent::display('themes:/site/discussions/form/default');
    }
}
