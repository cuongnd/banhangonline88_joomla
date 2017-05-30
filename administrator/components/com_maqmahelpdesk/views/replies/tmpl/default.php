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
	{ ?>
		<form action="index.php" method="post" id="adminForm" name="adminForm">
			<?php echo JHtml::_('form.token'); ?>
			<div class="breadcrumbs">
				<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
				<a href="index.php?option=com_maqmahelpdesk&task=replies"><?php echo JText::_('predefined_replies'); ?></a>
				<span><?php echo JText::_('manage'); ?></span>
			</div>
			<div class="contentarea">
				<table class="table table-striped table-bordered" cellspacing="0">
					<thead>
					<tr>
						<th class="algcnt valgmdl" width="20">#</th>
						<th class="algcnt valgmdl" width="20"><input type="checkbox" id="checkall-toggle" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/></th>
						<th><?php echo JText::_('subject'); ?></th>
						<th><?php echo JText::_('answer'); ?></th>
					</tr>
					</thead>
					<tbody>
						<?php
						if (count($rows) == 0)
						{ ?>
							<tr>
								<td colspan="4">
									<center><?php echo JText::_('register_not_found'); ?></center>
								</td>
							</tr><?php
						}
						else
						{
							for ($i = 0, $n = count($rows); $i < $n; $i++)
							{
								$row = &$rows[$i]; ?>
								<tr>
									<td class="algcnt valgmdl" width="20"><span class="lbl"><?php echo $row->id; ?></span></td>
									<td class="algcnt valgmdl" width="20"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
									<td>
										<a href="#replies_edit"
										   onClick="return listItemTask('cb<?php echo $i;?>','replies_edit')">
											<?php echo $row->subject; ?>
										</a>
									</td>
									<td>
										<?php echo strip_tags($row->answer); ?>
									</td>
								</tr><?php
							}
						} ?>
					</tbody>
					<tfoot>
					<tr>
						<td colspan="4"><?php echo $pageNav->getListFooter(); ?></td>
					</tr>
					</tfoot>
				</table>
				<div class="clr"></div>
			</div>

			<input type="hidden" name="option" value="com_maqmahelpdesk"/>
			<input type="hidden" name="task" value="replies"/>
			<input type="hidden" name="boxchecked" value="0"/>
		</form><?php
	}
}
