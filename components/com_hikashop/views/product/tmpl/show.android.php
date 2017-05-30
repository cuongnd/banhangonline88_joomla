<?php
$app = JFactory::getApplication();

$image_helper = hikashop_get('helper.image');
$debug = JUtility::get_debug();
$this->categories = array();
foreach ($this->categories as &$category) {
    $category->medium_image = $image_helper->getThumbnail($category->category_medium_image_file_path, array(500, 500), array('default' => true), true);
}
$this->product = $this->element;
$this->row = $this->product;
$this->setLayout('listing_price');
ob_start();
echo $this->loadTemplate();
$html_price = ob_get_clean();
$this->product->html_price = $html_price;
$images =& $this->product->images;
foreach ($images as &$image) {
    $image = $image_helper->getThumbnail($image->file_path, array(500, 500), array('default' => true), true);
}
$debug = JUtility::get_debug();

$this->product->style_product = json_encode($style);
$product=new stdClass();
$product->product_id=$this->product->product_id;
$product->category_id=$this->product->category_id;
$product->product_name=$this->product->product_name;
$product->product_code=$this->product->product_code;
$product->html_price=$this->product->html_price;
$product->list_image=$this->product->images;
$response=new stdClass();
$response->product=$product;
echo json_encode($response);