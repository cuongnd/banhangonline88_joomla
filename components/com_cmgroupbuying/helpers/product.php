<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingHelperProduct
{
	public static function countDealsOfProduct($productId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$now = CMGroupBuyingHelperDateTime::getCurrentDateTime();

		$query->select('id, partner_id')
			->from($db->quoteName('#__cmgroupbuying_deals'))
			->where($db->quoteName('product_id') . ' = ' . $db->quote($productId))
			->where($db->quoteName('published') . ' = ' . $db->quote('1'))
			->where($db->quoteName('approved') . ' = ' . $db->quote('1'))
			->where($db->quoteName('start_date') . ' <= ' . $db->quote($now))
			->where($db->quoteName('end_date') . ' >= ' . $db->quote($now));
		$db->setQuery($query);
		$deals = $db->loadAssocList();

		$dealCount = count($deals);

		if ($dealCount == 0)
		{
			$html = JText::_('COM_CMGROUPBUYING_PRODUCT_COUNT_0');
		}
		elseif ($dealCount >= 1)
		{
			$partnerNames= array();

			foreach ($deals as $deal)
			{
				$query->clear()
					->select('name')
					->from($db->quoteName('#__cmgroupbuying_partners'))
					->where($db->quoteName('id') . ' = ' . $db->quote($deal['partner_id']));
				$db->setQuery($query);
				$partnerName = $db->loadResult();

				if (!in_array($partnerName, $partnerNames))
				$partnerNames[] = $partnerName;
			}

			$htmlNames = implode(', ', $partnerNames);

			if ($dealCount == 1)
				$html = JText::sprintf('COM_CMGROUPBUYING_PRODUCT_COUNT_1', $htmlNames);
			else
				$html = JText::sprintf('COM_CMGROUPBUYING_PRODUCT_COUNT_N', $dealCount, $htmlNames);
		}

		return $html;
	}
}