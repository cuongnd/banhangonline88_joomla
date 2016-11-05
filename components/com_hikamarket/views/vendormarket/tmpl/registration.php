<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?>
    <dl class="hikam_options">
<?php
if ($this->form_type == 'register')
    $this->form_type == 'vendorregister';

if ((empty($this->user) || empty($this->user->user_id)) && $this->form_type == 'vendorregister') {
    $user_name = $this->mainUser->get('name');
    $user_username = $this->mainUser->get('username');
    $user_email = $this->mainUser->get('email');
    if (!empty($this->user->name))
        $user_name = $this->user->name;
    if (!empty($this->user->username))
        $user_username = $this->user->username;
    if (!empty($this->user->email))
        $user_email = $this->user->email;
    ?>
    <dt class="hikamarket_registration_name_line">
        <label for="register_name"><?php echo JText::_('HIKA_USER_NAME'); ?></label>
    </dt>
    <dd class="hikamarket_registration_name_line">
        <input type="text" id="register_name" name="data[register][name]"
               value="<?php echo $this->escape($user_name); ?>" class="inputbox required" maxlength="50"/>
    </dd>
    <?php if ($this->config->get('registration_email_is_username', 0) == 0) { ?>
        <dt class="hikamarket_registration_username_line">
            <label for="register_username"><?php echo JText::_('HIKA_USERNAME'); ?></label>
        </dt>
        <dd class="hikamarket_registration_username_line">
            <input type="text" id="register_username" name="data[register][username]"
                   value="<?php echo $this->escape($user_username); ?>" class="inputbox required validate-username"
                   maxlength="25"/>
        </dd>
    <?php } ?>
    <dt class="hikamarket_registration_email_line">
        <label for="register_email"><?php echo JText::_('HIKA_EMAIL'); ?></label>
    </dt>
    <dd class="hikamarket_registration_email_line">
        <input type="text" id="register_email" name="data[register][email]"
               value="<?php echo $this->escape($user_email); ?>" class="inputbox required validate-email"
               maxlength="100"/>
    </dd>

    <?php if ($this->config->get('registration_ask_password', 1) == 1) { ?>
        <dt class="hikamarket_registration_password_line">
            <label for="register_password"><?php echo JText::_('HIKA_PASSWORD'); ?></label>
        </dt>
        <dd class="hikamarket_registration_password_line">
            <input type="password" id="register_password" name="data[register][password]" value=""
                   class="inputbox required validate-password"/>
        </dd>
        <dt class="hikamarket_registration_password2_line">
            <label for="register_password2"><?php echo JText::_('HIKA_VERIFY_PASSWORD'); ?></label>
        </dt>
        <dd class="hikamarket_registration_password2_line">
            <input type="password" id="register_password2" name="data[register][password2]" value=""
                   class="inputbox required validate-passverify"/>
        </dd>
    <?php } ?>
    </dl>
    <?php
    foreach ($this->extraFields['user'] as $fieldName => $oneExtraField) {
        ?>
        <dl id="hikamarket_<?php echo 'user_' . $oneExtraField->field_namekey; ?>"
            class="hikam_options hikamarket_registration_user_<?php echo $fieldName; ?>_line">
            <dt><?php
                echo $this->fieldsClass->getFieldName($oneExtraField);
                ?></dt>
            <dd><?php
                $onWhat = 'onchange';
                if ($oneExtraField->field_type == 'radio')
                    $onWhat = 'onclick';

                echo $this->fieldsClass->display(
                    $oneExtraField,
                    @$this->user->$fieldName,
                    'data[user][' . $fieldName . ']',
                    false,
                    ' ' . $onWhat . '="hikashopToggleFields(this.value,\'' . $fieldName . '\',\'user\',0);"',
                    false,
                    $this->extraFields['user'],
                    $this->user
                );
                ?></dd>
        </dl>
        <?php
    }

    if ($this->shopConfig->get('address_on_registration', 1)) {
        ?>
        <h3 class="hikashop_registration_address_info_title"><?php
            echo JText::_('ADDRESS_INFORMATION');
            ?></h3>
        <?php
        foreach ($this->extraFields['address'] as $fieldName => $oneExtraField) {
            ?>
            <dl id="hikamarket_<?php echo 'address_' . $oneExtraField->field_namekey; ?>"
                class="hikam_options hikamarket_registration_user_<?php echo $fieldName; ?>_line">
                <dt><?php
                    echo $this->fieldsClass->getFieldName($oneExtraField);
                    ?></dt>
                <dd><?php
                    $onWhat = 'onchange';
                    if ($oneExtraField->field_type == 'radio')
                        $onWhat = 'onclick';

                    echo $this->fieldsClass->display(
                        $oneExtraField,
                        @$this->address->$fieldName,
                        'data[address][' . $fieldName . ']',
                        false,
                        ' ' . $onWhat . '="hikashopToggleFields(this.value,\'' . $fieldName . '\',\'address\',0);"',
                        false,
                        $this->extraFields['address'],
                        $this->address
                    );
                    ?></dd>
            </dl>
            <?php
        }
    }
    ?>
    <h3 class="hikashop_registration_vendor_info_title"><?php
        echo JText::_('VENDOR_INFORMATION');
        ?></h3>
    <dl class="hikam_options">
    <?php
}
?>
    <dt class="hikamarket_<?php echo $this->form_type; ?>_vendorname_line">
        <label for="<?php echo $this->form_type; ?>_vendorname"><?php echo JText::_('HIKA_VENDOR_NAME'); ?></label>
    </dt>
    <dd class="hikamarket_<?php echo $this->form_type; ?>_vendorname_line">
        <input type="text" id="<?php echo $this->form_type; ?>_vendorname"
               name="data[<?php echo $this->form_type; ?>][vendor_name]"
               value="<?php echo $this->escape($this->element->vendor_name); ?>" class="inputbox required"
               maxlength="50"/> *
    </dd>
    <dt class="hikamarket_<?php echo $this->form_type; ?>_vendordomain_line">
        <label for="<?php echo $this->form_type; ?>_vendordomain"><?php echo JText::_('HIKA_VENDOR_DOMAIN'); ?></label>
    </dt>
    <dd class="hikamarket_<?php echo $this->form_type; ?>_vendordomain_line">
        <input type="text" id="<?php echo $this->form_type; ?>_vendordomain"
               name="data[<?php echo $this->form_type; ?>][vendor_domain]"
               value="<?php echo $this->escape($this->element->vendor_domain); ?>" class="inputbox required"
               maxlength="50"/> *
    </dd>

