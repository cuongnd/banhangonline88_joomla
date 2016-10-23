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
class QuanlynhanvienControllerscreen extends JControllerLegacy
{
	public function ajax_save_remote_screen(){
		$response=new stdClass();
		$app=JFactory::getApplication();
		$input=$app->input;
		$json_list_json_screen=$input->getString('json_list_json_screen','');
		$json_list_json_screen=base64_decode($json_list_json_screen);
		$json_list_json_screen=json_decode($json_list_json_screen);

		$db=JFactory::getDbo();
		$query=$db->getQuery(true);
		$query->insert('#__screen')
			->columns('created,filename,user_id')
		;
		foreach($json_list_json_screen as $item)
		{
			$item=json_decode($item);
			$create_on=$query->q($item->create_on);
			$file_name=$query->q($item->file_name);
			$user_id=$item->user_id;
			$query->values("$create_on,$file_name,$user_id");
		}

		$db->setQuery($query);
		if(!$db->execute())
		{
			$response->e=1;
			$response->m=$db->getErrorMsg();
		}else {
			$response->e = 0;
			$response->m = JText::_('JGLOBAL_UPDATE_SUCCESSFUL');
		}
		echo json_encode($response);
		die;
	}
}
