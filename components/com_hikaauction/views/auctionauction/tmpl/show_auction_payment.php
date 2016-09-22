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
if(isset($this->cancel_auction) && (int)$this->cancel_auction == 1) {
?>
	<div class="auction_cancel">
		<h2><?php echo JText::sprintf('HKA_AUCTION_GIVE_UP_CONFIRMATION',$this->fullProduct->product_name); ?></h2>
		<br/>
		<div class="auction_decline_block">
			<form action="<?php echo hikaauction::completeLink('auction&task=cancel_auction'); ?>" method="post" name="hikaauction_product_form" enctype="multipart/form-data">
				<input type="submit" value="<?php echo JText::_('HKA_AUCTION_GIVE_UP');?>">
				<input type="hidden" id="product_id" name="product_id" value="<?php echo $this->fullProduct->product_id; ?>"/>
				<input type="hidden" name="ctrl" value="auction"/>
				<input type="hidden" name="task" value="cancel_auction"/>
				<input type="hidden" name="return_url" value="<?php
					echo urlencode(base64_encode(hikaauction::currentUrl()));
				?>"/>
			</form>
		</div>
		<div class="auction_payment_block">
			<form action="<?php echo hikaauction::completeLink('auction&task=show_auction_payment'); ?>" method="post" name="hikaauction_product_form" enctype="multipart/form-data">
				<input type="submit" value="<?php echo JText::_('HKA_AUCTION_CONTINUE');?>">
				<input type="hidden" id="product_id" name="product_id" value="<?php echo $this->fullProduct->product_id; ?>"/>
				<input type="hidden" name="ctrl" value="auction"/>
				<input type="hidden" name="task" value="show_auction_payment"/>
				<input type="hidden" name="cancel_auction" value="0"/>
				<input type="hidden" name="return_url" value="<?php
					echo urlencode(base64_encode(hikaauction::currentUrl()));
				?>"/>
			</form>
		</div>
	</div>
<?php
} else {
?>
	<div class="auction_payment">
		<h2><?php echo JText::sprintf('HKA_AUCTION_FINISHED_WINNER_MESSAGE',$this->fullProduct->product_name); ?></h2>
		<br/>
		<div class="auction_decline_block">
		<?php
			echo JText::_('HKA_AUCTION_GIVE_UP_TEXT') . '</br>';
		?>
			<form action="<?php echo hikaauction::completeLink('auction&task=show_auction_payment'); ?>" method="post" name="hikaauction_product_form" enctype="multipart/form-data">
				<input type="submit" value="<?php echo JText::_('HKA_AUCTION_GIVE_UP');?>">
				<input type="hidden" id="product_id" name="product_id" value="<?php echo $this->fullProduct->product_id; ?>"/>
				<input type="hidden" name="ctrl" value="auction"/>
				<input type="hidden" name="task" value="show_auction_payment"/>
				<input type="hidden" name="cancel_auction" value="1"/>
				<input type="hidden" name="return_url" value="<?php
					echo urlencode(base64_encode(hikaauction::currentUrl()));
				?>"/>
			</form>
		</div>
		<div class="auction_payment_block">
			<form action="<?php echo hikaauction::completeLink('auction&task=pay_auction'); ?>" method="post" name="hikaauction_product_form" enctype="multipart/form-data">
				<?php
				if(isset($this->user_address)) {
				echo JText::_('HKA_AUCTION_SELECT_ADDRESS');
				?>
				</br>
				<select class="auction_user_address" name="auction_user_address" style="width:100%;text-align:center;">
					<?php
					foreach($this->user_address as $id => $address){
						$tmp_address = '';
						if(isset($address->address_street))
							$tmp_address .= $address->address_street;
						if(isset($address->address_post_code))
							$tmp_address .= ' ' . $address->address_post_code;
						if(isset($address->address_city))
							$tmp_address .= ', ' . $address->address_city;
						if($tmp_address != '')
							echo '<option value="' . $address->address_id.'">' . $tmp_address . '</option>';
					}
					?>
				</select>
				</br>
				<input type="submit" value="<?php echo JText::_('PROCEED_TO_CHECKOUT');?>">
				<input type="hidden" id="product_id" name="product_id" value="<?php echo $this->fullProduct->product_id; ?>"/>
				<input type="hidden" name="ctrl" value="auction"/>
				<input type="hidden" name="task" value="pay_auction"/>
				<input type="hidden" name="return_url" value="<?php
					echo urlencode(base64_encode(hikaauction::currentUrl()));
					?>"/>
				</br>
				<?php
				} else {
					echo JText::sprintf('HKA_AUCTION_CREATE_ADDRESS',hikashop_completeLink('address',false,true));
				}

				?>
	 		</form>
	 	</div>
	</div>
<?php
}
?>
