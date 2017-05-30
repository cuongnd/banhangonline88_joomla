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
class hikamarketVendor_selectionType {

	public function __construct() {
		$this->app = JFactory::getApplication();
	}

	public function displayDropdown($map, $value, $delete = false, $options = '', $id = '') {
		$vendorClass = hikamarket::get('class.vendor');
		$vendors = $vendorClass->getList();
		return JHTML::_('select.genericlist', $vendors, $map, $options, 'value', 'text', $value, $id);
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
window.localPage.fieldSetVendor = function(el, name) {
	window.hikamarket.submitFct = function(data) {
		var d = document,
			vendorInput = d.getElementById(name + "_input_id"),
			vendorSpan = d.getElementById(name + "_span_id");
		if(vendorInput) { vendorInput.value = data.id; }
		if(vendorSpan) { vendorSpan.innerHTML = '.$vendor_format.'; }
	};
	window.hikamarket.openBox(el,null,(el.getAttribute("rel") == null));
	return false;
};
window.localPage.fieldRemVendor = function(el, name) {
	var d = document,
		vendorInput = d.getElementById(name + "_input_id"),
		vendorSpan = d.getElementById(name + "_span_id");
	if(vendorInput) { vendorInput.value = ""; }
	if(vendorSpan) { vendorSpan.innerHTML = " - "; }
	return false;
};
';
		$doc = JFactory::getDocument();
		$doc->addScriptDeclaration($js);

		$jsInit = true;
	}

	public function display($map, $value, $delete = false) {
		$this->initJs();

		$vendorClass = hikamarket::get('class.vendor');
		$popup = hikamarket::get('shop.helper.popup');

		$name = str_replace(array('][','[',']'), '_', $map);
		$vendor_id = (int)$value;
		$vendor = $vendorClass->get($vendor_id);
		$vendor_name = '';
		if(!empty($vendor)) {
			$vendor_name = @$vendor->vendor_name;
		} else {
			$vendor_id = '';
		}

		$vendor_display_name = $vendor_name;
		if($this->app->isAdmin())
			$vendor_display_name = $vendor_id.' - '.$vendor_name;

		$ret = '<span id="'.$name.'_span_id">'.$vendor_display_name.'</span>' .
			'<input type="hidden" id="'.$name.'_input_id" name="'.$map.'" value="'.$vendor_id.'"/> '.
			$popup->display(
				'<img src="'.HIKAMARKET_IMAGES.'icon-16/edit.png" style="vertical-align:middle;"/>',
				'VENDOR_SELECTION',
				hikamarket::completeLink('vendor&task=selection&single=true', true),
				'market_set_vendor_'.$name,
				760, 480, 'onclick="return window.localPage.fieldSetVendor(this,\''.$name.'\');"', '', 'link'
			);

		if($delete)
			$ret .= ' <a title="'.JText::_('HIKA_DELETE').'" href="#'.JText::_('HIKA_DELETE').'" onclick="return window.localPage.fieldRemVendor(this, \''.$name.'\');"><img src="'.HIKAMARKET_IMAGES.'icon-16/delete.png" style="vertical-align:middle;"/></a>';

		return $ret;
	}
}
