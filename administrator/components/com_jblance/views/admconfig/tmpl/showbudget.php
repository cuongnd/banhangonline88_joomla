<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	28 July 2012
 * @file name	:	views/admconfig/tmpl/showbudget.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Shows Budget Range (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 
 JHtml::_('behavior.multiselect');
 
 $saveOrder	= ($this->lists['order'] == 'b.ordering' && $this->lists['order_Dir'] == 'asc');
 if($saveOrder){
 	$saveOrderingUrl = 'index.php?option=com_jblance&task=admconfig.saveOrderAjax&tmpl=component';
 	JHtml::_('sortablelist.sortable', 'configList', 'adminForm', strtolower($this->lists['order_Dir']), $saveOrderingUrl);
 }
?>
<form action="index.php" method="post" id="adminForm" name="adminForm">
<div id="j-sidebar-container" class="span2">
	<?php include_once(JPATH_COMPONENT.'/views/configmenu.php'); ?>
</div>
<div id="j-main-container" class="span10">
	<table class="table table-striped" id="configList">
	<thead>
		<tr>
			<th width="1%" class="nowrap center">
				<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'b.ordering', $this->lists['order_Dir'], $this->lists['order'], null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
			</th>
			<th width="1%" >
				<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
			</th>
			<th>
				<?php echo JText::_('COM_JBLANCE_BUDGET_RANGE'); ?>
			</th>
			<th width="10%" class="nowrap center">
				<?php echo JText::_('COM_JBLANCE_PROJECT_TYPE'); ?>
			</th>
 			<th width="1%">
 				<?php echo JText::_('JPUBLISHED'); ?>
 			</th>
 			<th width="1%">
 				<?php echo JText::_('JGRID_HEADING_ID'); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="7">
				<?php echo $this->pageNav->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	for($i=0, $n=count($this->rows); $i < $n; $i++){
		$row = $this->rows[$i];

		$link_edit	= JRoute::_('index.php?option=com_jblance&view=admconfig&layout=editbudget&cid[]='. $row->id);
		$published = JHtml::_('jgrid.published', $row->published, $i, 'admconfig.');
		?>
		<tr sortable-group-id="<?php echo JText::_($row->project_type); ?>">
			<td>
				<?php 
				$iconClass = '';
				if(!$saveOrder){
					$iconClass = ' inactive tip-top hasTooltip" title="' . JHtml::tooltipText('JORDERINGDISABLED');
				}
				?>
				<span class="sortable-handler<?php echo $iconClass ?>">
					<i class="icon-menu"></i>
				</span>
				<?php if($saveOrder) : ?>
				<input type="text" style="display:none" name="order[]" size="5" value="<?php echo $row->ordering;?>" class="width-20 text-area-order" />
				<?php endif; ?>
			</td>
			<td>
				<?php echo JHtml::_('grid.id', $i, $row->id); ?>
			</td>
			<td>
				<a href="<?php echo $link_edit?>">
					<?php echo $row->title.' ('.JblanceHelper::formatCurrency($row->budgetmin, true, false, 0).' - '.JblanceHelper::formatCurrency($row->budgetmax, true, false, 0).')'; ?>
					<?php if($row->project_type == 'COM_JBLANCE_HOURLY'){
						echo '/'.JText::_('COM_JBLANCE_HOUR');
					}
					?>
				</a>					
			</td>
			<td align="center">
				<span class="label label-success"><?php echo JText::_($row->project_type); ?></span>
			</td>										
 			<td class="nowrap center">
 				<?php echo $published; ?>
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
	<input type="hidden" name="view" value="admconfig" />
	<input type="hidden" name="layout" value="showbudget" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="ctype" value="budget" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>