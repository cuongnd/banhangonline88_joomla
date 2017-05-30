<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
if(!hikamarket::acl('order/show/history'))
	return;

$acl_history_data = hikamarket::acl('order/show/historydata');

if(empty($this->ajax)) { ?>
<div id="hikamarket_order_block_history">
<?php
}

if(!empty($this->order->history)) {
?>
	<div class="hikamarket_ajax_loading_elem"></div>
	<div class="hikamarket_ajax_loading_spinner"></div>

<h3 style="display:inline-block"><?php echo JText::_('HISTORY')?></h3>
<div class="hikamarket_history_container">
<table id="hikamarket_order_history_listing" class="hikam_listing <?php echo (HIKASHOP_RESPONSIVE)?'table table-striped table-hover table-bordered':'hikam_table'; ?>">
	<thead>
		<tr>
			<th class="title"><?php
				echo JText::_('HIKA_TYPE');
			?></th>
			<th class="title"><?php
				echo JText::_('ORDER_STATUS');
			?></th>
			<th class="title"><?php
				echo JText::_('REASON');
			?></th>
			<th class="title"><?php
				echo JText::_('DATE');
			?></th>
<?php if($acl_history_data) { ?>
			<th class="title titlebox"></th>
<?php } ?>
		</tr>
	</thead>
	<tbody>
<?php
foreach($this->order->history as $k => $history) {
?>
		<tr>
			<td><?php
				$val = preg_replace('#[^a-z0-9]#i','_',strtoupper($history->history_type));
				$trans = JText::_($val);
				if($val != $trans)
					$history->history_type = $trans;
				echo $history->history_type;
			?></td>
			<td><?php
				echo hikamarket::orderStatus($history->history_new_status);
			?></td>
			<td><?php
				echo $history->history_reason;
			?></td>
			<td><?php
				echo hikamarket::getDate($history->history_created,'%Y-%m-%d %H:%M');
			?></td>
<?php if($acl_history_data) { ?>
			<td>
				<a onclick="return window.orderMgr.showHistory(this);" data-popup-href="<?php echo hikamarket::completeLink('order&task=history&order_id='.$this->order->order_id.'&cid='.$history->history_id, true); ?>" href="#"><img src="<?php echo HIKAMARKET_IMAGES; ?>icon-16/notice.png" alt="<?php echo JText::_('HIKAM_SEE_MORE'); ?>"/></a>
			</td>
<?php } ?>
		</tr>
<?php
}
?>
	</tbody>
</table>
</div>
<?php
}

if(!empty($this->ajax))
	return;

?>
</div>
<?php

if($acl_history_data) {
	echo $this->popup->display(
		'',
		'HIKAM_HISTORY_DETAILS',
		hikamarket::completeLink('order&task=history&order_id='.$this->order->order_id.'&cid=0', true),
		'hikamarket_showhistory_popup',
		750, 460, 'style="display:none;"', '', 'link'
	);
?>
<script type="text/javascript">
window.Oby.registerAjax('orderMgr.history',function(params){ window.orderMgr.refreshBlock('history'); });
window.orderMgr.showHistory = function(el) { return this.showEl(el, 'hikamarket_showhistory_popup'); };
</script>
<?php
}
