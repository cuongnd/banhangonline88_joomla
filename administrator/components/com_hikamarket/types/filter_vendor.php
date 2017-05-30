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
class hikamarketFilter_vendorType {

	protected $values = array();

	public function __construct() {
		$this->app = JFactory::getApplication();
	}

	protected function load($value) {
		$this->values = array();
		$db = JFactory::getDBO();

		$query = 'SELECT COUNT(*) FROM '.hikamarket::table('vendor').' WHERE vendor_published = 1';
		$db->setQuery($query);
		$ret = (int)$db->loadResult();
		if($ret > 10) {
			$this->values = $ret;
			return;
		}

		$query = 'SELECT * FROM '.hikamarket::table('vendor').' WHERE vendor_published = 1 ORDER BY vendor_name, vendor_id';
		$db->setQuery($query);
		$vendors = $db->loadObjectList();
		$this->values = array(
			JHTML::_('select.option', 0, JText::_('NO_VENDOR')),
			JHTML::_('select.option', -1, JText::_('ALL_VENDORS'))
		);
		if(!empty($vendors)) {
			foreach($vendors as $vendor) {
				if($vendor->vendor_id == 0 || $vendor->vendor_id == 1)
					continue;
				$this->values[] = JHTML::_('select.option', $vendor->vendor_id, $vendor->vendor_name . ' [' . $vendor->vendor_id . ']');
			}
		}
	}

	protected function initJs() {
		static $jsInit = null;
		if($jsInit === true)
			return;

		$vendor_format = 'data.vendor_name';
		if($this->app->isAdmin())
			$vendor_format = 'data.id + " - " + data.vendor_name';

		$js = '
if(!window.localPage)
	window.localPage = {};
window.localPage.filterChooseVendor = function(el, name) {
	window.hikamarket.submitFct = function(data) {
		var d = document,
			vendorInput = d.getElementById(name + "_input_id"),
			vendorSpan = d.getElementById(name + "_span_id");
		if(vendorInput) { vendorInput.value = data.id; }
		if(vendorSpan) { vendorSpan.innerHTML = '.$vendor_format.'; }
		if(d.adminForm)
			d.adminForm.submit();
		else {
			var f = d.getElementById("adminForm");
			if(!f) f = d.getElementById("hikamarketForm");
			if(!f && el.form) f = el.form;
			if(f) f.submit();
		}
	};
	window.hikamarket.openBox(el,null,(el.getAttribute("rel") == null));
	return false;
};
window.localPage.filterSetVendor = function(el, name, value) {
	var d = document,
		vendorInput = d.getElementById(name + "_input_id"),
		vendorSpan = d.getElementById(name + "_span_id");
	if(vendorInput) { vendorInput.value = value; }
	if(vendorSpan) {
		if(value == 0)
			vendorSpan.innerHTML = "'.JText::_('NO_VENDOR', true).'";
		else
			vendorSpan.innerHTML = "'.JText::_('ALL_VENDORS', true).'";
	}
	if(d.adminForm)
		d.adminForm.submit();
	else {
		var f = d.getElementById("adminForm");
		if(!f) f = d.getElementById("hikamarketForm");
		if(!f && el.form) f = el.form;
		if(f) f.submit();
	}
};
';
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);

		$jsInit = true;
	}

	public function display($map, $value, $invoicemap = '', $invoicevalue = 0) {
		if(empty($this->values))
			$this->load($value);
		if(is_array($this->values)) {
			$ret = JHTML::_('select.genericlist', $this->values, $map, 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $value);
		} else {
			$this->initJs();

			$vendorClass = hikamarket::get('class.vendor');
			$popup = hikamarket::get('shop.helper.popup');

			$name = str_replace(array('][','[',']'), '_', $map);
			$vendor_id = (int)$value;
			if($vendor_id > 1)
				$vendor = $vendorClass->get($vendor_id);
			$vendor_name = JText::_('ALL_VENDORS');
			if($vendor_id >= 0)
				$vendor_name = JText::_('NO_VENDOR');

			if(!empty($vendor))
				$vendor_name = @$vendor->vendor_name;

			$vendor_display_name = $vendor_name;
			if($this->app->isAdmin() && $vendor_id > 1)
				$vendor_display_name = $vendor_id.' - '.$vendor_name;

			$ret = '<span id="'.$name.'_span_id">'.$vendor_display_name.'</span>' .
				'<input type="hidden" id="'.$name.'_input_id" name="'.$map.'" value="'.$vendor_id.'"/> '.
				$popup->display(
					'<img src="'.HIKAMARKET_IMAGES.'icon-16/edit.png" style="vertical-align:middle;"/>',
					'VENDOR_SELECTION',
					hikamarket::completeLink('vendor&task=selection&single=true', true),
					'market_set_vendor_'.$name,
					760, 480, 'onclick="return window.localPage.filterChooseVendor(this,\''.$name.'\');"', '', 'link'
				);

			$ret .= ' <a title="'.JText::_('HIKAM_MY_VENDOR').'" href="#'.str_replace(' ','_',JText::_('HIKAM_MY_VENDOR',true)).'" onclick="return window.localPage.filterSetVendor(this, \''.$name.'\', 0);"><img src="'.HIKAMARKET_IMAGES.'icon-16/user.png" style="vertical-align:middle;"/></a>';
			$ret .= ' <a title="'.JText::_('ALL_VENDORS').'" href="#'.str_replace(' ','_',JText::_('ALL_VENDORS',true)).'" onclick="return window.localPage.filterSetVendor(this, \''.$name.'\', -1);"><img src="'.HIKAMARKET_IMAGES.'icon-16/delete.png" style="vertical-align:middle;"/></a>';
		}

		if($value > 1 && !empty($invoicemap)) {
			$choices = array(
				JHTML::_('select.option', 0, JText::_('VENDOR_SALES')),
				JHTML::_('select.option', 1, JText::_('VENDOR_INVOICES')),
			);
			$ret .= JHTML::_('select.genericlist', $choices, $invoicemap, 'class="inputbox" size="1" onchange="document.adminForm.submit();"', 'value', 'text', $invoicevalue);
		}

		return $ret;
	}
}
