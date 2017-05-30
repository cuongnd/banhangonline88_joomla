<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');

class JFormFieldFSJString extends JFormFieldText
{
	protected $type = 'FSJGtring';
	
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
		if (!$this->element['useglobal'])
			return parent::getLabel();
		
		$this->js_type = "basic";
		// Initialise variables.
		$label = '';

		if ($field->hidden)
		{
			return $label;
		}
		
		// Get the label text from the XML element, defaulting to the element name.
		$text = $field->element['label'] ? (string) $field->element['label'] : (string) $field->element['name'];
		$text = $field->translateLabel ? JText::_($text) : $text;

		// Build the class for the label.
		$class = !empty($field->description) ? 'hasTip' : '';
		$class = $field->required == true ? $class . ' required' : $class;
		$class = !empty($field->labelClass) ? $class . ' ' . $field->labelClass : $class;

		// Add the opening label tag and main attributes attributes.
		$label .= '<label id="' . $field->id . '-lbl" for="' . $field->id . '" class="' . $class . '"';

		// If a description is specified, use it to build a tooltip.
		if (!empty($field->description))
		{
			$label .= ' title="'
				. htmlspecialchars(
					trim($text, ':') . '::' . ($field->translateDescription ? JText::_($field->description) : $field->description),
					ENT_COMPAT, 'UTF-8'
					) . '"';
		}

		$cbchecked = "";
		if ($field->value != "")
			$cbchecked = " checked='checked' ";

		$checkbox = "<input id='".$field->id."-gcb' class='fsj_global_cb fsj_global_cb_".$field->js_type."' type='checkbox' $cbchecked>";

		// Add the label text and closing tag.
		if ($field->required)
		{
			$label .= '>' . $checkbox . $text . '<span class="star">&#160;*</span></label>';
		}
		else
		{
			$label .= '>' . $checkbox . $text . '</label>';
		}

		return $label;	
	}

	protected function getInput()
	{
		if (!$this->element['useglobal'])
			return parent::getInput();

		$field->is_global = ($field->value == "");

		// Initialize some field attributes.
		$size = $field->element['size'] ? ' size="' . (int) $field->element['size'] . '"' : '';
		$maxLength = $field->element['maxlength'] ? ' maxlength="' . (int) $field->element['maxlength'] . '"' : '';
		$class = $field->element['class'] ? ' class="' . (string) $field->element['class'] . '"' : '';
		$readonly = ((string) $field->element['readonly'] == 'true') ? ' readonly="readonly"' : '';
		$disabled = ((string) $field->element['disabled'] == 'true') ? ' disabled="disabled"' : '';

		// Initialize JavaScript field attributes.
		$onchange = $field->element['onchange'] ? ' onchange="' . (string) $field->element['onchange'] . '"' : '';

		$html = array();
		$html[] = "<div style='position:relative;float:left;'>";
		
		$hidestyle = "";
		if (!$field->is_global)
			$hidestyle = "display: none";
		
		$html[] = '<div id="' . $field->id . '-hide" class="fsj_fieldset_hide" style="'.$hidestyle.'"></div>';
		
		if ($field->is_global)
		{
			$html[] = '<input type="text" name="NONE" gname="' . $field->name . '" id="' . $field->id . '"' . ' value="'
				. htmlspecialchars($field->value, ENT_COMPAT, 'UTF-8') . '"' . $class . $size . $disabled . $readonly . $onchange . $maxLength . '/>';
		} else {
			$html[] = '<input type="text" name="' . $field->name . '" gname="' . $field->name . '" id="' . $field->id . '"' . ' value="'
				. htmlspecialchars($field->value, ENT_COMPAT, 'UTF-8') . '"' . $class . $size . $disabled . $readonly . $onchange . $maxLength . '/>';
		}
		
		$html[] = "</div>";
		
		return implode($html);	
	}	
	
	function AdminDisplay($value, $name, $item)
	{
		if (isset($this->fsjstring->tmpl) && $this->fsjstring->tmpl)
			return FSJ_Helper::ParseDataFields($this->fsjstring->tmpl, $item);

		return $value;	
	}
}
