<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;
$coupon = $this->coupon;
$error  = false;

if(empty($coupon))
{
	$error = true;
}
else
{
	$item = JModelLegacy::getInstance('OrderItem', 'CMGroupBuyingModel')->getItemById($coupon['item_id']);
	$order = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($coupon['order_id']);
	$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($coupon['deal_id']);

	if(empty($item) || empty($order) || empty($deal))
	{
		$error = true;
	}
}

if($error):
	echo JText::_('COM_CMGROUPBUYING_STAFF_COUPON_ERROR');
else:
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
	$couponElementsArray = json_decode($couponElementsJSON);

	if(empty($couponElementsArray))
	{
		echo JText::_('COM_CMGROUPBUYING_COUPON_INVALID_CONFIG');
		jexit();
	}

	if(isset($couponElementsArray->couponcode) && $couponElementsArray->couponcode->visible == 'true')
	{
		$displayCouponCode = true;
		$couponCode = $couponElementsArray->couponcode;
		$couponCodeString = $coupon['coupon_code'];
	}
	else
	{
		$displayCouponCode = false;
	}

	if(isset($couponElementsArray->qrcode) && $couponElementsArray->qrcode->visible == 'true')
	{
		$displayQRCode = true;
		$qrCode = $couponElementsArray->qrcode;
		$couponCodeString = $coupon['coupon_code'];
	}
	else
	{
		$displayQRCode = false;
	}

	if(isset($couponElementsArray->recipient) && $couponElementsArray->recipient->visible == 'true')
	{
		$recipient = $couponElementsArray->recipient;
		$buyerInfo = json_decode($order['buyer_info']);
		$friendInfo = json_decode($order['friend_info']);

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

	if(isset($couponElementsArray->option) && $couponElementsArray->option->visible == 'true')
	{
		$optionsOfDeal = JModelLegacy::getInstance('dealoption','cmgroupbuyingModel')->getOptions($deal['id']);
		$optionString = $optionsOfDeal[($item['option_id'])]['name'];
		$option = $couponElementsArray->option;
		$displayOption = true;
	}
	else
	{
		$displayOption = false;
	}

	if(isset($couponElementsArray->shortdesc) && $couponElementsArray->shortdesc->visible == 'true')
	{
		$shortDescString = $deal['short_description'];
		$shortDesc = $couponElementsArray->shortdesc;
		$displayShortDesc = true;
	}
	else
	{
		$displayShortDesc = false;
	}

	if(isset($couponElementsArray->highlights) && $couponElementsArray->highlights->visible == 'true')
	{
		$highlights = $couponElementsArray->highlights;
		$highlightsString = $deal['highlights'];
		$displayHighlights = true;
	}
	else
	{
		$displayHighlights = false;
	}

	if(isset($couponElementsArray->terms) && $couponElementsArray->terms->visible == 'true')
	{
		$termsString = $deal['terms'];
		$terms = $couponElementsArray->terms;
		$displayTerms = true;
	}
	else
	{
		$displayTerms = false;
	}

	if(isset($couponElementsArray->couponexp) && $couponElementsArray->couponexp->visible == 'true')
	{
		$couponExp = $couponElementsArray->couponexp;
		$couponExpString = $deal['coupon_expiration'];
		$displayCouponExp = true;
	}
	else
	{
		$displayCouponExp = false;
	}
?>
<style type="text/css">
div#coupon {
	position: relative;
	top: 0px;
	left: 0px;
	width: <?php echo $width; ?>px;
	height: <?php echo $height; ?>px;
}

