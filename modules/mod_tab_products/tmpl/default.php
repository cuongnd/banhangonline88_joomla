<?php
defined('_JEXEC') or die;
JHtml::_('jquery.framework');
JHtml::_('jquery.zozo_tab');

$doc->addScript(JUri::root() . 'media/system/js/lazy-load-xt-master/dist/jquery.lazyloadxt.js');
$doc->addLessStyleSheet(JUri::root() . 'modules/mod_tab_products/assets/less/style.less');
$doc->addStyleSheet(JUri::root() . 'media/system/js/lazy-load-xt-master/dist/jquery.lazyloadxt.fadein.css');
$doc->addLessStyleSheet(JUri::root() . 'media/system/js/slick-master/slick/slick-theme.less');
$doc->addLessStyleSheet(JUri::root() . 'modules/mod_tab_products/assets/less/mod_tab_products.less');
$doc->addScript(JUri::root() . 'modules/mod_tab_products/assets/js/jquery.vtvslider.js');
$doc->addScript(JUri::root() . 'media/system/js/jquery.utility.js');
$doc->addScript(JUri::root() . 'modules/mod_tab_products/assets/js/mod_tab_products.js');
require_once JPATH_ROOT . DS . 'administrator/components/com_hikashop/helpers/helper.php';
$style = $params->get('product_style', 'table');
$moduleclass_sfx = $params->get('moduleclass_sfx','');
$currencyHelper = hikashop_get('class.currency');
$mainCurr = $currencyHelper->mainCurrency();
$image=hikashop_get('helper.image');

?>

