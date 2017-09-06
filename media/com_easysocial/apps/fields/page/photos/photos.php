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

class SocialFieldsPagePhotos extends SocialFieldItem
{
	/**
	 * Displays the output form when someone tries to create a page.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	Array 					An array of data that has been submitted
	 * @param	SocialTableStepSession	The session table
	 * @return	string					The html codes for this field
	 *
	 */
	public function onRegister($post, SocialTableStepSession $session)
	{
		// Get any previously submitted data
		$value = isset($post['photo_albums']) ? $post['photo_albums'] : $this->params->get('default', true);
		$value = (bool) $value;

		// Detect if there's any errors
		$error = $session->getErrors($this->inputName);

		$this->set('error'	, $error);
		$this->set('value', $value);

		return $this->display();
	}

	/**
	 * Displays the output form when someone tries to edit a page.
	 *
	 * @since	2.0
	 * @access	public
	 * @param	Array 					An array of data that has been submitted
	 * @param	SocialTableStepSession	The session table
	 * @return	string					The html codes for this field
	 *
	 */
	public function onEdit(&$data, &$page, $errors)
	{
		$params = $page->getParams();
		$value = $page->getParams()->get('photo.albums', $this->params->get('default', true));
		$error = $this->getError($errors);

		$this->set('error'	, $error);
		$this->set('value', $value);

		return $this->display();
	}

	/**
	 * Executes after the page is created
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @param	SocialTableRegistration		The registration ORM table.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 */
	public function onEditBeforeSave(&$data, &$page)
	{
		// Get the posted value
		$value = isset($data['photo_albums']) ? $data['photo_albums'] : $page->getParams()->get('photo.albums', $this->params->get('default', true));
		$value = (bool) $value;

		$registry = $page->getParams();
		$registry->set('photo.albums', $value);

		$page->params = $registry->toString();
	}

	/**
	 * Executes after the page is created
	 *
	 * @since	1.0
	 * @access	public
	 * @param	array 	The posted data.
	 * @param	SocialTableRegistration		The registration ORM table.
	 * @return	bool	Determines if the system should proceed or throw errors.
	 *
	 */
	public function onRegisterBeforeSave(&$data, &$page)
	{
		// Get the posted value
		$value = isset($post['photo_albums']) ? $post['photo_albums'] : $this->params->get('default', true);
		$value = (bool) $value;

		$registry = $page->getParams();
		$registry->set('photo.albums', $value);

		$page->params = $registry->toString();
	}

	/**
	 * Override the parent's onDisplay
	 *
	 * @since	2.0
	 * @access	public
	 */
	public function onDisplay()
	{
		return;
	}

	/**
	 * Displays the sample field in the administration area.
	 *
	 * @since	2.0
	 * @access	public
	 * @return
	 */
	public function onSample()
	{
		$value = $this->params->get('default');

		$this->set('value', $value);

		return $this->display();
	}
}