<?php if (!empty($this->user->user_id) || $this->form_type != 'vendorregister') { ?>
    <dt class="hikamarket_<?php echo $this->form_type; ?>_email_line">
        <label for="<?php echo $this->form_type; ?>_vendoremail"><?php echo JText::_('HIKA_CONTACT_EMAIL'); ?></label>
    </dt>
    <dd class="hikamarket_<?php echo $this->form_type; ?>_email_line">
        <input type="text" id="<?php echo $this->form_type; ?>_vendoremail"
               name="data[<?php echo $this->form_type; ?>][vendor_email]"
               value="<?php echo $this->escape($this->element->vendor_email); ?>"
               class="inputbox required validate-email" maxlength="50"/> *
    </dd>
<?php }

if ((!empty($this->element->vendor_id) && hikamarket::acl('vendor/edit/image')) || $this->config->get('register_ask_image', 0)) {
    ?>
    <dt class="hikamarket_<?php echo $this->form_type; ?>_vendorimage_line">
        <label><?php echo JText::_('HIKAM_VENDOR_IMAGE'); ?></label>
    </dt>
    <dd class="hikamarket_<?php echo $this->form_type; ?>_vendorimage_line"><?php
        $options = array(
            'upload' => true,
            'gallery' => true,
            'text' => JText::_('HIKAM_VENDOR_IMAGE_EMPTY_UPLOAD'),
            'uploader' => array('plg.market.vendor', 'vendor_image'),
            'vars' => array('vendor_id' => @$this->vendor->vendor_id)
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
        ?></dd>
    <?php
}

if ((!isset($this->element->vendor_id) || $this->element->vendor_id > 1) && $this->options['ask_paypal']) {
    $r = ($this->config->get('register_paypal_required', 0) != 0);
    ?>
    <dt class="hikamarket_<?php echo $this->form_type; ?>_paypal_line">
        <label for="<?php echo $this->form_type; ?>_paypal_email"><?php echo JText::_('PAYPAL_EMAIL'); ?></label>
    </dt>
    <dd class="hikamarket_<?php echo $this->form_type; ?>_paypal_line">
        <input type="text" id="<?php echo $this->form_type; ?>_paypal_email"
               name="data[<?php echo $this->form_type; ?>][vendor_params][paypal_email]"
               value="<?php echo $this->escape(@$this->element->vendor_params->paypal_email); ?>"
               class="inputbox <?php echo $r ? 'required' : ''; ?> validate-email"
               maxlength="50"/><?php echo $r ? ' *' : ''; ?>
    </dd>
    <?php
}

if ((!isset($this->element->vendor_id) || $this->element->vendor_id > 1) && $this->options['ask_currency']) {
    ?>
    <dt class="hikamarket_<?php echo $this->form_type; ?>_currency_line">
        <label for="data<?php echo $this->form_type; ?>vendor_currency_id"><?php echo JText::_('CURRENCY'); ?></label>
    </dt>
    <dd class="hikamarket_<?php echo $this->form_type; ?>_currency_line"><?php
        echo $this->currencyType->display('data[' . $this->form_type . '][vendor_currency_id]', $this->element->vendor_currency_id);
        ?></dd>
    <?php
}

if (!empty($this->extraFields['vendor'])) {
    ?>
    </dl>
    <?php
    foreach ($this->extraFields['vendor'] as $fieldName => $oneExtraField) {
        ?>
        <dl id="hikamarket_<?php echo $this->form_type . '_' . $oneExtraField->field_namekey; ?>"
            class="hikam_options hikamarket_<?php echo $this->form_type; ?>_<?php echo $fieldName; ?>_line">
            <dt><?php
                echo $this->fieldsClass->getFieldName($oneExtraField);
                ?></dt>
            <dd><?php
                $onWhat = 'onchange';
                if ($oneExtraField->field_type == 'radio')
                    $onWhat = 'onclick';
                $oneExtraField->table_name = 'vendor'; //$this->form_type; //'register';
                if (isset($this->element->$fieldName))
                    $value = $this->element->$fieldName;
                else
                    $value = $this->vendorFields->$fieldName;

                echo $this->fieldsClass->display(
                    $oneExtraField,
                    $value,
                    'data[' . $this->form_type . '][' . $fieldName . ']',
                    false,
                    ' ' . $onWhat . '="hikashopToggleFields(this.value,\'' . $fieldName . '\',\'' . $this->form_type . '\',0,\'hikamarket_\');"',
                    false,
                    $this->extraFields['vendor'],
                    @$this->element
                );
                ?></dd>
        </dl>
        <?php
    }
    ?>
    <dl class="hikam_options">
    <?php
}

if ((!isset($this->element->vendor_id) || $this->element->vendor_id > 0) && $this->options['ask_description']) {
    ?>
    <dt class="hikamarket_<?php echo $this->form_type; ?>_description_line">
        <label><?php echo JText::_('HIKA_DESCRIPTION'); ?></label>
    </dt>
    <dd class="hikamarket_<?php echo $this->form_type; ?>_description_line"><?php
        $this->editor->content = $this->element->vendor_description;
        echo $this->editor->display();
        ?>
        <div style="clear:both"></div>
    </dd>
    <?php
}

if ((!isset($this->element->vendor_id) || $this->element->vendor_id > 1) && $this->options['ask_terms']) {
    $r = ($this->config->get('register_terms_required', 0) != 0);
    ?>
    <dt class="hikamarket_<?php echo $this->form_type; ?>_terms_line">
        <label><?php echo JText::_('HIKASHOP_CHECKOUT_TERMS'); ?></label><?php echo $r ? ' *' : ''; ?>
    </dt>
    <dd class="hikamarket_<?php echo $this->form_type; ?>_terms_line"><?php
        $this->editor->content = @$this->element->vendor_terms;
        $this->editor->name = 'vendor_terms';
        echo $this->editor->display();
        ?>
        <div style="clear:both"></div>
    </dd>
    <?php
}
?>
    </dl>
<?php
echo JHTML::_('form.token');

if (empty($this->user) && $this->form_type == 'vendorregister') { ?>
    <input type="hidden" name="data[register][id]" value="<?php echo (int)$this->mainUser->get('id'); ?>"/>
    <input type="hidden" name="data[register][gid]" value="<?php echo (int)$this->mainUser->get('gid'); ?>"/>
    <?php
}

if ($this->form_type == 'vendorregister') {
    ?>
    <div class="form-actions">
        <input class="button hikashop_cart_input_button btn btn-primary" type="submit" class="btn btn-primary"
               value="<?php echo JText::_('HIKA_REGISTER'); ?>"/>
    </div>
<?php }
