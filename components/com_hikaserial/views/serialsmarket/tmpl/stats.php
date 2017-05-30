<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div>
<form action="<?php echo hikamarket::completeLink('serials&task=stats&pack_id='.$this->pack_id.'&product_id='.$this->product_id); ?>" method="post" id="adminForm" name="adminForm">
<?php
	$cols = 3;
?>
	<table class="table table-striped table-bordered" style="width:100%;margin-top:3px;">
		<thead>
			<tr>
				<th><?php echo JText::_('SERIAL_DATA'); ?></th>
				<th><?php echo JText::_('SERIAL_STATUS'); ?></th>
<?php
	if($this->acls['order/show'] || $this->acls['order/listing']) {
		$cols++;
?>
				<th><?php echo JText::_('HIKASERIAL_ORDER'); ?></th>
<?php
	}
?>
<?php
	if($this->acls['user/show'] || $this->acls['user/listing']) {
		$cols++;
?>
				<th><?php echo JText::_('HIKA_USER'); ?></th>
<?php
	}
?>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="<?php echo $cols; ?>"><?php
					echo $this->pagination->getListFooter();
					echo $this->pagination->getResultsCounter();
				?></td>
			</tr>
		</tfoot>
		<tbody>
<?php
if(!empty($this->serials)) {
	foreach($this->serials as $serial) {
?>
			<tr>
				<td><?php
					echo $this->escape($serial->serial_data);
				?></td>
				<td><span id="hikaserial_serial_status_<?php echo (int)$serial->serial_id; ?>"><?php
					echo $this->escape($serial->serial_status);
				?></span></td>
<?php
		if($this->acls['order/show'] || $this->acls['order/listing']) {
?>
				<td><?php
					if($serial->order_id > 0 && isset($this->orders[ (int)$serial->order_id ])) {
						if($this->acls['order/show'])
							echo '<a href="'.hikamarket::completeLink('order&task=show&cid='.(int)$serial->order_id).'">';
						echo $this->escape( $this->orders[ (int)$serial->order_id ]->order_number );
						if($this->acls['order/show'])
							echo '</a>';
					} else
						echo '<span class="hikaserial_no_value">-</span>';
				?></td>
<?php
		}
?>
<?php
		if($this->acls['user/show'] || $this->acls['user/listing']) {
?>
				<td><?php
					if($serial->serial_user_id > 0 && isset($this->users[ (int)$serial->serial_user_id ])) {
						$user = $this->users[ (int)$serial->serial_user_id ];
						$user = !empty($user->name) ? $user->name : $user->user_email;

						if($this->acls['user/show'])
							echo '<a href="'.hikamarket::completeLink('user&task=show&cid='.(int)$serial->serial_user_id).'">';
						echo $this->escape($user);
						if($this->acls['user/show'])
							echo '</a>';
					} else
						echo '<span class="hikaserial_no_value">-</span>';
				?></td>
<?php
		}
?>
			</tr>
<?php
	}
} else {
?>
			<tr>
				<td colspan="<?php echo $cols; ?>">
					<div class="hikaserial_empty_msg"><?php
						echo JText::_('NOTICE_NO_SERIALS');
					?></div>
				</td>
			</tr>
<?php } ?>
		</tbody>
	</table>

	<input type="hidden" name="Itemid" value="<?php echo $this->Itemid; ?>" />
	<input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>" />
	<input type="hidden" name="task" value="stats" />
	<input type="hidden" name="pack_id" value="<?php echo (int)$this->pack_id; ?>" />
	<input type="hidden" name="product_id" value="<?php echo (int)$this->product_id; ?>" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->pageInfo->filter->order->value; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->pageInfo->filter->order->dir; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
