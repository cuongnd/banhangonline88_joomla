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
$auctionClass = hikaauction::get('class.auction');
$auctionConfig = hikaauction::config();
$auctionHistory = $auctionClass->getAuctionHistory($this->element->product_id);
$basePrice = $this->element->prices[0];
?>
<h2><?php echo JText::_('HKA_AUCTION_HISTORY'); ?></h2>
<div>
	<table class="table table-striped auction_history_table">
		<thead>
			<tr>
				<th class="title">
					<?php echo JText::_('HKA_BIDDER'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('HKA_AMOUNT'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('JDATE'); ?>
				</th>
			</tr>
		</thead>
		<tbody>
<?php
if(!empty($auctionHistory)) {
	foreach($auctionHistory as $value){
		$date = hikashop_getDate($value->auction_created,'%Y-%m-%d %H:%M');

		if($auctionConfig->get('bidding_history_name', 'username') == 'username')
			$bidder_name = $value->username;
		else
			$bidder_name = $value->name;

		if($auctionConfig->get('anonymous_auction_history', '0'))
			$bidder_name = substr_replace($bidder_name, '***', 1, strlen($bidder_name)-2);

		$price = $this->currencyHelper->format(@$value->auction_amount, $basePrice->price_currency_id);
?>
			<tr>
				<td class="auction_details">
					<?php echo $bidder_name; ?>
				</td>
				<td class="auction_details">
					<?php echo $price; ?>
				</td>
				<td class="auction_details">
					<?php echo $date; ?>
				</td>
			</tr>
<?php }
} else {
?>
			<tr>
				<td colspan="3"><span class="history_empty"><?php
					echo JText::_('AUCTION_HISTORY_EMPTY')
				?></span></td>
			</tr>
<?php
}
?>
		</tbody>
	</table>
</div>
<?php
