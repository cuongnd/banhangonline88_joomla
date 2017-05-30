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

class JFormFieldfsjtmdownloadfile extends JFormField
{
	protected function getInput()
	{
		return "";	
	}
	
	function AdminDisplay($value, $name, $item)
	{
		if ($item->tstate > 0)
		{
			$link = JRoute::_('index.php?option=com_fsj_transman&task=file.download&id=' . $item->id);
			return "<a class='btn btn-mini' href='" . $link . "'><i class='icon-download'></i></a>";
		}
		
		return "";
	}
}
