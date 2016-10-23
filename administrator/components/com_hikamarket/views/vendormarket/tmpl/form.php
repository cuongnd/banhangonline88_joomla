<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php if (!isset($this->vendor->vendor_id) || $this->vendor->vendor_id > 1) { ?>
<div class="iframedoc" id="iframedoc"></div>
<div id="hikashop_backend_tile_edition">
    <?php if((isset($this->vendor->vendor_id) && $this->vendor->vendor_id > 1) || (!isset($this->vendor->vendor_id) && hikamarket::level(1))) { ?>
    <div id="hikamarket_vendor_edition_header">
        <ul class="hika_tabs" rel="tabs:hikamarket_product_edition_tab_">
            <li class="active"><a href="#vendor" rel="tab:1"
                                  onclick="return window.hikashop.switchTab(this);"><?php echo JText::_('HIKA_VENDOR'); ?></a>
            </li>
            <?php if (hikamarket::level(1)) { ?>
                <li><a href="#acl" rel="tab:2"
                       onclick="return window.hikashop.switchTab(this);"><?php echo JText::_('ACL'); ?></a></li>
            <?php } ?>
            <li><a href="#stats" rel="tab:3"
                   onclick="return window.hikashop.switchTab(this);"><?php echo JText::_('STATISTICS'); ?></a></li>
        </ul>
        <div style="clear:both"></div>
        <?php
        }
        ?>
    </div>
    <form action="<?php echo hikamarket::completeLink('vendor'); ?>" method="post" name="adminForm" id="adminForm"
          enctype="multipart/form-data">
        <?php } else { ?>
        <div id="hikashop_backend_tile_edition">
            <?php } ?>

            <script type="text/javascript">
                window.vendorMgr = {cpt: {}};
                window.hikashop.ready(function () {
                    window.hikashop.dlTitle('adminForm');
                });
            </script>

            <?php if (isset($this->vendor->vendor_id) && $this->vendor->vendor_id > 1) { ?>
            <!-- Product edition : main tab -->
            <div id="hikamarket_product_edition_tab_1">
                <?php } ?>
                <div class="hk-container-fluid">

                    <div class="hkc-xl-4 hkc-lg-6 hikashop_tile_block">
                        <div>
                            <div class="hikashop_tile_title"><?php
                                echo JText::_('MAIN_INFORMATION');
                                ?></div>
                            <dl class="hika_options">

                                <dt class="hikamarket_vendor_name"><label
                                        for="data[vendor][vendor_name]"><?php echo JText::_('HIKA_NAME'); ?></label>
                                </dt>
                                <dd class="hikamarket_vendor_name input_large">
                                    <input type="text" name="data[vendor][vendor_name]" id="data[vendor][vendor_name]"
                                           value="<?php echo $this->escape(@$this->vendor->vendor_name); ?>"/>
                                </dd>

                                <dt class="hikamarket_vendor_email"><label
                                        for="data[vendor][vendor_email]"><?php echo JText::_('HIKA_EMAIL'); ?></label>
                                </dt>
                                <dd class="hikamarket_vendor_email input_large">
                                    <input type="text" name="data[vendor][vendor_email]" id="data[vendor][vendor_email]"
                                           value="<?php echo $this->escape(@$this->vendor->vendor_email); ?>"/>
                                </dd>

                                <?php
                                if (!isset($this->vendor->vendor_id) || $this->vendor->vendor_id > 1) {
                                    ?>
                                    <dt class="hikamarket_vendor_admin"><label
                                            for="data_vendor_vendor_admin_id_text"><?php echo JText::_('HIKA_ADMINISTRATOR'); ?></label>
                                    </dt>
                                    <dd class="hikamarket_vendor_admin"><?php
                                        echo $this->nameboxType->display(
                                            'data[vendor][vendor_admin_id]',
                                            @$this->vendor_admin->user_id,
                                            hikamarketNameboxType::NAMEBOX_SINGLE,
                                            'user',
                                            array(
                                                'delete' => true,
                                                'default_text' => '<em>' . JText::_('HIKA_NONE') . '</em>',
                                            )
                                        );
                                        ?></dd>

                                    <dt class="hikamarket_vendor_published"><label
                                            for="data[vendor][vendor_published]"><?php echo JText::_('HIKA_PUBLISHED'); ?></label>
                                    </dt>
                                    <dd class="hikamarket_vendor_published"><?php
                                        echo JHTML::_('hikaselect.booleanlist', 'data[vendor][vendor_published]', '', @$this->vendor->vendor_published);
                                        ?></dd>

                                    <dt class="hikamarket_vendor_currency"><label
                                            for="datavendorvendor_currency_id"><?php echo JText::_('CURRENCY'); ?></label>
                                    </dt>
                                    <dd class="hikamarket_vendor_currency"><?php
                                        echo $this->currencyType->display("data[vendor][vendor_currency_id]", @$this->vendor->vendor_currency_id);
                                        ?></dd>

                                    <?php if ($this->config->get('allow_zone_vendor', 0)) { ?>
                                        <dt class="hikamarket_vendor_zone"><label
                                                for="data_vendor_vendor_zone_text"><?php echo JText::_('ZONE'); ?></label>
                                        </dt>
                                        <dd><?php
                                            echo $this->nameboxType->display(
                                                'data[vendor][vendor_zone_id]',
                                                @$this->vendor->vendor_zone_namekey,
                                                hikamarketNameboxType::NAMEBOX_SINGLE,
                                                'zone',
                                                array(
                                                    'delete' => true,
                                                    'default_text' => '<em>' . JText::_('HIKA_NONE') . '</em>'
                                                )
                                            );
                                            ?></dd>
                                    <?php } ?>

                                    <?php
                                    if (file_exists(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_multisites' . DS . 'helpers' . DS . 'utils.php')) {
                                        include_once(JPATH_ADMINISTRATOR . DS . 'components' . DS . 'com_multisites' . DS . 'helpers' . DS . 'utils.php');
                                        if (class_exists('MultisitesHelperUtils') && method_exists('MultisitesHelperUtils', 'getComboSiteIDs')) {
                                            $comboSiteIDs = MultisitesHelperUtils::getComboSiteIDs(@$this->vendor->vendor_site_id, 'data[vendor][vendor_site_id]', JText::_('SELECT_A_SITE'));
                                            if (!empty($comboSiteIDs)) {
                                                ?>
                                                <dt class="hikamarket_vendor_siteid"><?php echo JText::_('SITE_ID'); ?></dt>
                                                <dd class="hikamarket_vendor_siteid"><?php echo $comboSiteIDs; ?></dd>
                                                <?php
                                            }
                                        }
                                    }
                                    ?>

                                    <?php
                                } // Vendor_id > 1
                                ?>

                                <dt class="hikamarket_vendor_templateid"><label
                                        for="data_vendor_vendor_template_id_text"><?php echo JText::_('VENDOR_PRODUCT_TEMPLATE'); ?></label>
                                </dt>
                                <dd class="hikamarket_vendor_templateid"><?php
                                    echo $this->nameboxType->display(
                                        'data[vendor][vendor_template_id]',
                                        @$this->vendor->vendor_template_id,
                                        hikamarketNameboxType::NAMEBOX_SINGLE,
                                        'product_template',
                                        array(
                                            'delete' => true,
                                            'default_text' => '<em>' . JText::_('HIKA_NONE') . '</em>',
                                        )
                                    );
                                    ?></dd>

                                <dt><label><?php echo JText::_('HIKAM_VENDOR_IMAGE'); ?></label></dt>
                                <dd>
                                    <?php
                                    $options = array(
                                        'upload' => true,
                                        'gallery' => true,
                                        'upload_base_url' => 'index.php?option=com_hikamarket&ctrl=upload',
                                        'text' => JText::_('HIKAM_VENDOR_IMAGE_EMPTY_UPLOAD'),
                                        'uploader' => array('vendor', 'vendor_image'),
                                        'vars' => array('vendor_id' => (int)@$this->vendor->vendor_id)
                                    );

                                    $content = '';
                                    if (!empty($this->vendor->vendor_image)) {
                                        $params = new stdClass();
                                        $params->file_path = @$this->vendor->vendor_image;
                                        $params->field_name = 'data[vendor][vendor_image]';
                                        $params->uploader_id = 'hikamarket_vendor_image';
                                        $params->delete = true;
                                        $js = '';
                                        $content = hikamarket::getLayout('uploadmarket', 'image_entry', $params, $js);
                                    }

                                    echo $this->uploaderType->displayImageSingle('hikamarket_vendor_image', $content, $options);
                                    ?>
                                    <input type="hidden" value="1" name="data_vendor_image"/>
                                </dd>

                                <dt class="hikamarket_vendor_alias"><label
                                        for="data[vendor][vendor_alias]"><?php echo JText::_('HIKA_ALIAS'); ?></label>
                                </dt>
                                <dd class="hikamarket_vendor_alias input_large">
                                    <input type="text" name="data[vendor][vendor_alias]" id="data[vendor][vendor_alias]"
                                           value="<?php echo $this->escape(@$this->vendor->vendor_alias); ?>"/>
                                </dd>

                            </dl>
                        </div>
                    </div>

                    <?php
                    if (!empty($this->extraFields['vendor'])) {
                        ?>
                        <div class="hkc-xl-4 hkc-lg-6 hikashop_tile_block">
                            <div>
                                <div class="hikashop_tile_title"><?php
                                    echo JText::_('FIELDS');
                                    ?></div>
                                <dl id="hikamarket_vendor_fields" class="hika_options">
                                    <?php
                                    foreach ($this->extraFields['vendor'] as $fieldName => $oneExtraField) {
                                        ?>
                                        <dl class="hika_options"
                                            id="hikamarket_vendor_<?php echo $oneExtraField->field_namekey; ?>"
                                            style="margin:0;padding:0;">
                                            <dt class="hikamarket_vendor_<?php echo $fieldName; ?>"><label
                                                    for="<?php echo $fieldName; ?>"><?php
                                                    echo $this->fieldsClass->getFieldName($oneExtraField);
                                                    if (!empty($oneExtraField->field_required))
                                                        echo ' *';
                                                    ?></label></dt>
                                            <dd class="hikamarket_vendor_<?php echo $fieldName; ?>"><?php
                                                $onWhat = 'onchange';
                                                if ($oneExtraField->field_type == 'radio')
                                                    $onWhat = 'onclick';
                                                $oneExtraField->field_required = false;
                                                echo $this->fieldsClass->display(
                                                    $oneExtraField,
                                                    @$this->vendor->$fieldName,
                                                    'data[vendor][' . $fieldName . ']',
                                                    false,
                                                    ' ' . $onWhat . '="hikashopToggleFields(this.value,\'' . $fieldName . '\',\'vendor\',0,\'hikamarket_\');"',
                                                    false,
                                                    $this->extraFields['vendor'],
                                                    $this->vendor
                                                );
                                                ?></dd>
                                        </dl>
                                        <?php
                                    }
                                    ?>
                                </dl>
                            </div>
                        </div>
                        <?php
                    }
                    ?>

                    <div class="hkc-xl-4 hkc-lg-6 hikashop_tile_block">
                        <div>
                            <div class="hikashop_tile_title"><?php
                                echo JText::_('HIKA_DESCRIPTION');
                                ?></div>
                            <?php
                            $this->editor->content = @$this->vendor->vendor_description;
                            $this->editor->name = 'vendor_description';
                            $ret = $this->editor->display();
                            if ($this->editor->editor == 'codemirror')
                                echo str_replace(array('(function() {' . "\n", '})()' . "\n"), array('window.hikashop.ready(function(){', '});'), $ret);
                            else
                                echo $ret;
                            ?>
                            <div style="clear:both"></div>
                        </div>
                    </div>

                    <div class="hkc-xl-clear"></div>

                    <div class="hkc-xl-4 hkc-lg-6 hikashop_tile_block">
                        <div>
                            <div class="hikashop_tile_title"><?php
                                echo JText::_('USERS');
                                ?></div>
                            <?php
                            $this->setLayout('users');
                            echo $this->loadTemplate();
                            ?>
                        </div>
                    </div>

                    <?php if (hikamarket::level(1) && (!isset($this->vendor->vendor_id) || $this->vendor->vendor_id > 1)) { ?>
                        <div class="hkc-xl-8 hkc-lg-6 hikashop_tile_block">
                            <div>
                                <div class="hikashop_tile_title"><?php
                                    echo JText::_('VENDOR_FEES');
                                    ?></div>
                                <?php
                                $this->setLayout('fees');
                                echo $this->loadTemplate();
                                ?>
                            </div>
                        </div>
                    <?php } ?>

                    <div class="hkc-xl-4 hkc-lg-6 hikashop_tile_block">
                        <div>
                            <div class="hikashop_tile_title"><?php
                                echo JText::_('VENDOR_OPTIONS');
                                ?></div>
                            <?php
                            $this->setLayout('options');
                            echo $this->loadTemplate();
                            ?>
                        </div>
                    </div>

                    <div class="hkc-xl-4 hkc-lg-6 hikashop_tile_block">
                        <div>
                            <div class="hikashop_tile_title"><?php
                                echo JText::_('HIKASHOP_CHECKOUT_TERMS');
                                ?></div>
                            <?php
                            $this->editor->content = @$this->vendor->vendor_terms;
                            $this->editor->name = 'vendor_terms';
                            $ret = $this->editor->display();
                            if ($this->editor->editor == 'codemirror')
                                echo str_replace(array('(function() {' . "\n", '})()' . "\n"), array('window.hikashop.ready(function(){', '});'), $ret);
                            else
                                echo $ret;
                            ?>
                            <div style="clear:both"></div>
                        </div>
                    </div>

                </div> <!-- container fluid -->
                <?php if (!isset($this->vendor->vendor_id) || $this->vendor->vendor_id > 1) { ?>
            </div>
        <?php } ?>

            <?php if (hikamarket::level(1) && (!isset($this->vendor->vendor_id) || $this->vendor->vendor_id > 1)) { ?>
                <div id="hikamarket_product_edition_tab_2" style="display:none;">
                    <div class="hk-container-fluid">

                        <div class="hkc-xl-4 hkc-lg-6 hikashop_tile_block">
                            <div>
                                <div class="hikashop_tile_title"><?php echo JText::_('GROUP'); ?></div>
                                <?php
                                $vendor_group = '';
                                if (isset($this->vendor->vendor_group))
                                    $vendor_group = $this->vendor->vendor_group;
                                echo $this->joomlaAcl->display('vendor_group', $vendor_group, false, false);
                                ?>
                            </div>
                        </div>

                        <div class="hkc-xl-4 hkc-lg-6 hikashop_tile_block">
                            <div>
                                <div class="hikashop_tile_title"><?php echo JText::_('ACL'); ?></div>
                                <?php
                                $acl = '';
                                if (!isset($this->vendor->vendor_acl))
                                    $acl = '';
                                else
                                    $acl = $this->vendor->vendor_acl;
                                echo $this->marketaclType->display('vendor_access', $acl, 'vendor_access_inherit');
                                ?>
                                </dl>
                            </div>
                        </div>

                    </div>
                </div>
            <?php } ?>

            <div style="clear:both" class="clr"></div>
            <?php if (!isset($this->vendor->vendor_id) || $this->vendor->vendor_id > 1) { ?>
            <input type="hidden" name="cid[]" value="<?php echo @$this->vendor->vendor_id; ?>"/>
            <input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>"/>
            <input type="hidden" name="task" value=""/>
            <input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>"/>
            <?php echo JHTML::_('form.token'); ?>
    </form>
    <?php } ?>

    <?php if(isset($this->vendor->vendor_id) && $this->vendor->vendor_id > 1) { ?>
    <div id="hikamarket_product_edition_tab_3" style="display:none;">
        <div class="hk-container-fluid">

            <div class="hkc-xl-12 hkc-lg-12 hikashop_tile_block">
                <div>
                    <div class="hikashop_tile_title"><?php echo JText::_('ORDERS'); ?> - <span
                            class="hk-label hk-label-blue"><?php echo $this->orders_count; ?></span></div>
                    <?php
                    $this->setLayout('orders');
                    echo $this->loadTemplate();
                    ?>
                </div>
            </div>

            <div class="hkc-xl-6 hkc-lg-6 hikashop_tile_block">
                <div>
                    <div class="hikashop_tile_title"><?php echo JText::_('VENDOR_INVOICES'); ?> - <span
                            class="hk-label hk-label-blue"><?php echo $this->invoices_count; ?></span></div>
                    <?php
                    $this->setLayout('invoices');
                    echo $this->loadTemplate();
                    ?>
                </div>
            </div>

            <div class="hkc-xl-6 hkc-lg-6 hikashop_tile_block">
                <div>
                    <div class="hikashop_tile_title"><?php echo JText::_('PRODUCTS'); ?> - <span
                            class="hk-label hk-label-blue"><?php echo $this->products_count; ?></span></div>
                    <?php
                    $this->setLayout('products');
                    echo $this->loadTemplate();
                    ?>
                </div>
            </div>

        </div>
    </div>
    <?php } ?>
</div>
