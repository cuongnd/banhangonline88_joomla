<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2017 Stack Ideas Sdn Bhd. All rights reserved.
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

class SocialFieldsEventPermissions extends SocialFieldItem
{
	/**
	 * Displays the form for event owner to define permissions
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function onRegister(&$post, &$session)
	{
		// Get the posted value if there's any
		$value = !empty($post['stream_permissions']) ? $post['stream_permissions'] : '';
		$type = !empty($post['permission_type']) ? $post['permission_type'] : '';
		$profileType = !empty($post['permission_profiles']) ? $post['permission_profiles'] : '';

		// If this is a new event being created, ensure that admin is always checked by default
		if (!$value && !is_array($value)) {
			$value = array('admin');
		}

		// Ensure that it's an array
		$value = ES::makeArray($value);
		$type = ES::makeArray($type);
		$profileType = ES::makeArray($profileType);

		$profiles = $this->getProfiles();

		$this->set('value', $value);
		$this->set('type', $type);
		$this->set('profileType', $profileType);
		$this->set('profiles', $profiles);

		return $this->display();
	}

	/**
	 * Displays the form for event owner to define permissions when event is being edited
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function onEdit(&$post, Socialevent &$event, $errors)
	{
		$permissions = $event->getParams()->get('stream_permissions', array());

		$value = !empty($post['stream_permissions']) ? $post['stream_permissions'] : $permissions;
		$value = ES::makeArray($value);

		$type = $event->getParams()->get('permission_type', array());
		$type = !empty($post['permission_type']) ? $post['permission_type'] : $type;

		$profileType = $event->getParams()->get('permission_profiles', array());
		$profileType = !empty($post['permission_profiles']) ? $post['permission_profiles'] : $profileType;

		$profiles = $this->getProfiles();

		$this->set('value', $value);
		$this->set('profiles', $profiles);
		$this->set('type', $type);
		$this->set('profileType', $profileType);

		return $this->display();
	}

	/**
	 * Displays the sample output for the back end.
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function onSample()
	{
		$profiles = $this->getProfiles();

		$this->set('profiles', $profiles);
		return $this->display();
	}

	/**
	 * Processes the save for new event creation
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function onRegisterBeforeSave(&$post, &$event)
	{
		return $this->onBeforeSave($post, $event);
	}

	/**
	 * Processes the save for event editing
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function onEditBeforeSave(&$post, Socialevent &$event)
	{
		return $this->onBeforeSave($post, $event);
	}

	/**
	 * Before the form is saved, we need to store these data into the event properties
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function onBeforeSave(&$post, Socialevent &$event)
	{
		$value = !empty($post['stream_permissions']) ? $post['stream_permissions'] : array();
		$value = ES::makeArray($value);

		$type = ES::makeArray(!empty($post['permission_type']) ? $post['permission_type'] : array());
		$profiles = ES::makeArray(!empty($post['permission_profiles']) ? $post['permission_profiles'] : array());
		
		// Set it into the event params so that we can retrieve this later
		$params = $event->getParams();
		$params->set('stream_permissions', $value);
		$params->set('permission_type', $type);
		$params->set('permission_profiles', $profiles);

		$event->params = $params->toString();

		unset($post['stream_permissions']);
		unset($post['permission_type']);
		unset($post['permission_profiles']);
	}

	/**
	 * Get list of profile types
	 *
	 * @since   2.0
	 * @access  public
	 */
	public function getProfiles()
	{
		// Get a list of profiles on the site
		$model = ES::model('Profiles');
		$profiles = $model->getProfiles();

		return $profiles;
	}
}