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
				<a href="index.php?option=com_maqmahelpdesk&task=contracts"><?php echo JText::_('contracts'); ?></a>
				<span><?php echo JText::_('contract_components'); ?></span>
			</div>
			<div class="contentarea">
				<table class="table table-striped table-bordered" cellspacing="0">
					<thead>
					<tr>
						<th class="algcnt valgmdl" width="20">#</th>
						<th class="algcnt valgmdl" width="20"><input type="checkbox" id="checkall-toggle" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/></th>
						<th><?php echo JText::_('name'); ?></th>
						<th><?php echo JText::_('description'); ?></th>
					</tr>
					</thead>
					<tbody><?php
						if (count($rows) == 0) { ?>
							<tr>
								<td colspan="4"><?php echo JText::_('register_not_found'); ?></td>
							</tr><?php
						} else {
							$k = 0;
							for ($i = 0, $n = count($rows); $i < $n; $i++) {
								$row = &$rows[$i]; ?>
								<tr class="<?php echo "row$k"; ?>">
									<td class="algcnt valgmdl" width="20"><span class="lbl"><?php echo $row->id; ?></span></td>
									<td class="algcnt valgmdl" width="20"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
									<td><a href="#components_edit" onclick="return listItemTask('cb<?php echo $i;?>','components_edit')"> <?php echo $row->name; ?> </a></td>
									<td><?php echo $row->description; ?></td><?php
									$k = 1 - $k; ?>
								</tr><?php
							} // for loop
						} // if ?>
					<tbody>
					<tfoot>
					<tr>
						<td colspan="4">
							<?php echo $pageNav->getListFooter(); ?>
						</td>
					</tr>
					</tfoot>
				</table>
				<div class="clr"></div>
			</div>

			<input type="hidden" name="option" value="com_maqmahelpdesk"/>
			<input type="hidden" name="task" value="components"/>
			<input type="hidden" name="boxchecked" value="0"/>
		</form><?php
	}
}
