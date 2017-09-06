<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	19 March 2012
 * @file name	:	views/admproject/tmpl/showsubscr.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Shows list of subscribers (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 JHtml::_('behavior.modal');
 JHtml::_('formbehavior.chosen', 'select');
 JHtml::_('behavior.multiselect');
 
 $config 		= JblanceHelper::getConfig();
 $currencysym 	= $config->currencySymbol;
 $dformat 		= $config->dateFormat;
 ?>
 
<form action="<?php echo JRoute::_('index.php?option=com_jblance&view=admproject&layout=showsubscr'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
	<div id="filter-bar" class="btn-toolbar">
		<div class="filter-search btn-group pull-left">
			<input type="text" name="sinv_num" id="sinv_num" placeholder="<?php echo JText::_('COM_JBLANCE_INVOICE_NO'); ?>" class="input-small" value="<?php echo htmlspecialchars($this->lists['sinv_num']);?>" />
		</div>
		<div class="filter-search btn-group pull-left">
			<input type="text" name="suser_id" id="suser_id" placeholder="<?php echo JText::_('COM_JBLANCE_USERID'); ?>" class="input-small" value="<?php echo htmlspecialchars($this->lists['suser_id']);?>" />
		</div>
		<div class="filter-search btn-group pull-left">
			<input type="text" name="ssubscr_id" id="ssubscr_id" placeholder="<?php echo JText::_('COM_JBLANCE_SUBSCR_ID'); ?>" class="input-small" value="<?php echo htmlspecialchars($this->lists['ssubscr_id']);?>" />
		</div>
		<div class="btn-group pull-left">
			<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="icon-search"></i></button>
			<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('suser_id').value='';document.getElementById('ssubscr_id').value='';document.getElementById('sinv_num').value='';this.form.getElementById('subscr_status').value='';this.form.getElementById('subscr_plan').value='0';this.form.getElementById('ug_id').value='';this.form.submit();"><i class="icon-remove"></i></button>
		</div>
		<div class="filter-search btn-group pull-left">
			<?php echo $this->lists['ug_id']; ?>
		</div>
		<div class="filter-search btn-group pull-left">
			<?php echo $this->lists['subscr_plan']; ?>
		</div>
		<div class="filter-search btn-group pull-left">
			<?php echo $this->lists['subscr_status']; ?>
		</div>
	</div>

    <table class="table table-striped">
		<thead>
		    <tr>
			    <th width="10">
			    	<?php echo JText::_('#'); ?>
			    </th>
			    <th width="10">
			    	<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
			    </th>
			    <th>
			    	<?php echo JText::_('COM_JBLANCE_PLAN_NAME'); ?>
			    </th>
			    <th width="15%">
			    	<?php echo JText::_('COM_JBLANCE_SUBSCR_NAME'); ?>
			    </th>
			    <th width="10%">
			    	<?php echo JText::_('COM_JBLANCE_GATEWAY'); ?>
			    </th>
			    <th width="5%" class="nowrap center">
					<?php echo JText::_('COM_JBLANCE_DAYS_LEFT'); ?>
			    </th>
			    <th width="8%">
			    	<?php echo JText::_('COM_JBLANCE_STATUS'); ?>
			    </th>
			    <th width="10%">
			    	<?php echo JText::_('COM_JBLANCE_START'); ?>
			    </th>
			    <th width="10%">
			    	<?php echo JText::_('COM_JBLANCE_END'); ?>
			    </th>
			    <th width="5%" class="nowrap center">
			    	<?php echo JText::_('COM_JBLANCE_PRICE').' ('.$currencysym.')'; ?>
			    </th>
			    <th width="5%" class="nowrap center">
			    	<?php echo JText::_('COM_JBLANCE_INVOICE_NO'); ?>
			    </th>
				<th width="1%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 'u.id', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
		    </tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="15">
					<?php echo $this->pageNav->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
	    <?php 
	   	for($i=0, $n=count($this->rows); $i < $n; $i++){
			$row = $this->rows[$i];
	        $uurl = 'index.php?option=com_users&task=user.edit&id='.$row->uid;
	        $over = '';
			$link_edit	= JRoute::_('index.php?option=com_jblance&view=admproject&layout=editsubscr&cid[]='.$row->id);
			$link_invoice	= JRoute::_('index.php?option=com_jblance&view=admproject&layout=invoice&id='.$row->id.'&tmpl=component&print=1&type=plan');
	    ?>
	        <tr>
		       	<td>
		       		<?php echo $this->pageNav->getRowOffset($i); ?>
		       	</td>
		        <td>
		       		<?php echo JHtml::_('grid.id', $i, $row->id); ?>
		        </td>
		        <td>
		        	<span class="small">[<?php echo $row->sid ?>]</span> <a href="<?php echo $link_edit; ?>"><?php echo $row->name ?></a>
		        </td>
		        <td>
		        	<span class="small">[<?php echo $row->uid ?>]</span> <a<?php echo $over ?>  href="<?php echo $uurl; ?>"><?php echo $row->uname ?></a>
		        </td>
		        <td class="nowrap center">
		        	<?php echo JblanceHelper::getGwayName($row->gateway); ?>
		        </td>
		        <td class="nowrap center">
		        	<?php echo $row->days; ?></td>
		        <td align="center">
		        	<?php echo JblanceHelper::getPaymentStatus($row->approved); ?>
		        </td>
		        <td class="nowrap center">
		        	<?php echo $row->date_approval != "0000-00-00 00:00:00" ? JHtml::_('date', $row->date_approval, $dformat, true) : "&nbsp;"; ?>
		        </td>
		        <td class="nowrap center">
		        	<?php echo $row->date_expire != "0000-00-00 00:00:00" ? JHtml::_('date', $row->date_expire, $dformat, true) : "&nbsp;"; ?>
		        </td>
		        <td align="right">
		        	<?php echo JblanceHelper::formatCurrency($row->price, false); ?>
		        </td>
				 <td class="nowrap center">
					<a rel="{handler: 'iframe', size: {x: 650, y: 450}}" href="<?php echo $link_invoice; ?>" class="modal"><?php echo $row->invoiceNo; ?></a>
				</td>
				 <td class="nowrap center">
					<?php echo $row->id; ?>
				</td>
	        </tr>
	    <?php
	    }
	    ?>
		</tbody>	
    </table>

	<input type="hidden" name="option" value="com_jblance" />
	<input type="hidden" name="view" value="admproject" />
	<input type="hidden" name="layout" value="showsubscr" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>