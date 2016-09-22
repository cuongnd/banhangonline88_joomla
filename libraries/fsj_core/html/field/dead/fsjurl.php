<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');

class JFormFieldFSJUrl extends JFormFieldText
{

	protected $type = 'FSJUrl';

	function AdminDisplay($value, $name, $item)
	{
		if ($value == "")
			return "";
		
		return "<a href='$value' target='_blank'>$value</a>";	
	}
}
