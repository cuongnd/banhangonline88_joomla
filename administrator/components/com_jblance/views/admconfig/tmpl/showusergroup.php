<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	14 March 2012
 * @file name	:	views/admconfig/tmpl/showusergroup.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Shows list of Users Groups (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 
 JHtml::_('behavior.multiselect');
 
 $saveOrder	= ($this->lists['order'] == 'ug.ordering' && $this->lists['order_Dir'] == 'asc');
 if($saveOrder){
 	$saveOrderingUrl = 'index.php?option=com_jblance&task=admconfig.saveOrderAjax&tmpl=component';
 	JHtml::_('sortablelist.sortable', 'configList', 'adminForm', strtolower($this->lists['order_Dir']), $saveOrderingUrl);
 }
?>
<form action="<?php echo JRoute::_('index.php?option=com_jblance&view=admconfig&layout=showusergroup'); ?>" method="post" id="adminForm" name="adminForm">
<div id="j-sidebar-container" class="span2">
	<?php include_once(JPATH_COMPONENT.'/views/configmenu.php'); ?>
</div>
<div id="j-main-container" class="span10">
	<table class="table table-striped" id="configList">
		<thead>
			<tr class="title">
				<th width="1%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'ug.ordering', $this->lists['order_Dir'], $this->lists['order'], null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
				</th>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th width="15%">
					<?php echo JText::_('COM_JBLANCE_GROUP_TITLE');?>
				</th>
				<th>
					<?php echo JText::_('COM_JBLANCE_GROUP_DESC');?>
				</th>
				<th width="5%" class="nowrap center">
					<?php echo JText::_('COM_JBLANCE_TOTAL_USERS');?>
				</th>

				<th width="1%" class="center">
					<?php echo JText::_('JPUBLISHED'); ?>
				</th>
			</tr>
		</thead>
		<?php $i = 0; ?>
		<?php
			if(empty($this->rows)){
		?>
		<tr>
			<td colspan="8" align="center"><?php echo JText::_('COM_JBLANCE_NO_USERGROUP_CREATED'); ?></td>
		</tr>
		<?php
			}
			else {
				foreach ($this->rows as $i => $row) {
					$row = $this->rows[$i];
					$published = JHtml::_('jgrid.published', $row->published, $i, 'admconfig.');
		?>
			<tr class="row<?php echo $i % 2; ?>" sortable-group-id="">
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
					<a href="<?php echo JRoute::_('index.php?option=com_jblance&view=admconfig&layout=editusergroup&cid[]='.$row->id); ?>">
						<?php echo $row->name; ?>
					</a>
				</td>
				<td>
					<?php echo $row->description; ?>
				</td>
				<td class="center">
					<?php echo $row->usercount;?>
				</td>
				
				<td class="center">
					<?php echo $published; ?>
				</td>
			</tr>
		<?php
				}
		?>
		<?php } ?>
		<tfoot>
		<tr>
			<td colspan="8">
				<?php echo $this->pageNav->getListFooter(); ?>
			</td>
		</tr>
		</tfoot>
	</table>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="ctype" value="usergroup" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_('form.token');?>
</div>
</form>