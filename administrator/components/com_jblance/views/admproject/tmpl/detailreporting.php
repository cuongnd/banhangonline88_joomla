<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	15 July 2012
 * @file name	:	views/admproject/tmpl/detailreporting.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Show Reportings (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 
 $config = JblanceHelper::getConfig();
 $dformat = $config->dateFormat;
?>

<form action="index.php" method="post" name="adminForm" id="adminForm">
<div id="j-main-container">
	<table class="table table-striped">
		<thead>
			<tr>
				<th width="10"><?php echo '#'; ?>
				</th>
				<th width="15%">
					<?php echo JText::_('COM_JBLANCE_CATEGORY'); ?>
				</th>
				<th>
					<?php echo JText::_('COM_JBLANCE_MESSAGE'); ?>
				</th>
				<th width="10%">
					<?php echo JText::_('COM_JBLANCE_REPORTED_BY'); ?>
				</th>
				<th width="10%">
					<?php echo JText::_('COM_JBLANCE_REPORTED_ON'); ?>
				</th>
				<th width="10%" class="nowrap center">
					<?php echo JText::_('COM_JBLANCE_IP_ADDRESS'); ?>
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
			$user	= JFactory::getUser($row->user_id);
			$link_user 	= JRoute::_('index.php?option=com_jblance&view=admproject&layout=edituser&cid[]='.$row->user_id);
		?>
			<tr>
				<td>
					<?php echo $this->pageNav->getRowOffset($i); ?>
				</td>
				<td>
					<?php echo $row->category; ?>
				</td>
				<td>
					<?php echo $this->escape($row->message); ?>
				</td>
				<td class="center">
					<?php if($user->id == 0) : ?>
						<?php echo JText::_('COM_JBLANCE_GUEST');?>
					<?php else : ?>
						<a href="<?php echo $link_user; ?>" target="_blank"><?php echo $user->name; ?></a>
					<?php endif; ?>
				</td>
				<td class="center">
					<?php echo JHtml::_('date', $row->date_created, $dformat, false); ?>
				</td>
				<td class="center">
					<?php echo $row->ip; ?>
				</td>
			</tr>
			<?php
			}
			?>
		</tbody>
	</table>
	<input type="hidden" name="option" value="com_jblance" />
	<input type="hidden" name="view" value="admproject" />
	<input type="hidden" name="layout" value="showreporting" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="filter_order" value="<?php echo $this->lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $this->lists['order_Dir']; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</div>
</form>