<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	13 March 2012
 * @file name	:	views/admproject/tmpl/showuser.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Shows list of Users (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 JHtml::_('behavior.tooltip');
 JHtml::_('behavior.multiselect');
 JHtml::_('formbehavior.chosen', 'select');
 
?>
<form action="<?php echo JRoute::_('index.php?option=com_jblance&view=admproject&layout=showuser'); ?>" method="post" name="adminForm" id="adminForm">
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
	<div id="filter-bar" class="btn-toolbar">
		<div class="filter-search btn-group pull-left">
			<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo htmlspecialchars($this->lists['search']);?>" class="hasTooltip" title="" />
		</div>
		<div class="btn-group pull-left">
			<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="icon-search"></i></button>
			<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';document.getElementById('ug_id').value='';this.form.submit();"><i class="icon-remove"></i></button>
		</div>
		<div class="btn-group pull-left">
			<?php echo $this->lists['ug_id']; ?>
		</div>
	</div>
	<table class="table table-striped">
		<thead>
			<tr>
				<th width="1%">
					<?php echo '#'; ?>
				</th>
				<th width="1%" >
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th>
					<?php echo JHtml::_( 'grid.sort', 'COM_JBLANCE_NAME', 'u.name', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th width="12%">
					<?php echo JHtml::_( 'grid.sort', 'COM_JBLANCE_USERNAME', 'u.username', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th width="1%" class="nowrap center">
				<?php echo JHtml::_('grid.sort', JText::_('COM_JBLANCE_APPROVED'), 'u.block', $this->lists['order_Dir'], $this->lists['order'] ); ?>
				</th>
				<th width="15%">
					<?php echo JHtml::_( 'grid.sort', 'JGLOBAL_EMAIL', 'u.email', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th width="20%">
					<?php echo JHtml::_( 'grid.sort', 'COM_JBLANCE_BUSINESS_NAME', 'ju.biz_name', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>
				<th width="5%">
					<?php echo JText::_('COM_JBLANCE_BALANCE'); ?>
				</th>
				<th width="1%" class="nowrap center">
					<?php echo JHtml::_( 'grid.sort', 'JGRID_HEADING_ID', 'u.id', $this->lists['order_Dir'], $this->lists['order']); ?>
				</th>				
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="10">
					<?php echo $this->pageNav->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		for ($i=0, $n=count($this->rows); $i < $n; $i++) {
			$row = $this->rows[$i];
			$uurl = 'index.php?option=com_users&task=user.edit&id='.$row->id;
			
			$link_edit	= JRoute::_( 'index.php?option=com_jblance&view=admproject&layout=edituser&cid[]='.$row->id);
			?>
			<tr>
				<td>
					<?php echo $this->pageNav->getRowOffset($i); ?>
				</td>
				<td>
					<?php echo JHtml::_('grid.id', $i, $row->id); ?>
				</td>
				<td>
					<a href="<?php echo $link_edit;?>"><?php echo $row->name; ?></a>			
				</td>
				<td>
					<?php echo $row->username; ?>
				</td>
				<td class="center">
					<?php echo JblanceHelper::boolean(!$row->block, $i, 'admproject.unblock', 'admproject.block'); ?>
				</td>
				<td>
					<?php echo JHtml::_("email.cloak", $row->email);?>
				</td>
				<td>
					<?php echo ($row->biz_name) ? $row->biz_name : '- <i>'.JText::_('COM_JBLANCE_NOT_APPLICABLE').'</i> -'; ?>
				</td>
				<td style="text-align: right;">
					<?php echo JblanceHelper::formatCurrency($row->total_fund, false); ?>
				</td>
				<td>
					<a href="<?php echo $uurl; ?>" title="<?php echo JText::_('COM_JBLANCE_EDIT_USER_ACCOUNT'); ?>"><?php echo $row->id;?></a>
				</td>					
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="com_jblance" />
	<input type="hidden" name="view" value="admproject" />
	<input type="hidden" name="layout" value="showuser" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>