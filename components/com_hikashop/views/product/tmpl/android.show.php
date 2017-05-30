<?php
$element=$this->element;
$image=hikashop_get('helper.image');
$currency_helper= hikashop_get('class.currency');


$product=new stdClass();
$product->id=$element->product_id;
$product->name=$element->product_name;
$product->code=$element->product_code;


$fist_image=reset($element->images);
$img = $image->getThumbnail($fist_image->file_path);
$product->main_image=JUri::root().$img->url;
$first_price=reset($element->prices);
if($item->discount) {
    $product->price=$first_price->price_value_without_discount_with_tax;
    $product->price_formatted=$currency_helper->format($first_price->price_value_without_discount_with_tax, $first_price->price_currency_id);
    $product->discount_price =$first_price->price_value_with_tax ;
    $product->discount_price_formatted = $currency_helper->format($first_price->price_value_with_tax,$first_price->price_currency_id);
}else{
    $product->price=$first_price->price_value_with_tax;
    $product->price_formatted=$currency_helper->format($first_price->price_value_with_tax,$first_price->price_currency_id);
    $product->discount_price =$first_price->price_value_with_tax;
    $product->discount_price_formatted =$currency_helper->format($first_price->price_value_with_tax,$first_price->price_currency_id);


}
echo json_encode($product);
?>