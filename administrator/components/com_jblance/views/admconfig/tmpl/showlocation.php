<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	12 March 2015
 * @file name	:	views/admconfig/tmpl/showlocation.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Shows list of Locations(jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 
 JHtml::_('behavior.multiselect');
 
 $saveOrder 	= ($this->lists['order'] == 'l.lft' && $this->lists['order_Dir'] == 'asc');
 if($saveOrder){
 	$saveOrderingUrl = 'index.php?option=com_jblance&task=admconfig.saveOrderAjax&tmpl=component';
 	JHtml::_('sortablelist.sortable', 'configList', 'adminForm', strtolower($this->lists['order_Dir']), $saveOrderingUrl, false, true);
 }
 
?>
<form action="<?php echo JRoute::_('index.php?option=com_jblance&view=admconfig&layout=showlocation'); ?>" method="post" name="adminForm" id="adminForm">
<div id="j-sidebar-container" class="span2">
	<?php include_once(JPATH_COMPONENT.'/views/configmenu.php'); ?>
</div>
<div id="j-main-container" class="span10">
	<div id="filter-bar" class="btn-toolbar">
		<div class="filter-search btn-group pull-left">
			<input type="text" name="search" id="search" placeholder="<?php echo JText::_('JSEARCH_FILTER'); ?>" value="<?php echo htmlspecialchars($this->lists['search']);?>" />
		</div>
		<div class="btn-group pull-left">
			<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_SUBMIT'); ?>" onclick="this.form.submit();"><i class="icon-search"></i></button>
			<button type="button" class="btn hasTooltip" title="<?php echo JHtml::tooltipText('JSEARCH_FILTER_CLEAR'); ?>" onclick="document.getElementById('search').value='';this.form.submit();"><i class="icon-remove"></i></button>
		</div>
	</div>
	<table class="table table-striped" id="configList">
		<thead>
			<tr>
				<th width="1%" class="nowrap center">
					<?php echo JHtml::_('grid.sort', '<i class="icon-menu-2"></i>', 'l.lft', $this->lists['order_Dir'], $this->lists['order'], null, 'asc', 'JGRID_HEADING_ORDERING'); ?>
				</th>
				<th width="1%" >
					<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
				</th>
				<th>
					<?php echo JText::_('COM_JBLANCE_TITLE'); ?>
				</th>
	 			<th width="1%" class="nowrap center">
	 				<?php echo JText::_('JPUBLISHED'); ?>
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
		for($i=0, $n=count($this->rows); $i < $n; $i++){
			$row = $this->rows[$i];

			$orderkey   = array_search($row->id, $this->ordering[$row->parent_id]);
				
			$link_edit	= JRoute::_('index.php?option=com_jblance&view=admconfig&layout=editlocation&cid[]='. $row->id);
			$published = JHtml::_('jgrid.published', $row->published, $i, 'admconfig.');
				
			// Get the parents of item for sorting
			if ($row->level > 1)
			{
				$parentsStr = "";
				$_currentParentId = $row->parent_id;
				$parentsStr = " " . $_currentParentId;
				for ($i2 = 0; $i2 < $row->level; $i2++)
				{
					foreach ($this->ordering as $k => $v)
					{
						$v = implode("-", $v);
						$v = "-" . $v . "-";
						if (strpos($v, "-" . $_currentParentId . "-") !== false)
						{
							$parentsStr .= " " . $k;
							$_currentParentId = $k;
							break;
						}
					}
				}
			}
			else
			{
				$parentsStr = "";
			}
			?>
			<tr class="row<?php echo $i % 2; ?>" sortable-group-id="<?php echo $row->parent_id; ?>" item-id="<?php echo $row->id ?>" parents="<?php echo $parentsStr ?>" level="<?php echo $row->level ?>">
				<td class="order nowrap center hidden-phone">
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
					<input type="text" style="display:none;" name="order[]" size="5" value="<?php echo $orderkey + 1; ?>" class="width-20 text-area-order" />
					<?php endif; ?>
				</td>
				<td>
					<?php echo JHtml::_('grid.id', $i, $row->id); ?>
				</td>
				<td>
					<?php echo str_repeat('<span class="gi">&mdash;</span>', $row->level-1) ?>
					<a href="<?php echo $link_edit; ?>">
						<?php echo $row->title;	?>
					</a>					
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
</div>
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="ctype" value="location" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
