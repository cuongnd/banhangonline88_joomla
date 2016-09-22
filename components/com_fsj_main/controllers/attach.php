<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'fsj_core.lib.fields.attach_handler');
jimport('fsj_core.plugins.attachment.attachment');

class FSJ_MainControllerAttach extends JControllerLegacy
{
	public function execute($task)
	{
		$task = strtolower($task);
		if (strpos($task, ".") > 0) list($temp, $task) = explode(".", $task, 2);
		return FSJ_Attachment::$task();
	}
}