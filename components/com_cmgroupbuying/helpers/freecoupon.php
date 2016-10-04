<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingHelperFreeCoupon
{
	public static function getLocationsOfFreeCoupon($couponId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_free_coupon_location'))
			->where($db->quoteName('coupon_id') . ' = ' . $db->quote($couponId));

		$db->setQuery($query);
		$rows = $db->loadAssocList();

		if(count($rows) > 0)
		{
			$locations = array();

			foreach($rows as $row)
			{
				$location = JModelLegacy::getInstance("Location", "CMGroupBuyingModel")->getLocationById($row['location_id']);
				$locations[] = $location['name'];
			}

			return $locations;
		}
		else
		{
			return '';
		}
	}

	public static function getFreeCouponsInLocation($locationId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('coupon_id'))
			->from($db->quoteName('#__cmgroupbuying_free_coupon_location'))
			->where($db->quoteName('location_id') . ' = ' . $db->quote($locationId));

		$db->setQuery($query);
		$rows = $db->loadColumn();

		$result = '-1';

		if(count($rows) > 0)
		{
			$result = implode(',', $rows);
		}

		return $result;
	}

	public static function generateFreeCouponStatus($couponId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('start_date, end_date, approved, published')
			->from($db->quoteName('#__cmgroupbuying_free_coupons'))
			->where($db->quoteName('id') . ' = ' . $db->quote($couponId));

		$db->setQuery($query);
		$coupon = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		if(empty($coupon))
		{
			return '';
		}
		else
		{
			if($coupon['approved'] == 0)
			{
				return JText::_('COM_CMGROUPBUYING_FREE_COUPON_STATUS_PENDING');
			}
			else
			{
				if($coupon['published'] == 0)
				{
					return JText::_('COM_CMGROUPBUYING_FREE_COUPON_STATUS_UNPUBLISHED');
				}
				else
				{
					if($coupon['start_date'] > CMGroupBuyingHelperDateTime::getCurrentDateTime())
					{
						return JText::_('COM_CMGROUPBUYING_FREE_COUPON_STATUS_UPCOMING');
					}
					elseif($coupon['start_date'] < CMGroupBuyingHelperDateTime::getCurrentDateTime()
							&& $coupon['end_date'] > CMGroupBuyingHelperDateTime::getCurrentDateTime())
					{
						return JText::_('COM_CMGROUPBUYING_FREE_COUPON_STATUS_AVAILABLE');
					}
					elseif($coupon['end_date'] < CMGroupBuyingHelperDateTime::getCurrentDateTime())
					{
						return JText::_('COM_CMGROUPBUYING_FREE_COUPON_STATUS_EXPIRED');
					}
				}
			}
		}
	}
}