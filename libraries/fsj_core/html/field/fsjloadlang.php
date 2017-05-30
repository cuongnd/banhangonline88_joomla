<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class JFormFieldFSJLoadLang extends JFormField
{
	protected $type = 'FSJLoadLang';

	static $counts = array();
	
	protected function getInput()
	{
		$lang = JFactory::getLanguage();
		$lang->load("com_fsj_main");		
		return "";
	}
	
	function AdminDisplay($value, $name, $item)
	{
		return "";
	}
}
