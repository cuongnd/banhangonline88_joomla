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

// Extend from user header
ES::import('fields:/user/header/header');

class SocialFieldsPagePermissions extends SocialFieldItem
{
    /**
     * Displays the form for page owner to define permissions
     *
     * @since   2.0
     * @access  public
     * @param   string
     * @return  
     */
    public function onRegister(&$post, &$session)
    {
        // Get the posted value if there's any
        $value = !empty($post['stream_permissions']) ? $post['stream_permissions'] : '';

        // If this is a new page being created, ensure that admin is always checked by default
        if (!$value && !is_array($value)) {
            $value = array('admin');
        }

        // Ensure that it's an array
        $value = ES::makeArray($value);


        $this->set('value', $value);

        return $this->display();
    }

    /**
     * Displays the form for page owner to define permissions when page is being edited
     *
     * @since   2.0
     * @access  public
     * @param   string
     * @return  
     */
    public function onEdit(&$post, SocialPage &$page, $errors)
    {
        $permissions = $page->getParams()->get('stream_permissions', array());

        $value = !empty($post['stream_permissions']) ? $post['stream_permissions'] : $permissions;
        $value = ES::makeArray($value);

        $this->set('value', $value);

        return $this->display();
    }

    /**
     * Displays the sample output for the back end.
     *
     * @since   2.0
     * @access  public
     * @param   string
     * @return  
     */
    public function onSample()
    {
        return $this->display();
    }

    /**
     * Processes the save for new page creation
     *
     * @since   2.0
     * @access  public
     * @param   string
     * @return  
     */
    public function onRegisterBeforeSave(&$post, &$page)
    {
        return $this->onBeforeSave($post, $page);
    }

    /**
     * Processes the save for page editing
     *
     * @since   2.0
     * @access  public
     * @param   string
     * @return  
     */
    public function onEditBeforeSave(&$post, SocialPage &$page)
    {
        return $this->onBeforeSave($post, $page);
    }

    /**
     * Before the form is saved, we need to store these data into the page properties
     *
     * @since   2.0
     * @access  public
     * @param   string
     * @return  
     */
    public function onBeforeSave(&$post, SocialPage &$page)
    {
        $value = !empty($post['stream_permissions']) ? $post['stream_permissions'] : array();
        $value = ES::makeArray($value);
        
        // Set it into the page params so that we can retrieve this later
        $params = $page->getParams();
        $params->set('stream_permissions', $value);

        $page->params = $params->toString();

        unset($post['stream_permissions']);
    }
}
