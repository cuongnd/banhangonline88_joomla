<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('list');

class JFormFieldFSJCombo extends JFormFieldList
{
	protected $type = 'FSJCombo';
	
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

	protected function getLabel()
	{
		//$this->element['class'] = 'btn-group';
		return parent::getLabel();
	}

	function getOptions()
	{
		FSJ_Lang_Helper::Load_Library("fsj_core");

		$options = array();
		if ($this->element['useglobal'])
			$options[] = JHtml::_('select.option', '', JText::_("FSJ_FORM_USE_GLOBAL"));
			
		// static options, including Use Global if needed
		$options = array_merge($options, parent::getOptions());
			
			
		// SQL options if available
		if (isset($this->element->fsjcombo->sql))
		{
			$db = JFactory::getDBO();
			$db->setQuery($this->element->fsjcombo->sql);
			
			$options = parent::getOptions();
			
			$options = array_merge($options, $db->loadObjectList());
		}
		
		return $options;
	}
	
	function AdminDisplay($value, $name, $item)
	{
		if ($value == "")
			return "";
		
		// need to modify this so that getOptions is used to get the labels to display.
			
		foreach ($this->fsjcombo->options as $option)
		{
			if ($option->id == $value)
				return JText::_($option->value);	
		}
		return "";	
	}
}
