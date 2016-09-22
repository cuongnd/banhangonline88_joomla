<?php
/**
 * @package	HikaShop for Joomla!
 * @version	1.2.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
if(!defined('HIKAMARKET_COMPONENT'))
	return;
?><dt class="hikamarket_product_plugin_hikaauction"><label><?php echo JText::_('HIKA_AUCTION'); ?></label></dt>
<dd class="hikamarket_product_plugin_hikaauction"><?php
	$product_auction = (int)@$this->data->product_auction;
	if($product_auction == 0 || $product_auction == 1) {
		echo JHTML::_('hikaselect.booleanlist', 'data[product][product_auction]', '', $product_auction);
	} else {
		echo JText::_('HIKA_AUCTION_FINISHED');
		?><input type="hidden" name="data[product][product_auction]" value="<?php echo $product_auction; ?>"/><?php
	}
?></dd>
<?php if($product_auction == 1) { ?>
<dt class="hikamarket_product_plugin_hikaauction"><label><?php echo JText::_('HIKA_AUCTION_STATUS'); ?></label></dt>
<dd class="hikamarket_product_plugin_hikaauction_status"><?php
	$sale_start = (int)$this->data->product_sale_start;
	$sale_end = (int)$this->data->product_sale_end;
	$now = time();

	if($sale_start > $now) {
		echo JText::sprintf('HIKA_AUCTION_START_IN_X', hikaauction::timeCounter($sale_start, $now));
	}
	if($sale_start < $now) {
		if($sale_end > $now)
			echo JText::sprintf('HIKA_AUCTION_END_IN_X', hikaauction::timeCounter($now, $sale_end));
		else
			echo JText::sprintf('HIKA_AUCTION_END_SINCE_X', hikaauction::timeCounter($sale_end, $now));
	}
?></dd>
<?php } ?>
<?php if($product_auction > 0) { ?>
<dt class="hikamarket_product_plugin_hikaauction_duration"><label><?php echo JText::_('HIKA_AUCTION_DURATION'); ?></dt>
<dd><?php
	echo hikaauction::timeCounter($this->data->product_sale_end, $this->data->product_sale_start);
?></dd>
<dt class="hikamarket_product_plugin_hikaauction_sales"><?php echo JText::_('HIKA_AUCTION_SALES'); ?></dt>
<dd><?php
	echo $this->sales;
?></dd>
<?php } ?>
