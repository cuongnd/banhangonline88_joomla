<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingHelperAlphauserpoints
{
	public static function checkInstalled()
	{
		$aupAPI = JPATH_SITE . '/components/com_alphauserpoints/helper.php';

		if(file_exists($aupAPI))
		{
			return true;
		}
		else
		{
			return false;
		}
	}

	public static function rewardReferral($referrerId)
	{
		if(CMGroupBuyingHelperAlphauserpoints::checkInstalled())
		{
			$aupAPI = JPATH_SITE . '/components/com_alphauserpoints/helper.php';
			require_once($aupAPI);
			AlphaUserPointsHelper::newpoints('plgaup_cmreward', $referrerId);
		}
	}

	public static function getUserNameByReferrerId($referrerId)
	{
		$username = '';

		if($referrerId != '' && CMGroupBuyingHelperAlphauserpoints::checkInstalled())
		{
			$aupAPI = JPATH_SITE . '/components/com_alphauserpoints/helper.php';
			require_once($aupAPI);
			$profile = AlphaUserPointsHelper::getUserInfo($referrerId);
			$username = $profile->name;
		}

		return $username;
	}

	public static function getUserPoints($userId)
	{
		$points = 0;

		if(CMGroupBuyingHelperAlphauserpoints::checkInstalled())
		{
			$aupAPI = JPATH_SITE . '/components/com_alphauserpoints/helper.php';
			require_once($aupAPI);
			$profile = AlphaUserPointsHelper::getUserInfo('', $userId); 
			$points = $profile->points;
		}

		return $points;
	}

	public static function getAUPId($userId)
	{
		$aupAPI = JPATH_SITE . '/components/com_alphauserpoints/helper.php';
		require_once($aupAPI);
		$aupId  = AlphaUserPointsHelper::getAnyUserReferreId($userId); 
		return $aupId;
	}

	public static function newPoints($aupId, $points)
	{
		$aupAPI = JPATH_SITE . '/components/com_alphauserpoints/helper.php';
		require_once($aupAPI);
		AlphaUserPointsHelper::newpoints('plgaup_cmpayment', $aupId, '', '', $points);
	}

	public static function newBonusPoints($aupId, $value)
	{
		if(CMGroupBuyingHelperAlphauserpoints::checkInstalled())
		{
			$aupAPI = JPATH_SITE . '/components/com_alphauserpoints/helper.php';
			require_once($aupAPI);

			$db = JFactory::getDBO();
			$query = $db->getQuery(true);
			$query->select('points, percentage');
			$query->from('#__alpha_userpoints_rules');
			$query->where('plugin_function = ' . $db->quote('plgaup_cmbonus'));
			$query->where('published = ' . $db->quote('1'));
			$db->setQuery($query);
			$rule = $db->loadObject();

			if(empty($rule))
				return;

			if($rule->percentage == 1 && $rule->points > 0)
			{
				$points = $value * $rule->points / 100;
				AlphaUserPointsHelper::newpoints('plgaup_cmbonus', $aupId, '', '', $points);
			}
			else
			{
				AlphaUserPointsHelper::newpoints('plgaup_cmbonus', $aupId);
			}
		}
	}
}
