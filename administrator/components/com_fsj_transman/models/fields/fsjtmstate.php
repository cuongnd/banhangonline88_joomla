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

class JFormFieldFSJTMState extends JFormField
{
	static $js = false;
	protected function getInput()
	{
		return "";	
	}
	
	function AdminDisplay($value, $name, $item, $i)
	{
		if (!self::$js)
		{
			if (!FSJ_Helper::IsJ3())
			{
				FSJ_Page::Style("libraries/fsj_core/assets/css/bootstrap/bootstrap_fsjonly.less");
				FSJ_Page::Style("administrator/components/com_fsj_transman/assets/css/fsj_transman.j25.less");
			}
			
			JFactory::getDocument()->addScript( JURI::root().'libraries/fsj_core/assets/js/jquery.fsj_tooltip.js' );
			JFactory::getDocument()->addScript( JURI::root().'libraries/fsj_core/assets/js/fsj.core.js' );
			self::$js = true;
		}
		
		if ($value == 1)
		{
			$html[] = "<div class='fsjTip' title='".JText::_("FSJ_TM_STATE_PUB") ."'>";
			$html[] = "<a class='btn btn-micro' href='javascript:void(0);' onclick='return listItemTask(\"cb{$i}\",\"files.unpub\")'><i class='icon-publish'></i></a>";
			$html[] = "</div>";
		} else if ($value == "" || $value == 0)
		{
			$html[] = "<div class='fsjTip' title='".JText::_("FSJ_TM_STATE_NOT_STARTED") ."'>";
			$html[] = '<a class="btn btn-micro fsjTip" href="javascript:void(0);" disabled="disabled"><i class="icon-minus-sign"></i></a>';
			$html[] = "</div>";
		} else if ($value == 3)
		{
			$html[] = "<div class='fsjTip' title='".JText::_("FSJ_TM_STATE_UNPUB") ."'>";
			$html[] = "<a class='btn btn-micro' href='javascript:void(0);' onclick='return listItemTask(\"cb{$i}\",\"files.pub\")'><i class='icon-unpublish'></i></a>";
			$html[] = "</div>";
		} else {
			$html[] = $value;
		}
		
		if ($item->no_base)
		{
			$html[] = "<div class='fsjTip' title='".JText::_("FSJ_TM_NOBASE") ."'>";
			$html[] = "&nbsp;<a class='btn btn-micro' href='javascript:void(0);' onclick='return listItemTask(\"cb{$i}\",\"files.createbase\")'><i class='icon-warning'></i></a>";
			$html[] = "</div>";
		}
		
		return implode($html);
	}
}
