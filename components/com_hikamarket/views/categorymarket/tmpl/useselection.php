<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php if($this->confirm) return; ?>
<div class="hikam_confirm">
<?php if($this->singleUser) {?>
	<?php echo JText::_('HIKA_CONFIRM_USER')?><br/>
	<table class="hikam_options">
		<tr>
			<td class="key"><label><?php echo JText::_('HIKA_NAME'); ?></label></td>
			<td id="hikamarket_order_customer_name"><?php echo $this->rows->name; ?></td>
		</tr>
		<tr>
			<td class="key"><label><?php echo JText::_('HIKA_EMAIL'); ?></label></td>
			<td id="hikamarket_order_customer_email"><?php echo $this->rows->email; ?></td>
		</tr>
		<tr>
			<td class="key"><label><?php echo JText::_('ID'); ?></label></td>
			<td id="hikamarket_order_customer_id"><?php echo $this->rows->user_id; ?></td>
		</tr>
	</table>
<?php } else { ?>
	<?php echo JText::_('HIKA_CONFIRM_USERS')?><br/>
	<table class="hikam_listing">
		<thead>
			<tr>
				<th class="title">
					<?php echo JText::_('HIKA_LOGIN'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('HIKA_NAME'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('HIKA_EMAIL'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('ID'); ?>
				</th>
			</tr>
		</thead>
<?php foreach($this->rows as $row) { ?>
		<tr>
			<td><?php echo $row->login; ?></td>
			<td><?php echo $row->name; ?></td>
			<td><?php echo $row->email; ?></td>
			<td><?php echo $row->user_id; ?></td>
		</tr>
<?php } ?>
	</table>
<?php } ?>
	<div class="hikam_confirm_btn"><a href="#" onclick="window.parent.hikamarket.submitBox(<?php echo $this->data; ?>); return false;"><img src="<?php echo HIKAMARKET_IMAGES ?>save.png"/><span><?php echo Jtext::_('HIKA_OK'); ?></span></a></div>
</div>
