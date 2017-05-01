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
    <div class="div">
        <h2 class="thong-tin-gian-hang"><?php echo JText::_('HIKA_THONG_TIN_GIAN_HANG') ?></h2>
    </div>
    <div class="div line"></div>
    <div class="vendor">
        <div class="row">
            <div class="col-md-2">
                <div class="vendor_image">
                    <img
                        src="<?php echo JUri::root() ?><?php echo $product_response->product->vendor->vendor_image->url ?>"/>
                </div>
            </div>
            <div class="col-md-8">
                <div class="vendor-info">
                    <h4 class="vendor-name"><?php echo $product_response->product->vendor->vendor_name ?></h4>
                    <span class="icon icon-ic_bookmark_black_24dp"></span>
                </div>
            </div>
            <div class="col-md-2">
                <div class="vendor-call">
                    <span class="button_icon icon-ic_call_black_24dp call"/>
                </div>
            </div>
        </div>
    </div>
    <div class="toolbar">
        <div class="row">
            <div class="col-md-4">
                <div class="total-buy">
                    <span class="button_icon span-total-buy icon-ic_shopping_basket_black_24dp"></span>
                    <h4><?php echo JText::_('11904') ?></h4>
                </div>
                <h4 class="title-total-buy"><?php echo JText::_('HIKA_LUOT_MUA') ?></h4>
            </div>
            <div class="col-md-4">
                <div class="don-hang-tot">
                    <span class="button_icon span-don-hang-tot icon-ic_shopping_basket_black_24dp"></span>
                    <h4><?php echo JText::_('11904') ?></h4>
                </div>
                <h4 class="title-don-hang-tot"><?php echo JText::_('HIKA_DON_HANG_TOT') ?></h4>
            </div>
            <div class="col-md-4">
                <div class="su-ly-don-hang">
                    <span class="button_icon span-su-ly-don-hang icon-ic_alarm_black_24dp"></span>
                    <h4><?php echo JText::_('11904') ?></h4>
                </div>
                <h4 class="title-su-ly-don-hang"><?php echo JText::_('HIKA_SU_LY_DON_HANG') ?></h4>
            </div>
        </div>
    </div>
    <div class="div line"></div>
    <div class="theo-doi-va-vao-gian-hang">
        <div class="row">
            <div class="col-md-6">
                <div class="theo-doi">
                    <span class="button_icon span-total-buy icon-ic_add_circle_outline_black_24dp"></span>
                    <h4><?php echo JText::_('HIKA_THEO_DOI') ?></h4>
                </div>
            </div>
            <div class="col-md-6">
                <div class="go-to-shop">
                    <span class="button_icon span-total-buy icon-ic_visibility_black_24dp"></span>
                    <h4><?php echo JText::_('HIKA_VAO_GIAN_HANG') ?></h4>
                </div>
            </div>
        </div>
    </div>
    <div class="div bottom-line"></div>


<?php
$html = ob_get_clean();

$style =JFile::read(JPATH_ROOT.DS.'components/com_hikashop/assets/less/view_product_show.android.less');
$style = JUtility::less_to_obj($style);
if ($debug) {
    echo "<pre>";
    print_r($style, false);
    echo "</pre>";
    die;
}
$html = JUtility::html_to_obj($html, $style);
if ($debug) {
    echo "<pre>";
    print_r($html, false);
    echo "</pre>";
}

$product_response->product->html_product = json_encode($html);
$product_response->product->style_product = json_encode($style);
echo json_encode($product_response);