body {
	background-color: #FFF !important;
}
</style>
	<div id="coupon" class="ui-widget-header">
		<?php if($displayBackground): ?>
		<img src="<?php echo JURI::root() . $couponBackground; ?>" />
		<?php endif; ?>

		<?php if($displayQRCode): ?>
		<div id="qr_code" class="ui-widget-content" style="width: <?php echo $qrCode->size; ?>px; height: <?php echo $qrCode->size; ?>px; left: <?php echo $qrCode->left; ?>px; top: <?php echo $qrCode->top; ?>px; position: absolute;">
			<img src="<?php echo JURI::root(); ?>index.php?option=com_cmgroupbuying&controller=coupon&task=generate&size=<?php echo $qrCode->size; ?>&code=<?php echo base64_encode($couponCodeString); ?>">
		</div>
		<?php endif; ?>

		<?php if($displayCouponCode): ?>
		<div class="ui-widget-content" id="coupon_code" style="font-size: <?php echo $couponCode->fontsize; ?>; text-align: <?php echo $couponCode->align; ?>; width: <?php echo $couponCode->width; ?>px; height: <?php echo $couponCode->height; ?>px; left: <?php echo $couponCode->left; ?>px; top: <?php echo $couponCode->top; ?>px; position: absolute;">
			<?php echo $couponCodeString; ?>
		</div>
		<?php endif; ?>

		<?php if($displayRecipient): ?>
		<div class="ui-widget-content" id="recipient" style="font-size: <?php echo $recipient->fontsize; ?>; text-align: <?php echo $recipient->align; ?>; width: <?php echo $recipient->width; ?>px; height: <?php echo $recipient->height; ?>px; left: <?php echo $recipient->left; ?>px; top: <?php echo $recipient->top; ?>px; position: absolute;">
			<?php echo $recipientString; ?>
		</div>
		<?php endif; ?>

		<?php if($displayOption): ?>
		<div class="ui-widget-content" id="option" style="font-size: <?php echo $option->fontsize; ?>; text-align: <?php echo $option->align; ?>; width: <?php echo $option->width; ?>px; height: <?php echo $option->height; ?>px; left: <?php echo $option->left; ?>px; top: <?php echo $option->top; ?>px; position: absolute;">
			<?php echo $optionString; ?>
		</div>
		<?php endif; ?>

		<?php if($displayShortDesc): ?>
		<div class="ui-widget-content" id="short_desc" style="font-size: <?php echo $shortDesc->fontsize; ?>; text-align: <?php echo $shortDesc->align; ?>; width: <?php echo $shortDesc->width; ?>px; height: <?php echo $shortDesc->height; ?>px; left: <?php echo $shortDesc->left; ?>px; top: <?php echo $shortDesc->top; ?>px; position: absolute;">
			<?php echo $shortDescString; ?>
		</div>
		<?php endif; ?>

		<?php if($displayHighlights): ?>
		<div class="ui-widget-content" id="highlights" style="font-size: <?php echo $highlights->fontsize; ?>; text-align: <?php echo $highlights->align; ?>; width: <?php echo $highlights->width; ?>px; height: <?php echo $highlights->height; ?>px; left: <?php echo $highlights->left; ?>px; top: <?php echo $highlights->top; ?>px; position: absolute;">
			<?php echo $highlightsString; ?>
		</div>
		<?php endif; ?>

		<?php if($displayTerms): ?>
		<div class="ui-widget-content" id="terms" style="font-size: <?php echo $terms->fontsize; ?>; text-align: <?php echo $terms->align; ?>; width: <?php echo $terms->width; ?>px; height: <?php echo $terms->height; ?>px; left: <?php echo $terms->left; ?>px; top: <?php echo $terms->top; ?>px; position: absolute;">
			<?php echo $termsString; ?>
		</div>
		<?php endif; ?>

		<?php if($displayCouponExp): ?>
		<div class="ui-widget-content" id="couponexp" style="font-size: <?php echo $couponExp->fontsize; ?>; text-align: <?php echo $couponExp->align; ?>; width: <?php echo $couponExp->width; ?>px; height: <?php echo $couponExp->height; ?>px; left: <?php echo $couponExp->left; ?>px; top: <?php echo $couponExp->top; ?>px; position: absolute;">
			<?php echo $couponExpString; ?>
		</div>
		<?php endif; ?>
	</div>
	<input type="button" id="print_button" value="<?php echo JText::_('COM_CMGROUPBUYING_COUPON_PRINT_BUTTON');?>" onClick="document.getElementById('print_button').style.display = 'none'; window.print(); window.document.getElementById('print_button').style.display = 'inline';">
<?php endif; ?>
