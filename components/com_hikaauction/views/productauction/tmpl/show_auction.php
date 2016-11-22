<?php
/**
 * @package	HikaShop for Joomla!
 * @version	1.2.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?></form>
<form action="<?php echo HIKASHOP_LIVE . 'index.php?option=com_hikaauction&ctrl=product&task=bid'.$this->bid_itemid; ?>" method="post" name="hikaauction_product_form" enctype="multipart/form-data">
<?php
if(!$this->auction_finished) {
	$now = time();
	$counter = hikaauction::timeArrayCounter($now, $this->element->product_sale_end);
?>
	<script type="text/javascript">
		var counter = new timeCounter({start: <?php echo time();?>,end:<?php echo $this->element->product_sale_end; ?>},{d:"auction_d",h:"auction_h",m:"auction_m",s:"auction_s"});
		window.hikashop.ready(function(){
			counter.start();
		});
		counter.onEnd(function() {
			var d = document, el = null;
			el = d.getElementById("auction_time_counter");
			if(el) el.style.display = "none";
			el = d.getElementById("auction_finished_label");
			if(el) el.style.display = "";

			el = d.getElementById("hikashop_product_quantity_main");
			if(el) el.style.display = "none";
		});
	</script>
<?php
}
?><div id="hikashop_product_top_part" class="hikashop_product_top_part">
<?php if(!empty($this->element->extraData->topBegin)) { echo implode("\r\n",$this->element->extraData->topBegin); } ?>
	<h1>
		<span id="hikashop_product_name_main" class="hikashop_product_name_main" itemprop="name"><?php
			if (hikashop_getCID('product_id')!=$this->element->product_id && isset ($this->element->main->product_name))
				echo $this->element->main->product_name;
			else
				echo $this->element->product_name;
		?></span>
<?php if($this->config->get('show_code')) { ?>
		<span id="hikashop_product_code_main" class="hikashop_product_code_main" itemprop="sku"><?php
			echo $this->element->product_code;
		?></span>
<?php }?>
	</h1>
<?php if(!empty($this->element->extraData->topEnd)) { echo implode("\r\n",$this->element->extraData->topEnd); } ?>
<?php
	$pluginsClass = hikashop_get('class.plugins');
	$plugin = $pluginsClass->getByName('content', 'hikashopsocial');
	if(!empty($plugin) && (@$plugin->published || @$plugin->enabled)) {
		echo '{hikashop_social}';
	}
?>
</div>
<?php if(HIKASHOP_RESPONSIVE){ ?>
	<div class="row-fluid">
<?php } ?>
<div id="hikashop_product_left_part" class="hikashop_product_left_part span6">
<?php if(!empty($this->element->extraData->leftBegin)) { echo implode("\r\n",$this->element->extraData->leftBegin); } ?>
<?php
	$this->row =& $this->element;
	$this->setLayout('show_block_img');
	echo $this->loadTemplate();
?>
<?php if(!empty($this->element->extraData->leftEnd)) { echo implode("\r\n",$this->element->extraData->leftEnd); } ?>
</div>

<div id="hikashop_product_right_part" class="hikashop_product_right_part span6">
<?php if(!empty($this->element->extraData->rightBegin)) { echo implode("\r\n",$this->element->extraData->rightBegin); }
	if($this->params->get('price_with_tax',3) == 3)
		$this->params->set('price_with_tax', $this->config->get('price_with_tax'));

	 if(!$this->auction_finished) { ?>
	<p class="time_counter" id="auction_time_counter">
		<span class="counter_value">
			<span id="auction_d"><?php echo sprintf('%02d', $counter[0]); ?></span>
			<span class="sub"><?php echo JText::_('HKA_DAYS'); ?></span>
		</span>
		<span class="sep">:</span>
		<span class="counter_value">
			<span id="auction_h"><?php echo sprintf('%02d', $counter[1]); ?></span>
			<span class="sub"><?php echo JText::_('HKA_HOURS'); ?></span>
		</span>
		<span class="sep">:</span>
		<span class="counter_value">
			<span id="auction_m"><?php echo sprintf('%02d', $counter[2]); ?></span>
			<span class="sub"><?php echo JText::_('HKA_MINUTES'); ?></span>
		</span>
		<span class="sep">:</span>
		<span class="counter_value">
			<span id="auction_s"><?php echo sprintf('%02d', $counter[3]); ?></span>
			<span class="sub"><?php echo JText::_('HKA_SECONDS'); ?></span>
		</span>
	</p>
<?php } ?>
	<p class="auction_finished" id="auction_finished_label" style="<?php if(!$this->auction_finished) echo 'display:none';?>"><?php
		echo JText::_('HIKA_AUCTION_FINISHED');
	?></p>

<div>
	<table width="100%" class="auction_details_table">
<?php 	if ($this->display_nb_bid) {
?>
			<tr>
				<td>
					<span class="auction_details"><?php echo JText::_('HKA_NUMBER_BIDS'); ?></label></span>
				</td>
				<td>
					<span class="auction_details"><?php echo $this->number_bids; ?></span>
				</td>
			</tr>
<?php	}
		if ($this->display_nb_bidders) {
?>
			<tr>
				<td>
					<span class="auction_details"><?php echo JText::_('HKA_NUMBER_BIDDERS'); ?></label></span>
				</td>
				<td>
					<span class="auction_details"><?php echo $this->number_bidders; ?></span>
				</td>
			</tr>
<?php
		}
		if ($this->display_starting_auction_price && !$this->auction_finished) {
?>
		<tr>
			<td>
				<span class="auction_details"><?php echo JText::_('HKA_STARTING_PRICE'); ?></label></span>
			</td>
			<td>
				<span class="auction_details"><?php echo $this->starting_price; ?></span>
			</td>
		</tr>
<?php
		}

	if(!$this->auction_finished) {
?>
<script type="text/javascript">
	if(!auctionPage.prices) auctionPage.prices = {};
	auctionPage.prices.base = <?php echo hikaauction::convertNumber($this->priceBase);?>;
	auctionPage.prices.bidding_mode = '<?php echo $this->bidding_mode;?>';
</script>
		<tr>
			<td>
				<span class="auction_details"><?php echo JText::_('HKA_CURRENT_PRICE'); ?></label></span>
			</td>
			<td>
				<span id="hikashop_product_price_main" class="hikashop_product_price_main hikaauction_auction_price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
					<span class="auction_details" name="current_price" id="current_price"><?php echo $this->current_price;?></span>
					<?php //<span style="display: none;" itemprop="price"> echo $this->current_price_amount; </span> ?>
					<meta itemprop="price" content="<?php  echo $this->current_price_amount; ?>">
					<meta itemprop="priceCurrency" content="<?php echo $this->auction_currency->currency_code; ?>" />
					<meta itemprop="priceValidUntil" content="<?php echo date('Y-m-d', $this->element->product_sale_end); ?>"/>
				</span
			</td>
		</tr>

<?php	if(hikashop_loadUser()) { ?>
			<tr>
				<td>
					<span class="auction_details"><?php echo JText::_('HKA_YOUR_PREVIOUS_MAX_BID'); ?></label></span>
				</td>
				<td>
					<span class="auction_details"><?php echo ($this->currencyHelper->format(@$this->maxBidOfUser, $this->basePrice->price_currency_id));?></span>
				</td>
			</tr>
<?php
			if(!empty($this->current_winner_id)){
?>
			<tr>
				<td colspan=2>
<?php			if($this->current_winner_id == $this->user){ ?>
					<span class="auction_details"><?php if(!$this->auction_finished) echo JText::sprintf('HKA_YO_ARE_CURRENTLY_WINNING_THE_AUCTION',$this->current_price); else  echo JText::sprintf('HKA_AUCTION_FINISHED_WINNER_MESSAGE',$this->current_price); ?></label></span>
<?php			} else { ?>
					<span class="auction_details"><?php if(!$this->auction_finished) echo JText::sprintf('HKA_YO_ARE_CURRENTLY_LOSING_THE_AUCTION',$this->current_price); else  echo JText::sprintf('HKA_AUCTION_FINISHED_BIDDERS_MESSAGE',$this->current_price); ?></label></span>
<?php			} ?>
				</td>
			</tr>
<?php		}
		}
	} else { ?>
		<tr>
			<td>
				<span class="auction_details"><?php echo JText::_('HKA_FINAL_PRICE'); ?></label></span>
			</td>
			<td>
			<span id="hikashop_product_price_main" class="hikashop_product_price_main hikaauction_auction_price" itemprop="offers" itemscope itemtype="http://schema.org/Offer">
				<span class="auction_details" name="final_price" id="final_price"><?php echo $this->current_price;?></span>
				<meta itemprop="price" content="<?php  echo $this->current_price_amount; ?>">
				<meta itemprop="priceCurrency" content="<?php echo $this->auction_currency->currency_code; ?>" />
				<meta itemprop="priceValidUntil" content="<?php echo date('Y-m-d', $this->element->product_sale_end); ?>"/>
			</span>
			</td>
		</tr>
<?php
	}
?>
	</table>
</div>
<?php if(!hikashop_loadUser() && !$this->auction_finished) {
			$login_url = '';

			if(!HIKASHOP_J16){
				$login_url = HIKASHOP_LIVE . 'index.php?option=com_user&view=login'.$this->login_itemid;
			}else{
				$login_url = HIKASHOP_LIVE . 'index.php?option=com_easysocial&view=login'.$this->login_itemid;
			}
			$login_url = JRoute::_($login_url.'&return='.urlencode(base64_encode(hikashop_currentUrl('',false))),false);
	?>
		<div>
			<span class="auction_details"><?php echo JText::sprintf('HKA_CREATE_AN_ACCOUNT_OR_LOG_IN_TO_SUBMIT_BID',$login_url);?></span>
		</div>
<?php } elseif(!$this->auction_finished){  ?>
		<div id="hikaauction_bidder_amount">
		<?php
			if($this->bidding_mode != 'free_bidding') {
		?>
			<input type="text" name="bid_amount" id="bid_amount" placeholder="<?php  echo JText::sprintf('HKA_YOUR_BID',$this->bid_price); ?>" onclick="bid_amount.value='';"/>
		<?php } else {
		?>
			<input type="text" name="bid_amount" id="bid_amount" onclick="bid_amount.value='';"/>
		<?php }
		?>
		<?php 		echo $this->cart->displayButton(JText::_('HIKAAUCTION_BID'),'btn_bid',$this->params,'','return auctionPage.bid(this, '.(int)$this->element->product_id.');','',1,1,'hikaauction_bid_button',false);	?>
		</div>
<?php } ?>
<?php if(!empty($this->element->extraData->rightMiddle)) { echo implode("\r\n",$this->element->extraData->rightMiddle); } ?>

	<div id="hikashop_product_contact_main" class="hikashop_product_contact_main">
<?php
	$contact = $this->config->get('product_contact',0);
	if (hikashop_level(1) && ($contact == 2 || ($contact == 1 && !empty ($this->element->product_contact)))) {
		$empty = '';
		$params = new HikaParameter($empty);
		global $Itemid;
		$url_itemid='';
		if(!empty($Itemid))
			$url_itemid = '&Itemid='.$Itemid;

		echo $this->cart->displayButton(JText :: _('CONTACT_US_FOR_INFO'), 'contact_us', $params, hikashop_completeLink('product&task=contact&cid=' . $this->element->product_id.$url_itemid), 'window.location=\'' . hikashop_completeLink('product&task=contact&cid=' . $this->element->product_id.$url_itemid) . '\';return false;');
	}
?>
	</div>
<?php
	$this->setLayout('show_block_dimensions');
	$html = $this->loadTemplate();
	echo $html;
	if(!empty($html))
		echo '<br />';
	unset($html);
?>
	<div id="hikashop_product_vote_mini" class="hikashop_product_vote_mini">
<?php
	if($this->params->get('show_vote_product') == '-1')
		$this->params->set('show_vote_product', $this->config->get('show_vote_product'));

	if($this->params->get('show_vote_product')) {
		$js = '';
		$this->params->set('vote_type', 'product');
		if(isset($this->element->main))
			$product_id = $this->element->main->product_id;
		else
			$product_id = $this->element->product_id;
		$this->params->set('vote_ref_id',$product_id);
		echo hikashop_getLayout('vote', 'mini', $this->params, $js);
	}
?>
	</div>
<?php
	if(!empty($this->fields)) {
		$this->setLayout('show_block_custom_main');
		echo $this->loadTemplate();
	}
?>
	<span id="hikashop_product_id_main" class="hikashop_product_id_main">
		<input type="hidden" name="product_id" value="<?php echo $this->element->product_id; ?>" />
	</span>
<?php if(!empty($this->element->extraData->rightEnd)) { echo implode("\r\n",$this->element->extraData->rightEnd); } ?>
</div>
<div id="hikashop_product_bottom_part" class="hikashop_product_bottom_part">
<?php if(!empty($this->element->extraData->bottomBegin)) { echo implode("\r\n",$this->element->extraData->bottomBegin); } ?>
	<div id="hikashop_product_description_main" class="hikashop_product_description_main" itemprop="description">
<?php
	echo JHTML::_('content.prepare',preg_replace('#<hr *id="system-readmore" */>#i','',$this->element->product_description));
