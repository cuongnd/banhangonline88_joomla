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
    <div class="vendor">
        <div class="row">
            <div class="col-md-3">
                <div class="vendor_image">
                    <img
                        src="<?php echo JUri::root() ?><?php echo $product_response->product->vendor->vendor_image->url ?>"/>
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
    </div>
    <div class="toolbar">
        <div class="row">
            <div class="col-md-4">
                <div class="total-buy">
                    <span class="button_icon span-total-buy icon-ic_emoji_flower"></span>
                    <h4><?php echo JText::_('11904') ?></h4>
                </div>
                <h4 class="title-total-buy"><?php echo JText::_('HIKA_LUOT_MUA') ?></h4>
            </div>
            <div class="col-md-4">
                <div class="total-buy">
                    <span class="button_icon span-total-buy icon-ic_emoji_flower"></span>
                    <h4><?php echo JText::_('11904') ?></h4>
                </div>
                <h4 class="title-total-buy"><?php echo JText::_('HIKA_DON_HANG_TOT') ?></h4>
            </div>
            <div class="col-md-4">
                <div class="total-buy">
                    <span class="button_icon span-total-buy icon-ic_emoji_flower"></span>
                    <h4><?php echo JText::_('11904') ?></h4>
                </div>
                <h4 class="title-total-buy"><?php echo JText::_('HIKA_SU_LY_DON_HANG') ?></h4>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="total-buy">
                <span class="button_icon span-total-buy icon-ic_emoji_flower"></span>
                <h4><?php echo JText::_('11904') ?></h4>
            </div>
            <h4 class="title-total-buy"><?php echo JText::_('LUOT_MUA') ?></h4>
        </div>
        <div class="col-md-6">
            <div class="total-buy">
                <span class="button_icon span-total-buy icon-ic_emoji_flower"></span>
                <h4><?php echo JText::_('11904') ?></h4>
            </div>
            <h4 class="title-total-buy"><?php echo JText::_('LUOT_MUA') ?></h4>
        </div>
    </div>


<?php
$html = ob_get_clean();


ob_start();
?>
    <style type="text/css">
        div.vendor {

            .vendor_image {
                border-radius: 80px;
                padding: 20px;
                margin: 10px;
                background-color: #FFFFFF;
                border: 1px solid #FFFFFF;
                font-size: 200px;
                height: 160px;
                width: 160px;
            }

            .vendor-name {
                border-radius: 80px;
                padding: 20px;
                margin: 10px;
                background-color: #FFFFFF;
                border: 1px solid #FFFFFF;
                font-size: 20px;
                height: 160px;
                color: #CCCCCC;
                text-align: left;
                width: 160px;
            }

            .vendor-call {
                border-radius: 80px;
                padding: 20px;
                margin: 10px;
                background-color: #FFFFFF;
                border: 1px solid #CCCCCC;
                color: #CCCCCC;
                font-size: 200px;
                height: 160px;
                width: 160px;
            }

        }
        .toolbar {

            .total-buy {
                text-align: center;
                .span-total-buy {
                    border-radius: 35px;
                    padding: 20px;
                    margin: 10px;
                    background-color: #FFFFFF;
                    border: 0px solid #FFFFFF;
                    color: #CCCCCC;
                    font-size: 200px;
                    height: 100px;
                    width: 100px;
                }

            }
            .title-total-buy
            {
               text-align: center;
            }
        }
        div.col-md-6.text-center {
        }
    </style>
<?php
$style = ob_get_clean();
$style = JUtility::remove_string_style_sheet($style);
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
