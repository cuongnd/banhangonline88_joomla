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
			$create_on=$item->create_on;
			$file_name=$item->file_name;
			$user_id=$item->user_id;
			$create_on=explode("_",$create_on);
			$create_on="$create_on[0]-$create_on[1]-$create_on[2] $create_on[3]-$create_on[4]-$create_on[5]";
			$create_on=JFactory::getDate($create_on);
			$create_on=$create_on->toSql();
			$query->values("$query->q($create_on),$query->q($file_name),$user_id");
		}

		JTable::addIncludePath('administrator/components/com_quanlynhanvien/tables');
		$table_screen=JTable::getInstance('screen');

		$response=new stdClass();
		$response->e=0;
		$response->m=JText::_('JGLOBAL_UPDATE_SUCCESSFUL');
		$response->m=print_r($json_list_json_screen,true);
		echo json_encode($response);
		die;
	}
}
