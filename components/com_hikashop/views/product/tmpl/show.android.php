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
    <div class="col-md-3">
        <div class="vendor_image">
            <img  src="<?php echo JUri::root() ?><?php echo $product_response->product->vendor->vendor_image->url ?>"/>
        </div>
    </div>
    <div class="col-md-6">
        <div class="vendor-info">
            <h4 class="vendor-name"><?php echo $product_response->product->vendor->vendor_name ?></h4>
            <span class="icon icon-ic_bookmark_black_24dp"></span>
        </div>
    </div>
    <div class="col-md-3">
        <div class="vendor-call">
            <span class="button_icon icon-ic_call_black_24dp call"/>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-4">
        <div class="total-buy">
            <span class="icon icon-ic_double_tick"></span>
            <h4><?php echo JText::_('luot mua') ?></h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="good-order">
            <img class="image_button" src="<?php echo JUri::root() ?>images/call.png"/>
            <h4><?php echo JText::_('don hang tot') ?></h4>
        </div>
    </div>
    <div class="col-md-4">
        <div class="time-process-order">
            <img class="image_button" src="<?php echo JUri::root() ?>images/call.png"/>
            <h4><?php echo JText::_('su ly don hang') ?></h4>
        </div>
    </div>
</div>
<div class="row">
    <div class="col-md-6">
        <div class="">
            <img class="image_button" src="<?php echo JUri::root() ?>images/call.png"/>
            <h4><?php echo JText::_('theo doi') ?></h4>
        </div>
    </div>
    <div class="col-md-6">
        <div class="">
            <img class="image_button " src="<?php echo JUri::root() ?>images/call.png"/>
            <h4 ><?php echo JText::_('vao gian hang') ?></h4>
        </div>
    </div>
</div>


<?php
$html=ob_get_clean();
$html=JUtility::html_to_obj($html);
ob_start();
?>
<style type="text/css">
.vendor{
    .vendor_image{
        text-align: center;
    }
    .vendor-name{
        font-weight: bold;
        text-transform: uppercase;
    }
    .vendor-call{
        border-radius: 10px;
        border: 1px solid #ccc;
    }
}
div.col-md-6.text-center{
    border: 1px;
}
</style>
<?php
$style=ob_get_clean();
$style=JUtility::remove_string_style_sheet($style);
$style=JUtility::less_to_obj($style);
if($debug) {
    echo "<pre>";
    print_r($style, false);
    echo "</pre>";
    die;
}
$product_response->product->html_product=json_encode($html);
$product_response->product->style_product=json_encode($style);
echo json_encode($product_response);
