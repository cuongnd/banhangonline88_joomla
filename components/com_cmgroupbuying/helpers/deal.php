<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingHelperDeal
{
	public static function getDealsByRefId($ref)
	{
		$deals = array();

		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_aggregator_counter'))
			->where($db->quoteName('ref_id') . ' = ' . $db->quote($ref));

		$db->setQuery($query);
		$counterArray = $db->loadAssocList();

		if(count($counterArray) == 0)
		{
			return $deals;
		}

		foreach($counterArray as $counter)
		{
			$deal = JModelLegacy::getInstance('Deal','CMGroupBuyingModel')->getDealById($counter['deal_id']);
			
			if(isset($deal['id']))
			{
				$temp = array();
				$temp['name'] = $deal['name'];
				$temp['view'] = $counter['view'];
				$deals[] = $temp;
			}
		}

		return $deals;
	}

	public static function displayDealPrice($price, $displaySigns = true, $configuration = null)
	{
		if($configuration == null)
			$configuration  = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
				->getConfiguration('currency_decimals, currency_dec_point, currency_thousands_sep, currency_postfix, currency_prefix');

		$price = floatval($price);
		$decimals = $configuration['currency_decimals'];
		$dec_point = $configuration['currency_dec_point'];
		$thousands_sep = $configuration['currency_thousands_sep'];
		$dealPrice = number_format($price, $decimals, $dec_point, $thousands_sep);

		if($displaySigns)
			if(JFactory::getLanguage()->isRTL()):
				return $configuration['currency_postfix'] . $dealPrice . $configuration['currency_prefix'];
			else:
				return $configuration['currency_prefix'] . $dealPrice . $configuration['currency_postfix'];
			endif;
		else
			return $dealPrice;
	}

	public static function convertDealPrice($price)
	{
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('currency_decimals, currency_dec_point, currency_thousands_sep');
		$price = floatval($price);
		$decimals = $configuration['currency_decimals'];
		$dec_point = $configuration['currency_dec_point'];
		$thousands_sep = $configuration['currency_thousands_sep'];
		$dealPrice = number_format($price, $decimals, $dec_point, $thousands_sep);
		return $dealPrice;
	}

	public static function countPaidCoupon($dealId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('COUNT(*)')
			->from($db->quoteName('#__cmgroupbuying_coupons'))
			->where($db->quoteName('deal_id') . ' = ' . $db->quote($dealId))
			->where($db->quoteName('coupon_status') . ' <> ' . $db->quote('0'));

		$db->setQuery($query);
		$count = $db->loadResult();
		return $count;
	}

	public static function countPaidOption($dealId, $optionId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('COUNT(*)')
			->from($db->quoteName('#__cmgroupbuying_coupons'))
			->where($db->quoteName('deal_id') . ' = ' . $db->quote($dealId))
			->where($db->quoteName('option_id') . ' = ' . $db->quote($optionId))
			->where($db->quoteName('coupon_status') . ' <> ' . $db->quote('0'));

		$db->setQuery($query);
		$count = $db->loadResult();
		return $count;
	}

	public static function countOrderedCoupon($dealId)
	{
		$db = JFactory::getDbo();
		$query .= $db->quoteName('#__cmgroupbuying_coupons');
		$query .= ' WHERE (deal_id = ' . $db->quote($dealId);
		$query .= ' AND coupon_status = 0';
		$query .= ' AND expired_date > ' . $db->quote(CMGroupBuyingHelperDateTime::getCurrentDateTime());
		$query .= ') OR (deal_id = ' . $db->quote($dealId);
		$query .= ' AND coupon_status = 1)';
		$db->setQuery($query);
		$count = $db->loadResult();
		return $count;
	}

	public static function countPaidCouponForReport($dealId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('COUNT(*)')
			->from($db->quoteName('#__cmgroupbuying_coupons'))
			->where($db->quoteName('deal_id') . ' = ' . $db->quote($dealId))
			->where($db->quoteName('coupon_status') . ' <> ' . $db->quote('0'));

		$db->setQuery($query);
		$count = $db->loadResult();
		return $count;
	}

	public static function countPaidItemForReport($dealId)
	{
		$db = JFactory::getDBO();
		$query = 'SELECT COUNT(*) FROM #__cmgroupbuying_order_items WHERE order_id IN (SELECT id FROM #__cmgroupbuying_orders WHERE deal_id = ' . $db->quote($dealId) . ' AND status = 1 OR status = 3)';
		$db->setQuery($query);
		$count = $db->loadResult();
		return $count; 
	}

	public static function getDealByItemId($itemId)
	{
		$db = JFactory::getDBO();
		$query = 'SELECT * FROM #__cmgroupbuying_deals WHERE id = (SELECT deal_id FROM #__cmgroupbuying_order_items WHERE id = ' . $db->quote($itemId) . ')';
		$db->setQuery($query);
		$deal = $db->loadAssoc();
		return $deal;
	}

	public static function getDealsByOrderId($orderId)
	{
		$db = JFactory::getDBO();
		$query = 'SELECT * FROM #__cmgroupbuying_deals WHERE id IN (SELECT deal_id FROM #__cmgroupbuying_order_items WHERE order_id = ' . $db->quote($orderId) . ')';
		$db->setQuery($query);
		$deals = $db->loadAssocList();
		return $deals;
	}

	public static function checkDealForTipping($deal)
	{
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);
		// Set tipped for unvoided, untipped, published deal only
		if($deal['voided'] == 0 && $deal['tipped'] == 0 && $deal['published'] == 1)
		{
			// Count paid orders of the deal
			$query->select('COUNT(*)')
				->from($db->quoteName('#__cmgroupbuying_coupons'))
				->where($db->quoteName('coupon_status') . ' = ' . $db->quote('1'))
				->where($db->quoteName('deal_id') . ' = ' . $db->quote($deal['id']));

			$db->setQuery($query);
			$count = $db->loadResult();

			// If number of paid orders is equal to or greater than minimum bought to tip
			if($deal['min_bought'] <= $count)
			{
				$today = CMGroupBuyingHelperDateTime::getCurrentDateTime();

				// Set the deal tipped
				$query->clear()
					->update($db->quoteName('#__cmgroupbuying_deals'))
					->set($db->quoteName('tipped')		. ' = ' . $db->quote('1'))
					->set($db->quoteName('tipped_date')	. ' = ' . $db->quote($today))
					->where($db->quoteName('id')		. ' = ' . $db->quote($deal['id']));
				$db->setQuery($query);

				// Update the deal successfully, now send mails to buyers
				if($db->execute())
				{
					CMGroupBuyingHelperMail::sendMailForTippedDeal($deal['id']);
				}
			}
		}
	}

	public static function getCouponsOfUserForDeal($userId, $dealId)
	{
		$db = JFactory::getDBO();
		$query = 'SELECT COUNT(*) FROM #__cmgroupbuying_coupons WHERE ';
		$query .= '(user_id = ' . $db->quote($userId) . ' AND deal_id = ' . $db->quote($dealId) . ' AND coupon_status = 0 AND expired_date > ' . $db->quote(CMGroupBuyingHelperDateTime::getCurrentDateTime()) . ') OR ';
		$query .= '(user_id = ' . $db->quote($userId) . ' AND deal_id = ' . $db->quote($dealId) . ' AND coupon_status = 1)';
		$db->setQuery($query);
		$count = $db->loadResult();
		return $count;
	}

	public static function getLocationsOfDeal($dealId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_deal_location'))
			->where($db->quoteName('deal_id') . ' = ' . $db->quote($dealId));

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

	public static function getDealsInLocation($locationId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select($db->quoteName('deal_id'))
			->from($db->quoteName('#__cmgroupbuying_deal_location'))
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

	public static function generateDealStatus($dealId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('start_date, end_date, voided, approved, published')
			->from($db->quoteName('#__cmgroupbuying_deals'))
			->where($db->quoteName('id') . ' = ' . $db->quote($dealId));

		$db->setQuery($query);
		$deal = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		if(empty($deal))
		{
			return '';
		}
		else
		{
			if($deal['approved'] == 0)
			{
				return JText::_('COM_CMGROUPBUYING_DEAL_STATUS_PENDING');
			}
			else
			{
				if($deal['voided'] == 1)
				{
					return JText::_('COM_CMGROUPBUYING_DEAL_STATUS_VOIDED');
				}
				else
				{
					if($deal['published'] == 0)
					{
						return JText::_('COM_CMGROUPBUYING_DEAL_STATUS_UNPUBLISHED');
					}
					else
					{
						$currentDateTime = CMGroupBuyingHelperDateTime::getCurrentDateTime();

						if($deal['start_date'] > $currentDateTime)
						{
							return JText::_('COM_CMGROUPBUYING_DEAL_STATUS_NOT_ON_SALE');
						}
						elseif($deal['start_date'] < $currentDateTime
								&& $deal['end_date'] > $currentDateTime)
						{
							return JText::_('COM_CMGROUPBUYING_DEAL_STATUS_ON_SALE');
						}
						elseif($deal['end_date'] < $currentDateTime)
						{
							return JText::_('COM_CMGROUPBUYING_DEAL_STATUS_ENDED');
						}
					}
				}
			}
		}
	}
}