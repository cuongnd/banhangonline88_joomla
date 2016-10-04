<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_COMPONENT.'/helpers/common.php';

jimport('joomla.application.component.controller');

class CMGroupBuyingControllerCategoryBrowsing extends JControllerLegacy
{
	function getDeals()
	{
		$categoryId = JFactory::getApplication()->input->post->get('categoryId', null);

		if($categoryId != null)
		{
			$category = JModelLegacy::getInstance('Category', 'CMGroupBuyingModel')->getCategoryById($categoryId);

			if(empty($category))
			{
				jexit();
			}
			else
			{
				echo '<h2 class="category_title">' . JText::sprintf('COM_CMGROUPBUYING_CATEGORY_BROWSING_DEAL_IN_CATEGORY_TITLE', $category['name']) . '</h2>';
				$deals = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getLimit(true, false, false, $categoryId, null, null, true);
				if(empty($deals))
				{
					echo '<div class="deal">' . JTEXT::_('COM_CMGROUPBUYING_CATEGORY_BROWSING_NO_DEAL_IN_CATEGORY') . '</div>';
				}
				else
				{
					foreach($deals as $deal)
					{
						$optionsOfDeal = JModelLegacy::getInstance('dealoption','cmgroupbuyingModel')->getOptions($deal['id']);
						$locations = CMGroupBuyingHelperDeal::getLocationsOfDeal($deal['id']);
						$locations = implode(", ", $locations);
						$dealPrice = CMGroupBuyingHelperDeal::displayDealPrice($optionsOfDeal[1]['price']);
						$originalPrice = CMGroupBuyingHelperDeal::displayDealPrice($optionsOfDeal[1]['original_price']);
						$discount = 100 - round($optionsOfDeal[1]['price'] / $optionsOfDeal[1]['original_price'] * 100);
						$discount = JText::sprintf('COM_CMGROUPBUYING_CATEGORY_BROWSING_DISCOUNT_OFF', $discount);
						echo '<div class="deal">';
						echo '<div class="deal_name" onclick="markerTrigger(\'' . $deal['map_latitude'] . ',' . $deal['map_longitude'] . '\')">' . $deal['name'] . '</div>';
						echo '<div class="deal_location">' . $locations . '</div>';
						echo '<div class="deal_price">' . $dealPrice . '</div>';
						echo '<div class="deal_original_price">' . $originalPrice . '</div>';
						echo '<div class="deal_discount">' . $discount . '</div>';
						echo '<div class="clear:both"></div>';
						echo '</div>';
					}
				}
			}
		}
		jexit();
	}
}