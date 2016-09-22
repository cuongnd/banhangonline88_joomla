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
if(empty($this->auctionHistory))
	return;
$basePrice = $this->element->prices[0];
?>
<div class="hkc-xl-4 hkc-lg-6 hikashop_product_block hikashop_product_edit_auctionhistory"><div>
	<div class="hikashop_product_part_title hikashop_product_edit_auctionhistory_title"><?php echo JText::_('HKA_AUCTION_HISTORY');?></div>
	<table class="adminlist table table-striped auction_history_table" style="width:100%;">
		<thead>
			<tr>
				<th>
					<span class="title"><?php echo JText::_('HKA_BIDDER'); ?></label></span>
				</th>
				<th>
					<span class="title"><?php echo JText::_('HKA_AMOUNT'); ?></label></span>
				</th>
				<th>
					<span class="title"><?php echo JText::_('JDATE'); ?></label></span>
				</th>
			</tr>
		</thead>
		<tbody>
<?php
	foreach($this->auctionHistory as $value){
		$date = hikashop_getDate($value->auction_created,'%Y-%m-%d %H:%M');
		$price = $this->currencyHelper->format(@$value->auction_amount, $basePrice->price_currency_id);
?>
			<tr>
				<td class="auction_details">
					<?php echo $value->username; ?>
				</td>
				<td class="auction_details">
					<?php echo $price; ?>
				</td>
				<td class="auction_details">
					<?php echo $date; ?>
				</td>
			</tr>
<?php }
?>
		</tbody>
	</table>
</div></div>
