<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');
jimport("joomla.filesystem.folder");

class JFormFieldFSJTMFilename extends JFormField
{
	protected function getInput()
	{
		return "";	
	}

	function AdminDisplay($value, $name, $item)
	{
		$value = str_replace(".ini", "", $value);
		$value = str_replace("_", " ", $value);

		return $value; 
	}
    
}
