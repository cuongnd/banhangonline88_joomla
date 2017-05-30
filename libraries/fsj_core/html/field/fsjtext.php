<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('textarea');

class JFormFieldFSJText extends JFormFieldTextarea
{
	protected $type = 'FSJText';

	protected function getInput()
	{
		// Translate placeholder text
		$hint = $this->translateHint ? JText::_($this->hint) : $this->hint;

		// Initialize some field attributes.
		$class        = !empty($this->class) ? ' class="' . $this->class . '"' : '';
		$disabled     = $this->disabled ? ' disabled' : '';
		$readonly     = $this->readonly ? ' readonly' : '';
		$columns      = $this->columns ? ' cols="' . $this->columns . '"' : '';
		$rows         = $this->rows ? ' rows="' . $this->rows . '"' : '';
		$required     = $this->required ? ' required aria-required="true"' : '';
		$hint         = $hint ? ' placeholder="' . $hint . '"' : '';
		$autocomplete = !$this->autocomplete ? ' autocomplete="off"' : ' autocomplete="' . $this->autocomplete . '"';
		$autocomplete = $autocomplete == ' autocomplete="on"' ? '' : $autocomplete;
		$autofocus    = $this->autofocus ? ' autofocus' : '';
		$spellcheck   = $this->spellcheck ? '' : ' spellcheck="false"';

		if ($this->element['width'] != "")
		{
			$this->element['fsjtext_style'] .= " ; width: " . $this->element['width'];
		}

		$style = $this->element['fsjtext_style'] ? ' style="' . $this->element['fsjtext_style'] . '"' : '';

		// Initialize JavaScript field attributes.
		$onchange = $this->onchange ? ' onchange="' . $this->onchange . '"' : '';
		$onclick = $this->onclick ? ' onclick="' . $this->onclick . '"' : '';

		// Including fallback code for HTML5 non supported browsers.
		FSJ_Page::StylesAndJS();
		//JHtml::_('jquery.framework');
		//JHtml::_('script', 'system/html5fallback.js', false, true);

		return '<textarea name="' . $this->name . '" id="' . $this->id . '"' . $columns . $rows . $class
			. $hint . $disabled . $readonly . $onchange . $onclick . $required . $autocomplete . $autofocus . $spellcheck . $style . ' >'
			. htmlspecialchars($this->value, ENT_COMPAT, 'UTF-8') . '</textarea>';
	}
	
	
	function AdminDisplay($value, $name, $item)
	{
		return $value;	
	}
}
