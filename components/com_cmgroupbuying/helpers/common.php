<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

require_once(JPATH_SITE . "/components/com_cmgroupbuying/helpers/datetime.php");
require_once(JPATH_SITE . "/components/com_cmgroupbuying/helpers/deal.php");
require_once(JPATH_SITE . "/components/com_cmgroupbuying/helpers/mail.php");
require_once(JPATH_SITE . "/components/com_cmgroupbuying/helpers/order.php");
require_once(JPATH_SITE . "/components/com_cmgroupbuying/helpers/coupon.php");
require_once(JPATH_SITE . "/components/com_cmgroupbuying/helpers/partner.php");
require_once(JPATH_SITE . "/components/com_cmgroupbuying/helpers/user.php");
require_once(JPATH_SITE . "/components/com_cmgroupbuying/helpers/xml.php");
require_once(JPATH_SITE . "/components/com_cmgroupbuying/helpers/aggregator.php");
require_once(JPATH_SITE . "/components/com_cmgroupbuying/helpers/alphauserpoints.php");
require_once(JPATH_SITE . "/components/com_cmgroupbuying/helpers/jomsocial.php");
require_once(JPATH_SITE . "/components/com_cmgroupbuying/helpers/geotargeting.php");
require_once(JPATH_SITE . "/components/com_cmgroupbuying/helpers/freecoupon.php");
require_once(JPATH_SITE . "/components/com_cmgroupbuying/helpers/plugin.php");
require_once(JPATH_SITE . "/components/com_cmgroupbuying/helpers/cart.php");
require_once(JPATH_SITE . "/components/com_cmgroupbuying/helpers/partnermanagement.php");
require_once(JPATH_SITE . "/components/com_cmgroupbuying/helpers/staffmanagement.php");

class CMGroupBuyingHelperCommon
{
	public static function prepareRedirect($url, $jroute = true)
	{
		$u = JURI::getInstance($url);
		$view = $u->getVar('view');
		$menu = JFactory::getApplication()->getMenu();

		if($view == "dealprevue" || $view == "deal")
		{
			$item = $menu->getItems('link', 'index.php?option=com_cmgroupbuying&view=todaydeal', true);
		}
		elseif($view == "freecouponprevue" || $view == "freecoupon")
		{
			$item = $menu->getItems('link', 'index.php?option=com_cmgroupbuying&view=freecoupon', true);
		}
		else
		{
			$item = $menu->getItems('link', 'index.php?option=com_cmgroupbuying&view=' . $view, true);
		}

		if(empty($item))
		{
			$item = $menu->getDefault();
		}

		$url = $url . '&Itemid=' . $item->id;

		if($jroute == true)
		{
			$url = JRoute::_($url, false);
		}

		return $url;
	}

	public static function prepareRedirectBackend($url, $link)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('id'))
			->from($db->quoteName('#__menu'))
			->where($db->quoteName('link') . ' = ' . $db->quote($link))
			->where($db->quoteName('published') . ' = ' . $db->quote('1'));
		$db->setQuery($query, 0, 1);

		$itemId = $db->loadResult();

		if(empty($itemId))
		{
			$itemId = 0;
		}

		$url = JUri::root() . $url . '&Itemid=' . $itemId;

		return $url;
	}

	public static function generateRandomString($length = 1)
	{
		$characters = '0123456789abcdefghijklmnopqrstuvwxyz';
		$string = '';

		for ($i = 0; $i < $length; $i++)
		{
			$string .= $characters[mt_rand(0, strlen($characters)-1)];
		}

		return $string;
	}

	public static function generateRandomLetter()
	{
		$letters = 'abcdefghijklmnopqrstuvwxyz';
		return $letters[mt_rand(0, strlen($letters)-1)];
	}

	public static function getLayout()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('layout'))
			->from($db->quoteName('#__cmgroupbuying_configuration'))
			->where($db->quoteName('id') . ' = ' . $db->quote('1'));
		$db->setQuery($query);

		$layout = $db->loadResult();
		return $layout;
	}

	public static function getFolders($folderName)
	{
		$scannedFolders = scandir(JPATH_SITE . '/components/com_cmgroupbuying/' . $folderName, 0);

		$folders = array();

		for($i = 0; $i < count($scannedFolders); $i++)
		{
			if($scannedFolders[$i] != "." && $scannedFolders[$i] != ".." && is_dir(JPATH_SITE . '/components/com_cmgroupbuying/'. $folderName . '/' . $scannedFolders[$i]))
			{
				$folders[] = $scannedFolders[$i];
			}
		}

		return $folders;
	}

	public static function getACYMailingModule($moduleId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__modules'))
			->where($db->quoteName('id') . ' = ' . $db->quote($moduleId));
		$db->setQuery($query);

		return $db->loadObject();
	}
}