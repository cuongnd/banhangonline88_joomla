<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div class="hikam_toolbar">
	<div class="hikam_toolbar_btn hikam_btn_32">
		<div class="btn">
			<a href="#cancel" onclick="return window.parent.hikamarket.closeBox();"><span class="btnIcon iconM-32-back"></span><span class="btnName"><?php echo JText::_('HIKA_CANCEL'); ?></span></a>
		</div>
		<div class="hikam_toolbar_right">
			<div class="btn">
				<a href="#save" onclick="return window.hikamarket.submitform('save','adminForm');"><span class="btnIcon iconM-32-apply"></span><span class="btnName"><?php echo JText::_('HIKA_OK'); ?></span></a>
			</div>
		</div>
		<div style="clear:right"></div>
	</div>
</div>
<form action="<?php echo hikamarket::completeLink('order&task=save&cid='.$this->order->order_id); ?>" method="post" name="adminForm" id="adminForm">
<dl class="hikam_options">
	<dt><?php echo JText::_('ORDER_STATUS'); ?></dt>
	<dd><span class="order-label order-label-<?php echo preg_replace('#[^a-z_0-9]#i', '_', str_replace(' ','_',$this->order->order_status)); ?>"><?php
		echo hikamarket::orderStatus($this->order->order_status);
	?></span></dd>
	<dt><?php echo JText::_('ORDER_NEW_STATUS'); ?></dt>
	<dd><?php
		echo $this->order_status->display('order[general][order_status]', $this->order->order_status, 'onchange="window.orderMgr.status_changed(this);"', false, @$this->order_status_filters);
	?></dd>
</dl>
<script type="text/javascript">
if(!window.orderMgr)
	window.orderMgr = {};
window.orderMgr.status_changed = function(el) {
	var fields = ['hikamarket_order_notify_lbl', 'hikamarket_order_notify_val'], displayValue = '';
	if(el.value == '<?php echo $this->order->order_status; ?>')
		displayValue = 'none';
	window.hikamarket.setArrayDisplay(fields, displayValue);
};
window.orderMgr.general_history_changed = function(el) {
	var fields = ['hikamarket_history_general_msg'], displayValue = '';
	if(!el.checked) displayValue = 'none';
	window.hikamarket.setArrayDisplay(fields, displayValue);
};
</script>
	<input type="hidden" name="closepopup" value="1"/>
	<input type="hidden" name="cid" value="<?php echo (int)$this->order->order_id; ?>" />
	<input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="ctrl" value="order" />
<?php if(JRequest::getVar('tmpl', '') != '') { ?>
	<input type="hidden" name="tmpl" value="<?php echo $this->escape(JRequest::getVar('tmpl')); ?>" />
<?php } ?>
	<?php echo JHTML::_('form.token'); ?>
</form>
