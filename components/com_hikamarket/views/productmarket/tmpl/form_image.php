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
$ajax = false;
if(!empty($this->upload_ajax))
	$ajax = true;
$product_type = (@$this->params->product_type == 'variant' || @$this->product->product_type == 'variant') ? 'variant' : 'product';

$upload = hikamarket::acl('product/edit/images/upload');
$options = array(
	'classes' => array(
		'mainDiv' => 'hikamarket_main_image_div',
		'contentClass' => 'hikamarket_product_images',
		'firstImg' => 'hikamarket_product_main_image_thumb',
		'otherImg' => 'hikamarket_small_image_div',
		'btn_add' => 'hikam_add_btn',
		'btn_upload' => 'hikam_upload_btn'
	),
	'upload' => $upload,
	'upload_base_url' => 'index.php?option=com_hikamarket&ctrl=upload',
	'gallery' => $upload,
	'text' => ($upload ? JText::_('HIKAM_PRODUCT_IMAGES_EMPTY_UPLOAD') : JText::_('HIKAM_PRODUCT_IMAGES_EMPTY')),
	'uploader' => array('product', 'product_image'),
	'vars' => array(
		'product_id' => $this->product->product_id,
		'product_type' => $product_type,
		'file_type' => 'product'
	),
	'ajax' => $ajax
);

$content = array();
if(!empty($this->product->images)) {
	foreach($this->product->images as $k => $image) {
		$image->product_id = $this->product->product_id;
		$image->product_type = $product_type;
		$this->params = $image;
		$content[] = $this->loadTemplate('image_entry');
	}
}

if(empty($this->editing_variant))
	echo $this->uploaderType->displayImageMultiple('hikamarket_product_image', $content, $options);
else
	echo $this->uploaderType->displayImageMultiple('hikamarket_product_variant_image', $content, $options);

echo $this->popup->display('','MARKET_EDIT_IMAGE','','hikamarket_product_image_edit',750, 460,'', '', 'link');
?>
<script type="text/javascript">
window.productMgr.editImage = function(el, id, type) {
	var w = window, t = w.hikamarket, href = null, n = el;
	if(type === undefined || type == '') type = 'product';
	if(type == 'variant') type = 'product_variant';
	if(!w.hkUploaderList['hikamarket_'+type+'_image']) return false;
	if(w.hkUploaderList['hikamarket_'+type+'_image'].imageClickBlocked) return false; // Firefox trick
	t.submitFct = function(data) {};
	if(el.getAttribute('rel') == null) {
		href = el.href;
		n = 'hikamarket_product_image_edit';
	}
	t.openBox(n,href,(el.getAttribute('rel') == null));
	return false;
}
window.productMgr.delImage = function(el, type) {
	if(type === undefined || type == '') type = 'product';
	if(type == 'variant') type = 'product_variant';
	if(!window.hkUploaderList['hikamarket_'+type+'_image']) return false;
	return window.hkUploaderList['hikamarket_'+type+'_image'].delImage(el);
}
</script>
