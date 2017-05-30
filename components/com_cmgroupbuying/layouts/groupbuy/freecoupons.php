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
?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style.css" type="text/css" />
<?php if(JFactory::getLanguage()->isRTL()): ?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style-rtl.css" type="text/css" />
<?php endif; ?>
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
?>
	<ul class="thumbnails">
	<?php
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
				$couponImages[] = JURI::root() . $coupon[$columnName];
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

		if($i % $numOfColumns == 1) echo '<div class="row-fluid">';

		echo '<li class="span4 deal" id ="deal_' . $coupon['id'] . '">';
		echo '<div class="thumbnail">';
		echo '<div class="deal_list" onClick="window.open(\''.$link.'\',\'_self\')">';
		echo '<div class="deal_photo">';
		echo $image;
		echo '</div>';
		echo '<div class="deal_info">';
		echo '<div class="deal_name">' . $couponName . '</div>';
		echo '<div class="deal_price">' . $couponDiscount . '</div>';
		echo '<div class="deal_view_button"><div class="btn btn-success">' . JText::_('COM_CMGROUPBUYING_DEAL_VIEW_BUTTON') . '</div></div>';
		echo '</div>';
		echo '</div>';
		echo '</div>';
		echo '</li>';

		if($i == $numOfColumns) echo '</div>';
		$i++;
	}
	?>
	</ul>
<?php
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