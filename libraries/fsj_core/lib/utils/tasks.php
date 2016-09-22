<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FSJ_Task_Helper
{
	function execute($task)
	{
		if (method_exists($this, $task))
		{
			return $this->$task();	
		} else {
			echo "Invalid Task - $task<br>";
			print_p($this);
			exit;
		}
	}	
	
	static function HandleTasks($view, $path = 'components')
	{
		$task = strtolower(JRequest::getCmd('task', JRequest::getCmd('action')));
		$task = str_replace("-", "_", $task);
		
		$bits = explode(".", $task);
		if (count($bits) != 2)
			return false;
		
		$task_class = preg_replace("/[^a-z0-9\_]/", '', $bits[0]);
		$task_ident = preg_replace("/[^a-z0-9\_]/", '', $bits[1]);
		
		$task_file = JPATH_SITE.DS.$path.DS.JRequest::getCmd('option').DS.'views'.DS.JRequest::getCmd('view').DS.'task.' . $task_class . '.php';
		
		if (!file_exists($task_file))
		{
			echo "No file : $task_file<br>";
			return false;
		}
	
		require_once ($task_file);
		
		$task_class_name = "Task_" . $task_class;
		
		if (!class_exists($task_class_name))
		{
			echo "No class : $task_class<br>";
			return false;
		}
		
		$task_obj = new $task_class_name();
		$task_obj->view = $view;
		
		return $task_obj->execute($task_ident);
	}
}