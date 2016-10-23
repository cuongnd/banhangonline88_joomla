<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	15 March 2012
 * @file name	:	views/admconfig/tmpl/showcustomfield.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Shows list of Custom Fields (jblance)
 */
 defined('_JEXEC') or die('Restricted access');

 JHtml::_('behavior.multiselect');
 JHtml::_('formbehavior.chosen', 'select');
 
 $saveOrder	= ($this->lists['order'] == 'c.ordering' && $this->lists['order_Dir'] == 'asc');
 if($saveOrder){
 	$saveOrderingUrl = 'index.php?option=com_jblance&task=admconfig.saveOrderAjax&tmpl=component';
 	JHtml::_('sortablelist.sortable', 'configList', 'adminForm', strtolower($this->lists['order_Dir']), $saveOrderingUrl, false, true);
 }
?>
<form action="<?php echo JRoute::_('index.php?option=com_jblance&view=admconfig&layout=showcustomfield'); ?>" method="post" name="adminForm" id="adminForm">
<div id="j-sidebar-container" class="span2">
	<?php include_once(JPATH_COMPONENT.'/views/configmenu.php'); ?>
</div>
<div id="j-main-container" class="span10">
	<div id="filter-bar" class="btn-toolbar">
		<div class="btn-group pull-right">
			<?php echo $this->lists['field_type']; ?>
		</div>
	</div>
	<table class="table table-striped" id="configList">
	<thead>
		<tr>
			<th width="1%" class="nowrap center">
				<?php echo JHtml::_('searchtools.sort', '', 'c.ordering', $this->lists['order_Dir'], $this->lists['order'], null, 'asc', 'JGRID_HEADING_ORDERING', 'icon-menu-2'); ?>
			</th>
			<th width="1%" >
				<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
			</th>
			<th>
				<?php echo JText::_('COM_JBLANCE_TITLE'); ?>
			</th>
			<th width="10%">
				<?php echo JText::_('COM_JBLANCE_FIELD_FOR'); ?>
			</th>
			<th width="8%">
				<?php echo JText::_('COM_JBLANCE_TYPE'); ?>
			</th>
			<th width="5%">
				<?php echo JText::_('COM_JBLANCE_REQUIRED'); ?>
			</th>
			<th width="5%">
				<?php echo JText::_('JPUBLISHED'); ?>
			</th>
			<th width="1%">
				<?php echo JText::_('JGRID_HEADING_ID'); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="9">
				<?php //echo $this->pageNav->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	for ($i=0, $n=count($this->rows); $i < $n; $i++) {
		$row = $this->rows[$i];
		$edit_field	= JRoute::_('index.php?option=com_jblance&view=admconfig&layout=editcustomfield&cid[]='.$row->id);
		$edit_group	= JRoute::_('index.php?option=com_jblance&view=admconfig&layout=editcustomfield&cid[]='.$row->id.'&type=group');
		$published = JHtml::_('jgrid.published', $row->published, $i, 'admconfig.');
		
		if($row->parent == 0){
			$parentsStr = "";
			$level = 1;
		}
		else {
			$parentsStr = "";
			$_currentParentId = $row->parent+1000;
			$parentsStr = " ".$_currentParentId." 1";
			$level = 2;
		}
		?>
		<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $row->parent+1000; ?>" item-id="<?php echo $row->id+1000?>" parents="<?php echo $parentsStr?>" level="<?php echo $level?>">
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
			<?php if($row->parent == 0): ?>
			<td align="center" colspan="4">
				<strong><a href="<?php echo $edit_group; ?>"><?php echo $row->field_title; ?></a></strong>				
			</td>
			<?php else: ?>
			<td>
				<?php echo str_repeat('<span class="gi">|—</span>', $level - 1) ?>
				<a href="<?php echo $edit_field; ?>"><?php echo $row->field_title; ?></a>					
			</td>
			<td>
				<?php echo ucfirst($row->field_for); ?>
			</td>										
			<td>
				<span class="label label-success"><?php echo $row->field_type; ?></span>
			</td>										
			<td class="center">
				<?php echo JblanceHelper::boolean($row->required, $i, 'admconfig.required', 'admconfig.unrequired'); ?>
			</td>	
			<?php endif; ?>									
			<td class="center">
				<?php echo $published; ?>
			</td>
			<td class="center">
				<?php echo $row->id; ?>
			</td>									
		</tr>
		<?php
	}
	?>
	</tbody>
	</table>
	<div class="alert alert-block">
		<h3><?php echo JText::_('COM_JBLANCE_NOTE'); ?>:</h3>
		<p><?php echo JText::_('COM_JBLANCE_CUSTOM_FIELD_FILE_WARNING');?></p>
	</div>

	<input type="hidden" name="option" value="com_jblance" />
	<input type="hidden" name="view" value="admconfig" />
	<input type="hidden" name="layout" value="showcustomfield" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="ctype" value="customfield" />
	<input type="hidden" name="fieldfor" value="<?php echo $this->fieldfor; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>