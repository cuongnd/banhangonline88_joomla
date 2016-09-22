<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
echo $this->leftmenu(
	'market',
	array(
		'#market_general' => JText::_('HIKAM_OPTIONS_GENERAL'),
		'#market_images' => JText::_('HIKAM_OPTIONS_IMAGES'),
		'#market_display' => JText::_('HIKAM_OPTIONS_SHOW'),
		'#market_email' => JText::_('HIKAM_OPTIONS_EMAIL'),
		'#market_registration' => JText::_('HIKAM_OPTIONS_REGISTRATION'),
		'#market_categories' => JText::_('HIKAM_OPTIONS_CATEGORIES'),
		'#market_limitations' => JText::_('HIKAM_OPTIONS_TITLE_VENDOR_LIMITATIONS'),
		'#market_tax' => JText::_('HIKAM_OPTIONS_TAX'),
	)
);
?>
<div id="page-market" class="rightconfig-container <?php if(HIKASHOP_BACK_RESPONSIVE) echo 'rightconfig-container-j30';?>">
	<!-- GENERAL -->
	<fieldset id="market_general" class="adminform">
		<legend><?php echo JText::_('HIKAM_OPTIONS_GENERAL');?></legend>
<table class="admintable" style="width:100%;cell-spacing:1">
<tr>
	<td class="key"><?php echo JText::_('HIKAM_UPDATABLE_ORDER_STATUSES'); ?></td>
	<td><?php
		$order_statuses = explode(',', $this->config->get('updatable_order_statuses', 'created'));
		if(!empty($order_statuses)) {
			foreach($order_statuses as &$order_status) {
				$order_status = trim($order_status);
			}
			unset($order_status);
		}
		echo $this->nameboxType->display(
			'config[updatable_order_statuses]',
			$order_statuses,
			hikamarketNameboxType::NAMEBOX_MULTIPLE,
			'order_status',
			array(
				'delete' => true,
				'sort' => false,
				'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>',
				'force_data' => true
			)
		);
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_VALID_ORDER_STATUSES'); ?></td>
	<td><?php
		$order_statuses = explode(',', $this->config->get('valid_order_statuses', 'confirmed,shipped'));
		if(!empty($order_statuses)) {
			foreach($order_statuses as &$order_status) {
				$order_status = trim($order_status);
			}
			unset($order_status);
		}
		echo $this->nameboxType->display(
			'config[valid_order_statuses]',
			$order_statuses,
			hikamarketNameboxType::NAMEBOX_MULTIPLE,
			'order_status',
			array(
				'delete' => true,
				'sort' => false,
				'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>',
				'force_data' => true
			)
		);
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('MARKET_USE_SAME_ORDER_NUMBER'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[use_same_order_number]",'',$this->config->get('use_same_order_number',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('PREFIX_VENDOR_PRODUCT_CODE'); ?></td>
	<td>
		<input type="text" name="config[prefix_product_code]" value="<?php echo $this->escape( @$this->config->get('prefix_product_code', '') ); ?>" />
	</td>
</tr>
<tr class="option_title">
	<td colspan="2"><?php echo JText::_('HIKAM_OPTIONS_TITLE_VENDOR_SELECTION'); ?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('ALLOW_ZONE_VENDORS'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[allow_zone_vendor]",'',$this->config->get('allow_zone_vendor', 0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('ALLOW_VENDOR_SELECTOR'); ?></td>
	<td><?php
		$options = array(
			JHTML::_('select.option', '', JText::_('HIKA_NONE'))
		);
		if(!empty($this->vendorselect_customfields)) {
			foreach($this->vendorselect_customfields as $field) {
				if(in_array($field->field_table, array('order', 'item')))
					$options[] = JHTML::_('select.option', $field->field_namekey, $field->field_table . ' - ' . $field->field_realname);
			}
		}
		echo JHTML::_('select.genericlist', $options, 'config[vendor_select_custom_field]', '', 'value', 'text', $this->config->get('vendor_select_custom_field', ''));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('PREFERRED_VENDOR_FOR_VENDOR_SELECTOR'); ?></td>
	<td><?php
		$options = array(
			JHTML::_('select.option', '', JText::_('HIKA_NONE'))
		);
		if(!empty($this->vendorselect_customfields)) {
			foreach($this->vendorselect_customfields as $field) {
				if($field->field_table == 'user')
					$options[] = JHTML::_('select.option', $field->field_namekey, $field->field_table . ' - ' . $field->field_realname);
			}
		}
		echo JHTML::_('select.genericlist', $options, 'config[preferred_vendor_select_custom_field]', '', 'value', 'text', $this->config->get('preferred_vendor_select_custom_field', ''));
	?></td>
</tr>
<tr class="option_title">
	<td colspan="2"><?php echo JText::_('HIKAM_OPTIONS_TITLE_VENDOR_PAYMENT_SHIPPING'); ?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('LIMIT_VENDORS_IN_CART'); ?></td>
	<td><?php
		$options = array(
			JHTML::_('select.option', 0, JText::_('HIKAM_NO_LIMIT_VENDOR')),
			JHTML::_('select.option', 1, JText::_('HIKAM_LIMIT_ONE_VENDOR')),
			JHTML::_('select.option', 2, JText::_('HIKAM_LIMIT_ONE_EXTRA_VENDOR'))
		);
		echo JHTML::_('hikaselect.radiolist', $options, 'config[vendors_in_cart]', '', 'value', 'text', $this->config->get('vendors_in_cart', 0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('SHOW_ONLY_VENDOR_PAYMENTS'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[only_vendor_payments]",'',$this->config->get('only_vendor_payments',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('SPLIT_PAYMENT_FEES_ON_VENDORS'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[split_order_payment_fees]",'',$this->config->get('split_order_payment_fees',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('SPLIT_SHIPPING_FEES_ON_VENDORS'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[split_order_shipping_fees]",'',$this->config->get('split_order_shipping_fees',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('SHIPPING_PER_VENDOR'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[shipping_per_vendor]",'',$this->config->get('shipping_per_vendor', 1));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('PLUGIN_VENDOR_CONFIG'); ?></td>
	<td><?php
		$options = array(
			JHTML::_('hikaselect.option', 0, JText::_('HIKASHOP_NO')),
			JHTML::_('hikaselect.option', 1, JText::_('HIKAM_OWN_PLUGIN')),
		);
		echo JHTML::_('hikaselect.radiolist', $options, 'config[plugin_vendor_config]', '', 'value', 'text', $this->config->get('plugin_vendor_config', 0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('FILTER_ORDER_STATUS_WHEN_VENDOR_PAID'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[filter_orderstatus_paid_order]",'',$this->config->get('filter_orderstatus_paid_order', 1));
	?></td>
</tr>
<?php
?>
</table>
	</fieldset>

	<!-- IMAGES -->
	<fieldset id="market_images" class="adminform">
		<legend><?php echo JText::_('HIKAM_OPTIONS_IMAGES');?></legend>
<table class="admintable" style="width:100%;cell-spacing:1">
<tr>
	<td class="key"><?php echo JText::_('HIKAM_DEFAULT_VENDOR_IMAGE'); ?></td>
	<td><?php
		$options = array(
			'upload' => true,
			'gallery' => true,
			'text' => JText::_('HIKAM_VENDOR_IMAGE_EMPTY_UPLOAD'),
			'uploader' => array('config', 'default_vendor_image'),
		);
		$params = new stdClass();
		$params->file_path = $this->config->get('default_vendor_image', '');
		$params->field_name = 'config[default_vendor_image]';
		$js = '';
		$content = hikamarket::getLayout('uploadmarket', 'image_entry', $params, $js);

		echo $this->uploaderType->displayImageSingle('hikamarket_config_default_vendor_image', $content, $options);
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_VENDOR_IMAGE_WIDTH'); ?></td>
	<td>
		<input type="text" name="config[vendor_image_y]" value="<?php echo $this->escape( @$this->config->get('vendor_image_y', '') ); ?>" /> px
	</td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_VENDOR_IMAGE_HEIGHT'); ?></td>
	<td>
		<input type="text" name="config[vendor_image_y]" value="<?php echo $this->escape( @$this->config->get('vendor_image_y', '') ); ?>" /> px
	</td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_IMAGE_FORCESIZE');?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[image_forcesize]",'',$this->config->get('image_forcesize',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_IMAGE_GRAYSCALE');?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[image_grayscale]",'',$this->config->get('image_grayscale',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_IMAGE_SCALE');?></td>
	<td><?php
		$scale_arr = array(
			JHTML::_('select.option', '1', JText::_('HIKAM_IMAGE_SCALE_INSIDE')),
			JHTML::_('select.option', '0', JText::_('HIKAM_IMAGE_SCALE_OUTSIDE')),
		);
		echo JHTML::_('hikaselect.radiolist', $scale_arr, "config[image_scale]" , '', 'value', 'text', $this->config->get('image_scale',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_IMAGE_RADIUS');?></td>
	<td>
		<input size="12" name="config[image_radius]" type="text" value="<?php echo $this->escape($this->config->get('image_scale', '')); ?>" /> px
	</td>
</tr>
</table>
	</fieldset>
	<fieldset id="market_display" class="adminform">
		<legend><?php echo JText::_('HIKAM_OPTIONS_SHOW');?></legend>
<table class="admintable" style="width:100%;cell-spacing:1">
<tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_DISPLAY_VENDOR_VOTE'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[display_vendor_vote]",'',$this->config->get('display_vendor_vote',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_DISPLAY_VENDOR_CONTACT_BTN'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[display_vendor_contact]",'',$this->config->get('display_vendor_contact',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_FRONT_SHOW_SOLD_BY'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[show_sold_by]",'',$this->config->get('show_sold_by',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_FRONT_SHOW_SOLD_BY_ME'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[show_sold_by_me]",'',$this->config->get('show_sold_by_me',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_FRONT_SHOW_MAIN_VENDOR_IN_LISTING'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[listing_show_main_vendor]",'',$this->config->get('listing_show_main_vendor',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_FRONT_VENDOR_DEFAULT_MENU'); ?></td>
	<td><?php
		echo $this->menusType->display('config[vendor_default_menu]', $this->config->get('vendor_default_menu',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_FRONT_VENDOR_CATEGORY_TO_VENDOR_PAGE'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[override_vendor_category_link]",'',$this->config->get('override_vendor_category_link',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('VENDOR_SHOW_MODULES'); ?></td>
	<td><?php
		echo $this->nameboxType->display(
			'config[vendor_show_modules]',
			explode(',', $this->config->get('vendor_show_modules')),
			hikamarketNameboxType::NAMEBOX_MULTIPLE,
			'modules',
			array(
				'delete' => true,
				'sort' => true,
				'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>',
				'force_data' => true
			)
		);
	?></td>
</tr>
</table>
	</fieldset>

	<!-- EMAIL -->
	<fieldset id="market_email" class="adminform">
		<legend><?php echo JText::_('HIKAM_OPTIONS_EMAIL');?></legend>
<table class="admintable" style="width:100%;cell-spacing:1">
<tr>
	<td class="key"><?php echo JText::_('HIKAM_CONTACT_MAIL_TO_VENDORS'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[contact_mail_to_vendor]",'',$this->config->get('contact_mail_to_vendor',1));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_ALWAYS_SEND_PRODUCT_EMAIL'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', 'config[always_send_product_email]','',$this->config->get('always_send_product_email',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_DISPLAY_VENDOR_NAME_IN_EMAILS'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', 'config[mail_display_vendor]','',$this->config->get('mail_display_vendor',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_NOTIFY_ADMIN_FOR_VENDOR_MODIFICATION'); ?></td>
	<td><?php
		$order_statuses = explode(',', $this->config->get('admin_notify_subsale', 'cancelled,refunded'));
		if(!empty($order_statuses)) {
			foreach($order_statuses as &$order_status) {
				$order_status = trim($order_status);
			}
			unset($order_status);
		}
		echo $this->nameboxType->display(
			'config[admin_notify_subsale]',
			$order_statuses,
			hikamarketNameboxType::NAMEBOX_MULTIPLE,
			'order_status',
			array(
				'delete' => true,
				'sort' => false,
				'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>',
				'force_data' => true
			)
		);
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_SEND_MAIL_SUBSALE_UPDATE_MAIN'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', 'config[send_mail_subsale_update_main]','',$this->config->get('send_mail_subsale_update_main',0));
	?></td>
</tr>
</table>
	</fieldset>

	<!-- REGISTRATION -->
	<fieldset id="market_registration" class="adminform">
		<legend><?php echo JText::_('HIKAM_OPTIONS_REGISTRATION');?></legend>
<table class="admintable" style="width:100%;cell-spacing:1">
<tr>
	<td class="key"><?php echo JText::_('HIKAM_ALLOW_VENDOR_REGISTRATION'); ?></td>
	<td><?php
		$options = array(
			JHTML::_('hikaselect.option', 0, JText::_('HIKASHOP_NO')),
			JHTML::_('hikaselect.option', 1, JText::_('HIKAM_REGISTER_MANUAL_VALIDATION')),
			JHTML::_('hikaselect.option', 2, JText::_('HIKAM_REGISTER_AUTO_VALIDATION')),
			JHTML::_('hikaselect.option', 3, JText::_('HIKAM_REGISTER_AUTO_CREATION'))
		);
		echo JHTML::_('select.genericlist', $options, 'config[allow_registration]', 'onchange="window.localPage.allowRegistrationChanged(this);"', 'value', 'text', $this->config->get('allow_registration',0));
	?>
<script type="text/javascript">
if(!window.localPage) window.localPage = {};
window.localPage.allowRegistrationChanged = function(el) {
	if(!el) return;
	var els = ['hikamarket_config_auto_registration_group'];
	window.hikashop.setArrayDisplay(els, (el.value == 3));
};
window.hikashop.ready(function(){ window.localPage.allowRegistrationChanged(document.getElementById('configallow_registration')) });
</script>
	</td>
</tr>
<tr id="hikamarket_config_auto_registration_group">
	<td class="key"><?php echo JText::_('HIKAM_ALLOW_VENDOR_REGISTRATION'); ?></td>
	<td><?php
		echo $this->joomlaaclType->display('config[auto_registration_group]', $this->config->get('auto_registration_group', 'all'), true, true);
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_REGISTRATION_ASK_CURRENCY'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[register_ask_currency]",'',$this->config->get('register_ask_currency',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_REGISTRATION_ASK_DESCRIPTION'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[register_ask_description]",'',$this->config->get('register_ask_description',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_REGISTRATION_ASK_TERMS'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[register_ask_terms]",'',$this->config->get('register_ask_terms',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_REGISTRATION_TERMS_REQUIRED'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[register_terms_required]",'',$this->config->get('register_terms_required',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_REGISTRATION_ASK_PAYPAL'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[register_ask_paypal]",'',$this->config->get('register_ask_paypal',1));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_REGISTRATION_PAYPAL_REQUIRED'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[register_paypal_required]",'',$this->config->get('register_paypal_required',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_REGISTRATION_ASK_PASSWORD'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[registration_ask_password]",'',$this->config->get('registration_ask_password',1));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_REGISTRATION_EMAIL_IS_USERNAME'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[registration_email_is_username]",'',$this->config->get('registration_email_is_username',0));
	?></td>
</tr>
<!--
<tr>
	<td class="key"><?php echo JText::_('HIKAM_REGISTRATION_ASK_IMAGE'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[register_ask_image]",'',$this->config->get('register_ask_image',0));
	?></td>
</tr>
-->
<tr>
	<td class="key"><?php echo JText::_('HIKAM_LINK_VENDOR_GROUP_WITH_ADMIN'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[link_admin_groups]",'',$this->config->get('link_admin_groups',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_USERGROUP_ON_REGISTRATION'); ?></td>
	<td><?php
		echo $this->joomlaaclType->displayList('config[user_group_registration]', $this->config->get('user_group_registration', ''), 'HIKA_INHERIT');
	?></td>
</tr>
</table>
	</fieldset>

	<!-- CATEGORIES -->
	<fieldset id="market_categories" class="adminform">
		<legend><?php echo JText::_('HIKAM_OPTIONS_CATEGORIES');?></legend>
<table class="admintable" style="width:100%;cell-spacing:1">
<tr>
	<td class="key"><?php echo JText::_('HIKAM_VENDOR_CREATE_CATEGORY'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[vendor_create_category]",'',$this->config->get('vendor_create_category',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_VENDOR_PARENT_CATEGORY'); ?></td>
	<td><?php
		echo $this->categoryType->displaySingle('config[vendor_parent_category]', $this->config->get('vendor_parent_category',''));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_VENDOR_CHROOT_CATEGORY'); ?></td>
	<td><?php
		$options = array(
			JHTML::_('hikaselect.option', 0, JText::_('HIKASHOP_NO')),
			JHTML::_('hikaselect.option', 1, JText::_('HIKAM_VENDOR_HOME')),
			JHTML::_('hikaselect.option', 2, JText::_('HIKASHOP_YES'))
		);
		echo JHTML::_('hikaselect.radiolist', $options, 'config[vendor_chroot_category]', 'onchange="window.hikamarket.switchBlock(this, 2, \'config__vendor_root_category\')"', 'value', 'text', $this->config->get('vendor_chroot_category',0));
	?></td>
</tr>
<tr id="config__vendor_root_category" <?php if($this->config->get('vendor_chroot_category',0) != 2) echo ' style="display:none;"'; ?>>
	<td class="key"><?php echo JText::_('HIKAM_VENDORS_ROOT_CATEGORY'); ?></td>
	<td><?php
		echo $this->categoryType->displaySingle('config[vendor_root_category]', $this->config->get('vendor_root_category', 0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_VENDOR_EXTRA_CATEGORIES'); ?></td>
	<td><?php
		echo $this->nameboxType->display(
			'config[vendor_extra_categories]',
			explode(',', $this->config->get('vendor_extra_categories', '')),
			hikamarketNameboxType::NAMEBOX_MULTIPLE,
			'category',
			array(
				'delete' => true,
				'root' => 0,
				'sort' => true,
				'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>',
				'force_data' => true
			)
		);
	?></td>
</tr>
</table>
	</fieldset>

	<!-- LIMITATIONS -->
	<fieldset id="market_limitations" class="adminform">
		<legend><?php echo JText::_('HIKAM_OPTIONS_TITLE_VENDOR_LIMITATIONS');?></legend>
<table class="admintable" style="width:100%;cell-spacing:1">
<tr>
	<td class="key"><?php echo JText::_('VENDOR_PRODUCT_LIMITATION'); ?></td>
	<td>
		<input type="text" name="config[vendor_product_limitation]" value="<?php echo (int)$this->config->get('vendor_product_limitation', 0); ?>" />
	</td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_OPTION_RELATED_ALL_VENDORS'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[related_all_vendors]",'',$this->config->get('related_all_vendors',1));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_OPTION_OPTIONS_ALL_VENDORS'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[options_all_vendors]",'',$this->config->get('options_all_vendors',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_OPTION_CHECK_VENDOR_COMPLETION'); ?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[check_vendor_completion]",'',$this->config->get('check_vendor_completion', 0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('DAYS_FOR_PAYMENT_REQUEST'); ?></td>
	<td>
		<input type="text" name="config[days_payment_request]" value="<?php echo (int)$this->config->get('days_payment_request', 0); ?>" />
	</td>
</tr>
</table>
	</fieldset>

	<!-- TAXES -->
	<fieldset id="market_tax" class="adminform">
		<legend><?php echo JText::_('HIKAM_OPTIONS_TAX');?></legend>
<table class="admintable" style="width:100%;cell-spacing:1">
<tr>
	<td class="key"><?php echo JText::_('HIKAM_MARKET_MODE'); ?></td>
	<td><?php
		$options = array(
			JHTML::_('select.option', 'fee', JText::_('MARKETMODE_FEE')),
			JHTML::_('select.option', 'commission', JText::_('MARKETMODE_COMMISSION')),
		);
		echo JHTML::_('hikaselect.radiolist', $options, 'config[market_mode]', '', 'value', 'text', $this->config->get('market_mode', 'fee'));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_VENDOR_PRICE_WITH_TAX');?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[calculate_vendor_price_with_tax]",'',$this->config->get('calculate_vendor_price_with_tax',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_APPLY_FEES_ON_SHIPPING');?></td>
	<td><?php
		echo JHTML::_('hikaselect.booleanlist', "config[fee_on_shipping]",'',$this->config->get('fee_on_shipping',0));
	?></td>
</tr>
<tr>
	<td class="key"><?php echo JText::_('HIKAM_MARKET_PAYVENDORCONTENT_MODE'); ?></td>
	<td><?php
		$options = array(
			JHTML::_('select.option', 'orders', JText::_('MARKETMODE_PAY_ORDERS')),
			JHTML::_('select.option', 'products', JText::_('MARKETMODE_PAY_PRODUCTS')),
		);
		echo JHTML::_('hikaselect.radiolist', $options, 'config[vendor_pay_content]', '', 'value', 'text', $this->config->get('vendor_pay_content', 'orders'));
	?></td>
</tr>
<tr>
	<td colspan="2">
<?php
	$params = new hikaParameter('');
	$params->set('configPanelIntegration', true);
	$js = '';
	echo hikamarket::getLayout('vendormarket', 'fees', $params, $js);
?>
	</td>
</tr>
<!--
vendor_limit_orders_display (integer)
vendor_limit_products_display (integer)
-->
</table>
	</fieldset>
</div>
