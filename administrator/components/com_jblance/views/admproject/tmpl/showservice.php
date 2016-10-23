<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	14 November 2014
 * @file name	:	views/admproject/tmpl/showservice.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Shows list of Services (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 
?>
<form action="<?php echo JRoute::_('index.php?option=com_jblance&view=admproject&layout=showservice'); ?>" method="post" name="adminForm" id="adminForm">
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
	<div id="filter-bar" class="btn-toolbar">
		<div class="filter-search btn-group pull-left">
			<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo htmlspecialchars($this->lists['search']);?>" />
		</div>
		<div class="btn-group pull-left">
			<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="icon-search"></i></button>
			<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.getElementById('filter_status').value='';this.form.submit();"><i class="icon-remove"></i></button>
		</div>
		<div class="btn-group pull-left">
			<?php //echo $this->lists['status']; ?>
		</div>
	</div>
	
	<table class="table table-striped">
		<thead>
			<tr>
				<th width="10"><?php echo '#'; ?>
				</th>
				<th width="10"><input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th><?php echo JHtml::_('grid.sort', 'COM_JBLANCE_SERVICE_TITLE', 's.service_title', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th width="5%" class="nowrap center"><?php echo JText::_('COM_JBLANCE_SOLD'); ?>
				</th>
				<th width="1%" class="nowrap center"><?php echo JHtml::_('grid.sort', 'COM_JBLANCE_APPROVED', 's.approved', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th width="1%" class="nowrap center"><?php echo JHtml::_('grid.sort', 'JGRID_HEADING_ID', 's.id', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="11"><?php echo $this->pageNav->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
			<?php
			for($i=0, $n=count($this->rows); $i < $n; $i++){
				$row = $this->rows[$i];
				$link_edit	= JRoute::_('index.php?option=com_jblance&view=admproject&layout=editservice&cid[]='. $row->id);
				//$bidsCount = $model->countBids($row->id);
			?>
			<tr>
				<td>
					<?php echo $this->pageNav->getRowOffset($i); ?>
				</td>
				<td>
					<?php echo JHtml::_('grid.id', $i, $row->id); ?>
				</td>
				<td>
					<a href="<?php echo $link_edit;?>"><?php echo $row->service_title; ?></a>
				</td>
				<td class="center">
					<span class="badge badge-success"><?php echo $row->buycount; ?></span>
				</td>
				<td class="center">
					<?php echo JblanceHelper::boolean($row->approved, $i, null, null); ?>
				</td>
				<td class="center">
					<?php echo $row->id;?>
				</td>
			</tr>
			<?php
			}
			?>
		</tbody>
	</table>
	<input type="hidden" name="option" value="com_jblance" />
	<input type="hidden" name="view" value="admproject" />
	<input type="hidden" name="layout" value="showservice" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>