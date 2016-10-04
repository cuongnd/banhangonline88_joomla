<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */


// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class CMGroupBuyingModelCouponLocation extends JModelLegacy
{
	function getLocations($couponId)
	{
		$db = JFactory::getDbo();
		$query = "SELECT location_id FROM #__cmgroupbuying_free_coupon_location WHERE coupon_id = $couponId";
		$db->setQuery($query);
		$locationArray = $db->loadColumn();

		if($db->getErrorNum())
		{
			JError::raiseError(500, $db->stderr());
			return false;
		}

		return $locationArray;
	}
}
?>