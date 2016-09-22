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

class JFormFieldFSJTMStatus extends JFormField
{
	protected function getInput()
	{
		return "";	
	}
	
	function AdminDisplay($value, $name, $item)
	{
		if ($value == "")
			$value = "0";
		
		$html[] = '<div style="float: left;padding-right: 8px;width: 32px;">'.$value.'%</div>';
		
		$style = "danger";
		if ($value == 100)
			$style = "success";
		else if ($value > 66)
			$style = "info";
		else if ($value > 33)
			$style = "warning";
		
		$html[] = '<div class="progress progress-'.$style.' active" style="width: 150px;height: 8px;margin: 5px 0px 0px 0px;">';
		
		if ($value < 5)
			$value = 4;
		
        $html[] = '<div class="bar" style="width: '.$value.'%"></div>';
		$html[] = '</div>';
		
		return implode($html);
	}
}
