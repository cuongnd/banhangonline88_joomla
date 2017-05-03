<?php
$app = JFactory::getApplication();

$image_helper = hikashop_get('helper.image');
$debug = JUtility::get_debug();
$product_response = new stdClass();
$product_response->categories = array();
foreach ($product_response->categories as &$category) {
    $category->medium_image = $image_helper->getThumbnail($category->category_medium_image_file_path, array(500, 500), array('default' => true), true);
}
$product_response->product = $this->element;
$this->row = $product_response->product;
$this->setLayout('listing_price');
ob_start();
echo $this->loadTemplate();
$html_price = ob_get_clean();
$product_response->product->html_price = $html_price;
$images =& $product_response->product->images;
foreach ($images as &$image) {
    $image = $image_helper->getThumbnail($image->file_path, array(500, 500), array('default' => true), true);
}
$debug = JUtility::get_debug();
ob_start();
?>
    <div type="div">
        <h2 type="h2" class="thong-tin-gian-hang"><?php echo JText::_('HIKA_THONG_TIN_GIAN_HANG') ?></h2>
    </div>
    <div type="div" class="line"></div>
    <div type="div" class="vendor">
        <div type="row" class="row">
            <div type="column" class="col-md-2">
                <img type="img"
                     src="<?php echo JUri::root() ?><?php echo $product_response->product->vendor->vendor_image->url ?>"/>
            </div>
            <div type="column" class="col-md-8">
                <h4 type="h4" class="vendor-name"><?php echo $product_response->product->vendor->vendor_name ?></h4>
                <span type="icon" class="icon icon-ic_bookmark_black_24dp"></span>>
            </div>
            <div type="column" class="col-md-2">
                <span type="button_icon" class="button_icon icon-ic_call_black_24dp call"/>
            </div>
        </div>
    </div>
    <div type="div" class="toolbar">
        <div type="row" class="row">
            <div type="column" class="col-md-4">
                <div type="div" class="luot-mua">
                    <button type="button_icon" class="button_icon span-total-buy icon-ic_shopping_basket_black_24dp"><?php echo JText::_('11904') ?></button>
                </div>
                <h4 type="h4" class="title-luot-mua"><?php echo JText::_('HIKA_LUOT_MUA') ?></h4>
            </div>
            <div type="column" class="col-md-4">
                <div type="div" class="don-hang-tot">
                    <button type="button_icon" class="button_icon span-don-hang-tot icon-ic_shopping_basket_black_24dp"><?php echo JText::_('11904') ?></button>
                </div>
                <h4 type="h4" class="title-don-hang-tot"><?php echo JText::_('HIKA_DON_HANG_TOT') ?></h4>
            </div>
            <div type="column" class="col-md-4">
                <div type="div" class=" su-ly-don-hang">
                    <button type="button_icon" class="button_icon span-su-ly-don-hang icon-ic_alarm_black_24dp"><?php echo JText::_('11904') ?></button>
                </div>
                <h4 type="h4" class="title-su-ly-don-hang"><?php echo JText::_('HIKA_SU_LY_DON_HANG') ?></h4>
            </div>
        </div>
    </div>
    <div type="div" class="line"></div>
    <div type="div" class="theo-doi-va-vao-gian-hang">
        <div type="row" class="row">
            <div type="column" class="col-md-6">
                <div type="div" class="theo-doi">
                    <button type="button_icon" class="button_icon span-theo-doi icon-ic_add_circle_outline_black_24dp"><?php echo JText::_('HIKA_THEO_DOI') ?></button>
                </div>
            </div>
            <div type="column" class="col-md-6">
                <div type="div" class="go-to-shop">
                    <button type="button_icon" class="button_icon span-go-to-shop icon-ic_visibility_black_24dp"><?php echo JText::_('HIKA_VAO_GIAN_HANG') ?></button>
                </div>
            </div>
        </div>
    </div>
    <div type="div" class="bottom-line"></div>


<?php
$html = ob_get_clean();

$style =JFile::read(JPATH_ROOT.DS.'components/com_hikashop/assets/less/view_product_show.android.less');
$style = JUtility::less_to_obj($style);
if ($debug) {
    echo "<pre>";
    print_r($style, false);
    echo "</pre>";

}
$html = JUtility::html_to_obj($html, $style);
if ($debug) {
    echo "<pre>";
    print_r($html, false);
    echo "</pre>";
}
if($debug){
    die();
}
$product_response->product->html_product = json_encode($html);
$product_response->product->style_product = json_encode($style);
echo json_encode($product_response);
