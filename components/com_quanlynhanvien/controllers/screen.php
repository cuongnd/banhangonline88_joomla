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
		$response->e=0;
		$response->m=JText::_('JGLOBAL_AUTH_SUCCESSFUL');
		echo json_encode($response);
		die;
	}
}
