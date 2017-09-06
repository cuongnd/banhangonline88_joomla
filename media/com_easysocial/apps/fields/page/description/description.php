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

ES::import('admin:/includes/fields/dependencies');

class SocialFieldsPageDescription extends SocialFieldItem
{
    /**
     * Executes before the page is created.
     *
     * @since   2.0
     * @access  public
     * @param   array           $data       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onRegisterBeforeSave(&$data, &$cluster)
    {
        // $desc = !empty($data[$this->inputName]) ? $data[$this->inputName] : '';
        $desc = $this->input->get($this->inputName, '', 'raw');

        // Set the description on the page
        $cluster->description = $desc;

        unset($data[$this->inputName]);
    }

    /**
     * Executes before the page is saved.
     *
     * @since   2.0
     * @access  public
     * @param   array           $data       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onEditBeforeSave(&$data, &$cluster)
    {
        // $desc = !empty($data[$this->inputName]) ? $data[$this->inputName] : '';
        $desc = $this->input->get($this->inputName, '', 'raw');

        // Set the description on the page
        $cluster->description = $desc;

        unset($data[$this->inputName]);
    }

    /**
     * Executes before the page is saved.
     *
     * @since   2.0
     * @access  public
     * @param   array           $data       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onAdminEditBeforeSave(&$data, &$cluster)
    {
        // $desc = !empty($data[$this->inputName]) ? $data[$this->inputName] : '';
        $desc = $this->input->get($this->inputName, '', 'raw');

        // Set the description on the page
        $cluster->description = $desc;

        unset($data[$this->inputName]);
    }

    /**
     * Displays the page description textbox.
     *
     * @since   2.0
     * @access  public
     * @param   array           $data       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     * @param   array           $errors     The errors array.
     */
    public function onEdit(&$data, &$cluster, $errors)
    {
        // $description = !empty($post[$this->inputName]) ? $post[$this->inputName] : $cluster->description;
        $description = $this->input->get($this->inputName, $cluster->description, 'raw');

        // Get the error.
        $error = $this->getError($errors);

		// Get the editor
		$editor = $this->getEditor();

		$this->set('editor', $editor);
		$this->set('value', $description);
        $this->set('error', $error);

        return $this->display();
    }

    /**
     * Displays the page description textbox.
     *
     * @since   2.0
     * @access  public
     * @param   array           $data       The posted data.
     * @param   SocialCluster   $cluster    The cluster object.
     * @param   array           $errors     The errors array.
     */
    public function onAdminEdit(&$data, &$cluster, $errors)
    {
        // $description = !empty($post[$this->inputName]) ? $post[$this->inputName] : $cluster->description;
        $description = $this->input->get($this->inputName, $cluster->description, 'raw');


        // Get the error.
        $error = $this->getError($errors);

		// Get the editor
		$editor = $this->getEditor();

		$this->set('editor', $editor);
		$this->set('value', $description);
        $this->set('error', $error);

        return $this->display();
    }

    /**
     * Displays the field input for user when they register their account.
     *
     * @since   1.4
     * @access  public
     * @param   array
     * @param   SocialTableRegistration
     * @return  string  The html output.
     */
    public function onRegister(&$post, &$registration)
    {
        // Get the value from posted data if it's available.
        $value = $this->input->get($this->inputName, $this->params->get('default'), 'raw');

        // Get any errors for this field.
        $error = $registration->getErrors($this->inputName);

        // Get the editor that is configured
        $editor = $this->getEditor();

        $this->set('editor', $editor);
        $this->set('value', $value);
        $this->set('error', $error);

        return $this->display();
    }

    /**
     * Validates the event creation
     *
     * @since   1.4.9
     * @access  public
     * @param   string
     * @return
     */
    public function onRegisterValidate(&$post)
    {
        $value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

        $valid = $this->validate($value);

        return $valid;
    }

    /**
     * Validates the event editing
     *
     * @since   1.4.9
     * @access  public
     * @param   string
     * @return
     */
    public function onEditValidate(&$post)
    {
        $value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

        $valid = $this->validate($value);

        return $valid;
    }

    /**
     * General validation function
     *
     * @since   1.4.9
     * @access  public
     * @param   string  Value of the string to validate
     * @return  bool    State of the validation
     *
     */
    private function validate($value)
    {
        if ($this->isRequired() && empty($value)) {
            return $this->setError(JText::_('PLG_FIELDS_PAGE_DESCRIPTION_VALIDATION_INPUT_REQUIRED'));
        }

        return true;
    }


    /**
     * Responsible to output the html codes that is displayed to a user.
     *
     * @since   2.0
     * @access  public
     * @param   SocialCluster   $cluster    The cluster object.
     */
    public function onDisplay($cluster)
    {
        // Do not allow html tags on description
        // $description = strip_tags($cluster->description);
        // Push variables into theme.
        // $this->set('value', nl2br($this->escape($cluster->description)));

        // Push variables into theme.
        $value = $cluster->getDescription();

        if (!$value) {
            return;
        }

        $this->set('value', $value);

        return $this->display();
    }

	/**
	* Retrieves the editor object.
	*
	* @since   2.0
	* @access  public
	* @param   string
	* @return
	*/
	public function getEditor()
	{
		$config = ES::config();
		$defaultEditor = $config->get('pages.editor','none');

		// If the settings is inherit means we will use joomla default editor itself
		if ($defaultEditor == 'inherit') {
			$defaultEditor = JFactory::getConfig()->get('editor');
		}

		$editor = JFactory::getEditor($defaultEditor);
		return $editor;
	}

    /**
     * Displays the sample codes for this field in the field editor
     *
     * @since   1.4
     * @access  public
     * @param   array
     * @param   SocialTableRegistration
     * @return  string  The html output.
     *
     */
    public function onSample()
    {
        $editor = $this->getEditor();

        $this->set('editor', $editor);

        return $this->display();
    }
}
