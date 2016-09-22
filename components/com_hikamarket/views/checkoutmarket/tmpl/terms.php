<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="hikashop_checkout_terms" class="hikashop_checkout_terms">
	<label>
		<input class="hikashop_checkout_terms_checkbox" id="hikashop_checkout_terms_checkbox" type="checkbox" name="hikashop_checkout_terms" value="1" <?php if(!empty($this->terms['shop'])) { echo ' checked="checked"'; } ?> />
<?php
	$text = JText::_('PLEASE_ACCEPT_TERMS');
	$link = '';
	$terms_article = $this->shopConfig->get('checkout_terms');
	if(!empty($this->terms_content[1]->vendor_terms)) {
		$link = hikamarket::completeLink('vendor&task=terms&cid=1', true);
	} else if(!empty($terms_article)){
		$link = JRoute::_('index.php?option=com_content&view=article&id='.$terms_article.'&tmpl=component');
	}
	if(!empty($link)) {
		echo $this->popupHelper->display(
			$text,
			'HIKASHOP_CHECKOUT_TERMS',
			$link,
			'shop_terms_and_cond',
			450, 480, '', '', 'link'
		);
	} else {
		echo $text;
	}
?>
	</label>
<?php
	foreach($this->vendors as $vendor) {
		if(!empty($this->terms_content[$vendor]->vendor_terms)) {
?>
	<br/><label>
		<input class="hikashop_checkout_terms_checkbox" id="hikamarket_checkout_terms_checkbox_<?php echo $vendor; ?>" type="checkbox" name="hikamarket_checkout_terms[<?php echo $vendor; ?>]" value="1" <?php if(!empty($this->terms['market'][$vendor])) { echo ' checked="checked"'; } ?> />
<?php
			echo $this->popupHelper->display(
				JText::sprintf('PLEASE_ACCEPT_TERMS_FOR_VENDOR', $this->terms_content[$vendor]->vendor_name),
				'HIKASHOP_CHECKOUT_TERMS',
				hikamarket::completeLink('vendor&task=terms&cid=' . $vendor, true),
				'shop_terms_and_cond_'.$vendor,
				450, 480, '', '', 'link'
			);
?>
	</label>
<?php
		}
	}
?>
</div>
