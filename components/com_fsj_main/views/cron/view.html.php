<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('fsj_core.lib.utils.cron_handler');

class fsj_mainViewCron extends JViewLegacy
{
	function display($tpl = null)
	{
		$key = JRequest::getVar('key');
		$runnow = JRequest::getVar('runnow');
		if ($runnow)
		{
			$result = FSJ_Cron_Handler::testTrigger($runnow, $key);

			if ($result)
			{
				$this->success = $result[$runnow]['success'];
				$this->result = $result[$runnow]['result'];
				$this->log = $result[$runnow]['log'];
				$this->cron = FSJ_Cron_Handler::getCron($runnow);

				parent::display("modal");
				return;
			}
		}

		if ($key)
		{
			FSJ_Cron_Handler::backgroundUrlTrigger($key);
			exit;
		}

		// check for test event, and if needed run test

		// if nothing else run general url trigger
		FSJ_Cron_Handler::urlTrigger($key);
		exit;
	}
}
