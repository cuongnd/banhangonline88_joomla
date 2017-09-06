<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	20 April 2012
 * @file name	:	views/admproject/tmpl/showescrow.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Show Funds transfer between users (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 
 JHtml::_('behavior.multiselect');
 $config = JblanceHelper::getConfig();
 $dformat = $config->dateFormat;
 $currencysym = $config->currencySymbol;
?>
<form action="<?php echo JRoute::_('index.php?option=com_jblance&view=admproject&layout=showescrow'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<div id="j-sidebar-container" class="span2">
	<?php echo $this->sidebar; ?>
</div>
<div id="j-main-container" class="span10">
	<table class="table table-striped">
	<thead>
		<tr>
			<th width="1%">
				<?php echo JText::_('#'); ?>
			</th>
			<th width="1%" >
				<input type="checkbox" name="checkall-toggle" value="" title="<?php echo JText::_('JGLOBAL_CHECK_ALL'); ?>" onclick="Joomla.checkAll(this)" />
			</th>
			<th width="6%">
				<?php echo JText::_('COM_JBLANCE_TRANSFER_DATE'); ?>
			</th>
			<th width="6%">
				<?php echo JText::_('COM_JBLANCE_RELEASE_DATE'); ?>
			</th>
			<th width="6%">
				<?php echo JText::_('COM_JBLANCE_ACCEPT_DATE'); ?>
			</th>
			<th width="10%">
				<?php echo JText::_('COM_JBLANCE_FROM'); ?>
			</th>
			<th width="10%">
				<?php echo JText::_('COM_JBLANCE_TO'); ?>
			</th>
			<th width="22%">
				<?php echo JText::_('COM_JBLANCE_TITLE'); ?>
			</th>
			<th width="5%">
				<?php echo JText::_('COM_JBLANCE_STATUS'); ?>
			</th>
			<th width="5%">
				<?php echo JText::_('COM_JBLANCE_AMOUNT').' ('.$currencysym.')'; ?>
			</th>
			<th width="20%">
				<?php echo JText::_('COM_JBLANCE_NOTE'); ?>
			</th>
			<th width="10%">
				<?php echo JText::_('COM_JBLANCE_ACTION'); ?>
			</th>
			<th width="1%">
				<?php echo JText::_('JGRID_HEADING_ID'); ?>
			</th>
		</tr>
	</thead>
	<tfoot>
		<tr>
			<td colspan="14">
				<?php echo $this->pageNav->getListFooter(); ?>
			</td>
		</tr>
	</tfoot>
	<tbody>
	<?php
	for($i=0, $n=count($this->rows); $i < $n; $i++){
		$row = $this->rows[$i];
		$link_release 	= JRoute::_('index.php?option=com_jblance&task=admproject.releaseescrow&id='.$row->id.'&'.JSession::getFormToken().'=1');
		$link_cancel 	= JRoute::_('index.php?option=com_jblance&task=admproject.cancelescrow&id='.$row->id.'&'.JSession::getFormToken().'=1');
		?>
		<tr>
			<td>
				<?php echo $this->pageNav->getRowOffset($i); ?>
			</td>
			<td>
				<?php echo JHtml::_('grid.id', $i, $row->id); ?>
			</td>
			<td class="center" nowrap>
				<?php echo ($row->date_transfer != "0000-00-00 00:00:00" ?  JHtml::_('date', $row->date_transfer, $dformat) :  "-"); ?>
			</td>
			<td class="center" nowrap>
				<?php echo ($row->date_release != "0000-00-00 00:00:00" ?  JHtml::_('date', $row->date_release, $dformat) :  "-"); ?>
			</td>
			<td class="center" nowrap>
				<?php echo ($row->date_accept != "0000-00-00 00:00:00" ?  JHtml::_('date', $row->date_accept, $dformat) :  "-"); ?>
			</td>
			<td>
				<span class="small">[<?php echo $row->from_id; ?>]</span> <?php echo $row->sender; ?>
			</td>
			<td>
				<span class="small">[<?php echo $row->to_id; ?>]</span> <?php echo $row->receiver; ?>
			</td>
			<td align="center">
				<?php echo ($row->title) ? $row->title : JText::_('COM_JBLANCE_NA'); ?>
				<?php if(!empty($row->type) && !empty($row->title)){?><div class="small">[<?php echo JText::_($row->type); ?>]</div><?php }?>
			</td>
			<td align="center">
				<?php echo JblanceHelper::getEscrowPaymentStatus($row->status); ?>
			</td>
			<!-- <td>
				<?php echo ($row->date_approval != "0000-00-00 00:00:00" ?  JHtml::_('date', $row->date_approval, $dformat) :  "Never"); ?>
			</td> -->
			<td align="right">
				<?php echo JblanceHelper::formatCurrency($row->amount, false); ?>
				<?php 
				if($row->pay_for > 0)
					echo '<span title="'.JText::_('COM_JBLANCE_PAID_FOR').'"><br>'.$row->pay_for.' '.JText::_('COM_JBLANCE_HRS').'</span>';
				?>
			</td>
			<td>
				<?php echo $row->note;?>
			</td>
			<td>
				<?php if($row->status == '') : ?>
				<div class="btn-group">
					<a href="<?php echo $link_release; ?>" class="btn btn-mini btn-info"><?php echo JText::_('COM_JBLANCE_RELEASE'); ?></a>
					<a href="<?php echo $link_cancel; ?>" class="btn btn-mini btn-danger"><?php echo JText::_('COM_JBLANCE_CANCEL'); ?></a>
				</div>
				<?php endif; ?>
			</td>
			<td>
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
	<input type="hidden" name="layout" value="showescrow" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>