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

// Include the fields library
ES::import('admin:/includes/fields/dependencies');

// Include helper file.
ES::import('fields:/page/permalink/helper');

/**
 * Permalink field for page
 *
 * @since	2.0
 */
class SocialFieldsPagePermalink extends SocialFieldItem
{
	/**
	 * Saves the permalink
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function save(&$post, &$page)
	{
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		// There could be possibility that the user removes their permalink so
		// we should not check for empty value here.

		if (empty($value) && !empty($page->title)) {
			$value = JFilterOutput::stringURLSafe($page->title);
		}

		$model = ES::model('pages');

		// $table = ES::table('Page');
		// $table->load($page->id);

		// $table->alias = $model->getUniqueAlias($value, $page->id);
		// $table->store();

		// Update the alias value
		$page->alias = $model->getUniqueAlias($value, $page->id);

		$post[$this->inputName] = $page->alias;
	}

	/**
	 * Before we store the page, we need to update the page's `permalink` column
	 * Previously is onRegisterAfterSave. Changed to before save so that we can retrieve the permalink during saving process
	 * @since	2.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 */
	public function onRegisterBeforeSave(&$post, &$page)
	{
		return $this->save($post, $page);
	}

	/**
	 * Saves the permalink after their profile is edited.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 */
	public function onEditBeforeSave(&$post, &$page)
	{
		return $this->save($post, $page);
	}

	/**
	 * Executes before the page is saved.
	 *
	 * @since   2.0
	 * @access  public
	 * @param   array           $data       The posted data.
	 * @param   SocialCluster   $cluster    The cluster object.
	 */
	public function onAdminEditBeforeSave(&$post, &$page)
	{
	    return $this->save($post, $page);
	}

	/**
	 * Performs validation for the gender field.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string
	 * @return
	 */
	public function validate($post, $page = null, $isCopy = false)
	{
		$key = $this->inputName;

		// Get the current value
		$value = isset($post[$key]) ? $post[$key] : '';

		if (!$this->isRequired() && empty($value)) {
			return true;
		}

		// Catch for errors if this is a required field.
		if ($this->isRequired() && empty($value)) {
			$this->setError(JText::_('PLG_FIELDS_PAGE_PERMALINK_REQUIRED'));

			return false;
		}

		if ($this->params->get('max') > 0 && JString::strlen($value) > $this->params->get('max')) {
			$this->setError(JText::_('PLG_FIELDS_PAGE_PERMALINK_EXCEEDED_MAX_LENGTH'));
			return false;
		}

		if (!SocialFieldsPagePermalinkHelper::allowed($value)) {
			$this->setError(JText::_('PLG_FIELDS_PERMALINK_CONFLICTS_WITH_SYSTEM'));
			return false;
		}

		// Determine the current user that is being edited
		$current = '';

		if ($page) {
			$current = $page->id;
		}

		if ($current) {
			$page = ES::page($current);

			// If the permalink is the same, just return true.
			if ($page->alias == $value) {
				return true;
			}
		}

		if ($isCopy) {
			// lets auto append the alias so that there will not be any conflict.
			$i = 0;
			$iterate = true;
			do {
				if (SocialFieldsPagePermalinkHelper::exists($value)) {
					$value = $value . '-' . ++$i;
				} else {
					$iterate = false;
				}
			} while ($iterate);

			// var_dump($value);
		}

		if (SocialFieldsPagePermalinkHelper::exists($value)) {
			$this->setError(JText::_('PLG_FIELDS_PAGE_PERMALINK_NOT_AVAILABLE'));

			return false;
		}

		if (!SocialFieldsPagePermalinkHelper::valid($value, $this->params)) {
			$this->setError(JText::_('PLG_FIELDS_PAGE_PERMALINK_INVALID_PERMALINK'));

			return false;
		}

		// now lets reset the value is this is a copy operation.
		if ($isCopy) {
			$post[$key] = $value;
		}

		return true;
	}

	/**
	 * Determines whether there's any errors in the submission in the registration form.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @param	SocialTableRegistration		The registration ORM table.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 */
	public function onRegisterValidate(&$post, &$session)
	{
		$state = $this->validate($post);

		return $state;
	}

	/**
	 * Performs validation when a user updates their profile.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @param	SocialTableRegistration		The registration ORM table.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 */
	public function onEditValidate(&$post, &$page, $isCopy = false)
	{
		$state = $this->validate($post, $page, $isCopy);

		return $state;
	}

	/**
	 * Displays the field input for user when they register their account.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	array
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 *
	 */
	public function onRegister(&$post, &$session)
	{
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		// Detect if there's any errors.
		$error = $session->getErrors($this->inputName);

		$this->set('error', $error);
		$this->set('value', $this->escape($value));
		$this->set('pageid', null);

		return $this->display();
	}

	/**
	 * Responsible to output the html codes that is displayed to
	 * a user when they edit their profile.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	SocialUser		The user that is being edited.
	 * @return
	 */
	public function onEdit(&$post, &$page, $errors)
	{
		$value = $page->alias;

		$error = $this->getError($errors);

		$this->set('value', $this->escape($value));
		$this->set('error', $error);

		$this->set('pageid', $page->id);

		return $this->display();
	}

	/**
	 * Displays the sample html codes when the field is added into the profile.
	 *
	 * @since	2.0
	 * @access	public
	 * @return	string	The html output.
	 *
	 */
	public function onSample()
	{
		return $this->display();
	}
}
