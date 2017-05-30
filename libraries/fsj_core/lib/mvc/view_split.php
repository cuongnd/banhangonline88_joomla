<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'fsj_core.lib.utils.tasks');

class FSJViewSplit extends JViewLegacy
{

	var $default_view = "main";

	public function display($tpl = null)
	{
		$base_path = 'components';

		if (JFactory::getApplication()->isAdmin())
			$base_path = 'administrator'.DS.'components';

		if (FSJ_Task_Helper::HandleTasks($this, $base_path))
			return false;

		$layout = JRequest::getCmd('layout', $this->default_view);	
		$layout = preg_replace("/[^a-z0-9\_]/", '', $layout);
				
		$file = JPATH_SITE.DS.$base_path.DS.JRequest::getCmd('option').DS.'views'.DS.JRequest::getCmd('view').DS.'layout.' . $layout . '.php';
		if (!file_exists($file))
		{
			echo "Layout file $file not found<br>";
			return;
		}
		require_once($file);
		
		$class_name = str_replace("com_","", JRequest::getCmd('option'))."View".JRequest::getCmd('view')."_" . $layout;
		
		$layout_handler = new $class_name();
		$layout_handler->setLayout($layout);
		$layout_handler->_models = $this->_models;
		$layout_handler->_defaultModel = $this->_defaultModel;
		if (!$layout_handler->init())
			return false;

		$layout_handler->display();
	}

	function init()
	{
		return true;
	}
	
	public function getName()
	{
		$this->_name = JRequest::getCmd('view');
		return $this->_name;
	}

	function _display($tpl = NULL)
	{
		// entry point into view
		parent::display($tpl);	
	}		
}
