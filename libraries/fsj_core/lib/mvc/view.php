<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

/** 
 * Customized base view class
 **/

class FSJView extends JViewLegacy
{
	var $output_message = null;
	
	var $item_model_name;
	var $item_model;
	var $inline = false;
	var $inline_settings = null;
	
	var $uid = 0;
	
	var $vars = array();
	
	var $input_or = array();
	var $manual_input = false;
	
	var $error_messages = array();
	
	function inputSet($var, $value)
	{
		if ($value == null)
			return;
		
		$this->input_or[$var] = $value;
	}
	
	function inputHas($var)
	{
		if (array_key_exists($var, $this->input_or))
			return true;
		
		return false;
	}
	
	function inputGet($var, $default = null)
	{
		if (array_key_exists($var, $this->input_or))
			return $this->input_or[$var];
		
		if (!$this->manual_input)
			return JFactory::getApplication()->input->getVar($var, $default);
		
		return $default;
	}

	function init()
	{	
		$this->uid = mt_rand(10000,99999);
		
		$app = JFactory::getApplication();
		
		foreach ($this->vars as $var => $type)
		{
			if ($type == "int")
			{
				//$this->$var = $app->input->getInt($var);
				$this->$var = (int)$this->inputGet($var);
			} else {
				$this->$var = $this->inputGet($var);
			}
		}			
		
		if ($this->item_model_name && !$this->item_model)
		{
			$this->item_model = $this->controller->getModel($this->item_model_name);
			$this->setModel($this->item_model, false);
		}
		
		// some common variables
		$this->view = $this->inputGet('view');
	}
	
	function Error($message = "")
	{
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseWarning(500, implode("\n", $errors));
		} else if ($message){
			JError::raiseWarning(500, $message);
		} else {
			JError::raiseWarning(500, "Unknown Error");
		}	
		return false;
	}
	
	function Error_404($message)
	{
		if ($this->inline)
		{
			echo FSJ_Page::errorBox("Error", $message);
			return false;
		}
		
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(404, implode("\n", $errors));
		} else if ($message){
			JError::raiseError(404, $message);
		} else {
			JError::raiseError(404, "Unknown Error");
		}	
		return false;	
	}

	public function loadLanguage($extension, $basePath = JPATH_SITE)
	{
		$lang = JFactory::getLanguage();

		return $lang->load(strtolower($extension), $basePath, null, false, true);
	}
}