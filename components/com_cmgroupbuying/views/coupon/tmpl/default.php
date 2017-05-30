<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

$app = JFactory::getApplication();

if($this->action == 'show_token_error'):
	echo JText::_('COM_CMGROUPBUYING_COUPON_INVALID_TOKEN');
elseif($this->action == 'show_coupon_error'):
	echo JText::_('COM_CMGROUPBUYING_COUPON_NO_COUPON');
elseif($this->action == 'list'):
?>
	<div class="page_title">
		<p><?php echo $this->pageTitle; ?></p>
	</div>
	<ul>
		<?php foreach($this->coupons as $coupon): ?>
			<li><a href="<?php echo CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=coupon&download=' . $coupon['coupon_code']); ?>"><?php echo $coupon['coupon_code']; ?></a></li>
		<?php endforeach; ?>
	</ul>
<?php
elseif($this->action == 'download'):
	$configuration	= $this->configuration;
	$deal			= $this->deal;
	$coupon			= $this->coupon;
	$order			= $this->order;
	$item			= $this->item;
	$optionsOfDeal	= JModelLegacy::getInstance('dealoption','cmgroupbuyingModel')->getOptions($deal['id']);
	$dealOption		= $optionsOfDeal[$item['option_id']];

	$couponBackground = $deal['coupon_path'];

	if(file_exists(JPATH_ROOT . "/" . $couponBackground))
		$displayBackground = true;
	else
		$displayBackground = false;

	if($displayBackground)
	{
		list($width, $height, $type, $attr) = getimagesize(JPATH_ROOT . "/" . $couponBackground);
	}
	else
	{
		$width  = 600;
		$height = 600;
	}

	$couponElementsJSON = $deal['coupon_elements'];
	$couponElementsObj = json_decode($couponElementsJSON);

	if(empty($couponElementsObj))
	{
		echo JText::_('COM_CMGROUPBUYING_COUPON_INVALID_CONFIG');
		$app->close();
	}

	if(isset($couponElementsObj->couponcode) && $couponElementsObj->couponcode->visible == 'true')
	{
		$displayCouponCode	= true;
		$couponCode			= $couponElementsObj->couponcode;
		$couponCodeString	= $coupon['coupon_code'];
	}
	else
	{
		$displayCouponCode = false;
	}

	if(isset($couponElementsObj->qrcode) && $couponElementsObj->qrcode->visible == 'true')
	{
		$displayQRCode		= true;
		$qrCode				= $couponElementsObj->qrcode;
		$couponCodeString	= $coupon['coupon_code'];
	}
	else
	{
		$displayQRCode = false;
	}

	if(isset($couponElementsObj->recipient) && $couponElementsObj->recipient->visible == 'true')
	{
		$recipient		= $couponElementsObj->recipient;
		$buyerInfo		= json_decode($order['buyer_info']);
		$friendInfo		= json_decode($order['friend_info']);

		if($friendInfo->email != '' && $friendInfo->full_name != '')
		{
			$recipientString = $friendInfo->full_name;
		}
		elseif($buyerInfo->name != '')
		{
			$recipientString = $buyerInfo->name;
		}
		else
		{
			$recipientString = $buyerInfo->first_name . ' ' . $buyerInfo->last_name;
		}

		$displayRecipient = true;
	}
	else
	{
		$displayRecipient = false;
	}

	if(isset($couponElementsObj->option) && $couponElementsObj->option->visible == 'true')
	{
		$optionString	= $dealOption['name'];
		$option			= $couponElementsObj->option;
		$displayOption	= true;
	}
	else
	{
		$displayOption = false;
	}

	if(isset($couponElementsObj->shortdesc) && $couponElementsObj->shortdesc->visible == 'true')
	{
		$shortDescString	= $deal['short_description'];
		$shortDesc			= $couponElementsObj->shortdesc;
		$displayShortDesc	= true;
	}
	else
	{
		$displayShortDesc = false;
	}

	if(isset($couponElementsObj->highlights) && $couponElementsObj->highlights->visible == 'true')
	{
		$highlights			= $couponElementsObj->highlights;
		$highlightsString	= $deal['highlights'];
		$displayHighlights	= true;
	}
	else
	{
		$displayHighlights = false;
	}

	if(isset($couponElementsObj->terms) && $couponElementsObj->terms->visible == 'true')
	{
		$termsString	= $deal['terms'];
		$terms			= $couponElementsObj->terms;
		$displayTerms	= true;
	}
	else
	{
		$displayTerms = false;
	}

	if(isset($couponElementsObj->couponexp) && $couponElementsObj->couponexp->visible == 'true')
	{
		$couponExp			= $couponElementsObj->couponexp;
		$couponExpString	= $deal['coupon_expiration'];
		$displayCouponExp	= true;
	}
	else
	{
		$displayCouponExp = false;
	}

	if(isset($couponElementsObj->price) && $couponElementsObj->price->visible == 'true')
	{
		$price			= $couponElementsObj->price;
		$priceString	= CMGroupBuyingHelperDeal::displayDealPrice($dealOption['price'], true, $configuration);
		$displayPrice	= true;
	}
	else
	{
		$displayPrice = false;
	}

	if(isset($couponElementsObj->originalprice) && $couponElementsObj->originalprice->visible == 'true')
	{
		$originalPrice			= $couponElementsObj->originalprice;
		$originalPriceString	= CMGroupBuyingHelperDeal::displayDealPrice($dealOption['original_price'], true, $configuration);
		$displayOriginalPrice	= true;
	}
	else
	{
		$displayOriginalPrice = false;
	}

	if(isset($couponElementsObj->advanceprice) && $couponElementsObj->advanceprice->visible == 'true')
	{
		$advancePrice			= $couponElementsObj->advanceprice;
		$advancePriceString		= CMGroupBuyingHelperDeal::displayDealPrice($dealOption['advance_price'], true, $configuration);
		$displayAdvancePrice	= true;
	}
	else
	{
		$displayAdvancePrice = false;
	}

	if(isset($couponElementsObj->remainprice) && $couponElementsObj->remainprice->visible == 'true')
	{
		$remainPrice			= $couponElementsObj->remainprice;
		$remainingAmount		= $dealOption['price'] - $dealOption['advance_price'];
		$remainPriceString		= CMGroupBuyingHelperDeal::displayDealPrice($remainingAmount, true, $configuration);
		$displayRemainPrice	= true;
	}
	else
	{
		$displayRemainPrice = false;
	}

	if(strtolower($this->format) == 'html')
	{
		include 'default_html.php';
	}
	else
	{
		include 'default_pdf.php';
	}

	$app->close();
endif;
?>
