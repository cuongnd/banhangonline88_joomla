<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
JHtml::_('jQuery.help_step');
JHtml::_('jQuery.auo_typing_text');
$doc = JFactory::getDocument();
$doc->addScript(JUri::root() . 'components/com_hikamarket/assets/js/view_productmarket_form.js');
?>
<script type="text/javascript">
    window.productMgr = {cpt: {}};
    window.hikashop.ready(function () {
        window.hikamarket.dlTitle('hikamarket_products_form');
    });
</script>

<div class="view-productmarket-form">
    <form action="<?php echo hikamarket::completeLink('product'); ?>" method="post" name="hikamarket_form"
          id="hikamarket_products_form" enctype="multipart/form-data">
        <?php if (hikamarket::acl('product/edit/variants')) { ?>
        <div id="hikamarket_product_edition_header"
             style="<?php if (empty($this->product->characteristics) || empty($this->product->product_id)) echo 'display:none;'; ?>">

            <?php
            if (!empty($this->product)) {
                $image = $this->imageHelper->getThumbnail(@$this->product->images[0]->file_path, array(50, 50), array('default' => true));
                if ($image->success)
                    $image_url = $image->url;
                else
                    $image_url = $image->path;
                unset($image);
                ?>
                <h3><img src="<?php echo $image_url; ?>" alt=""
                         style="vertical-align:middle;margin-right:5px;"/><?php echo $this->product->product_name; ?>
                </h3>
                <ul class="hikam_tabs" rel="tabs:hikamarket_product_edition_tab_">
                    <li class="active"><a href="#product" rel="tab:1"
                                          onclick="return window.hikamarket.switchTab(this);"><?php echo JText::_('PRODUCT'); ?></a>
                    </li>
                    <li><a href="#variants" rel="tab:2"
                           onclick="return window.hikamarket.switchTab(this);"><?php echo JText::_('VARIANTS'); ?><span
                                id="hikamarket_product_variant_label"></span></a></li>
                </ul>
                <div style="clear:both"></div>
                <?php
            }
            ?>
        </div>
        <div id="hikamarket_product_edition_tab_1">
            <?php } ?>
            <span class="hika_add_production_note"><?php echo JText::_('HIKA_ADD_PRODUCTION_NOTE'); ?></span>
            <table class="hikam_blocks">
                <tr>
                    <td colspan="2">
                        <h4 class="hika_production_main_information"><?php echo JText::_('HIKA_PRODUCTION_MAIN_INFORMATION') ?></h4>
                    </td>
                </tr>
                <tr>
                    <td class="hikam_block_l hikam_block_d30 images-library">
                        <?php
                        if (hikamarket::acl('product/edit/images')) {
                            echo $this->loadTemplate('image');
                        }
                        ?>

                    </td>
                    <td class="hikam_block_r">
                        <dl class="hikam_options">
                            <?php if (hikamarket::acl('product/edit/name')) { ?>
                                <dt class="hikamarket_product_name"><label><?php echo JText::_('HIKA_NAME'); ?></label>
                                </dt>
                                <dd class="hikamarket_product_name"><input type="text"
                                                                           name="data[product][product_name]"
                                                                           value="<?php echo $this->escape(@$this->product->product_name); ?>"/>
                                </dd>

                            <?php } else { ?>
                                <dt class="hikamarket_product_name"><label><?php echo JText::_('HIKA_NAME'); ?></label>
                                </dt>
                                <dd class="hikamarket_product_name"><?php echo @$this->product->product_name; ?></dd>
                            <?php }

                            if (hikamarket::acl('product/edit/code')) { ?>
                                <dt class="hikamarket_product_code">
                                    <label><?php echo JText::_('PRODUCT_CODE'); ?></label>
                                </dt>
                                <dd class="hikamarket_product_code"><input type="text"
                                                                           name="data[product][product_code]"
                                                                           value="<?php echo $this->escape(@$this->product->product_code); ?>"/>
                                </dd>
                            <?php }

                            if (hikamarket::acl('product/edit/quantity')) { ?>
                                <dt class="hikamarket_product_quantity">
                                    <label><?php echo JText::_('PRODUCT_QUANTITY'); ?></label></dt>
                                <dd class="hikamarket_product_quantity">
                                    <?php echo $this->quantityType->display('data[product][product_quantity]', @$this->product->product_quantity); ?>
                                </dd>
                            <?php }
                            if (@$this->product->product_type != 'variant' && hikamarket::acl('product/edit/category')) { ?>
                                <dt class="hikamarket_product_category">
                                    <label><?php echo JText::_('PRODUCT_CATEGORIES'); ?></label></dt>
                                <dd class="hikamarket_product_category"><?php
                                    $categories = null;
                                    if (!empty($this->product->categories))
                                        $categories = array_keys($this->product->categories);
                                    echo $this->nameboxType->display(
                                        'data[product][categories]',
                                        $categories,
                                        hikamarketNameboxType::NAMEBOX_MULTIPLE,
                                        'category',
                                        array(
                                            'delete' => true,
                                            'sort' => true,
                                            'root' => $this->vendorCategories,
                                            'default_text' => '<em>' . JText::_('HIKA_NONE') . '</em>',
                                        )
                                    );
                                    ?></dd>
                            <?php }
                            if (@$this->product->product_type != 'variant' && hikamarket::acl('product/edit/manufacturer')) { ?>
                                <dt class="hikamarket_product_manufacturer">
                                    <label><?php echo JText::_('MANUFACTURER'); ?></label></dt>
                                <dd class="hikamarket_product_manufacturer"><?php
                                    echo $this->nameboxType->display(
                                        'data[product][product_manufacturer_id]',
                                        (int)@$this->product->product_manufacturer_id,
                                        hikamarketNameboxType::NAMEBOX_SINGLE,
                                        'brand',
                                        array(
                                            'delete' => true,
                                            'default_text' => '<em>' . JText::_('HIKA_NONE') . '</em>',
                                        )
                                    );
                                    ?></dd>
                            <?php }

                            if (hikamarket::acl('product/edit/published')) { ?>
                                <dt class="hikamarket_product_published">
                                    <label><?php echo JText::_('HIKA_PUBLISHED'); ?></label></dt>
                                <dd class="hikamarket_product_published"><?php echo JHTML::_('hikaselect.booleanlist', "data[product][product_published]", '', @$this->product->product_published); ?></dd>
                            <?php }

                            if (hikamarket::acl('product/edit/translations')) {
                                if (!empty($this->product->translations) && !empty($this->product->product_id)) {
                                    ?>
                                    <dt class="hikamarket_product_translations">
                                        <label><?php echo JText::_('HIKA_TRANSLATIONS'); ?></label></dt>
                                    <dd class="hikamarket_product_translations"><?php
                                        foreach ($this->product->translations as $language_id => $translation) {
                                            $lngName = $this->translationHelper->getFlag($language_id);
                                            echo '<div class="hikamarket_multilang_button">' .
                                                $this->popup->display(
                                                    $lngName, strip_tags($lngName),
                                                    hikamarket::completeLink('product&task=edit_translation&product_id=' . @$this->product->product_id . '&language_id=' . $language_id, true),
                                                    'hikamarket_product_translation_' . $language_id,
                                                    760, 480, '', '', 'link'
                                                ) .
                                                '</div>';
                                        }
                                        ?></dd>
                                    <?php
                                }
                            }

                            if (hikamarket::level(1) && $this->vendor->vendor_id == 1 && hikamarket::acl('product/subvendor') && hikamarket::acl('product/edit/vendor')) {
                                ?>
                                <dt class="hikamarket_product_vendor">
                                    <label><?php echo JText::_('HIKA_VENDOR'); ?></label>
                                </dt>
                                <dd class="hikamarket_product_vendor"><?php
                                    echo $this->nameboxType->display(
                                        'data[product][product_vendor_id]',
                                        (int)@$this->product->product_vendor_id,
                                        hikamarketNameboxType::NAMEBOX_SINGLE,
                                        'vendor',
                                        array(
                                            'delete' => true,
                                            'default_text' => '<em>' . JText::_('HIKA_NONE') . '</em>'
                                        )
                                    );
                                    ?></dd>
                            <?php }
                            ?>
                        </dl>
                    </td>
                </tr>
                <?php
                if (hikamarket::acl('product/edit/short_description')) {
                    if (!$this->config->get('front_small_editor')) { ?>
                        <tr class="hikamarket_product_short_description">
                            <td colspan="2">
                                <label
                                    class="hikamarket_product_short_description_label"><?php echo JText::_('HIKA_SHORT_DESCRIPTION'); ?></label>
                                <?php echo $this->editor_short_description->display(); ?>
                                <div style="clear:both"></div>
                            </td>
                        </tr>
                    <?php } else { ?>
                        <tr>
                            <td colspan="2">
                                <dl class="hikam_options">
                                    <dt class="hikamarket_product_short_description">
                                        <label><?php echo JText::_('HIKA_SHORT_DESCRIPTION'); ?></label></dt>
                                    <dd class="hikamarket_product_short_description"><?php echo $this->editor_short_description->display(); ?>
                                        <div style="clear:both"></div>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                    <?php }
                }
                if (hikamarket::acl('product/edit/description')) {
                    if (!$this->config->get('front_small_editor')) { ?>
                        <tr class="hikamarket_product_description">
                            <td colspan="2">
                                <label
                                    class="hikamarket_product_description_label"><?php echo JText::_('HIKA_DESCRIPTION'); ?></label>
                                <?php echo $this->editor->display(); ?>
                                <div style="clear:both"></div>
                            </td>
                        </tr>
                    <?php } else { ?>
                        <tr>
                            <td colspan="2">
                                <dl class="hikam_options">
                                    <dt class="hikamarket_product_description">
                                        <label><?php echo JText::_('HIKA_DESCRIPTION'); ?></label></dt>
                                    <dd class="hikamarket_product_description"><?php echo $this->editor->display(); ?>
                                        <div style="clear:both"></div>
                                    </dd>
                                </dl>
                            </td>
                        </tr>
                    <?php }
                }
                ?>
                <tr>
                    <td colspan="2">
                        <h4 class="hika_production_seo"><?php echo JText::_('HIKA_PRODUCTION_SEO') ?></h4>
                    </td>
                </tr>

                <?php
                if (!isset($this->product->product_type) || in_array($this->product->product_type, array('main', 'waiting_approval'))) {
                    ?>
                    <tr>
                        <td colspan="2">
                            <dl class="hikam_options">
                                <?php
                                if (hikamarket::acl('product/edit/pagetitle')) { ?>
                                    <dt class="hikamarket_product_pagetitle">
                                        <label><?php echo JText::_('PAGE_TITLE'); ?></label></dt>
                                    <dd class="hikamarket_product_pagetitle"><input type="text" class="fullrow"
                                                                                    size="45"
                                                                                    name="data[product][product_page_title]"
                                                                                    value="<?php echo $this->escape(@$this->product->product_page_title); ?>"/>
                                    </dd>
                                    <?php
                                }

                                if (hikamarket::acl('product/edit/url')) { ?>
                                    <dt class="hikamarket_product_url"><label><?php echo JText::_('URL'); ?></label>
                                    </dt>
                                    <dd class="hikamarket_product_url"><input type="text" class="fullrow" size="45"
                                                                              name="data[product][product_url]"
                                                                              value="<?php echo $this->escape(@$this->product->product_url); ?>"/>
                                    </dd>
                                    <?php
                                }

                                if (hikamarket::acl('product/edit/metadescription')) { ?>
                                    <dt class="hikamarket_product_metadescription">
                                        <label><?php echo JText::_('PRODUCT_META_DESCRIPTION'); ?></label></dt>
                                    <dd class="hikamarket_product_metadescription"><textarea
                                            id="product_meta_description"
                                            class="fullrow" cols="35"
                                            rows="2"
                                            name="data[product][product_meta_description]"><?php echo $this->escape(@$this->product->product_meta_description); ?></textarea>
                                    </dd>
                                    <?php
                                }

                                if (hikamarket::acl('product/edit/keywords')) { ?>
                                    <dt class="hikamarket_product_keywords">
                                        <label><?php echo JText::_('PRODUCT_KEYWORDS'); ?></label></dt>
                                    <dd class="hikamarket_product_keywords"><textarea id="product_keywords"
                                                                                      class="fullrow"
                                                                                      cols="35" rows="2"
                                                                                      name="data[product][product_keywords]"><?php echo $this->escape(@$this->product->product_keywords); ?></textarea>
                                    </dd>
                                    <?php
                                }

                                if (hikamarket::acl('product/edit/alias')) { ?>
                                    <dt class="hikamarket_product_alias">
                                        <label><?php echo JText::_('HIKA_ALIAS'); ?></label></dt>
                                    <dd class="hikamarket_product_alias"><input type="text" class="fullrow" size="45"
                                                                                name="data[product][product_alias]"
                                                                                value="<?php echo $this->escape(@$this->product->product_alias); ?>"/>
                                    </dd>
                                    <?php
                                }

                                if (hikamarket::acl('product/edit/canonical')) { ?>
                                    <dt class="hikamarket_product_canonical">
                                        <label><?php echo JText::_('PRODUCT_CANONICAL'); ?></label></dt>
                                    <dd class="hikamarket_product_canonical"><input type="text" class="fullrow"
                                                                                    size="45"
                                                                                    name="data[product][product_canonical]"
                                                                                    value="<?php echo $this->escape(@$this->product->product_canonical); ?>"/>
                                    </dd>
                                    <?php
                                }

                                if (hikamarket::acl('product/edit/tags')) {
                                    $tagsHelper = hikamarket::get('shop.helper.tags');
                                    if (!empty($tagsHelper) && $tagsHelper->isCompatible()) { ?>
                                        <dt class="hikamarket_product_tags">
                                            <label><?php echo JText::_('JTAG'); ?></label>
                                        </dt>
                                        <dd class="hikamarket_product_tags"><?php
                                            $tags = $tagsHelper->loadTags('product', $this->product);
                                            echo $tagsHelper->renderInput($tags, array('name' => 'data[tags]', 'class' => 'inputbox'));
                                            ?></dd>
                                        <?php
                                    }
                                }
                                ?>

                                <?php
                                if (hikamarket::acl('product/edit/characteristics')) { ?>
                                    <dt class="hikamarket_product_characteristics">
                                        <label><?php echo JText::_('CHARACTERISTICS'); ?></label></dt>
                                    <dd class="hikamarket_product_characteristics"><?php
                                        echo $this->loadTemplate('characteristic');
                                        ?></dd>
                                    <?php
                                }

                                if (hikamarket::acl('product/edit/related')) { ?>
                                    <dt class="hikamarket_product_related">
                                        <label><?php echo JText::_('RELATED_PRODUCTS'); ?></label></dt>
                                    <dd class="hikamarket_product_related"><?php
                                        echo $this->nameboxType->display(
                                            'data[product][related]',
                                            @$this->product->related,
                                            hikamarketNameboxType::NAMEBOX_MULTIPLE,
                                            'product',
                                            array(
                                                'delete' => true,
                                                'sort' => true,
                                                'root' => $this->rootCategory,
                                                'allvendors' => (int)$this->config->get('related_all_vendors', 1),
                                                'default_text' => '<em>' . JText::_('HIKA_NONE') . '</em>',
                                            )
                                        );
                                        ?></dd>
                                    <?php
                                }

                                if (hikashop_level(1) && hikamarket::acl('product/edit/options')) { ?>
                                    <dt class="hikamarket_product_options">
                                        <label><?php echo JText::_('OPTIONS'); ?></label>
                                    </dt>
                                    <dd class="hikamarket_product_options"><?php
                                        echo $this->nameboxType->display(
                                            'data[product][options]',
                                            @$this->product->options,
                                            hikamarketNameboxType::NAMEBOX_MULTIPLE,
                                            'product',
                                            array(
                                                'delete' => true,
                                                'sort' => true,
                                                'root' => $this->rootCategory,
                                                'allvendors' => (int)$this->config->get('options_all_vendors', 0),
                                                'default_text' => '<em>' . JText::_('HIKA_NONE') . '</em>',
                                            )
                                        );
                                        ?></dd>
                                    <?php
                                }
                                ?>

                                <?php


                                if (hikamarket::acl('product/edit/tax')) { ?>
                                    <dt class="hikamarket_product_tax">
                                        <label><?php echo JText::_('TAXATION_CATEGORY'); ?></label></dt>
                                    <dd class="hikamarket_product_tax"><?php
                                        echo $this->categoryType->display('data[product][product_tax_id]', @$this->product->product_tax_id, 'tax');
                                        ?></dd>
                                    <?php
                                }
                                ?>
                            </dl>
                        </td>
                    </tr>


                    <?php
                }
                ?>
                <tr>
                    <td colspan="2">
                        <h4 class="hika_production_price"><?php echo JText::_('HIKA_PRODUCTION_PRICE') ?></h4>
                        <dl class="hikam_options">
                            <?php
                            if (hikamarket::acl('product/edit/price')) { ?>

                                <dt class="hikamarket_product_price"><label><?php echo JText::_('PRICES'); ?></label>
                                </dt>
                                <dd class="hikamarket_product_price">
                                    <div
                                        class="hika_production_price_note"><?php echo JText::_('HIKA_PRODUCTION_PRICE_NOTE') ?></div>
                                    <?php
                                    echo $this->loadTemplate('price');
                                    ?></dd>
                            <?php }

                            if (@$this->product->product_type != 'variant' && hikamarket::acl('product/edit/msrp')) {
                                $curr = '';
                                $mainCurr = $this->currencyClass->getCurrencies($this->main_currency_id, $curr);
                                ?>

                                <dt class="hikamarket_product_msrp">
                                    <label><?php echo JText::_('PRODUCT_MSRP'); ?></label>
                                </dt>
                                <dd class="hikamarket_product_msrp">
                                    <div
                                        class="hika_production_price_msrp"><?php echo JText::_('HIKA_PRODUCTION_PRICE_MSRP') ?></div>
                                    <input type="text" name="data[product][product_msrp]"
                                           value="<?php echo $this->escape(@$this->product->product_msrp); ?>"/> <?php echo $mainCurr[$this->main_currency_id]->currency_symbol . ' ' . $mainCurr[$this->main_currency_id]->currency_code; ?>
                                </dd>
                            <?php }

                            if (hikamarket::acl('product/edit/qtyperorder')) { ?>
                                <dt class="hikamarket_product_qtyperorder">
                                    <label><?php echo JText::_('QUANTITY_PER_ORDER'); ?></label></dt>
                                <dd class="hikamarket_product_qtyperorder">
                                    <input type="text" name="data[product][product_min_per_order]"
                                           value="<?php echo (int)@$this->product->product_min_per_order; ?>"/><?php
                                    echo ' ' . JText::_('HIKA_QTY_RANGE_TO') . ' ';
                                    echo $this->quantityType->display('data[product][product_max_per_order]', @$this->product->product_max_per_order);
                                    ?></dd>
                            <?php }

                            if (hikamarket::acl('product/edit/saledates')) { ?>
                                <dt class="hikamarket_product_salestart">
                                    <label><?php echo JText::_('PRODUCT_SALE_DATES'); ?></label></dt>
                                <dd class="hikamarket_product_salestart"><?php
                                    echo JHTML::_('calendar', hikamarket::getDate((@$this->product->product_sale_start ? @$this->product->product_sale_start : ''), '%Y-%m-%d %H:%M'), 'data[product][product_sale_start]', 'product_sale_start', '%Y-%m-%d %H:%M', array('size' => '20'));
                                    echo ' <span class="calendar-separator">' . JText::_('HIKA_RANGE_TO') . '</span> ';
                                    echo JHTML::_('calendar', hikamarket::getDate((@$this->product->product_sale_end ? @$this->product->product_sale_end : ''), '%Y-%m-%d %H:%M'), 'data[product][product_sale_end]', 'product_sale_end', '%Y-%m-%d %H:%M', array('size' => '20'));
                                    ?></dd>
                            <?php }

                            if (hikamarket::acl('product/edit/warehouse')) { ?>
                                <dt class="hikamarket_product_warehouse">
                                    <label><?php echo JText::_('WAREHOUSE'); ?></label>
                                </dt>
                                <dd class="hikamarket_product_warehouse"><?php
                                    echo $this->nameboxType->display(
                                        'data[product][product_warehouse_id]',
                                        (int)@$this->product->product_warehouse_id,
                                        hikamarketNameboxType::NAMEBOX_SINGLE,
                                        'warehouse',
                                        array(
                                            'delete' => true,
                                            'default_text' => '<em>' . JText::_('HIKA_NONE') . '</em>',
                                        )
                                    );
                                    ?></dd>
                            <?php }

                            if (hikamarket::acl('product/edit/weight')) { ?>
                                <dt class="hikamarket_product_weight">
                                    <label><?php echo JText::_('PRODUCT_WEIGHT'); ?></label></dt>
                                <dd class="hikamarket_product_weight"><input type="text"
                                                                             name="data[product][product_weight]"
                                                                             value="<?php echo $this->escape(@$this->product->product_weight); ?>"/><?php echo $this->weight->display('data[product][product_weight_unit]', @$this->product->product_weight_unit); ?>
                                </dd>
                            <?php }

                            if (hikamarket::acl('product/edit/volume')) { ?>
                                <dt class="hikamarket_product_volume">
                                    <label><?php echo JText::_('PRODUCT_VOLUME'); ?></label></dt>
                                <dd class="hikamarket_product_volume">
                                    <?php
                                    if (HIKASHOP_RESPONSIVE) {
                                        ?>
                                        <div class="input-prepend">
							<span class="add-on"><?php
                                echo hikamarket::tooltip(JText::_('PRODUCT_LENGTH'), '', '', '<i class="hk-icon-14 iconM-14-length"></i>', '', 0)
                                ?></span>
                                            <input size="10" style="width:50px" type="text"
                                                   name="data[product][product_length]"
                                                   value="<?php echo $this->escape(@$this->product->product_length); ?>"/>
                                        </div>
                                        <div class="input-prepend">
							<span class="add-on"><?php
                                echo hikamarket::tooltip(JText::_('PRODUCT_WIDTH'), '', '', '<i class="hk-icon-14 iconM-14-width"></i>', '', 0);
                                ?></span>
                                            <input size="10" style="width:50px" type="text"
                                                   name="data[product][product_width]"
                                                   value="<?php echo $this->escape(@$this->product->product_width); ?>"/>
                                        </div>
                                        <div class="input-prepend">
							<span class="add-on"><?php
                                echo hikamarket::tooltip(JText::_('PRODUCT_HEIGHT'), '', '', '<i class="hk-icon-14 iconM-14-height"></i>', '', 0);
                                ?></span>
                                            <input size="10" style="width:50px" type="text"
                                                   name="data[product][product_height]"
                                                   value="<?php echo $this->escape(@$this->product->product_height); ?>"/>
                                        </div>
                                        <?php echo $this->volume->display('data[product][product_dimension_unit]', @$this->product->product_dimension_unit); ?>
                                        <?php
                                    } else {
                                        ?>
                                        <label><?php echo JText::_('PRODUCT_LENGTH'); ?></label>
                                        <input size="10" type="text" name="data[product][product_length]"
                                               value="<?php echo $this->escape(@$this->product->product_length); ?>"/>
                                        <br/>
                                        <label><?php echo JText::_('PRODUCT_WIDTH'); ?></label>
                                        <input size="10" type="text" name="data[product][product_width]"
                                               value="<?php echo $this->escape(@$this->product->product_width); ?>"/><?php echo $this->volume->display('data[product][product_dimension_unit]', @$this->product->product_dimension_unit); ?>
                                        <br/>
                                        <label><?php echo JText::_('PRODUCT_HEIGHT'); ?></label>
                                        <input size="10" type="text" name="data[product][product_height]"
                                               value="<?php echo $this->escape(@$this->product->product_height); ?>"/>
                                        <?php
                                    }
                                    ?>
                                </dd>
                            <?php }

                            if (hikamarket::acl('product/edit/customfields')) {
                            if (!empty($this->fields)) {
                            ?>
                        </dl>
                        <div style="clear:both"></div>
                        <?php
                        foreach ($this->fields as $fieldName => $oneExtraField) {
                            ?>
                            <dl id="hikashop_product_<?php echo $fieldName; ?>" class="hikam_options">
                                <dt class="hikamarket_product_<?php echo $fieldName; ?>"><?php echo $this->fieldsClass->getFieldName($oneExtraField); ?></dt>
                                <dd class="hikamarket_product_<?php echo $fieldName; ?>">
                                    <?php echo $this->fieldsClass->getFieldDescription($oneExtraField); ?>
                                    <?php
                                    $onWhat = 'onchange';
                                    if ($oneExtraField->field_type == 'radio')
                                        $onWhat = 'onclick';
                                    echo $this->fieldsClass->display($oneExtraField, $this->product->$fieldName, 'data[product][' . $fieldName . ']', false, ' ' . $onWhat . '="hikashopToggleFields(this.value,\'' . $fieldName . '\',\'product\',0);"');
                                    ?></dd>
                            </dl>
                            <?php
                        }
                        ?>
                        <dl class="hikam_options">
                            <?php
                            }
                            }

                            if (hikamarket::acl('product/edit/acl') && hikashop_level(2)) { ?>
                                <dt class="hikamarket_product_acl">
                                    <label><?php echo JText::_('ACCESS_LEVEL'); ?></label>
                                </dt>
                                <dd class="hikamarket_product_acl"><?php
                                    $product_access = 'all';
                                    if (isset($this->product->product_access))
                                        $product_access = $this->product->product_access;
                                    echo $this->joomlaAcl->display('data[product][product_access]', $product_access, true, true);
                                    ?></dd>
                            <?php }

                            if (hikamarket::acl('product/edit/plugin')) {
                                $html = array();
                                JPluginHelper::importPlugin('hikashop');
                                JPluginHelper::importPlugin('hikamarket');
                                $dispatcher = JDispatcher::getInstance();
                                $dispatcher->trigger('onMarketProductBlocksDisplay', array(&$this->product, &$html));

                                foreach ($html as $h) {
                                    echo $h;
                                }
                            }
                            ?>
                        </dl>
                        <div style="clear:both"></div>
                        <?php
                        if (hikamarket::acl('product/edit/files')) {
                            echo $this->loadTemplate('file');
                        }
                        ?>
                    </td>
                </tr>
            </table>
            <?php if (hikamarket::acl('product/edit/variants')) { ?>
        </div>
        <div id="hikamarket_product_edition_tab_2" style="display:none;">
            <div id="hikamarket_product_variant_list"><?php
                echo $this->loadTemplate('variants');
                ?></div>
            <div id="hikamarket_product_variant_edition">
            </div>
        </div>
    <?php } ?>
        <?php if (!empty($this->product->product_type) && $this->product->product_type == 'variant' && !empty($this->product->product_parent_id)) { ?>
            <input type="hidden" name="data[product][product_type]"
                   value="<?php echo $this->product->product_type; ?>"/>
            <input type="hidden" name="data[product][product_parent_id]"
                   value="<?php echo (int)$this->product->product_parent_id; ?>"/>
        <?php } ?>
        <input type="hidden" name="cancel_action" value="<?php echo @$this->cancel_action; ?>"/>
        <input type="hidden" name="cancel_url" value="<?php echo @$this->cancel_url; ?>"/>
        <input type="hidden" name="cid[]" value="<?php echo @$this->product->product_id; ?>"/>
        <input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>"/>
        <input type="hidden" name="task" value=""/>
        <input type="hidden" name="ctrl" value="product"/>
        <?php echo JHTML::_('form.token'); ?>
    </form>
</div>
<?php
$js_content = '';
$doc = JFactory::getDocument();
ob_start();
?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $("body.productmarket").view_productmarket_form({

            });
        });
    </script>
<?php
$js_content = ob_get_clean();
$js_content = JUtility::remove_string_javascript($js_content);
$doc->addScriptDeclaration($js_content);
?>