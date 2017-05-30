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
require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'helpers'.DS.'file_helper.php');

class JFormFieldFSJTMFiles extends JFormField
{
	protected function getInput()
	{
		return "";	
	}
	
	function AdminDisplay($value, $name, $item)
	{
		$text1[] = "<div class='pull-right'>";
		$text1[] = "<a class='btn btn-small btn-info' href='" . JRoute::_('index.php?option=com_fsj_transman&view=files&filter_element='.$item->element.'&filter_xpath='.$item->client_id."|".$item->prefix.".".$item->component) . "'>".JText::_("FSJ_TM_EDIT") . "</a>";
		$text1[] = "</div>";
		
		$path = JPATH_ROOT . DS . $item->path . DS . $item->element;
		$count = 0;	

		$files = FSJ_TM_File_Helper::GetFiles(false, $item->element, $item->client_id."|".$item->prefix.".".$item->component);

		$counts = array(
			'done' => 0, // 
			'in_base' => 0, // 
			'no_base' => 0, //
			
			'pub' => 0,
			'unpub' => 0,
			'notstarted' => 0,
			
			'partial' => 0,
			'complete' => 0
			);
		
		foreach ($files as $file)
		{
			
			if ($file->no_base)
			{
				$counts['no_base'] ++;
			} else {
				$counts['in_base'] ++;
			}
			
			if ((int)$file->tstate == 1)
				$counts['pub'] ++;
			elseif ((int)$file->tstate == 3)
				$counts['unpub'] ++;
			elseif ($file->tstate == "" || $file->tstate == 0)
				$counts['notstarted'] ++;
			
			if ($file->status == 100)
			{
				$counts['complete'] ++;
			} elseif ($file->status > 0) {
				$counts['partial'] ++;
			}
			
			if ($file->status == 100 && (int)$file->tstate == 1)
				$counts['done'] ++;
		}
		
		$counts['in_lang'] = $counts['pub'] + $counts['unpub'];

		$text1[] = "<span class='badge badge-success'>{$counts['done']}</span> ";
		if ($counts['unpub'] > 0)
			$text1[] = "<span class='badge badge-info'>{$counts['unpub']}</span> ";
		if ($counts['partial'] > 0)
			$text1[] = "<span class='badge badge-warning'>{$counts['partial']}</span> ";
		if ($counts['notstarted'] > 0)
			$text1[] = "<span class='badge badge-important'>{$counts['notstarted']}</span> ";
		if ($counts['no_base'] > 0)
			$text1[] = "<span class='badge badge-inverse'>{$counts['no_base']}</span> ";

		$text1 = implode($text1);
	
		return $text1;
		
		//$output[] = "<a href='" . JRoute::_('index.php?option=com_fsj_transman&view=files&filter_element='.$item->element.'&filter_xpath='.$item->client_id."|".$item->prefix.".".$item->component)."'>".$text1."</a>";
		//$output[] = "<a href='" . JRoute::_('index.php?option=com_fsj_transman&view=files&filter_element='.$item->element.'&filter_xpath='.$item->client_id."|".$item->prefix.".".$item->component)."'>".$count."&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</a>";
		
		return implode($output);
	}
}
