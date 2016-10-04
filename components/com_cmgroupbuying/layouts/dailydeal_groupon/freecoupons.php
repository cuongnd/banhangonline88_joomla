<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

$configuration = $this->configuration;
$view = $this->view;
$imageWidth = $this->imageWidth;
$imageHeight = $this->imageHeight;
$numOfColumns = $this->numOfColumns;
$rowSpace = $this->rowSpace;
$colSpace = $this->colSpace;
$coupons = $this->coupons;

if($imageWidth == '') $imageWidth = 250;
if($imageHeight == '') $imageHeight = 200;
if($numOfColumns == '') $numOfColumns = 2;
if($rowSpace == '') $rowSpace = 10;
if($colSpace == '') $colSpace = 10;

if($configuration['jquery_loading'] != "" && version_compare(JVERSION, '3.0.0', 'lt'))
{
	JFactory::getDocument()->addScript($configuration['jquery_loading']);
}
if($configuration['deal_list_effect'] == "slideshow" && !empty($coupons)):

	JFactory::getDocument()->addScript('components/com_cmgroupbuying/assets/js/jquery.aslideshow.js');
?>
<link rel="stylesheet" href="components/com_cmgroupbuying/assets/css/jquery.aslideshow.css" type="text/css" />
<?php endif; ?>

<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style.css" type="text/css" />
<?php if(JFactory::getLanguage()->isRTL()): ?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style-rtl.css" type="text/css" />
<?php
	$lastDealStyle1 = "margin-left: " . $colSpace . "px;";
	$lastDealStyle2 = "margin-left: 0px;";
else:
	$lastDealStyle1 = "margin-right: " . $colSpace . "px;";
	$lastDealStyle2 = "margin-right: 0px;";
endif; ?>

<?php
$style = <<<CMGB
<style>
ul.deal_list li.deal {
	width: {$imageWidth}px;
	margin-bottom: {$rowSpace}px;
	{$lastDealStyle1}
}

ul.deal_list li.last_deal {
	{$lastDealStyle2}
}

ul.deal_list li.deal .deal_photo,
ul.deal_list li.deal .deal_photo img {
	width: {$imageWidth}px;
	height: {$imageHeight}px;
}
</style>
CMGB;

	echo $style;
?>
<div class="all_deal_section clearfix">
	<div class="page_title">
		<p><?php echo $this->pageTitle; ?></p>
	</div>
<?php
if(empty($coupons))
{
	echo '<p class="cmgroupbuying_error">' . $this->noCoupon . '</p>';
}
else
{
	echo '<ul class="deal_list clearfix">';

	$i = 1;

	foreach($coupons as $coupon)
	{
		$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=freecoupon&id=' . $coupon['id'] . '&alias=' . $coupon['alias']);
		$couponName = $coupon['name'];

		if($coupon['discount'] != '')
		{
			$couponDiscount = JText::sprintf('COM_CMGROUPBUYING_FREE_COUPONS_DISCOUNT', $coupon['discount']);
		}
		else
		{
			$couponDiscount = '';
		}

		$couponImages = array();

		for($j = 1; $j <= 5; $j++)
		{
			$columnName = 'image_path_' . $j;

			if($coupon[$columnName] != '')
			{
				$couponImages[]   = JURI::root() . $coupon[$columnName];
			}
		}

		if(!empty($couponImages))
		{
			$image = '<img src="' . $couponImages[0] . '" width="' . $imageWidth . '" height="' . $imageHeight . '" />';
		}
		else
		{
			$image = '';
		}

		echo '<a href="' . $link . '">';

		if($i == $numOfColumns)
		{
			echo '<li class="deal last_deal" id ="deal_' . $coupon['id'] . '">';
			$i = 1;
		}
		else
		{
			echo '<li class="deal" id ="deal_' . $coupon['id'] . '">';
			$i++;
		}

		echo '<div class="deal_photo" >';

		if($configuration['deal_list_effect'] == "slideshow" && $image != ''):
			echo '<div class="deal_photo_slideshow" id="deal_photo_slideshow_' . $coupon['id'] . '">';

			foreach($couponImages as $image):
				echo '<img src="' . $image . '" width="' . $imageWidth . '" height="' . $imageHeight . '" />';
			endforeach;

			echo '</div>';
		elseif($configuration['deal_list_effect'] == "show_short_desc" && $image != ''):
			echo $image;
			echo '<div class="deal_list_short_description" id="deal_list_short_description_' . $coupon['id'] . '">';
			echo '<div>'. $coupon['short_description'] . '</div>';
			echo '</div>';
		else:
			echo $image;
		endif;

		echo '</div>';
		echo '<div class="deal_info">';
		echo '<div class="deal_name">' . $couponName . '</div>';
		echo '<div class="deal_price">' . $couponDiscount . '</div>';
		echo '<div class="deal_view_button"><div class="cm_button">' . JText::_('COM_CMGROUPBUYING_DEAL_VIEW_BUTTON') . '</div></div>';
		echo '</div>';
		echo '</li></a>';
	}

	echo '</ul>';
}
?>
</div>
<form action="<?php echo JRoute::_('index.php', false); ?>" method="post">
	<input type="hidden" name="option" id="option" value="com_cmgroupbuying"/>
	<input type="hidden" name="view" value="<?php echo $view; ?>" />
	<?php if($view != 'search'): ?>
	<div class="cmgroupbuying_pagination"><?php echo $this->pageNav->getListFooter(); ?></div>
	<?php endif; ?>
</form>
<?php
if($configuration['deal_list_effect'] == "slideshow" && !empty($coupons)):
	$seconds = $configuration['deal_list_slideshow_timing'] * 1000;
?>
<script type="text/javascript">
jQuery(document).ready(function(){
	<?php foreach($coupons as $coupon): ?>
	jQuery('#deal_photo_slideshow_<?php echo $coupon['id']; ?>').slideshow({
		playhover:true,
		playframe:false,
		width:<?php echo $imageWidth; ?>,
		height:<?php echo $imageHeight; ?>,
		title:false,
		panel:false,
		time:<?php echo $seconds; ?>,
		loadframe:false,
		imgresize:true
	});
	<?php endforeach; ?>
});
</script>
<?php else: ?>
<script type="text/javascript">
jQuery(document).ready(function(){
	jQuery('.deal').hover(function(){
		dealId = jQuery(this).attr('id').replace('deal_', '');
		jQuery('#deal_list_short_description_' + dealId).fadeToggle();
	});
});
</script>
<?php endif; ?>