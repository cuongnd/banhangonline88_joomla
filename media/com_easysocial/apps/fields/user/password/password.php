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
FD::import('admin:/includes/fields/dependencies');

/**
 * Field application for Password
 *
 * @since	2.0
 */
class SocialFieldsUserPassword extends SocialFieldItem
{
	/**
	 * Class constructor.
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function __construct($options)
	{
		parent::__construct($options);
	}

	/**
	 * Displays the field input for user when they register their account.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	array
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 */
	public function onRegister(&$post, &$registration)
	{
		// Get the value
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : JText::_($this->params->get('default'), true);

		// Set value
		$this->set('value', $this->escape($value));

		// Set errors
		$error = $registration->getErrors($this->inputName);

		$this->set('error', $error);

		return $this->display();
	}

	/**
	 * Validates the field input for user when they register their account.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	array
	 * @return	bool	State of the validation
	 *
	 */
	public function onRegisterValidate(&$post)
	{
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		return $this->validateInput($value);
	}

	/**
	 * Executes before a user's registration is saved.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @param	SocialTableRegistration		The registration ORM table.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 */
	public function onRegisterBeforeSave(&$post, &$user)
	{
		// use isset instead of !empty because we do not even wan empty string or false value here
		if ($this->params->get('readonly') && isset($post[$this->inputName])) {
			unset($post[$this->inputName]);
		}

		return true;
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
	public function onEdit(&$post, &$user, $errors)
	{
		// Get the value.
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : $this->value;

		// Get the error.
		$error = $this->getError($errors);

		// Set the value.
		$this->set('value', $this->escape($value));
		$this->set('error', $error);

		return $this->display();
	}

	public function onAdminEdit(&$post, &$user, $errors)
	{
		// Get the value.
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : $this->value;

		// Get the error.
		$error = $this->getError($errors);

		// Set the value.
		$this->set('value', $this->escape($value));
		$this->set('error', $error);

		// Manually override the readonly parameter for admin
		$this->params->set('readonly', false);
		$this->set('params', $this->params);

		return $this->display();
	}

	/**
	 * Validates the field input for user when they edit their account.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	array
	 * @return	bool	State of the validation
	 *
	 */
	public function onEditValidate(&$post)
	{
		$value = !empty($post[$this->inputName]) ? $post[$this->inputName] : '';

		return $this->validateInput($value);
	}

	/**
	 * Executes before a user's edit is saved.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @param	SocialUser	The user object.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 */
	public function onEditBeforeSave(&$post, &$user)
	{
		// use isset instead of !empty because we do not even wan empty string or false value here
		if ($this->params->get('readonly') && isset($post[ $this->inputName ])) {
			unset($post[ $this->inputName ]);
		}

		return true;
	}

	/**
	 * Executes before a user's edit is save in admin more
	 *
	 * @since	2.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @param	SocialUser	The user object.
	 * @return	bool	Determines if the system should proceed or throw
	 */
	public function onAdminEditBeforeSave(&$post, &$user)
	{
		// Regardless of readonly parameter, we allow admin to edit this field
		return true;
	}

	/**
	 * Displays the sample html codes when the field is added into the profile.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	array
	 * @param	SocialTableRegistration
	 * @return	string	The html output.
	 *
	 */
	public function onSample()
	{
		return $this->display();
	}

	/**
	 * General validation function
	 *
	 * @since	2.0
	 * @access	public
	 * @param	string	Value of the string to validate
	 * @return	bool	State of the validation
	 *
	 */
	private function validateInput($value)
	{
		if ($this->isRequired() && empty($value)) {
			return $this->setError(JText::_('PLG_FIELDS_PASSWORD_VALIDATION_INPUT_REQUIRED'));
		}

		if (!empty($value) && $this->params->get('min') > 0 && JString::strlen($value) < $this->params->get('min')) {
			return $this->setError(JText::_('PLG_FIELDS_PASSWORD_VALIDATION_INPUT_TOO_SHORT'));
		}

		if ($this->params->get('max') > 0 && JString::strlen($value) > $this->params->get('max')) {
			return $this->setError(JText::_('PLG_FIELDS_PASSWORD_VALIDATION_INPUT_TOO_LONG'));
		}

		return true;
	}

	/**
	 * Checks if this field is complete.
	 *
	 * @since  2.0
	 * @access public
	 * @param  SocialUser    $user The user being checked.
	 */
	public function onFieldCheck($user)
	{
		return $this->validateInput($this->value);
	}

	/**
	 * Trigger to get this field's value for various purposes.
	 *
	 * @since  2.0
	 * @access public
	 * @param  SocialUser    $user The user being checked.
	 * @return Mixed               The value data.
	 */
	public function onGetValue($user)
	{
		return $this->getValue();
	}

	/**
	 * Checks if this field is filled in.
	 *
	 * @since  2.0
	 * @access public
	 * @param  array		$data	The post data.
	 * @param  SocialUser	$user	The user being checked.
	 */
	public function onProfileCompleteCheck($user)
	{
		if (!FD::config()->get('user.completeprofile.strict') && !$this->isRequired()) {
			return true;
		}

		return !empty($this->value);
	}
}
