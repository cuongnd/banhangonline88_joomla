<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	15 March 2012
 * @file name	:	views/admconfig/tmpl/showpaymode.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Shows list of Payment Gateways(jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 
 JHtml::_('behavior.multiselect');

 $saveOrder	= ($this->lists['order'] == 'p.ordering' && $this->lists['order_Dir'] == 'asc');
 if($saveOrder){
 	$saveOrderingUrl = 'index.php?option=com_jblance&task=admconfig.saveOrderAjax&tmpl=component';
 	JHtml::_('sortablelist.sortable', 'configList', 'adminForm', strtolower($this->lists['order_Dir']), $saveOrderingUrl);
 }
?>
<div id="j-sidebar-container" class="span2">
	<?php include_once(JPATH_COMPONENT.'/views/configmenu.php'); ?>
</div>
<div id="j-main-container" class="span10">
<form action="<?php echo JRoute::_('index.php?option=com_jblance&view=admconfig&layout=showpaymode'); ?>" method="post" id="adminForm" name="adminForm">
	
	<table class="table table-striped" id="configList">
		<thead>
			<tr>
				<th width="1%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'p.ordering', $this->lists['order_Dir'], $this->lists['order'], null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
				</th>
				<th width="1%" >
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th>
					<?php echo JText::_('COM_JBLANCE_TITLE'); ?>
				</th>
				<th width="1%">
					<?php echo JText::_('JPUBLISHED'); ?>
				</th>
			</tr>
		</thead>
		<tfoot>
			<tr>
				<td colspan="8">
					<?php echo $this->pageNav->getListFooter(); ?>
				</td>
			</tr>
		</tfoot>
		<tbody>
		<?php
		for($i=0, $n=count($this->rows); $i < $n; $i++){
			$row = $this->rows[$i];
			$edit_paymode = JRoute::_('index.php?option=com_jblance&view=admconfig&layout=editpaymode&cid[]='.$row->id);
			$published = JHtml::_('jgrid.published', $row->published, $i, 'admconfig.');
			?>
			<tr>
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
					<a href="<?php echo $edit_paymode?>"><?php echo $row->gateway_name; ?></a>					
				</td>										
				<td class="center">
					<?php echo $published; ?>
				</td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>

	<input type="hidden" name="option" value="com_jblance" />
	<input type="hidden" name="view" value="admconfig" />
	<input type="hidden" name="layout" value="showpaymode" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="ctype" value="paymode" />
	<input type="hidden" name="fieldfor" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<?php 
$user = JFactory::getUser();
$isSuperAdmin = false;
if(isset($user->groups[8]))
	$isSuperAdmin = true;
?>
<?php if($isSuperAdmin) : ?>
<form action="index.php" method="post" name="adminRunSqlForm" enctype="multipart/form-data" class="form-horizontal">
	<div class="well well-small">
		<h4><?php echo JText::_('COM_JBLANCE_RUN_SQL'); ?></h4>
		<p><?php echo JText::_('COM_JBLANCE_RUN_SQL_MESSAGE'); ?></p>
		<div class="control-group">
    		<label class="control-label hasTooltip" for="runsql"><?php echo JText::_('COM_JBLANCE_FILE'); ?>:</label>
			<div class="controls">
				<input type="file" class="input-medium" name="runsql" id="runsql" size="20" maxlength="30" />
			</div>
  		</div>
		<div class="form-actions">
			<input class="btn btn-primary" type="button" name="submit_app" value="<?php echo JText::_('COM_JBLANCE_RUN_SQL'); ?>" onclick="this.form.submit();" />
			<input type="hidden" name="actiontype" value="1" />
		</div>
	</div>
	<input type="hidden" name="option" value="com_jblance" />
	<input type="hidden" name="task" value="admconfig.runsql" />
	<?php echo JHtml::_('form.token'); ?>
</form>
<?php endif; ?>
</div>