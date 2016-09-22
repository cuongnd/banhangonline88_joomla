<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php if(hikamarket::acl('user/edit/address')  && ($this->vendor->vendor_id <= 1)) { ?>
	<p>
		<span><?php echo JText::_('HIKAM_SELECT_DEFAULT_ADDRESS'); ?></span>
<?php
	$current = 0;
	$values = array();
	if(!empty($this->addresses)) {
		foreach($this->addresses as $k => $address) {
			$addr = $this->addressClass->miniFormat($address, $this->fields['address']);
			$values[] = JHTML::_('select.option', $k, $addr);
			if(!empty($address->address_default))
				$current = $address->address_default;
		}
	}
	if(empty($values))
		$values = array(JHTML::_('select.option', '', '<em>'.JText::_('HIKAM_NO_ADDRESS').'</em>'));
	echo JHTML::_('select.genericlist', $values, 'data[user][default_address]', 'class="hikamarket_default_address_dropdown"', 'value', 'text', $current, 'hikamarket_default_address_selector');
?>
	</p>
	<div id="hikamarket_user_addresses_show">
<?php
foreach($this->addresses as $address) {
?>
	<div class="hikamarket_user_address address_selection" id="hikamarket_user_address_<?php echo $address->address_id; ?>">
<?php
	$this->address = $address;
	$this->setLayout('address');
	echo $this->loadTemplate();
?>
	</div>
<?php
}
?>
		<div id="hikamarket_user_address_template" class="hikamarket_user_address address_selection" style="display:none;">
			{CONTENT}
		</div>
		<div class="" style="margin-top:6px;">
			<a class="btn btn-success" href="#newAddress" onclick="return window.addressMgr.new();"><?php echo JText::_('HIKA_NEW'); ?></a>
		</div>
	</div>
	<div id="hikamarket_user_addresses_edition">
	</div>
<script type="text/javascript">
if(!window.addressMgr) window.addressMgr = {};
window.Oby.registerAjax('hikamarket_address_changed', function(params) {
	if(!params) return;

	var d = document,
		el_show = d.getElementById('hikamarket_user_addresses_show'),
		el_edit = d.getElementById('hikamarket_user_addresses_edition');

	if(params.edit) {
		el_show.style.display = 'none';
		el_edit.style.display = '';
		return;
	}
	if(el_edit.children.length == 0)
		return;

	var target_id = params.previous_cid || params.cid,
		target = d.getElementById('hikamarket_user_address_' + target_id),
		el_sel = d.getElementById('hikamarket_default_address_selector');
		content = el_edit.innerHTML;

	el_edit.style.display = 'none';
	el_edit.innerHTML = '';

	for(var k in el_sel.options) {
		if(params.previous_cid && el_sel.options[k].value == params.previous_cid && params.previous_cid != 0 && params.previous_cid != params.cid)
			el_sel.options[k].value = params.cid;
		if(el_sel.options[k].value == params.cid)
			el_sel.options[k].text = params.miniFormat;
	}
	if(params.previous_cid !== undefined && params.previous_cid === 0) {
		var o = d.createElement('option');
		o.text = params.miniFormat;
		o.value = params.cid;
		el_sel.add(o, el_sel.options[el_sel.selectedIndex]);
		el_sel.selectedIndex--;
		o.fireEvent(el_sel,'change');
	}
	if(jQuery) jQuery(el_sel).trigger("liszt:updated");

	if(target) {
		target.innerHTML = content;
	} else if(params.cid > 0) {
		window.hikashop.dup('hikamarket_user_address_template', {'VALUE':params.cid, 'CONTENT':content}, 'hikamarket_user_address_'+params.cid);
	}

	el_show.style.display = '';
});
window.addressMgr.new = function() {
	var d = document, w = window, o = w.Oby,
		el_edit = d.getElementById('hikamarket_user_addresses_edition');
	if(el_edit) {
		el_edit.innerHTML = '';
		var url = '<?php echo hikamarket::completeLink('user&task=address&subtask=edit&cid=0&user_id='.$this->user_id, true, true); ?>';
		o.xRequest(url, {update:el_edit});
	}
	return false;
};
window.addressMgr.delete = function(el, cid) {
	if(!confirm('<?php echo JText::_('HIKASHOP_CONFIRM_DELETE_ADDRESS', true); ?>'))
		return false;
	var w = window, o = w.Oby, d = document;
	o.xRequest(el.href, {mode: 'POST', data: '<?php echo hikamarket::getFormToken(); ?>=1'}, function(xhr) { if(xhr.status == 200) {
		var target = d.getElementById('hikamarket_user_address_' + cid);
		if(xhr.responseText == '1') {
			if(target)
				target.parentNode.removeChild(target);
			var el_sel = d.getElementById('hikamarket_default_address_selector');
			for(var k in el_sel.options) {
				if(el_sel.options[k].value == cid) {
					el_sel.remove(k);
					break;
				}
			}
			o.fireEvent(el_sel,'change');
			if(jQuery) jQuery(el_sel).trigger("liszt:updated");
			o.fireAjax('hikamarket_address_deleted',{'cid':cid,'uid':target,'el':el});
		} else if(xhr.responseText != '0') {
			if(target) o.updateElem(target, xhr.responseText);
		}
	}});
	return false;
};
</script>
<?php } else {
	foreach($this->addresses as $address) {
		$address_css = '';
		if(!empty($address->address_default))
			$address_css = ' address_default';
?>
	<div class="hikamarket_user_address address_selection<?php echo $address_css; ?>" id="hikamarket_user_address_<?php echo $address->address_id; ?>">
<?php
	$this->address = $address;
	$this->setLayout('address');
	echo $this->loadTemplate();
?>
	</div>
<?php
	}
}
