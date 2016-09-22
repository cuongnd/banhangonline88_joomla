<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class MaQmaHtmlDefault
{
	static function display(&$rows, &$pageNav)
	{
		?>
		<form action="index.php" method="post" id="adminForm" name="adminForm">
			<?php echo JHtml::_('form.token'); ?>
			<div class="breadcrumbs">
				<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
				<a href="index.php?option=com_maqmahelpdesk&task=mail"><?php echo JText::_('email_fetch'); ?></a>
				<span><?php echo JText::_('logs'); ?></span>
			</div>
			<div class="contentarea">
				<table class="table table-striped table-bordered" cellspacing="0">
					<thead>
					<tr>
						<th width="20" align="right">#</th>
						<th class="title"><?php echo JText::_('email'); ?></th>
						<th class="title"><?php echo JText::_('date'); ?></th>
						<th class="title"><?php echo JText::_('sender'); ?></th>
						<th class="title"><?php echo JText::_('log'); ?></th>
					</tr>
					</thead>
					<tbody><?php
						if (count($rows) == 0) {
							?>
						<tr>
							<td colspan="5"><?php echo JText::_('register_not_found'); ?></td>
						</tr><?php
						} else {
							$k = 0;
							for ($i = 0, $n = count($rows); $i < $n; $i++) {
								$row = &$rows[$i];?>
							<tr class="<?php echo "row$k"; ?>">
								<td width="20" align="right"><span class="lbl"><?php echo $row->id; ?></span></td>
								<td><?php echo $row->mailaccount; ?></td>
								<td><?php echo $row->date; ?></td>
								<td><?php echo $row->email; ?></td>
								<td><?php echo $row->log; ?></td><?php
								$k = 1 - $k; ?>
							</tr><?php
							} // for loop
						} // if  ?>
					</tbody>
					<tfoot>
					<tr>
						<td colspan="6"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
					</tfoot>
				</table>
				<div class="clr"></div>
			</div>

			<input type="hidden" name="option" value="com_maqmahelpdesk"/>
			<input type="hidden" name="task" value="mails"/>
			<input type="hidden" name="boxchecked" value="0"/>
		</form><?php
	}
}
