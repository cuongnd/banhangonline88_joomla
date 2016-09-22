<?php
/**
*
* Layout for the add to cart popup
*
* @package	VirtueMart
* @subpackage Cart
* @author Max Milbers
*
* @link http://www.virtuemart.net
* @copyright Copyright (c) 2013 VirtueMart Team. All rights reserved.
* @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.php
* VirtueMart is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* @version $Id: cart.php 2551 2010-09-30 18:52:40Z milbo $
*/

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

$media_model 	= VmModel::getModel('media');
if (!class_exists('CurrencyDisplay')) require(VMPATH_ADMIN . DS . 'helpers' . DS . 'currencydisplay.php');
$currency = CurrencyDisplay::getInstance( );

?>

<!-- popup cart -->
<div class="popup-cart custom-box-shadow"> 

	<?php if($this->products){ ?>

	<i class="success-icon megadeal-icon-cart-checked"></i>
	<h3 class="title"><?php echo JText::_('COM_VIRTUEMART_POPUP_PRODUCT_ADDED_SUCCESS'); ?> 
		<span><?php echo JText::_('COM_VIRTUEMART_POPUP_YOUR_SHIPING_CART'); ?></span>
	</h3>
	
	<div class="item-wrap">
	<?php foreach($this->products as $product){
			if($product->quantity>0){
				$images  = $media_model->createMediaByIds($product->virtuemart_media_id, $product->quantity); ?>
				<div class="col-sm-5">
					<?php if(isset($images[0]) && $images[0]) {
						echo $images[0]->displayMediaThumb ('class="ProductImage"', FALSE); 
					} ?>	
				</div> <!-- /.col-sm-5 -->
				<div class="col-sm-7">
					<h4 class="item-name"> <?php echo $product->product_name; ?> </h4>
					<div class="sp-price-box">
						<?php if ( isset($product->allPrices[0]['product_override_price']) && round($product->allPrices[0]['product_override_price']) != 0) { ?>
	                    	<ins>
				                <?php echo $currency->createPriceDiv ('product_override_price', '', $product->allPrices[0], FALSE, FALSE, 1.0, TRUE); ?>
				            </ins>
				            <del>    
				                <?php echo $currency->createPriceDiv ('product_price', '', $product->allPrices[0], FALSE, FALSE, 1.0, TRUE); ?>
				            </del>
                    	<?php } else{ ?>
                    		<ins>
				                <?php echo $currency->createPriceDiv ('product_price', '', $product->allPrices[0], FALSE, FALSE, 1.0, TRUE); ?>
				            </ins>
                    	<?php } ?>
					</div>
					<p class="popup-cart-product-quantity">
						<span>Quantity: </span>
						<?php echo $product->quantity; ?>
					</p>
				</div> <!-- /.col-sm-7 -->
			<?php } else { 
				if(!empty($product->errorMsg)){ ?>
					<div><?php echo $product->errorMsg ?></div>
				<?php } // !empty($product->errorMsg)
			} // else

		} // END:: foreach
	} // has product ?>
	</div> <!-- //item-wrap -->

	<div class="button-group">
		<a class="continue_link btn btn-border" href="<?php echo $this->continue_link; ?>" >
			<?php echo vmText::_('COM_VIRTUEMART_CONTINUE_SHOPPING'); ?> 
		</a>
		<a class="showcart btn btn-border" href="<?php echo  $this->cart_link; ?>">
			<?php echo vmText::_('COM_VIRTUEMART_CART_SHOW_TITLE'); ?>
		</a>
	</div> <!-- //button-group -->

</div> <!-- //.popup-cart -->

<?php


if(VmConfig::get('popup_rel',1)){
	//VmConfig::$echoDebug=true;
	if ($this->products and is_array($this->products) and count($this->products)>0 ) {

		$product = reset($this->products);

		$customFieldsModel = VmModel::getModel('customfields');
		$product->customfields = $customFieldsModel->getCustomEmbeddedProductCustomFields($product->allIds,'R');

		$customFieldsModel->displayProductCustomfieldFE($product,$product->customfields);
		if(!empty($product->customfields)){
			?>
			<div class="product-related-products">
			<h4><?php echo vmText::_('COM_VIRTUEMART_RELATED_PRODUCTS'); ?></h4>
			<?php
		}
		foreach($product->customfields as $rFields){

				if(!empty($rFields->display)){
				?><div class="product-field product-field-type-<?php echo $rFields->field_type ?>">
				<div class="product-field-display"><?php echo $rFields->display ?></div>
				</div>
			<?php }
		} ?>
		</div>
	<?php
	}
}

?><br style="clear:both">
