<?php // no direct access
defined('_JEXEC') or die('Restricted access');

//dump ($cart,'mod cart');
// Ajax is displayed in vm_cart_products
// ALL THE DISPLAY IS Done by Ajax using "hiddencontainer"
if (!class_exists('CurrencyDisplay')) require(VMPATH_ADMIN .'/helpers/currencydisplay.php');
$currency = CurrencyDisplay::getInstance( );

?>

<!-- Virtuemart 2 Ajax Card -->
<div id="vmCartModule<?php echo $params->get('moduleid_sfx'); ?>" class="vmCartModule <?php echo $params->get('moduleclass_sfx'); ?>">

    <div class="megadeal-vm-cart-icon-wrapper">
        <i class="spvm-cart-toggle megadeal-icon-cart">
            <span class="spvm-total-product">
                <?php echo  $data->totalProduct; ?>
            </span>
        </i>
        
        <?php if ($data->totalProduct && $show_price && $currencyDisplay->_priceConfig['salesPrice'][0]) { ?>
            <span class="spvm-cart-total-bill"><?php echo $data->billTotal_discounted_net; ?></span>
        <?php }else{
            echo $currency->createPriceDiv ('-empty', FALSE, '00.00', FALSE, FALSE, 1.0, FALSE);
        } ?>
    </div>

    <div class="megadeal-vm-carts-product-wrapper">  

    <?php if ($show_product_list) { ?>

        <div class="hiddencontainer" style=" display: none; ">
            <div class="vmcontainer">
                <div class="product_row">
                    <span class="quantity"></span>&nbsp;x&nbsp;<span class="product_name"></span>

                <?php if ($show_price and $currencyDisplay->_priceConfig['salesPrice'][0]) { ?>
                    <div class="subtotal_with_tax" style="float: right;"></div>
                <?php } ?>
                <div class="customProductData"></div><br>
                </div>
            </div>
        </div>
        <div class="vm_cart_products">
            <div class="vmcontainer">
                <?php foreach ($data->products as $product){ ?>
                    <div class="product_row">
                        <span class="quantity">
                            <?php echo $product['quantity']; ?>
                        </span>&nbsp;x&nbsp;
                        <span class="product_name">
                            <?php echo $product['product_name']; ?>
                        </span>
                        <?php if ($show_price and $currencyDisplay->_priceConfig['salesPrice'][0]) { ?>
                            <div class="subtotal_with_tax" style="float: right;">
                                <?php echo $product['subtotal_with_tax'] ?>
                            </div>
                        <?php } ?>
                        <?php if ( !empty($product['customProductData']) ) { ?>
                        <div class="customProductData">
                            <?php echo $product['customProductData'] ?>
                        </div>
                        <?php } ?>
                            
                    </div>
                <?php } ?>
            </div>
        </div>
    <?php } ?>

    <div class="vm-cart-total-wrapper">
        <div class="total">
            <?php if ($data->totalProduct && $show_price && $currencyDisplay->_priceConfig['salesPrice'][0]) { ?>
            <?php echo $data->billTotal; ?>
            <?php } ?>
        </div>
        <div class="total_products"><?php echo  $data->totalProductTxt ?></div>
    </div> <!-- /.vm-cart-total-wrapper -->

    <div class="show-cart">
        <?php if ($data->totalProduct){?>
            <?php echo $data->cart_show; ?>
        <?php } ?>
    </div>

    <div style="clear:both;"></div>
        <div class="payments-signin-button" ></div>
    <noscript>
        <?php echo vmText::_('MOD_VIRTUEMART_CART_AJAX_CART_PLZ_JAVASCRIPT') ?>
    </noscript>
    </div>
</div>
