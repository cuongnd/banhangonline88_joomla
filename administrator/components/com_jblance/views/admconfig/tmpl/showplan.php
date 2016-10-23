<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	15 March 2012
 * @file name	:	views/admconfig/tmpl/showplan.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Shows list of Plans(jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 
 JHtml::_('formbehavior.chosen', 'select');
 JHtml::_('behavior.multiselect');
 
 $config = JblanceHelper::getConfig();
 $currencysym = $config->currencySymbol;
 
 $saveOrder	= ($this->lists['order'] == 'p.ordering' && $this->lists['order_Dir'] == 'asc');
 if($saveOrder){
 	$saveOrderingUrl = 'index.php?option=com_jblance&task=admconfig.saveOrderAjax&tmpl=component';
 	JHtml::_('sortablelist.sortable', 'configList', 'adminForm', strtolower($this->lists['order_Dir']), $saveOrderingUrl);
 }
 ?>
<form action="<?php echo JRoute::_('index.php?option=com_jblance&view=admconfig&layout=showplan'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div id="j-sidebar-container" class="span2">
	<?php include_once(JPATH_COMPONENT.'/views/configmenu.php'); ?>
</div>
<div id="j-main-container" class="span10">
	<div id="filter-bar" class="btn-toolbar">
		<div class="btn-group pull-right">
			<?php echo $this->lists['ug_id']; ?>
		</div>
	</div>

	<table class="table table-striped" id="configList">
		<thead>
			<tr>
				<th width="1%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'p.ordering', $this->lists['order_Dir'], $this->lists['order'], null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
				</th>
				<th width="1%">
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th>
					<?php echo JText::_('COM_JBLANCE_PLAN_NAME'); ?>
				</th>
				<th width="10%" class="nowrap center">
					<?php echo JText::_('COM_JBLANCE_DURATION'); ?>
				</th>
				<th width="10%" class="nowrap center">
					<?php echo JText::_('COM_JBLANCE_BONUS'); ?>
				</th>
				<th width="5%" class="nowrap center">
					<?php echo JText::_('JPUBLISHED'); ?>
				</th>
				<th width="5%" class="nowrap center">
					<?php echo JText::_('COM_JBLANCE_SUBSCRIBERS'); ?>
				</th>
				<th width="10%">
					<?php echo JText::_('COM_JBLANCE_PRICE').' ('.$currencysym.')'; ?>
				</th>
				<th width="10%" class="nowrap center">
					<?php echo JText::_('COM_JBLANCE_USER_GROUP'); ?>
				</th>
				<th width="1%" class="nowrap center">
					<?php echo JText::_('COM_JBLANCE_DEFAULT'); ?>
				</th>
				<th width="1%" class="nowrap center">
					<?php echo JText::_('JGRID_HEADING_ID'); ?>
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
		for ($i=0, $n=count($this->rows); $i < $n; $i++){
		$row = $this->rows[$i];
		$link_edit	= JRoute::_('index.php?option=com_jblance&view=admconfig&layout=editplan&cid[]='.$row->id);
		$published = JHtml::_('jgrid.published', $row->published, $i, 'admconfig.');
	?>
		<tr sortable-group-id="<?php echo $row->ug_id; ?>">
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
		    <td class="center">
		    	<?php echo JHtml::_('grid.id', $i, $row->id); ?>
		    </td>
		    <td>
		    	<a href="<?php echo $link_edit; ?>"><?php echo $row->name; ?></a>
		    </td>
		    <td class="center">
		    	<?php echo $row->days.' '.ucfirst($row->days_type); ?>
		    </td>
			<td class="center">
				<?php echo $row->bonusFund; ?>
			</td>
		    <td class="center">
		    	<?php echo $published; ?>
		    </td>
		    <td class="center">
		    	<?php echo $row->subscr; ?>
		    </td>
		   <td class="center">
		    	<?php echo $row->price; ?>
		    </td>
		     <td class="center">
		    	<?php echo $row->groupName; ?>
		    </td>
		    <td class="center">
				<?php if ($row->default_plan):?>
					<a class="btn btn-micro disabled jgrid hasTooltip"><i class="icon-featured"></i></a>
				<?php else :?>
					<a class="btn btn-micro" href="<?php echo JRoute::_('index.php?option=com_jblance&task=admconfig.setplandefault&cid[]='.$row->id.'&ug_id='.$row->ug_id.'&'.JSession::getFormToken().'=1');?>">
						<i class="icon-unfeatured"></i>
					</a>
				<?php endif;?>
			</td>
			<td class="center">
		     	<?php echo $row->id ?>
			</td>
	    </tr>
	<?php
	}
	?>
	</tbody>
	</table>
	<div class="alert alert-error">
		<h3><?php echo JText::_('COM_JBLANCE_PLAN_TIPS');?></h3>
		<p><?php echo JText::_('COM_JBLANCE_PLAN_TIPS_DESC');?></p>
	</div>
	<input type="hidden" name="option" value="com_jblance" />
	<input type="hidden" name="view" value="admconfig" />
	<input type="hidden" name="layout" value="showplan" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="ctype" value="plan" />
	<input type="hidden" name="hidemainmenu" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>