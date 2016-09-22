<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');

FD::import('fields:/user/textarea/textarea');

class SocialFieldsEventDescription extends SocialFieldsUserTextarea
{
    /**
     * Support for generic getFieldValue('DESCRIPTION')
     *
     * @author Jason Rey <jasonrey@stackideas.com>
     * @since  1.3.9
     * @access public
     * @return SocialFieldValue    The value container
     */
    public function getValue()
    {
        $container = $this->getValueContainer();

        if ($this->field->type == SOCIAL_TYPE_EVENT && !empty($this->field->uid)) {
            $event = FD::event($this->field->uid);

            $container->value = $event->getDescription();

            $container->data = $event->description;
        }

        return $container;
    }

    /**
     * Displays the field for edit.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     * @param   array           $post       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     * @param   array           $errors     The errors array.
     * @return  string                      The html codes for this field.
     */
    public function onEdit(&$post, &$cluster, $errors)
    {
        $description = !empty($post[$this->inputName]) ? $post[$this->inputName] : $cluster->description;

        // Get the error.
        $error = $this->getError($errors);

        $this->set('value', $this->escape($description));
        $this->set('error', $error);

        return $this->display();
    }

    /**
     * Displays the field for admin edit.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     * @param   array           $post       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     * @param   array           $errors     The errors array.
     * @return  string                      The html codes for this field.
     */
    public function onAdminEdit(&$post, &$cluster, $errors)
    {
        $description = !empty($post[$this->inputName]) ? $post[$this->inputName] : $cluster->description;

        // Get the error.
        $error = $this->getError($errors);

        $this->set('value', $this->escape($description));
        $this->set('error', $error);

        return $this->display();
    }

    /**
     * Responsible to output the html codes that is displayed to a user.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     * @param   SocialCluster   $cluster    The cluster object.
     * @return  string                      The html codes for this field.
     */
    public function onDisplay($cluster)
    {
        // since now the interface do not support text editor, we
        // neeed to strip html tags. these html tags might come from migration.
        $value = strip_tags($cluster->description);

        // Prevent any injection
        $value = $this->escape($value);

        // Respect newlines
        $value = nl2br($value);

        // Push variables into theme.
        $this->set('value', $value);

        return $this->display();
    }

    /**
     * Executes before the event is created.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     * @param   array           $post       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onRegisterBeforeSave(&$post, &$cluster)
    {
        $desc = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

        // Set the description on the event
        $cluster->description = $desc;

        unset($post[$this->inputName]);
    }

    /**
     * Executes before the event is saved.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     * @param   array           $post       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onEditBeforeSave(&$post, &$cluster)
    {
        $desc = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

        // Set the description on the event
        $cluster->description = $desc;

        unset($post[$this->inputName]);
    }

    /**
     * Executes before the event is saved.
     *
     * @author  Jason Rey <jasonrey@stackideas.com>
     * @since   1.3
     * @access  public
     * @param   array           $post       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onAdminEditBeforeSave(&$post, &$cluster)
    {
        $desc = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

        // Set the description on the event
        $cluster->description = $desc;

        unset($post[$this->inputName]);
    }
}
