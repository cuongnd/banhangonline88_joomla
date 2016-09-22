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
		$supportConfig = HelpdeskUtility::GetConfig();
		$database = JFactory::getDBO(); ?>

		<form action="index.php" method="POST" id="adminForm" name="adminForm">
			<?php echo JHtml::_('form.token'); ?>
			<div class="breadcrumbs">
				<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
				<a href="index.php?option=com_maqmahelpdesk&task=client"><?php echo JText::_('clients_manager'); ?></a>
				<a href="index.php?option=com_maqmahelpdesk&task=group"><?php echo JText::_('groups'); ?></a>
				<span><?php echo JText::_('manage'); ?></span>
			</div>
			<div class="contentarea">
				<table class="table table-striped table-bordered" cellspacing="0">
					<thead>
					<tr>
						<th class="algcnt valgmdl" width="20">#</th>
						<th class="algcnt valgmdl" width="20"><input type="checkbox" id="checkall-toggle" name="checkall-toggle" value="" onclick="Joomla.checkAll(<?php echo count($rows); ?>);"/></th>
						<th class="valgmdl" nowrap><?php echo JText::_('name'); ?></th>
						<th class="valgmdl" nowrap><?php echo JText::_('description'); ?></th>
						<th class="algcnt valgmdl" width="70"><?php echo JText::_('nr_clients'); ?></th>
						<th class="algcnt valgmdl"><?php echo JText::_('default'); ?></th>
						<th class="algcnt valgmdl"><?php echo JText::_('unregister'); ?></th>
					</tr>
					</thead>
					<tbody><?php
						if (count($rows) == 0) {
							?>
							<tr>
								<td colspan="8"><?php echo JText::_('register_not_found'); ?></td>
							</tr><?php
						} else {
							for ($i = 0, $n = count($rows); $i < $n; $i++)
							{
								$row = &$rows[$i];

								$img1 = $row->unregister ? 'ok' : 'remove';
								$img2 = $row->isdefault ? 'ok' : 'remove'; ?>

								<tr>
									<td class="algcnt valgmdl" width="20"><span class="lbl"><?php echo $row->id; ?></span></td>
									<td class="algcnt valgmdl" width="20">
										<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]"
											   value="<?php echo $row->id; ?>" onClick="isChecked(this.checked);"/>
									</td>
									<td class="valgmdl"><a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','group_edit')"><?php echo $row->gname; ?></a></td>
									<td class="valgmdl"><?php echo $row->description;?></td>
									<td class="algcnt valgmdl" width="70"><?php
										$database->setQuery("SELECT COUNT(*) FROM #__support_dl_users WHERE id_group='" . $row->id . "'");
										echo $database->loadResult(); ?>
									</td>
									<td class="algcnt valgmdl" width="50"><span class="btn btn-<?php echo ($img2=='ok' ? 'success' : 'danger');?>"><i class="ico-<?php echo $img2;?>-sign ico-white"></i></span></td>
									<td class="algcnt valgmdl" width="50"><span class="btn btn-<?php echo ($img1=='ok' ? 'success' : 'danger');?>"><i class="ico-<?php echo $img1;?>-sign ico-white"></i></span></td>
								</tr><?php
							} // for loop
						} // if ?>
					</tbody>
					<tfoot>
					<tr>
						<td colspan="8">
							<?php echo $pageNav->getListFooter(); ?>
						</td>
					</tr>
					</tfoot>
				</table>
				<div class="clr"></div>
			</div>

			<input type="hidden" name="option" value="com_maqmahelpdesk"/>
			<input type="hidden" name="task" value="group"/>
			<input type="hidden" name="boxchecked" value="0"/>
		</form><?php
	}
}
