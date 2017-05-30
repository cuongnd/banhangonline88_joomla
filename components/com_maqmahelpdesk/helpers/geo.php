<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class HelpdeskGeo
{
	static function GetCountry()
	{
		$database = JFactory::getDBO();
		$ipaddress = HelpdeskUser::GetIP();
		$ipaddress = ip2long($ipaddress);

		$sql = "SELECT `countryname`
				FROM `#__support_country`
				WHERE `startip`>='$ipaddress'
				  AND `endip`<='$ipaddress'";
		$database->setQuery($sql);

		return $database->loadResult();
	}
}
