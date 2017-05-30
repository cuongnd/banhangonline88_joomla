<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */

// no direct access
defined('_JEXEC') or die('Restricted access');
class TCron 
{
	static function execute() {
		$conf = TConf::getConfig();
		require_once(JPATH_BASE.'/administrator/components/com_adsmanager/models/cron.php');
		$cronmodel = new AdsmanagerModelCron();	
		
		$last = $cronmodel->getLastCronTime();
		$current = time();
		$cronmodel->saveCronTime($current);

		//Daily Action
		if (date("d",$current) != date("d",$last))
		{
			if ($conf->crontype != "onrequest")
				echo "Daily Action<br/>\n";
			require_once(JPATH_BASE.'/administrator/components/com_adsmanager/models/content.php');
			require_once(JPATH_BASE.'/administrator/components/com_adsmanager/models/field.php');
			$contentmodel = new AdsmanagerModelContent();
			$fieldmodel = new AdsmanagerModelField();
			$contentmodel->manage_expiration($fieldmodel->getPlugins(),$conf);
		}

		//Hourly Action
		if (date("H",$current) != date("H",$last))
		{
			if ($conf->crontype != "onrequest")
				echo "Hourly Action<br/>\n";
			if (function_exists("managePaidOption")) {
				managePaidOption();
			}
			/*$db = JFactory::getDbo();
		
			$query = "SELECT id FROM #__adsmanager_ads";
			$db->setQuery($query);
		
			$ads = $db->loadObjectList();
		
			foreach($ads as $ad) {
				$query = "UPDATE #__adsmanager_ads
						  SET ordering = ".rand(0, 1000)."
						  WHERE id = ".(int)$ad->id;
				$db->setQuery($query);
				$db->query();
			}*/
		}
	}
}