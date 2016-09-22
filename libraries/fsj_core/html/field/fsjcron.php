<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

JFormHelper::loadFieldClass('text');

class JFormFieldFSJCron extends JFormFieldText
{
	protected $type = 'FSJCron';
	
	function __construct()
	{
		parent::__construct();
	}

	protected function getInput()
	{
		$type = $this->element['fsjcron_type'];

		$data = $this->form->getData();

		$item = new stdClass();
		$item->id = $data->get('id');

		if ($type == "lastrun") return $this->AdminDisplayLastRun($item);
		if ($type == "runat") return $this->AdminDisplayRunAt($this->value);
		if ($type == "run") return $this->AdminDisplayRun($item, $this->element['fsjcron_source']);
	}
	
	function AdminDisplay($value, $name, $item)
	{
		$type = $this->fsjcron->type;

		if ($type == "lastrun") return $this->AdminDisplayLastRun($item);
		if ($type == "log") return $this->AdminDisplayLog($item);
		if ($type == "runat") return $this->AdminDisplayRunAt($value);
		if ($type == "run") return $this->AdminDisplayRun($item, $this->fsjcron->source);
	}

	function AdminDisplayLastRun($item)
	{
		JFormFieldFSJCron::loadLastRun();

		$source = (string)(isset($this->fsjcron->source) ? $this->fsjcron->source : $this->element['fsjcron_source']);
		$idfield = (string)(isset($this->fsjcron->source_id) ? $this->fsjcron->source_id : $this->element['fsjcron_source_id']);

		$id = $item->$idfield;

		if (!empty(self::$lastrun[$source][$id]))
		{
			$lastrun = self::$lastrun[$source][$id]->lastrun;

			if ($lastrun < 1)
				return "Never";

			return date("Y-m-d H:i:s", $lastrun);
		}
			
		return "Not enabled";
	}

	function AdminDisplayRunAt($time)
	{
		return date("Y-m-d H:i:s", $time);
	}

	static $log_js = false;
	function AdminDisplayLog($item)
	{
		$this->outputLogJS();

		return "<div style='max-height: 60px; overflow: hidden;cursor: pointer;' class='cron_log_show'>" . $item->log . "</div>";
	}

	function outputLogJS()
	{
		if (!self::$log_js)
		{
			FSJ_Page::Script("libraries/fsj_core/assets/js/field/field.cronlog.js");
			FSJ_Page::IncludeModal();

			self::$log_js = true;
		}
	}

	static $lastrun;

	static function loadLastRun()
	{
		if (empty(self::$lastrun))
		{
			$db = JFactory::getDBO();
			$sql = "SELECT * FROM #__fsj_main_cron";
			$db->setQuery($sql);
			$result = $db->loadObjectList();

			self::$lastrun = array();

			foreach ($result as $item)
			{
				self::$lastrun[$item->source][$item->source_id] = $item;
			}
		}
	}

	static $crons;
	function AdminDisplayRun($item, $type)
	{
		jimport('fsj_core.lib.utils.cron_handler');
		
		self::loadCronList();

		$type = (string)$type;

		if (!isset(self::$crons[$type][$item->id]))
			return "Unable to find cron task to run. Please save the item first.";

		$cron_id = self::$crons[$type][$item->id]->id;

		FSJ_Page::IncludeModal();

		$key = FSJ_Cron_Handler::getTestKey($cron_id);

		$url = JRoute::_("index.php?option=com_fsj_main&view=cron&runnow=" . $cron_id . "&key=" . $key . "&mode=modal&tmpl=component", false );
		$url = str_replace("administrator/", "", $url);

		return "<a href='" . $url . "' class='btn btn-small fsj_show_modal_iframe' data_modal_width='760'>Run</a>";
	}

	static function loadCronList()
	{
		if (empty(self::$crons)) 
		{
			$db = JFactory::getDBO();
			$sql = "SELECT * FROM #__fsj_main_cron";
			$db->setQuery($sql);
			$items = $db->loadObjectList();

			self::$crons = array();

			foreach ($items as $item)
			{
				self::$crons[$item->source][$item->source_id] = $item;
			}
		}
	}
}
