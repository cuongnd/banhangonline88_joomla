<?php

// cron system caller for Freestyle Joomla

// should be able to be called from the system plugin, and return VERY quickly
// if from system plugin, all running should be done in background

// should be able to be called from a url, with a KEY provided from somewhere

// WHEN DONE TESTING CHANGE ALL //DONE comments
/*

To call from plugin:
	generate random key
	update all db items with key if they need updating and current time as last run
	Call web page in background with key
	On page load list all to be run and update last run after running and clear key

To call from web url:
	update all db items with key if they need updating and current time as last run
	load items with key and run them
*/

class FSJ_Cron_Handler
{
	static function pluginTrigger()
	{
		$cron = new FSJ_Cron_Handler();
		$key = $cron->getRunKey();
		if ($key) $cron->callBackgroundPage($key);
	}

	static function backgroundUrlTrigger($key)
	{
		$cron = new FSJ_Cron_Handler();
		$cron->runCrons($key);
	}

	static function urlTrigger()
	{
		$cron = new FSJ_Cron_Handler();
		$key = $cron->getRunKey();
		if ($key) $cron->runCrons($key);
	}

	static function testTrigger($cron_id, $validate)
	{
		// need to check the validate code against the site to make sure its OK
		if (!self::isTestKeyValid($cron_id, $validate))
		{
			echo "Invalid CRON url<br>";
			return;
		}

		$cron = new FSJ_Cron_Handler();
		$key = $cron->getRunKey($cron_id);

		if ($key) 
		{
			return $cron->runCrons($key, true);
		}
	}

	static function isTestKeyValid($cron_id, $key)
	{
		jimport('fsj_core.lib.utils.crypt');

		$key = FSJ_Helper::base64url_decode($key);
		$enc = FSJ_Crypt::decrypt($key, FSJ_Crypt::getEncKey("cron_task"));

		$enc = trim($enc);
		$cron_id = trim((string)$cron_id);

		return $enc == $cron_id;
	}

	static function getTestKey($cron_id)
	{
		jimport('fsj_core.lib.utils.crypt');

		$enc = FSJ_Crypt::encrypt($cron_id, FSJ_Crypt::getEncKey("cron_task"));
		$enc = FSJ_Helper::base64url_encode($enc);

		return $enc;
	}

	function getRunKey($cron_id = null)
	{
		$key = substr(md5("cron_key_".time()), 0, 8);

		// update all db items with key if they need updating and current time as last run
		$db = JFactory::getDBO();
		if ($cron_id > 0)
		{ 
			$qry = "UPDATE #__fsj_main_cron SET runkey = '" . $db->escape($key) . "', lastrun = UNIX_TIMESTAMP() WHERE id = '" . $db->escape($cron_id) . "'";
		} else {
			$qry = "UPDATE #__fsj_main_cron SET runkey = '" . $db->escape($key) . "', lastrun = UNIX_TIMESTAMP() WHERE state = 1 AND ((UNIX_TIMESTAMP() - lastrun) - (`interval` * 60)) > 0";
		}
		
		$db->setQuery($qry);
		$db->Query();

		$count = $db->getAffectedRows();

		if ($count > 0) return $key;
		return null;
	}

	function runCrons($key, $force = false)
	{
		$output = array();

		$db = JFactory::getDBO();
		$qry = "SELECT * FROM #__fsj_main_cron WHERE runkey = '" . $db->escape($key) . "'";
		
		if (!$force) $qry .= " AND state = 1";

		$db->setQuery($qry);
		$events = $db->loadObjectList();

		foreach ($events as $event)
		{
			$result = $this->runEvent($event, $force);
			if (is_array($result))
			{
				list ($result, $log) = $result;
			} else {
				$log = $result;
			}
			$success = 0;

			if ($result === true) 
			{
				$result = "Success";
				$success = 1;
			}

			$output[$event->id] = array(
									'success' => $success,
									'result' => $result,
									'log' => $log
									);

			// need to store the result in to the cron log table when we have made one
			// also need a page to view the log results

			$qry = "INSERT INTO #__fsj_main_cronlog (source, source_id, event, whentime, whendate, success, result, log) VALUES (";
			$qry .= "'" . $db->escape($event->source) . "', ";
			$qry .= "'" . $db->escape($event->source_id) . "', ";
			$qry .= "'" . $db->escape($event->event) . "', ";
			$qry .= "'" . time() . "', ";
			$qry .= "'" . date("Y-m-d") . "', ";
			$qry .= "'" . $success . "', ";
			$qry .= "'" . $db->escape($result) . "', ";
			$qry .= "'" . $db->escape($log) . "')";
			$db->setQuery($qry);
			$db->Query();

			//$qry = "UPDATE #__fsj_main_cron SET lastrun = 0";
			$qry = "UPDATE #__fsj_main_cron SET lastrun = UNIX_TIMESTAMP(), runkey = '' WHERE id = " . $event->id;
			$db->setQuery($qry);
			$db->Query();
			
		}

		return $output;
	}

	function callBackgroundPage($key)
	{
		$js = "\ntry { jQuery('<div />').load('" . JRoute::_("index.php?option=com_fsj_main&view=cron&key=" . $key, false) . "');}  catch (e) {}\n";
		$document = JFactory::getDocument();
		$document->addScriptDeclaration($js); 
	}

	function runEvent($event, $force)
	{
		$filename = JPATH_ROOT.DS.$event->file;
		$class = $event->class;
		$function = $event->function;

		if (!file_exists($filename)) return "File missing - $filename<br>";

		include_once($filename);

		if (!class_exists($class)) return "Class missing - $class<br>";

		$object = new $class();

		if (!method_exists($object, $function)) return "Function missing - $function<br>";

		$result = "";

		ob_start();
		try {

			// if result is true for success, otherwise an error message
			$result = $object->$function($event, $force);

			if ($result !== true)
			{
				echo $result;
			}
		} catch (exception $e)
		{
			echo "<div class='alert alert-error'>";
			echo "<h4>" . $e->getMessage() . "</h4>";
			echo "<pre>";
			echo $e;
			echo "</pre>";
			echo "</div>";
		}
		$log = ob_get_contents();
		ob_end_clean();

		return array($result, $log);
	}

	static function getCron($cron_id)
	{
		$db = JFactory::getDBO();
		$qry = "SELECT * FROM #__fsj_main_cron WHERE id = '" . $db->escape($cron_id) . "'";
		$db->setQuery($qry);
		return $db->loadObject();
	}
}