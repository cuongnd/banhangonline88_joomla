<?php
/**
 * @package SJ Minicart Pro for Hikashop
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 * 
 */

defined('_JEXEC') or die;
	$image_config=array(
		'type'			=> $params->get('imgcfg_type'),
		'width' 		=> $params->get('imgcfg_width'),
		'height' 		=> $params->get('imgcfg_height'),
		'quality' 		=> 90,
		'function' 		=> ($params->get('imgcfg_function') == 'none')?null:'resize',
		'function_mode' => ($params->get('imgcfg_function') == 'none')?null:substr($params->get('imgcfg_function'), 7),
		'transparency'  => $params->get('imgcfg_transparency', 1)?true:false,
		'background' 	=> $params->get('imgcfg_background')
	);
	$options = $params->toObject();
?>
<div class="mc-product-wrap">
	<?php   foreach($cart->products as $cart_item_id => $product) { //var_dump($product);die;?>
	<div class="mc-product " data-product-id="<?php echo  $product->id;  ?>" data-old-quantity ="<?php echo $product->cart_product_quantity ?>">
		<div class="mc-product-inner">
			<div class="mc-image" >
				<?php $img = HKMinicartproHelper::getPHKImage($product, $params);?>
				<a href="<?php echo $product->link ?>" title="<?php echo $product->product_name; ?>" <?php echo HKMinicartproHelper::parseTarget($params->get('item_link_target')); ?>>
					<?php echo HKMinicartproHelper::imageTag($img,$image_config);?>
				</a>
			</div>
			<div class="mc-attribute">
				<div class="attr-name attr">
					<span class="label">
						<?php echo JText::_('PRODUCT_NAME_LABLE'); ?>
					</span>
					<span class="value">
						<a href="<?php echo $product->link ?>" <?php echo HKMinicartproHelper::parseTarget($params->get('item_link_target')); ?> title="<?php echo $product->product_name; ?>">
						<?php echo  $product->product_name; ?>
						</a>
					</span>
				</div>
				<div class="attr-quantity attr">
					<span class="label">
						<?php echo JText::_('PRODUCT_QUANTITY_LABEL'); ?>
					</span>
					<span class="value">
						<input type="text" maxlength="4" size="2" name="mc-quantity" class="mc-quantity" data-max-quantity="<?php echo $product->product_quantity;?>" value="<?php echo $product->cart_product_quantity ?>" />
					</span>
					<span class="quantity-control">
						<span class="quantity-plus"></span>
						<span class="quantity-minus"></span>
					</span>
				</div>
				<div class="attr-price attr">
					<span class="label">
						<?php echo JText::_('PRODUCT_PRICE_LABEL'); ?>
					</span>
					<span class="value">
					<?php
						echo $product->_price;
					?>
					</span>
				</div>
				<div class="mc-remove">
				</div>
			</div>
		</div>
		<div class="clear"></div>
	</div>
	<?php } ?>
</div>