?>
	</div>
	<span id="hikashop_product_url_main" class="hikashop_product_url_main">
<?php
	if(!empty ($this->element->product_url)) {
		echo JText :: sprintf('MANUFACTURER_URL', '<a href="' . $this->element->product_url . '" target="_blank">' . $this->element->product_url . '</a>');
	}
?>
	</span>
<?php
	$this->setLayout('show_block_product_files');
	echo $this->loadTemplate();
?>
<?php if(!empty($this->element->extraData->bottomMiddle)) { echo implode("\r\n",$this->element->extraData->bottomMiddle); } ?>
<?php if(!empty($this->element->extraData->bottomEnd)) { echo implode("\r\n",$this->element->extraData->bottomEnd); } ?>
</div> </div>
	<input type="hidden" name="product_id" value="<?php echo $this->element->product_id; ?>"/>
	<input type="hidden" name="ctrl" value="product"/>
	<input type="hidden" name="current_price" id="current_price" value="<?php echo $this->current_price_amount; ?>"/>
	<input type="hidden" name="starting_price" id="starting_price" value="<?php echo $this->starting_price_amount; ?>"/>
	<input type="hidden" name="bid_increment" id="bid_increment" value="<?php echo $this->bid_increment; ?>"/>
	<input type="hidden" name="task" value="bid"/>
	<input type="hidden" name="return_url" value="<?php
		echo urlencode(base64_encode(hikashop_currentUrl()));
	?>"/>
</form>
<?php
if($this->show_auction_history_in_page) {
	$this->setLayout('show_block_auction_history');
	echo $this->loadTemplate();
}
?>
<form>
