<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	03 April 2012
 * @file name	:	views/admproject/tmpl/showwithdraw.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Show Withdraw transactions (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 JHtml::_('behavior.multiselect');
 JHtml::_('behavior.modal');
 JHtml::_('formbehavior.chosen', 'select');
 
 $model 	  = $this->getModel();
 $config 	  = JblanceHelper::getConfig();
 $dformat 	  = $config->dateFormat;
 $currencysym = $config->currencySymbol;
?>
<form action="<?php echo JRoute::_('index.php?option=com_jblance&view=admproject&layout=showwithdraw'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
	<div id="filter-bar" class="btn-toolbar">
		<div class="filter-search btn-group pull-left">
			<input type="text" name="cinv_num" id="cinv_num" placeholder="<?php echo JText::_('COM_JBLANCE_INVOICE_NO'); ?>" class="input-small" value="<?php echo htmlspecialchars($this->lists['cinv_num']);?>" />
		</div>
		<div class="filter-search btn-group pull-left">
			<input type="text" name="cuser_id" id="cuser_id" placeholder="<?php echo JText::_('COM_JBLANCE_USERID'); ?>" class="input-small" value="<?php echo htmlspecialchars($this->lists['cuser_id']);?>" />
		</div>
		<div class="filter-search btn-group pull-left">
			<input type="text" name="ccredit_id" id="ccredit_id" placeholder="<?php echo JText::_('COM_JBLANCE_WITHDRAW_ID'); ?>" class="input-small" value="<?php echo htmlspecialchars($this->lists['ccredit_id']);?>" />
		</div>
		<div class="btn-group pull-left">
			<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="icon-search"></i></button>
			<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('cinv_num').value='';document.getElementById('cuser_id').value='';document.getElementById('ccredit_id').value='';this.form.getElementById('cstatus').value='';this.form.submit();"><i class="icon-remove"></i></button>
		</div>
		<div class="filter-search btn-group pull-left">
			<?php echo $this->lists['cstatus']; ?>
		</div>
	</div>
	
	<table class="table table-striped">
		<thead>
			<tr>
				<th width="1%">
					<?php echo JText::_('#'); ?>
				</th>
				<th width="1%" >
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th width="15%">
					<?php echo JText::_('COM_JBLANCE_NAME'); ?>
				</th>
				<th width="8%">
					<?php echo JText::_('COM_JBLANCE_REQUEST_DATE'); ?>
				</th>
				<th width="8%">
					<?php echo JText::_('COM_JBLANCE_GATEWAY'); ?>
				</th>
				<th width="8%">
					<?php echo JText::_('COM_JBLANCE_STATUS'); ?>
				</th>
				<th width="5%">
					<?php echo JText::_('COM_JBLANCE_APPROVED'); ?>
				</th>
				<th width="8%">
					<?php echo JText::_('COM_JBLANCE_AMOUNT').' ('.$currencysym.')'; ?>
				</th>
				<th width="8%">
					<?php echo JText::_('COM_JBLANCE_FEE').' ('.$currencysym.')'; ?>
				</th>
				<th width="10%">
					<?php echo JText::_('COM_JBLANCE_TOTAL_AMOUNT').' ('.$currencysym.')'; ?>
				</th>
				<th width="8%">
					<?php echo JText::_('COM_JBLANCE_INVOICE_NO'); ?>
				</th>
				<th width="21%">
					<?php echo JText::_('COM_JBLANCE_DEPOSIT_TO'); ?>
				</th>
				<th width="1%">
					<?php echo JText::_('JGRID_HEADING_ID'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="13">
					<?php echo $this->pageNav->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		$k = 0;
		for($i=0, $n=count($this->rows); $i < $n; $i++){
			$row = $this->rows[$i];
			$link_invoice	= JRoute::_('index.php?option=com_jblance&view=admproject&layout=invoice&id='.$row->id.'&tmpl=component&print=1&type=withdraw');
			?>
			<tr class="<?php echo "row$k"; ?>">
				<td>
					<?php echo $this->pageNav->getRowOffset($i); ?>
				</td>
				<td>
					<?php echo JHtml::_('grid.id', $i, $row->id); ?>
				</td>
				<td>
					<?php echo $row->name; ?>				
				</td>
				<td nowrap="nowrap">
					<?php echo JHtml::_('date', $row->date_withdraw, $dformat); ?>
				</td>
				<td>
					<?php echo JblanceHelper::getGwayName($row->gateway); ?>
				</td>
				<td align="center">
					<?php echo JblanceHelper::getPaymentStatus($row->approved); ?>
				</td>
				<td nowrap="nowrap">
					<?php echo ($row->date_approval != "0000-00-00 00:00:00" ?  JHtml::_('date', $row->date_approval, $dformat) :  "Never"); ?>
				</td>
				<td align="right">
					<?php echo JblanceHelper::formatCurrency($row->amount, false); ?>
				</td>
				<td align="right">
					<?php echo JblanceHelper::formatCurrency($row->withdrawFee, false); ?>
				</td>
				<td align="right">
					<?php echo JblanceHelper::formatCurrency($row->finalAmount, false); ?>
				</td>
				<td class="center" nowrap="nowrap">
					<a rel="{handler: 'iframe', size: {x: 650, y: 450}}" href="<?php echo $link_invoice; ?>" class="modal"><?php echo $row->invoiceNo; ?></a>
				</td>
				<td>
					<?php  $html = $model->getWithdrawParams($row->params); 
					echo $html; ?>
				</td>
				<td align="right">
					<?php echo $row->id;?>
				</td>
			</tr>
			<?php
			$k = 1 - $k;
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="com_jblance" />
	<input type="hidden" name="view" value="admproject" />
	<input type="hidden" name="layout" value="showwithdraw" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>