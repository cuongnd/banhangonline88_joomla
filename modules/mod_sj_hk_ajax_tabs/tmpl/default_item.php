<?php
/**
 * @package SJ Ajax Tabs for HikaShop
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined('_JEXEC') or die;

?>
<div class="item-wrap ajaxtabs-item">
	<?php
	$img = HKAjaxtabsBaseHelper::getPHKImage($item, $params);
	if ($img):?>
		<div class="item-image">
			<a href="<?php echo $item->link; ?>"
			   title="<?php echo $item->title; ?>" <?php echo HKAjaxtabsBaseHelper::parseTarget($params->get('item_link_target', '_blank')); ?>>
				<?php echo HKAjaxtabsBaseHelper::imageTag($img); ?>
			</a>
		</div>
	<?php endif; // image display ?>

	<?php if ((int)$params->get('item_title_display', 1)): ?>
		<div class="item-title">
			<a href="<?php echo $item->link; ?>"
			   title="<?php echo $item->title; ?>" <?php echo HKAjaxtabsBaseHelper::parseTarget($params->get('item_link_target', '_blank')); ?>>
				<?php echo HKAjaxtabsBaseHelper::truncate($item->title, $params->get('item_title_max_characs', 20)); ?>
			</a>
		</div>
	<?php endif; // title display ?>

	<?php if((int)$params->get('display_votes',1)){ ?>
		<div class="item-votes">
			<?php echo $minivotes;?>
		</div>
	<?php } ?>
	
	<?php if ((int)$params->get('item_prices_display', 1) && !empty($item->_price)) { ?>
		<div class="item-prices">
				<span class="item-price">
					<?php echo $item->_price; ?>
				</span>
			<?php if ((int)$params->get('item_per_unit_display', 1)) { ?>
				<span class="item-per-unit">
					<?php echo JText::_('PER_UNIT'); ?>
				</span>
			<?php } ?>
		</div>
	<?php } ?>

	<?php if ((int)$params->get('item_desc_display', 1)): ?>
		<div class="item-description">
			<?php echo HKAjaxtabsBaseHelper::truncate($item->description, $params->get('item_desc_max_characs',100) ); ?>
		</div>
	<?php endif; // description display ?>

	<?php if((int)$params->get('display_add_to_cart',1) || (int)$params->get('display_add_to_wishlist',1) ) { ?>
		<div class="item-btn-add">
			<?php  echo $btn_add;  ?>
		</div>
	<?php } ?>
	
	<?php if ((int)$params->get('item_detail_display', 1)): ?>
		<div class="item-detail">
			<a href="<?php echo $item->link; ?>"
			   title="<?php echo $item->title; ?>" <?php echo HKAjaxtabsBaseHelper::parseTarget($params->get('item_link_target', '_blank')); ?>>
				<?php echo $params->get('item_detail_text', 'readmore'); ?>
			</a>
		</div>
	<?php endif; // readmore display ?>

</div>

