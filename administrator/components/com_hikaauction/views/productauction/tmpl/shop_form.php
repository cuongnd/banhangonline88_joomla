<?php
/**
 * @package	HikaShop for Joomla!
 * @version	1.2.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><dl class="hika_options">
	<dt class="hikashop_product_auctionbutton"><?php echo JText::_('HIKA_AUCTION'); ?></dt>
	<dd class="hikashop_product_auctionbutton"><?php
		$product_auction = (int)@$this->data->product_auction;

		if($product_auction == 0 || $product_auction == 1) {
			echo JHTML::_('hikaselect.booleanlist', 'data[product][product_auction]', '', $product_auction);
		} else {
			echo JText::_('HIKA_AUCTION_FINISHED');
			?>
			<input type="hidden" name="data[product][product_auction]" value="<?php echo $product_auction; ?>"/>
			<?php
		}
	?></dd>
</dl>
<dl class="hika_options">
	<dt class="hikashop_product_auctionbutton"><?php echo JText::_('HKA_BID_INCREMENT'); ?></dt>
	<dd class="hikashop_product_auctionbutton"><?php
		$product_auction = (int)@$this->data->product_auction;
		if(isset($this->data->product_bid_increment))
			$product_bid_increment = (int)$this->data->product_bid_increment;
		else
			$product_bid_increment = 1;

		if($product_auction == 0 || $product_auction == 1) { ?>
			<input type="text" name="data[product][product_bid_increment]" value="<?php echo $product_bid_increment; ?>"/> <?php
		} else {
			echo JText::_('HIKA_AUCTION_FINISHED');
			?>
			<input type="hidden" name="data[product][product_bid_increment]" value="<?php echo $product_bid_increment; ?>"/>
			<?php
		}
	?></dd>
</dl>
<?php if($product_auction == 1) { ?>
<dl class="hika_options">
	<dt class="hikashop_product_auctionstatus"><?php echo JText::_('HIKA_AUCTION_STATUS'); ?></dt>
	<dd class="hikashop_product_auctionstatus"><?php
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
</dl>
<?php } ?>
<?php if($product_auction > 0) { ?>
<dl class="hika_options">
	<dt class="hikashop_product_auctionduration"><?php echo JText::_('HIKA_AUCTION_DURATION'); ?></dt>
	<dd class="hikashop_product_auctionduration"><?php
		echo hikaauction::timeCounter($this->data->product_sale_end, $this->data->product_sale_start);
	?></dd>
</dl>
<dl class="hika_options">
	<dt class="hikashop_product_auctionsales"><?php echo JText::_('HIKA_AUCTION_SALES'); ?></dt>
	<dd class="hikashop_product_auctionsales"><?php
		echo $this->sales->orders . ' (' . $this->sales->sales . ' ' . JText::_('PRODUCTS') . ')';
	?></dd>
</dl>
<?php } ?>
