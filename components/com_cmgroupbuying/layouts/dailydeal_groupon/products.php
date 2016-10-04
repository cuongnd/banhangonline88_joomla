<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

require_once JPATH_SITE . '/components/com_cmgroupbuying/helpers/product.php';

$products = $this->products;
$imageWidth = $this->imageWidth;
$imageHeight = $this->imageHeight;
?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style.css" type="text/css" />
<?php if(JFactory::getLanguage()->isRTL()): ?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style-rtl.css" type="text/css" />
<?php endif; ?>
<div class="all_deal_section">
	<div class="page_title">
		<p><?php echo $this->pageTitle; ?></p>
	</div>
<?php
if(empty($products))
{
	echo '<p class="cmgroupbuying_error">' . JText::_('COM_CMGROUPBUYING_PRODUCTS_NO_PRODUCT_FOUND') . '</p>';
}
else
{
?>
	<div class="products thumbnails">
	<?php foreach ($products as $product): ?>
		<?php $detailLink = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=product&id=' . $product['id'] . '&alias=' . $product['alias']); ?>
		<div class="product thumbnail">
			<div class="row-fluid">
				<div class="product_image span4" style="width: <?php echo $imageWidth; ?>px">
					<a href="<?php echo $detailLink; ?>"><img style="width: <?php echo $imageWidth; ?>px" src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['name']); ?>" /></a>
				</div>
				<div class="product_detail span8">
					<div class="product_name">
							<a href="<?php echo $detailLink; ?>"><?php echo $product['name']; ?></a>
					</div>
					<div class="product_description">
						<?php echo $product['short_description']; ?>
					</div>
					<div class="product_deals">
						<?php echo CMGroupBuyingHelperProduct::countDealsOfProduct($product['id']); ?>
					</div>
				</div>
			</div>
		</div>
	<?php endforeach; ?>
	</div>
<?php
}
?>
</div>
<form action="<?php echo JRoute::_('index.php', false); ?>" method="post">
	<input type="hidden" name="option" id="option" value="com_cmgroupbuying"/>
	<input type="hidden" name="view" value="products" />
	<div class="cmgroupbuying_pagination"><?php echo $this->pageNav->getListFooter(); ?></div>
</form>
