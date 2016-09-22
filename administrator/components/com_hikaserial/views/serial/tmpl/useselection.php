<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php if($this->confirm) return; ?>
<div class="hikas_confirm">
<?php if($this->singleUser) {?>
	<?php echo JText::_('HIKA_CONFIRM_USER')?><br/>
	<table class="hikas_options">
		<tr>
			<td class="key"><label><?php echo JText::_('HIKA_NAME'); ?></label></td>
			<td id="hikaserial_pack_name"><?php echo $this->rows->pack_name; ?></td>
		</tr>
		<tr>
			<td class="key"><label><?php echo JText::_('PACK_DATA'); ?></label></td>
			<td id="hikaserial_pack_data"><?php echo $this->rows->pack_data; ?></td>
		</tr>
		<tr>
			<td class="key"><label><?php echo JText::_('PACK_GENERATOR'); ?></label></td>
			<td id="hikaserial_pack_data"><?php echo $this->rows->pack_generator; ?></td>
		</tr>
		<tr>
			<td class="key"><label><?php echo JText::_('ID'); ?></label></td>
			<td id="hikaserial_pack_id"><?php echo $this->rows->pack_id; ?></td>
		</tr>
	</table>
<?php } else { ?>
	<?php echo JText::_('HIKA_CONFIRM_USERS')?><br/>
	<table class="hikas_listing">
		<thead>
			<tr>
				<th class="title">
					<?php echo JText::_('HIKA_LOGIN'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('PACK_DATA'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('PACK_GENERATOR'); ?>
				</th>
				<th class="title">
					<?php echo JText::_('ID'); ?>
				</th>
			</tr>
		</thead>
<?php foreach($this->rows as $row) { ?>
		<tr>
			<td><?php echo $row->pack_name; ?></td>
			<td><?php echo $row->pack_data; ?></td>
			<td><?php echo $row->pack_generator; ?></td>
			<td><?php echo $row->pack_id; ?></td>
		</tr>
<?php } ?>
	</table>
<?php } ?>
	<div class="hikas_confirm_btn"><a href="#" onclick="window.top.hikaserial.submitBox(<?php echo $this->data; ?>); return false;"><img src="<?php echo HIKASERIAL_IMAGES ?>save.png"/><span><?php echo Jtext::_('HIKA_OK'); ?></span></a></div>
</div>
