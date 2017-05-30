<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

require_once JPATH_COMPONENT.'/helpers/common.php';

class CMGroupBuyingControllerDealmap extends JControllerLegacy
{
	public function generateXML()
	{
		header("Content-type: text/xml");
		$html = "<?xml version='1.0'?>";
		$html .= "<markers>";
		$deals = JModelLegacy::getInstance("Deal", "CMGroupBuyingModel")->getAllActiveDeals();

		if(!empty($deals))
		{
			foreach($deals as $deal)
			{
				$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerById($deal['partner_id']);

				$category = JModelLegacy::getInstance("Category", "CMGroupBuyingModel")->getCategoryById($deal['category_id']);
				$dealImages = array();
				$optionsOfDeal = JModelLegacy::getInstance('dealoption','cmgroupbuyingModel')->getOptions($deal['id']);
				$dealPrice = CMGroupBuyingHelperDeal::displayDealPrice($optionsOfDeal[1]['price']);
				$originalPrice = CMGroupBuyingHelperDeal::displayDealPrice($optionsOfDeal[1]['original_price']);
				$savedPrice = CMGroupBuyingHelperDeal::displayDealPrice($optionsOfDeal[1]['original_price'] - $optionsOfDeal[1]['price']);
				$paidCoupons = CMGroupBuyingHelperDeal::countPaidCoupon($deal['id']);
				$dealLink = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=deal&id=' . $deal['id'] . '&alias=' . $deal['alias']);

				for($i = 1; $i <= 5; $i++)
				{
					$columnName = 'image_path_' . $i;

					if($deal[$columnName] != '')
					{
						$dealImages[]   = JURI::root() . $deal[$columnName];
					}
				}

				if(!empty($dealImages))
				{
					$image = $dealImages[0];
				}
				else
				{
					$image = '';
				}

				if($deal['map_latitude'] == 0 && $deal['map_longitude'] == 0)
				{
					for($i=1; $i<=5; $i++)
					{
						$locationElementsJSON = $partner['location' . $i];
						$locationElementsArray = json_decode($locationElementsJSON);

						if(!empty($locationElementsArray))
						{
							$name = $locationElementsArray->name;
							$latitude = $locationElementsArray->latitude;
							$longitude = $locationElementsArray->longitude;

							if($name != "" && is_numeric($latitude) && is_numeric($longitude))
							{
								$deal['map_latitude'] = $latitude;
								$deal['map_longitude'] = $longitude;
								break;
							}
						}
					}
				}

				$html .= '<marker ';
				$html .= 'name="' . $this->parseToXML($deal['name']) . '" ';
				$html .= 'image="' . $this->parseToXML($image) . '" ';
				$html .= 'description="' . $this->parseToXML($deal['short_description']) . '" ';
				$html .= 'original_price="' . $this->parseToXML($originalPrice) . '" ';
				$html .= 'price="' . $this->parseToXML($dealPrice) . '" ';
				$html .= 'save="' . $this->parseToXML($savedPrice) . '" ';
				$html .= 'bought="' . $this->parseToXML($paidCoupons) . '" ';
				$html .= 'latitude="' . $this->parseToXML($deal['map_latitude']) . '" ';
				$html .= 'longitude="' . $this->parseToXML($deal['map_longitude']) . '" ';
				$html .= 'category="' . $this->parseToXML($category['alias']) . '" ';
				$html .= 'link="' . $this->parseToXML($dealLink) . '" ';
				$html .= '/>';
			}
		}

		$html .= "</markers>";
		echo $html;
		jexit();
	}

	public function parseToXML($html) 
	{ 
		$html = str_replace('<', '&lt;', $html); 
		$html = str_replace('>', '&gt;', $html); 
		$html = str_replace('"', '&quot;', $html); 
		$html = str_replace("'", '&apos;', $html); 
		$html = str_replace("&", '&amp;', $html); 
		return $html; 
	} 
}
?>
