<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;
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
		<img src="<?php echo JURI::root(); ?>index.php?option=com_cmgroupbuying&controller=coupon&task=generate&size=<?php echo $qrCode->size; ?>&code=<?php echo base64_encode($couponCodeString); ?>&<?php echo JSession::getFormToken(); ?>=1">
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

	<?php if($displayPrice): ?>
	<div class="ui-widget-content" id="price" style="font-size: <?php echo $price->fontsize; ?>; text-align: <?php echo $price->align; ?>; width: <?php echo $price->width; ?>px; height: <?php echo $price->height; ?>px; left: <?php echo $price->left; ?>px; top: <?php echo $price->top; ?>px; position: absolute;">
		<?php echo $priceString; ?>
	</div>
	<?php endif; ?>

	<?php if($displayOriginalPrice): ?>
	<div class="ui-widget-content" id="price" style="font-size: <?php echo $originalPrice->fontsize; ?>; text-align: <?php echo $originalPrice->align; ?>; width: <?php echo $originalPrice->width; ?>px; height: <?php echo $originalPrice->height; ?>px; left: <?php echo $originalPrice->left; ?>px; top: <?php echo $originalPrice->top; ?>px; position: absolute;">
		<?php echo $originalPriceString; ?>
	</div>
	<?php endif; ?>

	<?php if($displayAdvancePrice): ?>
	<div class="ui-widget-content" id="price" style="font-size: <?php echo $advancePrice->fontsize; ?>; text-align: <?php echo $advancePrice->align; ?>; width: <?php echo $advancePrice->width; ?>px; height: <?php echo $advancePrice->height; ?>px; left: <?php echo $advancePrice->left; ?>px; top: <?php echo $advancePrice->top; ?>px; position: absolute;">
		<?php echo $advancePriceString; ?>
	</div>
	<?php endif; ?>

	<?php if($displayRemainPrice): ?>
	<div class="ui-widget-content" id="price" style="font-size: <?php echo $remainPrice->fontsize; ?>; text-align: <?php echo $remainPrice->align; ?>; width: <?php echo $remainPrice->width; ?>px; height: <?php echo $remainPrice->height; ?>px; left: <?php echo $remainPrice->left; ?>px; top: <?php echo $remainPrice->top; ?>px; position: absolute;">
		<?php echo $remainPriceString; ?>
	</div>
	<?php endif; ?>
</div>
<input type="button" id="print_button" value="<?php echo JText::_('COM_CMGROUPBUYING_COUPON_PRINT_BUTTON');?>" onClick="document.getElementById('print_button').style.display = 'none'; window.print(); window.document.getElementById('print_button').style.display = 'inline';">