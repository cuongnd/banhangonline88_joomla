<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('radio');

class JFormFieldFSJYesNo extends JFormFieldRadio
{
	protected $type = 'FSJYesNo';
	var $class;
	
	function __construct()
	{
		parent::__construct();
	}
	
	public function __get($name)
	{
		$res = parent::__get($name);
		
		if ($res)
			return $res;
		
		return $this->$name;		
	}
	
	protected function getInput()
	{
		if ($this->class == "")
			$this->class = "btn-group btn-group-yesno";
		
		return parent::getInput();
	}

	protected function getLabel()
	{
		return parent::getLabel();
	}

	function getOptions()
	{
		FSJ_Lang_Helper::Load_Library("fsj_core");
		
		$display = $this->element['display'];
		
		$yestxt = 'JYES';
		$notxt = 'JNO';
		
		switch ($display)
		{
			case 'tf':
				$yestxt = "FSJ_TRUE";
				$notxt = "FSJ_FALSE";
				break;
			case '10':
				$yestxt = "1";
				$notxt = "0";
				break;
			case 'custom':
				$yestxt = $this->element['custom_yes'];
				$notxt = $this->element['custom_no'];
				break;
		}
		
		if ($this->element['useglobal'])
		{
			if (isset($this->element['useglobal_text']))
			{
				$options[] = JHtml::_('select.option', '', $this->element['useglobal_text']);
			} else {
				$options[] = JHtml::_('select.option', '', "FSJ_FORM_USE_GLOBAL");
			}
		}
		
		$options[] = JHtml::_('select.option', '0', $notxt);
		$options[] = JHtml::_('select.option', '1', $yestxt);

		return $options;
	}
	
	function AdminDisplay($value, $name, $item)
	{
		
		if ($value == "1")
		{
			if (isset($this->fsjyesno->custom_yes)) return JText::_($this->fsjyesno->custom_yes);
			return JText::_('JYES');
		}
		
		if (isset($this->fsjyesno->custom_no)) return JText::_($this->fsjyesno->custom_no);
		return JText::_('JNO');
	}
}
