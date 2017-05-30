<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

class CMGroupBuyingHelperAggregator
{
	public static function generateDealXML($xmlTree, $deal, $ref)
	{
		$option = JModelLegacy::getInstance('dealoption','cmgroupbuyingModel')->getOption($deal['id'], 1);
		$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerById($deal['partner_id']);
		$category = JModelLegacy::getInstance('Category', 'CMGroupBuyingModel')->getCategoryById($deal['category_id']);
		$locationsOfDeal = implode(",", CMGroupBuyingHelperDeal::getLocationsOfDeal($deal['id']));
		$dealImages = array();

		for($i = 1; $i <= 5; $i++)
		{
			$columnName = 'image_path_' . $i;
			
			if($deal[$columnName] != '')
			{
				$dealImages[] = JURI::root() . $deal[$columnName];
			}
		}

		if(count($dealImages) == 0)
		{
			$dealImages[0] = '';
		}

		$merchant = array();

		if(isset($partner['id']))
		{
			$locations = array();

			for($i=1; $i<=5; $i++)
			{
				$locationElementsJSON = $partner['location' . $i];
				$locationElementsArray = json_decode($locationElementsJSON);

				$name = isset($locationElementsArray->name) ? $locationElementsArray->name : '';
				$latitude = isset($locationElementsArray->latitude) ? $locationElementsArray->latitude : '';
				$longitude = isset($locationElementsArray->longitude) ? $locationElementsArray->longitude : '';

				if($name != "" && is_numeric($latitude) && is_numeric($longitude))
				{
					$locations[] =  $locationElementsArray;
				}
			}

			if(empty($locations))
			{
				$partnerAddress = '';
				$partnerPhone = '';
				$partnerLatitude = '';
				$partnerLongitude = '';
			}
			else
			{
				$partnerAddress = $locations[0]->address;
				$partnerPhone = $locations[0]->phone;
				$partnerLatitude = $locations[0]->latitude;
				$partnerLongitude = $locations[0]->longitude;
			}

			$merchant['merchantName'] = $partner['name'];
			$merchant['merchantWebsite'] = $partner['website'];
			$merchant['merchantAddress'] = $partnerAddress;
			$merchant['merchantTelephone'] = $partnerPhone;
			$merchant['merchantAbout'] = $partner['about'];
			$merchant['merchantLatitude'] = $partnerLatitude;
			$merchant['merchantLongitude'] = $partnerLongitude;
		}
		else
		{
			$merchant['merchantName'] = "";
			$merchant['merchantWebsite'] = "";
			$merchant['merchantAddress'] = "";
			$merchant['merchantTelephone'] = "";
			$merchant['merchantAbout'] = "";
			$merchant['merchantLatitude'] = "";
			$merchant['merchantLongitude'] = "";
		}

		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('currency_decimals, currency_dec_point, currency_thousands_sep,
				currency_postfix, currency_prefix');

		$variableArray = array();
		$variableArray['deal_id'] = $deal['id'];
		$variableArray['deal_name'] = htmlspecialchars($deal['name'], ENT_QUOTES);
		$variableArray['deal_link'] = htmlspecialchars(JURI::root() . CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=deal&id=' . $deal['id'] . '&alias=' . $deal['alias'] . '&ref=' . $ref, false), ENT_QUOTES);
		$variableArray['deal_original_price'] = CMGroupBuyingHelperDeal::displayDealPrice($option['original_price'], false, $configuration);
		$variableArray['deal_price'] = CMGroupBuyingHelperDeal::displayDealPrice($option['price'], false, $configuration);
		$variableArray['deal_discount'] = 100 - round($option['price'] / $option['original_price'] * 100);
		$variableArray['deal_short_description'] = htmlspecialchars($deal['short_description'], ENT_QUOTES);
		$variableArray['deal_description'] = htmlspecialchars($deal['description'], ENT_QUOTES);
		$variableArray['deal_start_date'] = $deal['start_date'];
		$variableArray['deal_end_date'] = $deal['end_date'];
		$variableArray['deal_highlights'] = htmlspecialchars($deal['highlights'], ENT_QUOTES);
		$variableArray['deal_terms'] = htmlspecialchars($deal['terms'], ENT_QUOTES);
		$variableArray['deal_image'] = $dealImages[0];
		$variableArray['deal_bought'] = CMGroupBuyingHelperDeal::countPaidCoupon($deal['id']);
		$variableArray['deal_tipped'] = $deal['tipped'];
		$variableArray['deal_category'] = htmlspecialchars($category['name'], ENT_QUOTES);
		$variableArray['deal_location'] = $locationsOfDeal;
		$variableArray['partner_name'] = htmlspecialchars($merchant['merchantName'], ENT_QUOTES);
		$variableArray['partner_website'] = $merchant['merchantWebsite'];
		$variableArray['partner_address'] = htmlspecialchars($merchant['merchantAddress'], ENT_QUOTES);
		$variableArray['partner_telephone'] = $merchant['merchantTelephone'];
		$variableArray['partner_map_latitude'] = $merchant['merchantLatitude'];
		$variableArray['partner_map_longitude'] = $merchant['merchantLongitude'];
		$variableArray['partner_about'] = htmlspecialchars($merchant['merchantAbout'], ENT_QUOTES);
		$variableArray['category_name'] = htmlspecialchars($category['name'], ENT_QUOTES);

		foreach($variableArray as $key=>$value)
		{
			$xmlTree = str_replace("{" . $key . "}", $value, $xmlTree);
		}

		return $xmlTree;
	}

	public static function getAggregatorSiteByRef($ref)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_aggregator_sites'))
			->where($db->quoteName('ref') . ' = ' . $db->quote($ref));

		$db->setQuery($query);
		$site = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $site;
	}
}
?>