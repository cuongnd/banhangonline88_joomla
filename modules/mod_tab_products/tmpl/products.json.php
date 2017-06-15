<?php
$app = JFactory::getApplication();
$input = $app->input;
$post = json_decode(file_get_contents('php://input'));
$category_id = $post->category_id;
$params->set('categories', array($category_id));
$category = reset($modtab_productshelper->get_list_category_product($params, false));
$list_product = $category->list;
$debug=JUtility::get_debug();
$file_path = $category->detail->file_path;
$list_sub_category_detail = $category->list_sub_category_detail;
$list_small_product = $category->list_small_product;
$image = hikashop_get('helper.image');
$lazyload = false;
$cartHelper = hikashop_get('helper.cart');
$currencyHelper = hikashop_get('class.currency');
$style = $params->get('product_style', 'table');
$class_column_table = $params->get('class_column_table', 'col-lg-4 col-md-3');
$config =& hikashop_config();
$defaultParams = $config->get('default_params');
$temp = new \Joomla\Registry\Registry();
$defaultParams=$temp->loadObject($defaultParams);
$module_id = 0;
$url = '';
?>

<?php if ($style == 'table') {
    $list_list_product=array_chunk($list_product,3);
    ?>
    <!--col-lg col-md-->
    <?php foreach($list_list_product as $list_product){ ?>
        <div class="row ">
            <?php foreach ($list_product AS $product) { ?>
                <?php
                $list_image = $product->list_image;
                $list_image = explode(';', $list_image);
                $first_image = reset($list_image);
                $link = hikashop_contentLink('product&task=show&cid=' . $product->product_id);
                ?>
                <div
                    class="<?php echo $class_column_table ?>">
                    <div class="item">
                        <?php echo $image->display($first_image, false, "", 'class="image  img-responsive"', '', 200, 300, '', true); ?>
                        <div class="title">
                            <?php
                            $ajax = 'return hikashopModifyQuantity(\'' . $product->product_id . '\',field,1,0,\'cart\',' . $module_id . ')';
                            echo $cartHelper->displayButton(JText::_('ADD_TO_CART'), 'add', $defaultParams, $url, $ajax, '', 10, 1); ?>
                            <br/>
                            <a title="<?php echo $product->product_name ?>" class="link-product"
                               href="<?php echo $link ?>"><?php echo $product->product_name ?></a>
                        </div>
                        <div
                            class="price"><?php echo $currencyHelper->format($product->price_value, $mainCurr); ?></div>
                    </div>
                </div>
            <?php } ?>
        </div>
    <?php } ?>
    <!-- end col-lg col-md -->
<?php } elseif ($style == 'slider') { ?>
    <?php
    $total_item_on_slide_screen = $params->get('total_item_on_slide_screen', 4);
    $list_chunk_product = array_chunk($list_product, $total_item_on_slide_screen);
    ?>
    <div class="wrapper-content ">

        <div class="row">
            <div class="col-lg-12">

                <div class="list-sub-category">
                    <div class="row">
                        <button class="col-lg-1 backward pull-left"><i class="icon-chevron-left"></i></button>
                        <div class="col-lg-10 slider frame">
                            <ul class="sub-category-item slidee">
                                <?php for ($i = 0; $i < count($list_sub_category_detail); $i++) { ?>
                                    <?php

                                    $sub_category = $list_sub_category_detail[$i];
                                    $icon_file_path = $sub_category->category_icon_file_path;
                                    ?>
                                    <li><a class="sub-category"
                                           data-category_id="<?php echo $sub_category->category_id ?>"
                                           href="javascript:void(0)"><?php echo $image->display($icon_file_path, false, "", 'class="icon  img-responsive"', '', 40, 40, '', true); ?>
                                            <div
                                                class="category-name"><?php echo JString::sub_string($sub_category->category_name, 20) ?></div>
                                        </a></li>
                                <?php } ?>
                            </ul>
                        </div>
                        <button class="forward pull-left col-lg-1"><i class="icon-chevron-right"></i></button>
                    </div>

                </div>

            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="banner">
                    <?php echo $image->display($file_path, false, "", 'class="category_image img-responsive"', '', 980, 250, '', true); ?>
                </div>
                <div class="product_slide ">
                    <div class="control pre"><i
                            class="icon_vg40 icon_vg40_control_left"></i></div>
                    <div class="control next"><i
                            class="icon_vg40 icon_vg40_control_right"></i></div>
                    <div class="row list-product">
                        <?php
                        foreach ($list_product AS $product) { ?>
                            <?php
                            $list_image = $product->list_image;
                            $list_image = explode(';', $list_image);
                            $first_image = reset($list_image);
//$link = hikashop_contentLink('product&task=show&cid=' . $product->product_id);
                            $link = hikashop_contentLink('product&task=show&cid=' . $product->product_id);
                            ?>
                            <div
                                class="slide item item-<?php echo $product->product_id ?> col-xs-4 col-sm-4 col-md-4 col-lg-4">
                                <?php echo $image->display($first_image, false, "", 'class="image  img-responsive"', '', 200, 300, '', true); ?>
                                <?php
                                $ajax = 'return hikashopModifyQuantity(\'' . $product->product_id . '\',field,1,0,\'cart\',' . $module_id . ')';
                                echo $cartHelper->displayButton(JText::_('ADD_TO_CART'), 'add', $defaultParams, $url, $ajax, '', 10, 1); ?>
                                <div class="product-name"><a
                                        title="<?php echo $product->product_name ?>"
                                        href="<?php echo $link ?>"><?php echo JString::sub_string($product->product_name, 100) ?> </a>
                                </div>
                                <div
                                    class="price"><?php echo $currencyHelper->format($product->price_value, $mainCurr); ?></div>
                            </div>
                        <?php } ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-lg-12">
                <div class="list-small-product">
                    <?php
                    $total_column = 10;
                    $column = 5;
                    $list_chunk_product = array_chunk($list_small_product, $column);
                    foreach ($list_chunk_product as $small_product1) {
                        {
                            ?>
                            <div class="row hidden-xxxs hidden-xxs hidden-xs hidden-sm">
                                <?php
                                foreach ($small_product1 as $small_product) { ?>
                                    <?php
                                    $list_image = $small_product->list_image;
                                    $list_image = explode(';', $list_image);
                                    $first_image = reset($list_image);
                                    $second_image = $list_image[1];
                                    $link = hikashop_contentLink('product&task=show&cid=' . $small_product->product_id);
                                    ?>
                                    <div
                                        class="col-lg-<?php echo $total_column / $column ?> col-md-<?php echo $total_column / $column ?>">
                                        <div
                                            id="small-product_<?php echo $module->id ?>_<?php echo $small_product->product_id ?>"
                                            class="small-product ">
                                            <div
                                                id="flip_image_<?php echo $module->id ?>_<?php echo $small_product->product_id ?>"
                                                class="flip-image">
                                                <div class="front"><a
                                                        title="<?php echo $small_product->product_name ?>"
                                                        href="<?php echo $link ?>"><?php echo $image->display($first_image, false, "", 'class="image  img-responsive "', '', 200, 300, '', true); ?></a>
                                                </div>
                                                <div class="back"><a
                                                        title="<?php echo $small_product->product_name ?>"
                                                        href="<?php echo $link ?>"><?php echo $image->display($second_image, false, "", 'class="image  img-responsive "', '', 200, 300, '', true); ?></a>
                                                </div>
                                            </div>
                                            <?php
                                            $ajax = 'return hikashopModifyQuantity(\'' . $small_product->product_id . '\',field,1,0,\'cart\',' . $module_id . ')';
                                            echo $cartHelper->displayButton(JText::_('ADD_TO_CART'), 'add', $defaultParams, $url, $ajax, '', 10, 1); ?>

                                            <div class="product-name"><a
                                                    title="<?php echo $small_product->product_name ?>"
                                                    href="<?php echo $link ?>"><?php echo JString::sub_string($small_product->product_name, 100) ?> </a>
                                            </div>
                                            <div
                                                class="price"><?php echo $currencyHelper->format($small_product->price_value, $mainCurr); ?></div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php
                        }
                    } ?>

                    <?php
                    $total_column = 12;
                    $column = 4;
                    $list_chunk_product = array_chunk($list_small_product, $column);
                    foreach ($list_chunk_product as $small_product1) {
                        {
                            ?>
                            <div class="row hidden-lg hidden-md hidden-xs hidden-xxs hidden-xxxs">
                                <?php
                                foreach ($small_product1 as $small_product) { ?>
                                    <?php
                                    $list_image = $small_product->list_image;
                                    $list_image = explode(';', $list_image);
                                    $first_image = reset($list_image);
                                    $second_image = $list_image[1];
                                    $link = hikashop_contentLink('product&task=show&cid=' . $small_product->product_id);
                                    ?>
                                    <div class="col-sm-<?php echo $total_column / $column ?>">
                                        <div
                                            id="small-product_<?php echo $module->id ?>_<?php echo $small_product->product_id ?>"
                                            class="small-product ">
                                            <div
                                                id="flip_image_<?php echo $module->id ?>_<?php echo $small_product->product_id ?>"
                                                class="flip-image">
                                                <div class="front"><a
                                                        title="<?php echo $small_product->product_name ?>"
                                                        href="<?php echo $link ?>"><?php echo $image->display($first_image, false, "", 'class="image  img-responsive "', '', 200, 300, '', true); ?></a>
                                                </div>
                                                <div class="back"><a
                                                        title="<?php echo $small_product->product_name ?>"
                                                        href="<?php echo $link ?>"><?php echo $image->display($second_image, false, "", 'class="image  img-responsive "', '', 200, 300, '', true); ?></a>
                                                </div>
                                            </div>
                                            <?php
                                            $ajax = 'return hikashopModifyQuantity(\'' . $small_product->product_id . '\',field,1,0,\'cart\',' . $module_id . ')';
                                            echo $cartHelper->displayButton(JText::_('ADD_TO_CART'), 'add', $defaultParams, $url, $ajax, '', 10, 1); ?>
                                            <div class="product-name"><a
                                                    title="<?php echo $small_product->product_name ?>"
                                                    href="<?php echo $link ?>"><?php echo JString::sub_string($small_product->product_name, 100) ?> </a>
                                            </div>
                                            <div
                                                class="price"><?php echo $currencyHelper->format($small_product->price_value, $mainCurr); ?></div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php
                        }
                    } ?>
                    <?php
                    $total_column = 12;
                    $column = 3;
                    $list_chunk_product = array_chunk($list_small_product, $column);
                    foreach ($list_chunk_product as $small_product1) {
                        {
                            ?>
                            <div class="row hidden-lg hidden-md hidden-sm  hidden-xxs hidden-xxxs">
                                <?php
                                foreach ($small_product1 as $small_product) { ?>
                                    <?php
                                    $list_image = $small_product->list_image;
                                    $list_image = explode(';', $list_image);
                                    $first_image = reset($list_image);
                                    $second_image = $list_image[1];
                                    $link = hikashop_contentLink('product&task=show&cid=' . $small_product->product_id);
                                    ?>
                                    <div class="col-xs-<?php echo $total_column / $column ?>">
                                        <div
                                            id="small-product_<?php echo $module->id ?>_<?php echo $small_product->product_id ?>"
                                            class="small-product ">
                                            <div
                                                id="flip_image_<?php echo $module->id ?>_<?php echo $small_product->product_id ?>"
                                                class="flip-image">
                                                <div class="front"><a
                                                        title="<?php echo $small_product->product_name ?>"
                                                        href="<?php echo $link ?>"><?php echo $image->display($first_image, false, "", 'class="image  img-responsive "', '', 200, 300, '', true); ?></a>
                                                </div>
                                                <div class="back"><a
                                                        title="<?php echo $small_product->product_name ?>"
                                                        href="<?php echo $link ?>"><?php echo $image->display($second_image, false, "", 'class="image  img-responsive "', '', 200, 300, '', true); ?></a>
                                                </div>
                                            </div>
                                            <div class="product-name"><a
                                                    title="<?php echo $small_product->product_name ?>"
                                                    href="<?php echo $link ?>"><?php echo JString::sub_string($small_product->product_name, 100) ?> </a>
                                            </div>
                                            <div
                                                class="price"><?php echo $currencyHelper->format($small_product->price_value, $mainCurr); ?></div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php
                        }
                    } ?>

                    <?php
                    $total_column = 12;
                    $column = 2;
                    $list_chunk_product = array_chunk($list_small_product, $column);
                    foreach ($list_chunk_product as $small_product1) {
                        {
                            ?>
                            <div class="row hidden-lg hidden-md hidden-sm">
                                <?php
                                foreach ($small_product1 as $small_product) { ?>
                                    <?php
                                    $list_image = $small_product->list_image;
                                    $list_image = explode(';', $list_image);
                                    $first_image = reset($list_image);
                                    $second_image = $list_image[1];
                                    $link = hikashop_contentLink('product&task=show&cid=' . $small_product->product_id);
                                    ?>
                                    <div class="col-xs-<?php echo $total_column / $column ?>">
                                        <div
                                            id="small-product_<?php echo $module->id ?>_<?php echo $small_product->product_id ?>"
                                            class="small-product ">
                                            <div
                                                id="flip_image_<?php echo $module->id ?>_<?php echo $small_product->product_id ?>"
                                                class="flip-image">
                                                <div class="front"><a
                                                        title="<?php echo $small_product->product_name ?>"
                                                        href="<?php echo $link ?>"><?php echo $image->display($first_image, false, "", 'class="image  img-responsive "', '', 200, 300, '', true); ?></a>
                                                </div>
                                                <div class="back"><a
                                                        title="<?php echo $small_product->product_name ?>"
                                                        href="<?php echo $link ?>"><?php echo $image->display($second_image, false, "", 'class="image  img-responsive "', '', 200, 300, '', true); ?></a>
                                                </div>
                                            </div>
                                            <div class="product-name"><a
                                                    title="<?php echo $small_product->product_name ?>"
                                                    href="<?php echo $link ?>"><?php echo JString::sub_string($small_product->product_name, 100) ?> </a>
                                            </div>
                                            <div
                                                class="price"><?php echo $currencyHelper->format($small_product->price_value, $mainCurr); ?></div>
                                        </div>
                                    </div>
                                <?php } ?>
                            </div>
                            <?php
                        }
                    } ?>
                </div>
            </div>
        </div>


    </div>
<?php } ?>
                    