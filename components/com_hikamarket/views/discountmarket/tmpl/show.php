<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><script type="text/javascript">
window.discountMgr = {};
window.discountMgr.cpt = {};
</script>
<form action="<?php echo hikamarket::completeLink('discount');?>" method="post" name="hikamarket_form" id="hikamarket_discount_form">
	<dl class="hikam_options large">
<?php
	if(hikamarket::acl('discount/edit/code')) { ?>
		<dt class="hikamarket_discount_code"><label><?php echo JText::_('DISCOUNT_CODE'); ?></label></dt>
		<dd class="hikamarket_discount_code"><input type="text" size="45" name="data[discount][discount_code]" value="<?php echo $this->escape(@$this->discount->discount_code); ?>" /></dd>
<?php }

	if(hikamarket::acl('discount/edit/type')) { ?>
		<dt class="hikamarket_discount_type"><label><?php echo JText::_('DISCOUNT_TYPE'); ?></label></dt>
		<dd class="hikamarket_discount_type"><?php
			$options = array(
				JHTML::_('select.option', 'coupon', JText::_('COUPONS')),
				JHTML::_('select.option', 'discount', JText::_('DISCOUNTS')),
			);
			echo JHTML::_('select.genericlist', $options, 'data[discount][discount_type]', '', 'value', 'text', @$this->discount->discount_type);
		?></dd>
<?php }

	if(hikamarket::acl('discount/edit/flatamount')) { ?>
		<dt class="hikamarket_discount_flatamount"><label><?php echo JText::_('DISCOUNT_FLAT_AMOUNT'); ?></label></dt>
		<dd class="hikamarket_discount_flatamount">
			<input type="text" size="30" name="data[discount][discount_flat_amount]" value="<?php echo $this->escape(@$this->discount->discount_flat_amount); ?>" />
			<?php echo $this->currencyType->display('data[discount][discount_currency_id]', @$this->discount->discount_currency_id); ?>
		</dd>
<?php }

	if(hikamarket::acl('discount/edit/percentamount')) { ?>
		<dt class="hikamarket_discount_percentamount"><label><?php echo JText::_('DISCOUNT_PERCENT_AMOUNT'); ?></label></dt>
		<dd class="hikamarket_discount_percentamount"><input type="text" size="30" name="data[discount][discount_percent_amount]" value="<?php echo $this->escape(@$this->discount->discount_percent_amount); ?>" /></dd>
<?php }

	if(hikamarket::acl('discount/edit/taxcategory')) { ?>
		<dt class="hikamarket_discount_taxcategory"><label><?php echo JText::_('TAXATION_CATEGORY'); ?></label></dt>
		<dd class="hikamarket_discount_taxcategory"><?php echo $this->categoryType->display('data[discount][discount_tax_id]', @$this->discount->discount_tax_id, 'tax'); ?></dd>
<?php }

	if(hikamarket::acl('discount/edit/usedtimes')) { ?>
		<dt class="hikamarket_discount_usedtimes"><label><?php echo JText::_('DISCOUNT_USED_TIMES'); ?></label></dt>
		<dd class="hikamarket_discount_usedtimes"><input type="text" size="30" name="data[discount][discount_used_times]" value="<?php echo $this->escape(@$this->discount->discount_used_times); ?>" /></dd>
<?php }

	if(hikamarket::acl('discount/edit/published')) { ?>
		<dt class="hikamarket_discount_publish"><label><?php echo JText::_('HIKA_PUBLISHED'); ?></label></dt>
		<dd class="hikamarket_discount_publish"><?php echo JHTML::_('hikaselect.booleanlist', "data[discount][discount_published]" , '', @$this->discount->discount_published); ?></dd>
<?php }

	if(hikamarket::acl('discount/edit/dates')) { ?>
		<dt class="hikamarket_discount_dates"><label><?php echo JText::_('HIKA_DATES'); ?></label></dt>
		<dd class="hikamarket_discount_dates"><?php
			echo JHTML::_('calendar', hikamarket::getDate((@$this->discount->discount_start?@$this->discount->discount_start:''),'%Y-%m-%d %H:%M'), 'data[discount][discount_start]','discount_start','%Y-%m-%d %H:%M',array('size' => '20'));
			echo ' <span class="calendar-separator">' . JText::_('HIKA_RANGE_TO') . '</span> ';
			echo JHTML::_('calendar', hikamarket::getDate((@$this->discount->discount_end?@$this->discount->discount_end:''),'%Y-%m-%d %H:%M'), 'data[discount][discount_end]','discount_end','%Y-%m-%d %H:%M',array('size' => '20'));
		?></dd>
<?php }

	if(hikashop_level(1)) {
		if(hikamarket::acl('discount/edit/minorder')) { ?>
		<dt class="hikamarket_discount_minorder"><label><?php echo JText::_('MINIMUM_ORDER_VALUE'); ?></label></dt>
		<dd class="hikamarket_discount_minorder"><input type="text" size="30" name="data[discount][discount_minimum_order]" value="<?php echo $this->escape(@$this->discount->discount_minimum_order); ?>" /></dd>
<?php	}

		if(hikamarket::acl('discount/edit/minproducts')) { ?>
		<dt class="hikamarket_discount_minproducts"><label><?php echo JText::_('MINIMUM_NUMBER_OF_PRODUCTS'); ?></label></dt>
		<dd class="hikamarket_discount_minproducts"><input type="text" size="30" name="data[discount][discount_minimum_products]" value="<?php echo $this->escape(@$this->discount->discount_minimum_products); ?>" /></dd>
<?php	}

		if(hikamarket::acl('discount/edit/quota')) { ?>
		<dt class="hikamarket_discount_quota"><label><?php echo JText::_('DISCOUNT_QUOTA'); ?></label></dt>
		<dd class="hikamarket_discount_quota"><input type="text" size="30" name="data[discount][discount_quota]" value="<?php echo $this->escape(@$this->discount->discount_quota); ?>" /></dd>
<?php	}

		if(hikamarket::acl('discount/edit/peruser')) { ?>
		<dt class="hikamarket_discount_peruser"><label><?php echo JText::_('DISCOUNT_QUOTA_PER_USER'); ?></label></dt>
		<dd class="hikamarket_discount_peruser"><input type="text" size="30" name="data[discount][discount_quota_per_user]" value="<?php echo $this->escape(@$this->discount->discount_quota_per_user); ?>" /></dd>
<?php	}

		if(hikamarket::acl('discount/edit/product')) { ?>
		<dt class="hikamarket_discount_product"><label><?php echo JText::_('PRODUCT'); ?></label></dt>
		<dd class="hikamarket_discount_product"><?php
			$product_id = null;
			if(!empty($this->discount->discount_product_id))
				$product_id = explode(',', trim($this->discount->discount_product_id, ','));
			echo $this->nameboxType->display(
				'data[discount][discount_product_id]',
				$product_id,
				hikamarketNameboxType::NAMEBOX_MULTIPLE,
				'product',
				array(
					'delete' => true,
					'root' => $this->rootCategory,
					'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>',
				)
			);
		?></dd>
<?php	}

		if(hikamarket::acl('discount/edit/category')) { ?>
		<dt class="hikamarket_discount_category"><label><?php echo JText::_('CATEGORY'); ?></label></dt>
		<dd class="hikamarket_discount_category"><?php
			$category_id = null;
			if(!empty($this->discount->discount_category_id))
				$category_id = explode(',', trim($this->discount->discount_category_id, ','));
			echo $this->nameboxType->display(
				'data[discount][discount_category_id]',
				$category_id,
				hikamarketNameboxType::NAMEBOX_MULTIPLE,
				'category',
				array(
					'delete' => true,
					'root' => $this->rootCategory,
					'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>',
				)
			);
		?></dd>
<?php	}

		if(hikamarket::acl('discount/edit/categorychild')) { ?>
		<dt class="hikamarket_discount_categorychild"><label><?php echo JText::_('INCLUDING_SUB_CATEGORIES'); ?></label></dt>
		<dd class="hikamarket_discount_categorychild"><?php echo JHTML::_('hikaselect.booleanlist', 'data[discount][discount_category_childs]' , '', @$this->discount->discount_category_childs); ?></dd>
<?php	}

		if(hikamarket::acl('discount/edit/zone')) { ?>
		<dt class="hikamarket_discount_zone"><label><?php echo JText::_('ZONE'); ?></label></dt>
		<dd class="hikamarket_discount_zone"><?php
			$zone_id = null;
			if(!empty($this->discount->discount_zone_id))
				$zone_id = explode(',', trim($this->discount->discount_zone_id, ','));
			echo $this->nameboxType->display(
				'data[discount][discount_zone_id]',
				$zone_id,
				hikamarketNameboxType::NAMEBOX_MULTIPLE,
				'zone',
				array(
					'delete' => true,
					'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>',
				)
			);
		?></dd>
<?php	}

		if($this->vendor->vendor_id == 1 && hikamarket::level(1) && hikamarket::acl('discount/edit/targetvendor')) { ?>
	<dt class="hikamarket_discount_targetvendor"><label><?php echo JText::_('DISCOUNT_TARGET_VENDOR'); ?></label></dt>
	<dd class="hikamarket_discount_targetvendor"><?php
		$discount_vendor_id = (int)@$this->discount->discount_target_vendor;
		if($discount_vendor_id > 1) {
			$vendorClass = hikamarket::get('class.vendor');
			$discountVendor = $vendorClass->get($discount_vendor_id);
			$discount_vendor_name = $discount_vendor_id . ' - ' . $discountVendor->vendor_name;
		} else {
			$discount_vendor_id = -1;
			$discount_vendor_name = JText::_('HIKAM_SELECT_VENDOR');
		}
		$values = array(
			JHTML::_('select.option', '0', JText::_('HIKASHOP_NO')),
			JHTML::_('select.option', '1', JText::_('HIKASHOP_YES')),
			JHTML::_('select.option', $discount_vendor_id, $discount_vendor_name)
		);

		echo JHTML::_('hikaselect.radiolist', $values, 'data[discount][discount_target_vendor]' , 'onclick="window.discountMgr.setVendor(this, true);" onchange="window.discountMgr.setVendor(this, false);"', 'value', 'text', @$this->discount->discount_target_vendor);

		$popup = hikamarket::get('shop.helper.popup');
		$popupLinkData = '<img src="'.HIKAMARKET_IMAGES.'icon-16/edit.png" style="vertical-align:middle;" alt="'.JText::_('HIKA_EDIT').'"/>';
		if(HIKASHOP_RESPONSIVE)
			$popupLinkData = '';

		echo $this->popup->display(
			$popupLinkData,
			'EDIT',
			hikamarket::completeLink('vendor&task=selection&single=true', true),
			'market_discount_set_vendor',
			760, 480, 'onclick="return window.discountMgr.changeVendor(this);"', '', 'link'
		);
?>
<script type="text/javascript">
window.discountMgr.current_vendor = <?php echo $discount_vendor_id; ?>;
window.discountMgr.setVendor = function(el, c) {
	var v = 0;
	if(el.value) v = parseInt(el.value);
	if(v == -1 || (c && v > 1 && v == window.discountMgr.current_vendor && el.checked)) {
		var p = document.getElementById("market_discount_set_vendor");
		window.discountMgr.changeVendor(p);
	}
	if(c)
		window.discountMgr.current_vendor = v;
}
window.discountMgr.changeVendor = function(el) {
	window.hikamarket.submitFct = function(data) {
		var d = document, w = window, o = w.Oby,
			el = d.getElementById("data[discount][discount_target_vendor]-1"),
			lbl = d.getElementById("data[discount][discount_target_vendor]-1-lbl");
		if(!el) el = d.getElementById("data_discount_discount_target_vendor-1");
		if(el) el.value = data.id;
		if(el && !el.checked) el.checked = "checked";
		if(!lbl) { lbl = el; while(lbl && lbl.nodeName.toLowerCase() != "label") { lbl = lbl.nextSibling; } }
		if(lbl) lbl.innerHTML = data.id + " - " + data.vendor_name;
	};
	window.hikamarket.openBox(el,null,(el.getAttribute("rel") == null));
}
</script>
<?php
		}

		if(hikamarket::acl('discount/edit/autoload')) { ?>
		<dt class="hikamarket_discount_autoload"><label><?php echo JText::_('COUPON_AUTO_LOAD'); ?></label></dt>
		<dd class="hikamarket_discount_autoload"><?php echo JHTML::_('hikaselect.booleanlist', 'data[discount][discount_auto_load]' , '', @$this->discount->discount_auto_load); ?></dd>
<?php	}

		if(hikamarket::acl('discount/edit/percenttoproduct')) { ?>
		<dt class="hikamarket_discount_percenttoproduct"><label><?php echo JText::_('COUPON_APPLIES_TO_PRODUCT_ONLY'); ?></label></dt>
		<dd class="hikamarket_discount_percenttoproduct"><?php echo JHTML::_('hikaselect.booleanlist', 'data[discount][discount_coupon_product_only]' , '', @$this->discount->discount_coupon_product_only); ?></dd>
<?php	}

		if(hikamarket::acl('discount/edit/nodoubling')) { ?>
		<dt class="hikamarket_discount_nodoubling"><label><?php echo JText::_('COUPON_HANDLING_OF_DISCOUNTED_PRODUCTS'); ?></label></dt>
		<dd class="hikamarket_discount_nodoubling"><?php
			$options = array(
				JHTML::_('select.option', 0, JText::_('STANDARD_BEHAVIOR')),
				JHTML::_('select.option', 1, JText::_('IGNORE_DISCOUNTED_PRODUCTS')),
				JHTML::_('select.option', 2, JText::_('OVERRIDE_DISCOUNTED_PRODUCTS'))
			);
			echo JHTML::_('hikaselect.genericlist', $options, 'data[discount][discount_coupon_nodoubling]', '', 'value', 'text', @$this->discount->discount_coupon_nodoubling);
		?></dd>
<?php	}
	}

	JPluginHelper::importPlugin('hikashop');
	$dispatcher = JDispatcher::getInstance();
	$html = array();
	$dispatcher->trigger('onDiscountFrontBlocksDisplay', array(&$this->discount, &$html));
	if(!empty($html))
		echo implode("\r\n", $html);

	if(hikashop_level(2) && hikamarket::acl('discount/edit/acl')) {
?>
		<dt class="hikamarket_discount_acl"><label><?php echo JText::_('ACCESS_LEVEL'); ?></label></dt>
		<dd class="hikamarket_discount_acl"><?php
			$discount_access = 'all';
			if(isset($this->discount->discount_access))
				$discount_access = $this->discount->discount_access;
			echo $this->joomlaAcl->display('data[discount][discount_access]', $discount_access, true, true);
		?></dd>
<?php
	}
?>
	</dl>
	<input type="hidden" name="cancel_action" value="<?php echo @$this->cancel_action; ?>"/>
	<input type="hidden" name="cid" value="<?php echo @$this->discount->discount_id; ?>"/>
	<input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>"/>
	<input type="hidden" name="task" value="show"/>
	<input type="hidden" name="ctrl" value="discount"/>
	<?php echo JHTML::_('form.token'); ?>
</form>
<script type="text/javascript">
window.hikashop.ready(function(){ window.hikamarket.dlTitle('hikamarket_discount_form'); });
</script>
