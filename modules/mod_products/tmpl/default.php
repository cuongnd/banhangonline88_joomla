<?php
$doc = JFactory::getDocument();
JHtml::_('jQuery.lazyload');
$doc->addLessStyleSheet(JUri::root() . 'modules/mod_products/assets/less/style.less');
$doc->addLessStyleSheet(JUri::root() . 'media/system/js/slick-master/slick/slick-theme.less');
$doc->addStyleSheet(JUri::root() . 'media/system/js/slick-master/slick/slick.css');
$doc->addScript(JUri::root() . 'media/system/js/slick-master/slick/slick.js');
JHtml::_('jQuery.utility');
$doc->addScript(JUri::root() . 'modules/mod_products/assets/js/mod_products.js');
defined('_JEXEC') or die;
require_once JPATH_ROOT . DS . 'administrator/components/com_hikashop/helpers/helper.php';
$style = $params->get('product_style', 'table');
$currencyHelper = hikashop_get('class.currency');
$mainCurr = $currencyHelper->mainCurrency();

?>
<div id="mod_products_<?php echo $module->id ?>" class="mod_products">
    <?php if ($style == 'table') { ?>
        <?php
        $column = $params->get('total_table_column', 4);
        $list_chunk_product = array_chunk($list_product, $column);

        ?>
        <?php foreach ($list_chunk_product AS $list_product) { ?>
            <div class="row">
                <?php foreach ($list_product AS $product) { ?>
                    <?php
                    $list_image = $product->list_image;
                    $list_image = explode(';', $list_image);
                    $fist_image = reset($list_image);
                    $link = hikashop_contentLink('product&task=show&cid=' . $product->product_id);
                    ?>
                    <div class="span<?php echo 12 / $column ?>">
                        <div class="item">
                            <div class="title"><a title="<?php echo $product->product_name ?>"
                                                  href="<?php echo $link ?>"><?php echo $product->product_name ?></a>
                            </div>
                            <img class="image img-responsive"
                                 data-src="<?php echo JUri::root() ?>images/com_hikashop/upload/<?php echo $fist_image ?>">
                            <div
                                class="price"><?php echo $currencyHelper->format($product->price_value, $mainCurr); ?></div>
                        </div>
                    </div>
                <?php } ?>
            </div>
        <?php } ?>
    <?php } elseif ($style == 'slider') { ?>
        <?php

        ?>
        <div class="row">
            <div class="span12">
                <div class="product_slide">
                    <?php foreach ($list_product AS $product) { ?>
                        <?php
                        $list_image = $product->list_image;
                        $list_image = explode(';', $list_image);
                        $fist_image = reset($list_image);
                        $link = hikashop_contentLink('product&task=show&cid=' . $product->product_id);
                        ?>
                        <div class="item">
                            <div class="title"><a title="<?php echo $product->product_name ?>"
                                                  href="<?php echo $link ?>"><?php echo $product->product_name ?></a>
                            </div>
                            <img class="image  img-thumbnail"
                                 data-src="<?php echo JUri::root() ?>images/com_hikashop/upload/<?php echo $fist_image ?>">
                            <div
                                class="price"><?php echo $currencyHelper->format($product->price_value, $mainCurr); ?></div>
                        </div>
                    <?php } ?>
                </div>
            </div>
        </div>
    <?php } ?>
</div>
<?php
$js_content = '';
$doc = JFactory::getDocument();
ob_start();
?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $("#mod_products_<?php echo $module->id ?>").mod_products({
            module_id:<?php echo $module->id   ?>,
            style: "<?php echo $style ?>"
        });


    });
</script>
<?php
$js_content = ob_get_clean();
$js_content = JUtility::remove_string_javascript($js_content);
$doc->addScriptDeclaration($js_content);

?>

