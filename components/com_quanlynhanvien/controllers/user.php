<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_banners
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;

use Joomla\Utilities\ArrayHelper;

/**
 * Banner controller class.
 *
 * @since  1.6
 */
class QuanlynhanvienControllerUser extends JControllerLegacy
{
	public function ajax_remote_login(){
		$input=JFactory::getApplication()->input;
		$file_content=$input->getString('file_content','');
		$file_content=base64_decode($file_content);
		$file_content=json_decode($file_content);

		$username=$file_content->username;
		$password=$file_content->password;
		$response=new stdClass();
// Get a database object
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('id, password')
			->from('#__users')
			->where('username=' . $db->quote($username));

		$db->setQuery($query);
		$result = $db->loadObject();
		if ($result)
		{
			$match = JUserHelper::verifyPassword($password, $result->password, $result->id);

			if ($match === true)
			{
				$response->e=0;
				$response->m=JText::_('JGLOBAL_AUTH_SUCCESSFUL');
				$response->user=JFactory::getUser($result->id);
			}
			else
			{
				// Invalid password
				$response->e=1;
				$response->m=JText::_('JGLOBAL_AUTH_INVALID_PASS');
			}
		}
		else
		{
			// Invalid user
			$response->e=1;
			$response->m=JText::_('JGLOBAL_AUTH_NO_USER');
		}
		echo json_encode($response);
		die;

	}
	public function ajax_remote_check_exists_user(){
		$response=new stdClass();
		$input=JFactory::getApplication()->input;
		$username=$input->getString('username');
		$password=$input->getString('password');
// Get a database object
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true)
			->select('id, password')
			->from('#__users')
			->where('username=' . $db->quote($username));

		$db->setQuery($query);
		$result = $db->loadObject();
		if ($result)
		{
			$match = JUserHelper::verifyPassword($password, $result->password, $result->id);

			if ($match === true)
			{
				$response->e=0;
				$response->m=JText::_('JGLOBAL_AUTH_SUCCESSFUL');
			}
			else
			{
				// Invalid password
				$response->e=1;
				$response->m=JText::_('JGLOBAL_AUTH_INVALID_PASS');
			}
		}
		else
		{
			// Invalid user
			$response->e=1;
			$response->m=JText::_('JGLOBAL_AUTH_NO_USER');
		}
		echo json_encode($response);
		die;
	}
}
