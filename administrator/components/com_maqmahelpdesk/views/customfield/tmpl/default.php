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
	static function display(&$rows, &$pageNav, $lists)
	{
		?>
		<form action="index.php" method="post" id="adminForm" name="adminForm">
			<?php echo JHtml::_('form.token'); ?>
			<div class="breadcrumbs">
				<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
				<a href="index.php?option=com_maqmahelpdesk&task=customfield"><?php echo JText::_('cfields'); ?></a>
				<span><?php echo JText::_('manage'); ?></span>
			</div>

			<div id="filtersarea">
				<?php echo JString::strtoupper(JText::_('filters'));?> <img src="../media/com_maqmahelpdesk/images/ui/separator.png"
																   style="padding:5px;"
																   align="absmiddle"/> <?php echo JText::_('type') . ': ' . $lists['cftype'];?>
			</div>

			<div class="contentarea">
				<table class="table table-striped table-bordered" cellspacing="0">
					<thead>
					<tr>
						<th class="algcnt valgmdl" width="20">#</th>
						<th class="algcnt valgmdl" width="20">
							<input type="checkbox" id="checkall-toggle" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/>
						</th>
						<th><?php echo JText::_('name'); ?></th>
						<th class="algcnt valgmdl"><?php echo JText::_('type'); ?></th>
						<th class="algcnt valgmdl"><?php echo JText::_('field_type'); ?></th>
					</tr>
					</thead>
					<tbody>
						<?php
						if (count($rows) == 0) { ?>
							<tr>
								<td colspan="5"><?php echo JText::_('register_not_found'); ?></td>
							</tr><?php
						} else {
							for ($i = 0, $n = count($rows); $i < $n; $i++) {
								$row = &$rows[$i]; ?>
								<tr>
									<td class="algcnt valgmdl" width="20"><span class="lbl"><?php echo $row->id; ?></span></td>
									<td class="algcnt valgmdl" width="20"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
									<td>
										<a href="#customfield_edit"
										   onClick="return listItemTask('cb<?php echo $i;?>','customfield_edit')">
											<?php echo $row->caption; ?>
										</a>
										<?php if ($row->cftype == 'W') : ?>
										<br/>
										<?php echo JText::_('cfields_codes'); ?> <span class="lbl">[cfield<?php echo $row->id;?>
											_caption]</span> / <span class="lbl">[cfield<?php echo $row->id;?>_value]</span>
										<?php endif; ?>
									</td>
									<td class="algcnt valgmdl"><?php
										if ($row->cftype == 'W') echo JText::_('wk_field');
										else if ($row->cftype == 'U') echo JText::_('users_field');
										else if ($row->cftype == 'C') echo JText::_('contract_field');
										else if ($row->cftype == 'D') echo JText::_('downloads') . ' - ' . JText::_('CLIENT_ACCESS');
										else if ($row->cftype == 'L') echo JText::_('WK_CLIENTS'); ?>
									</td>
									<td class="algcnt valgmdl"><?php echo JText::_('formfield_' . $row->ftype); ?></td>
								</tr><?php
							} // for loop
						} // if ?>
					</tbody>
					<tfoot>
					<tr>
						<td colspan="5">
							<?php echo $pageNav->getListFooter(); ?>
						</td>
					</tr>
					</tfoot>
				</table>
				<div class="clr"></div>
			</div>

			<input type="hidden" name="option" value="com_maqmahelpdesk"/>
			<input type="hidden" name="task" value="customfield"/>
			<input type="hidden" name="boxchecked" value="0"/>
		</form><?php
	}
}
