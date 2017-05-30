<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

$configuration = $this->configuration;
$items = $this->items;
$pageNav = $this->pageNav;
$keyword = $this->keyword;
$locationId = $this->locationId;
$categoryId = $this->categoryId;
$locationList = $this->locationList;
$categoryList = $this->categoryList;

if($configuration['jquery_loading'] != "" && version_compare(JVERSION, '3.0.0', 'lt'))
{
	JFactory::getDocument()->addScript($configuration['jquery_loading']);
}
?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style.css" type="text/css" />
<?php if(JFactory::getLanguage()->isRTL()): ?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style-rtl.css" type="text/css" />
<?php endif; ?>
<div class="all_deal_section">
	<div class="page_title">
		<p><?php echo $this->pageTitle; ?></p>
	</div>
	<form action="<?php echo JRoute::_('index.php', false); ?>" method="get" class="search-form">
		<div class="row">
			<div class="label"><?php echo JText::_('COM_CMGROUPBUYING_SEARCH_KEYWORD'); ?></div>
			<div class="control">
				<input type="text" id="keyword" name="keyword" placeholder="Keyword" value="<?php echo $keyword; ?>">
			</div>
		</div>
		<div class="row">
			<div class="label"><?php echo JText::_('COM_CMGROUPBUYING_SEARCH_LOCATION'); ?></div>
			<div class="control">
				<?php echo JHTML::_('select.genericList', $locationList, 'location_id', '' , 'id', 'name', $locationId); ?>
			</div>
		</div>
		<div class="row">
			<div class="label"><?php echo JText::_('COM_CMGROUPBUYING_SEARCH_CATEGORY'); ?></div>
			<div class="control">
				<?php echo JHTML::_('select.genericList', $categoryList, 'category_id', '' , 'id', 'name', $categoryId); ?>
			</div>
		</div>
		<input type="submit" value="<?php echo JText::_('COM_CMGROUPBUYING_SEARCH_SUBMIT_BUTTON'); ?>" class="button btn btn-primary" />
	</form>
<?php
if(empty($items))
{
	echo '<p class="cmgroupbuying_error">' . $this->noDeal . '</p>';
}
else
{
?>
		<div class="search-result item-list">
		<?php
			$i = 1;

			foreach($items as $item)
			{
				if ($item['type'] == 'deal')
				{
					$optionsOfDeal = JModelLegacy::getInstance('dealoption','cmgroupbuyingModel')->getOptions($item['id']);
					$itemPrice = CMGroupBuyingHelperDeal::displayDealPrice($optionsOfDeal[1]['price'], true, $configuration);
					$itemOriginalPrice = CMGroupBuyingHelperDeal::displayDealPrice($optionsOfDeal[1]['original_price'], true, $configuration);

					$itemName = $item['name'];
					$itemDesc = '<div class="item-description">' . $item['description'] . '</div>';
					$itemDesc .= '<div class="item-price">';
					$itemDesc .= '<div class="price">' . $itemPrice . '</div>';
					$itemDesc .= '<div class="original-price">' . $itemOriginalPrice . '</div>';
					$itemDesc .= '</div>';
					$itemDesc .= '<div class="item-type"><span class="label label-success">' . JText::_('COM_CMGROUPBUYING_DEAL') . '</span></div>';
					$imageImage = $item['image'];
					$itemUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=deal&id=' . $item['id'] . '&alias=' . $item['alias']);


				}
				elseif ($item['type'] == 'coupon')
				{
					$coupon = JModelLegacy::getInstance('FreeCoupon','cmgroupbuyingModel')->getCouponById($item['id'], 'discount');
					$itemName = $item['name'];
					$itemDesc = '<div class="item-description">' . $item['description'] . '</div>';
					$itemDesc .= '<div class="item-price"><div class="price">' . $coupon['discount'] . '</div></div>';
					$itemDesc .= '<div class="item-type"><span class="label label-success">' . JText::_('COM_CMGROUPBUYING_FREE_COUPON') . '</span></div>';
					$imageImage = $item['image'];
					$itemUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=freecoupon&id=' . $item['id'] . '&alias=' . $item['alias']);
				}

				echo '<div class="item">';
				echo '<div class="item-container">';
				echo '<div class="item-image">';
				echo '<a href="' . $itemUrl . '"><img src="' . $imageImage . '" alt="' . htmlspecialchars($itemName) . '" /></a>';
				echo '</div>';
				echo '<div class="item-detail">';
				echo '<div class="item-name"><a href="' . $itemUrl . '">' . $itemName . '</a></div>';
				echo '<div class="item-description-container">' . $itemDesc . '</div>';
				echo '</div>';
				echo '</div>';
				echo '</div>';
			}
		?>
		</div>
	<?php
	}
	?>
	<form action="<?php echo JRoute::_('index.php', false); ?>" method="post">
		<input type="hidden" name="option" id="option" value="com_cmgroupbuying"/>
		<input type="hidden" name="view" value="search" />
		<div class="cmgroupbuying_pagination"><?php echo $pageNav->getListFooter(); ?></div>
	</form>
</div>
