<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingHelperGeotargeting
{
	public static function getUserCoordinate($service = '')
	{
		$coordinate = array('latitude' => null, 'longitude' => null, 'city_name' => null);
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('maxmind_path, geotargeting_cookie_lifetime, ipinfodb_key, geotargeting_cookie_lifetime');

		if($service == 'maxmind')
		{
			include("geoipcity.inc");
			include("geoipregionvars.php");
			$gi = geoip_open($configuration['maxmind_path'], GEOIP_STANDARD);
			$ip = $_SERVER['REMOTE_ADDR'];
			$record = geoip_record_by_addr($gi,$ip);

			if(!empty($record))
			{
				$coordinate['latitude'] = $record->latitude;
				$coordinate['longitude'] = $record->longitude;
				$coordinate['city_name'] = $record->city;
				CMGroupBuyingHelperGeotargeting::createGeoCookie($coordinate, $configuration['geotargeting_cookie_lifetime']);
			}
		}
		elseif($service == 'ipinfodb')
		{
			include("ip2locationlite.class.php");
			$ipLite = new ip2location_lite;
			$ipLite->setKey($configuration['ipinfodb_key']);
			$locations = $ipLite->getCity($_SERVER['REMOTE_ADDR']);
			$coordinate['latitude'] = $locations['latitude'];
			$coordinate['longitude'] = $locations['longitude'];
			$coordinate['city_name'] = $locations['cityName'];
			CMGroupBuyingHelperGeotargeting::createGeoCookie($coordinate, $configuration['geotargeting_cookie_lifetime']);
		}

		return $coordinate;
	}

	public static function createGeoCookie($coordinate, $cookieLifetime)
	{
		$cookieLifetime = time() + $cookieLifetime * 60 * 60;
		$coordinate = $coordinate['latitude'] . "," . $coordinate['longitude']. "," . $coordinate['city_name'];
		setcookie("geotargeting", $coordinate, $cookieLifetime, '/');
	}

	public static function getGeoCookie()
	{
		$coordinate = null;
		$coordinateCookie = JFactory::getApplication()->input->cookie->get('geotargeting', '', 'string');

		if($coordinateCookie != '')
		{
			$coordinate = array();
			$tempArray  = explode(",", $coordinateCookie);
			$coordinate['latitude'] = $tempArray[0];
			$coordinate['longitude'] = $tempArray[1];
			$coordinate['city_name'] = $tempArray[2];
		}
		return $coordinate;
	}
}
?>