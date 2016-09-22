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

class JFormFieldfsjtmmakepackage extends JFormField
{
	protected function getInput()
	{
		return $this->actionButtons($this->form->getValue('id'), $this->form->getValue('pubfolder'));
	}
	
	function AdminDisplay($value, $name, $item)
	{
		return $this->actionButtons($item->id, $item->pubfolder);
	}
	
	function actionButtons($id, $pubfolder)
	{
		if ($id < 1)
			return JText::_("FSJ_TM_PICK_LANG_FIRST");
		
		$output = array();
		$link = JRoute::_('index.php?option=com_fsj_transman&task=transman.download&id=' . $id);
		$output[] = "<a class='btn' href='" . $link . "'><i class='icon-download'></i>&nbsp;".JText::_('FSJ_TM_DOWNLOAD')."</a>";
		
		if ($pubfolder)
		{
			$link = JRoute::_('index.php?option=com_fsj_transman&task=transman.pubfile&id=' . $id);
			$output[] = "<a class='btn' href='" . $link . "'><i class='icon-publish'></i>&nbsp;".JText::_('FSJ_TM_PUBLISH')."</a>";
		}	
		
		return implode("\n", $output);
	}
}
