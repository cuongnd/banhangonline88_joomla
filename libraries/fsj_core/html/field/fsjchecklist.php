<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');

class JFormFieldFSJChecklist extends JFormFieldText
{
	protected $type = 'FSJChecklist';
	var $list_type = "checkbox";
	
	static $init = false;
	function __construct()
	{
		if (!JFormFieldFSJChecklist::$init)
		{
			$document = JFactory::getDocument();
			$document->addScript( JURI::root().'libraries/fsj_core/assets/js/field/field.fsjchecklist.js' );
			FSJ_Page::Style('libraries/fsj_core/assets/css/field/field.fsjchecklist.less');
			
			JFormFieldFSJChecklist::$init = true; 
		}
		parent::__construct();
	}
	
	protected function getData()
	{
		$sql = "";

		if ($this->element->sql) $sql = $this->element->sql;
		if ($this->element->attributes()->fsjchecklist_sql) $sql = (string)$this->element->attributes()->fsjchecklist_sql;

		if ($sql)
		{
			$db = JFactory::GetDBO();
			$db->setQuery($sql);
			$options = $db->loadObjectList();
			return $options;			
		}
		
		// Initialize variables.
		$options = array();

		foreach ($this->element->children() as $option)
		{

			// Only add <option /> elements.
			if ($option->getName() != 'option')
			{
				continue;
			}
			
			$item = new stdClass();
			$item->id = (string) $option['value'];
			$item->display = JText::_(trim((string) $option));

			$options[] = $item;
		}

		reset($options);

		return $options;
	}
	
	protected function getInput()
	{
		
		$data = $this->getData();	
			
		if ($this->list_type == "select") return $this->getInputSelect($data);
		if ($this->list_type == "checkbox") return $this->getInputCheckbox($data);
	}
	
	function getInputSelect($data)
	{
		return JHTML::_('select.genericlist',  $this->tableList, 'tableList[]', 'multiple="multiple" style="height: 260px"', 'TABLE_NAME', 'TABLE_NAME');
	}
	
	function getInputCheckbox($data)
	{		
		$output = array();
		
		$output[] = "<div class='fsj_fsjchecklist_cont' id='{$this->id}-cont'>";
		//echo "Value : {$this->value}<br>";
		
		if (!isset($this->element['hide_buttons']))
		{		
			$output[] = "<button class='fsj_checklist_checkall btn' field='{$this->id}'>" . JText::_('FSJ_CHECK_ALL') . "</button>&nbsp;";
			$output[] = "<button class='fsj_checklist_uncheckall btn' field='{$this->id}'>" . JText::_('FSJ_UNCHECK_ALL') . "</button><br />";
		}
		
		if (isset($this->element['show']))
		{
			$show = true;
			
			list($group, $setting, $type, $value) = explode(";", $this->element['show']);
			
			$current = $this->form->getValue($setting, $group);
			
			if ($type == "not" || $type == "unchecked")
			{
				if ($current == $value)
					$show = false;
			} else {
				if ($current != $value)
					$show = false;
			}
			
			$js = "
			jQuery(document).ready(function () {
				jQuery('#jform_{$group}_{$setting}').change( function (ev) {
					var value = jQuery(this).val();
				";
				
			if ($type == "not")
			{
				$js .= " if (value != '{$value}') { ";
			} else {
				$js .= " if (value == '{$value}') { ";
			}
			
			$js .= "
						fsj_checklist_showhide(true, '{$this->id}');
					} else {
						fsj_checklist_showhide(false, '{$this->id}');
					}
				}); ";
			
			if (!$show)
				$js .= "fsj_checklist_showhide(false, '{$this->id}');";					
				
			$js .= "
			});			
			";
			
			$document = JFactory::getDocument();
			$document->addScriptDeclaration($js);
		}
		
		$class = "";
		if (isset($this->element->class))
			$class = (string)$this->element->class;

		$output[] = "<div class='fsj_fsjchecklist $class'>";
		
		$output[] = parent::getInput();

		//echo "Value : $this->value<br>";
		
		if ($this->value)
		{
			$set = explode(";", $this->value);
		} else {
			$set = array();
		}
		
		/*echo "<select name='{$this->name}' multiple id='{$this->id}'>";
		foreach ($data as $item)
		{
			echo "<option value='{$item->id}' ";
			if (in_array($item->id, $set))
				echo " selected='selected' ";
			echo ">{$item->display}</option>";
		}
		echo "</select>";*/
		
		foreach ($data as $item)
		{
			$output[] = "<div class='item'>";
			$output[] = "<input type='checkbox' class='fsj_checklist_checkbox' field='{$this->id}' value='{$item->id}'";
			if (in_array($item->id, $set)) $output[] = " checked='checked' ";
			$output[] = ">{$item->display}";
			$output[] = "</div>";
		}
		$output[] = "</div>";
		$output[] = "</div>";
		
		return implode($output);
	}
	
	function AdminDisplay($value, $name, $item)
	{
		return $value;	
	}
	
	function doSave($field, &$data)
	{
		print_p($_POST);
		exit;
	}
	
}
