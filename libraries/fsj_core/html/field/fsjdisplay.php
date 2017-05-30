<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');

/**
 * For displaying either a HTML text based on what is configured as part of the form
 * or will display the value of the form in a box.
 **/


class JFormFieldFSJDisplay extends JFormFieldText
{
	protected $type = 'FSJDisplay';
	
	function __construct()
	{
		parent::__construct();
	}
	
	protected function getLabel()
	{
		if ($this->element->attributes()->hide_label)
			return "";
		
		return parent::getLabel();
	}

	protected function getInput()
	{
		$text = ((string)$this->element->attributes()->inputtmpl);
		
		if (isset($this->element->inputtmpl) && trim($this->element->inputtmpl)) return FSJ_Helper::ParseDataFields($this->element->inputtmpl, $item);

		return "<div style='float:left;padding-top: 3px;margin:5px;font-size:120%;'>".$this->value."</div>";
	}
	
	function AdminDisplay($value, $name, $item)
	{
		if (isset($this->fsjdisplay->tmpl))
			return FSJ_Helper::ParseDataFields($this->fsjdisplay->tmpl, $item);

		return $value;	
	}
}
