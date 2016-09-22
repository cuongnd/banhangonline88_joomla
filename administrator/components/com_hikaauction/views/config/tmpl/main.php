<?php
/**
 * @package	HikaShop for Joomla!
 * @version	1.2.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php if(!HIKAAUCTION_BACK_RESPONSIVE) { ?>
<div id="page-main">
	<table style="width:100%">
		<tr>
			<td valign="top" width="50%">
<?php } else { ?>
<div id="page-main" class="row-fluid">
	<div class="span6">
<?php } ?>
<fieldset class="adminform">
	<legend><?php echo JText::_('HKA_BEHAVIOUR_OPTIONS'); ?></legend>
	<table class="admintable table" cellspacing="1">
		<!-- <tr>
			<td class="key"><?php echo JText::_('HKA_DEALMODE'); ?></td>
			<td><?php  ?></td>
		</tr>
		-->
		<tr>
			<td class="key"><?php echo JText::_('HKA_DISPLAY_STARTING_AUCTION_PRICE'); ?></td>
			<td><?php echo JHTML::_('hikaselect.booleanlist', 'config[display_starting_auction_price]', '', (int)$this->config->get('display_starting_auction_price', 0)); ?></td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_('HKA_DISPLAY_NB_BIDS'); ?></td>
			<td><?php echo JHTML::_('hikaselect.booleanlist', 'config[display_nb_bid]', '', (int)$this->config->get('display_nb_bid', 0)); ?></td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_('HKA_DISPLAY_NB_BIDDERS'); ?></td>
			<td><?php echo JHTML::_('hikaselect.booleanlist', 'config[display_nb_bidders]', '', (int)$this->config->get('display_nb_bidders', 0)); ?></td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_('HKA_LIMIT_GLOBAL_AUCTION_QUANTITY'); ?></td>
			<td><?php echo JHTML::_('hikaselect.booleanlist', 'config[limit_auction_global_quantity]', '', (int)$this->config->get('limit_auction_global_quantity', 0)); ?></td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_('HKA_SHOW_AUCTION_PRICE_IN_LISTING'); ?></td>
			<td><?php echo JHTML::_('hikaselect.booleanlist', 'config[show_auction_price_in_listing]', '', (int)$this->config->get('show_auction_price_in_listing', 1)); ?></td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_('HKA_SHOW_AUCTION_HISTORY_IN_PAGE'); ?></td>
			<td><?php echo JHTML::_('hikaselect.booleanlist', 'config[show_auction_history_in_page]', '', (int)$this->config->get('show_auction_history_in_page', 0)); ?></td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_('HKA_ANONYMOUS_AUCTION_HISTORY'); ?></td>
			<td><?php echo JHTML::_('hikaselect.booleanlist', 'config[anonymous_auction_history]', '', (int)$this->config->get('anonymous_auction_history', 0)); ?></td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_('HKA_HISTORY_BIDDING_NAME'); ?></td>
				<td><?php
					$options = array(
						JHTML::_('hikaselect.option', 'username', JText::_('HIKA_USERNAME')),
						JHTML::_('hikaselect.option', 'name', JText::_('CART_PRODUCT_NAME'))
					);
					echo JHTML::_('hikaselect.genericlist', $options, 'config[bidding_history_name]', 'class="bidding_history_name_option" size="1"', 'value', 'text', $this->config->get('bidding_history_name', 'username'));
				?>
			</td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_('HKA_BID_INCREMENT'); ?></td>
			<td><input type="text" name="config[bid_increment]" value="<?php echo (int)$this->config->get('bid_increment', 1); ?>"/></td>
		</tr>
		<!-- <tr>
			<td class="key"><?php echo JText::_('HKA_OPT_PRICEMODE'); ?></td>
			<td><?php  ?></td>
		</tr> -->
		<tr>
			<td class="key"><?php echo JText::_('HKA_BIDDING_MODE'); ?></td>
				<td><?php
					$options = array(
						JHTML::_('hikaselect.option', 'bid_increment_bidding', JText::_('BID_INCREMENT_BIDDING')),
						JHTML::_('hikaselect.option', 'current_price_bidding', JText::_('CURRENT_PRICE_BIDDING')),
						JHTML::_('hikaselect.option', 'free_bidding', JText::_('FREE_BIDDING'))
					);
					echo JHTML::_('hikaselect.genericlist', $options, 'config[bidding_mode]', 'class="bidding_mode_option" size="1"', 'value', 'text', $this->config->get('bidding_mode', 'bid_increment_bidding'));
				?>
			</td>
		</tr>
	</table>
</fieldset>
<?php if(!HIKAAUCTION_BACK_RESPONSIVE) { ?>
		</td>
		<td valign="top" width="50%">
<?php } else { ?>
		</div>
		<div class="span6">
<?php } ?>
<fieldset class="adminform">
	<legend><?php echo JText::_('HKA_GENERAL_OPTIONS'); ?></legend>
	<table class="admintable table" cellspacing="1">
	<tr>
		<td class="key"><?php echo JText::_('HKA_MAX_AUCTION_DURATION'); ?></td>
		<td><input type="text" name="config[maxduration]" value="<?php echo (int)$this->config->get('maxduration', 30); ?>"/> <?php echo JText::_('HKA_DAYS'); ?></td>
	</tr>
	<?php  ?>
	<tr>
		<td class="key"><?php echo JText::_('CONFIRM_ORDER_STATUSES'); ?></td>
		<td><?php
			echo $this->orderstatusType->display('config[confirm_status]', $this->config->get('confirm_status', 'confirmed'));
		?></td>
	</tr>
	<?php  ?>
	</table>
</fieldset>
<fieldset class="adminform">
	<legend><?php echo JText::_('HKA_ADVANCED_OPTIONS'); ?></legend>
		<table class="admintable table" cellspacing="1">
		<tr>
			<td class="key"><?php echo JText::_('HKA_CRON_CHECKS_PERIOD'); ?></td>
			<td><input type="text" name="config[cron_checks_period]" value="<?php echo (int)$this->config->get('cron_checks_period', 7200); ?>"/></td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_('HKA_CRON_QUEUE_PERIOD'); ?></td>
			<td><input type="text" name="config[cron_queue_period]" value="<?php echo (int)$this->config->get('cron_queue_period', 1200); ?>"/></td>
		</tr>
		<tr>
			<td class="key"><?php echo JText::_('HKA_QUEUE_MAIL_ITEMS'); ?></td>
			<td><input type="text" name="config[queue_mail_items]" value="<?php echo (int)$this->config->get('queue_mail_items', 20); ?>"/></td>
		</tr>
		<?php  ?>
		</table>
		<?php if(!HIKAAUCTION_BACK_RESPONSIVE) { ?>
					</td>
				</tr>
			</table>
		</div>
		<?php } else { ?>
			</div>
		</div>
		<?php } ?>
