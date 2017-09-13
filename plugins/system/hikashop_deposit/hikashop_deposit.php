<?php
jimport('joomla.plugin.plugin');
class plgSystemHikashop_deposit extends JPlugin{
}

function hikashop_product_price_for_quantity_in_cart(&$product){
	$currencyClass = hikashop_get('class.currency');
	$quantity = @$product->cart_product_quantity;

	if(!empty($product->product_id)){
		$productClass = hikashop_get('class.product');
		$productData = $productClass->get($product->product_id);
		if(!empty($productData->deposit)){
			$quantity = $quantity*$productData->deposit/100;
		}
	}
	$currencyClass->quantityPrices($product->prices,$quantity,$product->cart_product_total_quantity);
}

function hikashop_product_price_for_quantity_in_order(&$product){
	$quantity = $product->order_product_quantity;
	if(!empty($product->product_id)){
		$productClass = hikashop_get('class.product');
		$productData = $productClass->get($product->product_id);
		if(!empty($productData->deposit)){
			$quantity = $quantity*$productData->deposit/100;
		}
	}
	$product->order_product_price = $product->order_product_price*$product->deposit/100;
	$product->order_product_tax = 0;
	$product->order_product_total_price_no_vat = $product->order_product_price*$quantity;
	$product->order_product_total_price = ($product->order_product_price+$product->order_product_tax)*$quantity;
}
