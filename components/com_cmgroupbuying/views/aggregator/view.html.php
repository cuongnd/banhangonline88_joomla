<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

require_once JPATH_COMPONENT.'/helpers/common.php';

header("Content-type: application/xml");

class CMGroupBuyingViewAggregator extends JViewLegacy
{
	function display($tpl = null)
	{
		$jinput = JFactory::getApplication()->input;
		$type = $jinput->get('type', null, 'string');
		$id = $jinput->get('id', null, 'string');
		$ref = $jinput->get('ref', null, 'string');

		if($ref == null)
		{
			jexit();
		}

		$aggregatorSite = CMGroupBuyingHelperAggregator::getAggregatorSiteByRef($ref);

		if(!isset($aggregatorSite['id']))
		{
			jexit();
		}

		if($type == "all")
		{
			$deals = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getAllActiveDeals();
		}
		elseif($type == "location")
		{
			if(is_numeric($id))
			{
				$location = JModelLegacy::getInstance('Location', 'CMGroupBuyingModel')->getLocationById($id);

				if(!isset($location['id']))
				{
					jexit();
				}

				$locationString = CMGroupBuyingHelperDeal::getDealsInLocation($location['id']);
				$deals = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getLimit(true, false, false, null, null, $locationString);
			}
		}
		elseif($type == "category")
		{
			if(is_numeric($id))
			{
				$category = JModelLegacy::getInstance('Category', 'CMGroupBuyingModel')->getCategoryById($id);

				if(!isset($category['id']))
				{
					jexit();
				}

				$deals = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getLimit(true, false, false, $category['id'], null, '');
			}
		}
		elseif($type == "partner")
		{
			if(is_numeric($id))
			{
				$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerById($id);

				if(!isset($partner['id']))
				{
					jexit();
				}

				$deals = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getLimit(true, false, false, null, $partner['id'], '');
			}
		}
		else
		{
			jexit();
		}
		
		echo $this->generateXML($aggregatorSite, $deals);
		jexit();
	}
	
	function generateXML($aggregatorSite, $deals)
	{
		$xml = $aggregatorSite['xml_tree_header'];

		if(!empty($deals))
		{
			foreach($deals as $deal)
			{
				$xml .= CMGroupBuyingHelperAggregator::generateDealXML($aggregatorSite['xml_tree_deals'], $deal, $aggregatorSite['ref']);
			}
		}
		
		$xml .= $aggregatorSite['xml_tree_footer'];

		return $xml;
	}

	function getDeals($deals, $ref = '')
	{
		$items = array();

		if(!empty($deals))
		{
			foreach($deals as $deal)
			{
				$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerById($deal['partner_id']);
				$merchant = array();

				if(isset($partner['id']))
				{
					for($i=1; $i<=5; $i++)
					{
						$locationElementsJSON = $partner['location' . $i];
						$locationElementsArray = json_decode($locationElementsJSON);
						$name = $locationElementsArray->name;
						$latitude = $locationElementsArray->latitude;
						$longitude = $locationElementsArray->longitude;

						if($name != "" && is_numeric($latitude) && is_numeric($longitude))
						{
							$locations[] =  $locationElementsArray;
						}
					}

					if(empty($locations))
					{
						$partnerAddress = '';
						$partnerPhone = '';
					}
					else
					{
						$partnerAddress = $locations[0]->address;
						$partnerPhone = $locations[0]->phone;
					}

					$merchant['merchantName'] = $partner['name'];
					$merchant['merchantWebsite'] = $partner['website'];
					$merchant['merchantAddress'] = $partnerAddress;
					$merchant['merchantTelephone'] = $partnerPhone;
					$merchant['merchantAbout'] = htmlspecialchars($partner['about'], ENT_QUOTES);
				}
				else
				{
					$merchant['merchantName'] = "";
					$merchant['merchantWebsite'] = "";
					$merchant['merchantAddress'] = "";
					$merchant['merchantTelephone'] = "";
					$merchant['merchantAbout'] = "";
				}

				$dealImages = array();

				for($j = 1; $j <= 5; $j++)
				{
					$columnName = 'image_path_' . $j;

					if($deal[$columnName] != '')
					{
						$dealImages[]   = JURI::root() . $deal[$columnName];
					}
				}

				$option = JModelLegacy::getInstance('dealoption','cmgroupbuyingModel')->getOption($deal['id'], 1);
				$item = array();
				$item['dealName'] = $deal['name'];
				$item['dealShortDescription'] = htmlspecialchars($deal['short_description'], ENT_QUOTES);
				$item['dealDescription'] = htmlspecialchars($deal['description'], ENT_QUOTES);
				$item['dealOriginalPrice'] = $option['original_price'];
				$item['dealPrice'] = $option['price'];
				$item['dealDiscount'] = 100 - round($option['price'] / $option['original_price'] * 100);
				$item['endAt'] = $deal['end_date'];
				$item['deallink'] = htmlspecialchars(JURI::root() . CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=deal&id=' . $deal['id'] . '&alias=' . $deal['alias'] . '&ref=' . $ref, false), ENT_QUOTES);
				$item['highlights'] = htmlspecialchars($deal['highlights'], ENT_QUOTES);
				$item['terms'] = htmlspecialchars($deal['terms'], ENT_QUOTES);
				$item['isTipped'] = $deal['tipped'];
				$item['merchant'] = $merchant;
				$items[] = $item;
			}
		}

		return $items;
	}
}