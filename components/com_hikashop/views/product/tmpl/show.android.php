<?php
$app=JFactory::getApplication();

$image_helper = hikashop_get('helper.image');
$debug=JUtility::get_debug();
$product_response=new stdClass();
$product_response->categories=array();
foreach($product_response->categories as &$category) {
    $category->medium_image = $image_helper->getThumbnail($category->category_medium_image_file_path, array(500, 500), array('default' => true), true);
}
$product_response->product=$this->element;
$this->row = $product_response->product;
$this->setLayout('listing_price');
ob_start();
echo $this->loadTemplate();
$html_price=ob_get_clean();
$product_response->product->html_price=$html_price;
$images=&$product_response->product->images;
foreach($images as &$image) {
    $image = $image_helper->getThumbnail($image->file_path, array(500, 500), array('default' => true), true);
}
$debug=JUtility::get_debug();
ob_start();
?>
<div class="row">
    <div class="col-md-12">
        <h4><?php echo JText::_('thong tin gian hang') ?></h4>
    </div>
</div>
<div class="row">
    <div class="col-md-12">
        <h4><?php echo JText::_('thong tin gian hang') ?></h4>
    </div>
</div>
<?php
$html=ob_get_clean();
$html=JUtility::html_to_obj($html);
if($debug) {
    echo "<pre>";
    print_r($html, false);
    echo "</pre>";
    die;
}
$product_response->product->html_product=json_encode($html);
echo json_encode($product_response);