<div class="mod_tab_products <?php echo $moduleclass_sfx ?>" id="mod_tab_products_<?php echo $module->id ?>">

    <!-- Tab Navigation Menu -->
    <h3 class="title"><?php echo $module->title ?></h3>
    <div class="tab-product">
        <ul>

            <?php foreach ($list_category_product as $category) { ?>
                <?php
                $list_icon=$category->detail->list_icon;
                $list_icon=explode(',',$list_icon);
                $icon=reset($list_icon);
                ?>
                <li><a><?php echo $image->display($icon, false, "", '', '', 25, 25); ?><?php echo $category->detail->category_name ?></a></li>
            <?php } ?>
        </ul>

        <!-- Content container -->
        <div>
            <?php foreach ($list_category_product as $category) { ?>
                <div>
                    <?php
                    $list_product = $category->list;
                    $list_sub_category_detail = $category->list_sub_category_detail;
                    $list_small_product = $category->list_small_product;
                    ?>
                    <?php if ($style == 'table') { ?>
                        <?php
                        $column = $params->get('total_table_column', 4);
                        $list_chunk_product = array_chunk($list_product, $column);

                        ?>
                        <?php foreach ($list_chunk_product AS $list_product) { ?>
                            <div class="row-fluid">
                                <?php foreach ($list_product AS $product) { ?>
                                    <?php
                                    $list_image = $product->list_image;
                                    $list_image = explode(';', $list_image);
                                    $fist_image = reset($list_image);
                                    $link = hikashop_contentLink('product&task=show&cid=' . $product->product_id);
                                    ?>
                                    <div class="span<?php echo 12 / $column ?>">
                                        <div class="item">

                                            <img class="image img-responsive"
                                                 src="<?php echo JUri::root() ?>images/com_hikashop/upload/<?php echo $fist_image ?>">
                                            <div class="title"><a title="<?php echo $product->product_name ?>"
                                                                  href="<?php echo $link ?>"><?php echo $product->product_name ?></a>
                                            </div>
                                            <div
                                                class="price"><?php echo $currencyHelper->format($product->price_value, $mainCurr); ?></div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                        <?php } ?>
                    <?php } elseif ($style == 'slider') { ?>
                        <?php
                        $total_item_on_slide_screen = $params->get('total_item_on_slide_screen', 4);
                        $list_chunk_product = array_chunk($list_product, $total_item_on_slide_screen);
                        $total_column_sub_category=7;
                        ?>
                        <div class="wrapper-content ">
                            <div class="wrapper-content-left pull-left" style="width: <?php echo count($list_sub_category_detail)>$total_column_sub_category?'90%':'100%' ?> ">

                                <div class="row-fluid">
                                    <div class="span12">
                                        <div class="list-sub-category">
                                            <ul>
                                                <?php for($i=0;$i<count($list_sub_category_detail);$i++){ ?>
                                                    <?php

                                                    if($i>$total_column_sub_category-1)
                                                    {
                                                        break;
                                                    }
                                                    $sub_category=$list_sub_category_detail[$i];
                                                    $list_icon=explode(',',$sub_category->list_icon);

                                                    $first_icon=reset($list_icon);

                                                    ?>
                                                <li><a class="sub-category" data-category_id="<?php echo $sub_category->category_id ?>" href="javascript:void(0)"><img class="icon img-responsive" src="<?php echo JUri::root() ?>images/com_hikashop/upload/<?php echo $first_icon ?>"><div class="category-name"><?php echo JString::sub_string($sub_category->category_name,20) ?></div></a></li>
                                                <?php } ?>
                                            </ul>
                                        </div>
                                    </div>
                                </div>
                                <div class="row-fluid">
                                    <div class="span12">
                                        <div class="product_slide ">
                                            <div class="control pre"><i class="icon_vg40 icon_vg40_control_left"></i></div>
                                            <div class="control next"><i class="icon_vg40 icon_vg40_control_right"></i></div>
                                            <div class="row-fluid list-product">
                                                <?php foreach ($list_product AS $product) { ?>
                                                    <?php
                                                    $list_image = $product->list_image;
                                                    $list_image = explode(';', $list_image);
                                                    $fist_image = reset($list_image);
                                                    //$link = hikashop_contentLink('product&task=show&cid=' . $product->product_id);
                                                    $link='';
                                                    ?>

                                                    <div class="slide item item-<?php echo $product->product_id ?> span4">
                                                        <img class="image  img-thumbnail img-responsive"
                                                             src="<?php echo JUri::root() ?>images/com_hikashop/upload/<?php echo $fist_image ?>">
                                                        <div class="product-name"><a title="<?php echo $product->product_name ?>"
                                                                              href="<?php echo $link ?>"><?php echo JString::sub_string($product->product_name,30) ?> </a>
                                                        </div>
                                                        <div
                                                            class="price"><?php echo $currencyHelper->format($product->price_value, $mainCurr); ?></div>
                                                    </div>
                                                <?php } ?>
                                            </div>

                                        </div>
                                    </div>
                                </div>
                                <div class="row-fluid">
                                    <div class="span12">

                                        <?php foreach($list_small_product as $small_product){ ?>
                                            <?php
                                            $list_image = $small_product->list_image;
                                            $list_image = explode(';', $list_image);
                                            $fist_image = reset($list_image);
                                            $link = hikashop_contentLink('product&task=show&cid=' . $small_product->product_id);

                                            ?>
                                            <div class="small-product pull-left">

                                                <img class="image  img-thumbnail img-responsive"
                                                     src="<?php echo JUri::root() ?>images/com_hikashop/upload/<?php echo $fist_image ?>">
                                                <div class="product-name"><a title="<?php echo $small_product->product_name ?>"
                                                                             href="<?php echo $link ?>"><?php echo JString::sub_string($small_product->product_name,25) ?> </a>
                                                </div>
                                                <div
                                                    class="price"><?php echo $currencyHelper->format($small_product->price_value, $mainCurr); ?></div>


                                            </div>
                                        <?php } ?>
                                    </div>
                                </div>

                            </div>
                            <?php if(count($list_sub_category_detail)>$total_column_sub_category){ ?>
                                <div class="wrapper-content-right pull-left" style="width: 10%" >
                                    <div class="list-sub-category vertical">
                                        <ul class="vertical">
                                            <?php for($i=$total_column_sub_category;$i<count($list_sub_category_detail);$i++){ ?>
                                                <?php
                                                $sub_category=$list_sub_category_detail[$i];
                                                $list_icon=explode(',',$sub_category->list_icon);

                                                $first_icon=reset($list_icon);

                                                ?>
                                                <li><a class="sub-category" data-category_id="<?php echo $sub_category->category_id ?>" href="javascript:void(0)"><img class="icon img-responsive" src="<?php echo JUri::root() ?>images/com_hikashop/upload/<?php echo $first_icon ?>"><div class="category-name"><?php echo JString::sub_string($sub_category->category_name,20) ?></div></a></li>
                                            <?php } ?>
                                        </ul>
                                    </div>

                                </div>
                            <?php } ?>
                        </div>
                    <?php } ?>


                </div>
            <?php } ?>
        </div>
    </div>
</div>

<?php

$js_content = '';
$doc = JFactory::getDocument();
ob_start();
?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $("#mod_tab_products_<?php echo $module->id ?>").mod_tab_products({
            module_id:<?php echo $module->id   ?>,
            style: "<?php echo $style ?>",
            params:<?php echo json_encode($params->toObject()) ?>
        });


    });
</script>
<?php
$js_content = ob_get_clean();
$js_content = JUtility::remove_string_javascript($js_content);
$doc->addScriptDeclaration($js_content);

?>






