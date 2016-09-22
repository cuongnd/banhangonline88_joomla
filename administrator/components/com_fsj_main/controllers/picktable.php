<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('fsj_core.lib.utils.plugin_handler');
jimport("fsj_core.plugins.picktype.picktype");

class fsj_mainControllerPickTable extends JControllerLegacy
{
	function __construct($config = array())
	{
		parent::__construct($config);
	}
	
	function display($cachable = false, $urlparams = Array())
	{
		$ph = FSJ_Plugin_Type_PickType::GetInstance("table");	
		$table = JRequest::getVar('table');
		$com = JRequest::getVar('com');
		$ph->id = JRequest::getVar('id');
		$ph->pluginid = $table;
		$ph->Init($table, $com);
		$ph->Display();
	}
}
