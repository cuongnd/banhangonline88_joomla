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

		<form action="index.php" method="post" id="adminForm" name="adminForm">
			<?php echo JHtml::_('form.token'); ?>
			<div class="breadcrumbs">
				<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
				<a href="index.php?option=com_maqmahelpdesk&task=workgroup"><?php echo JText::_('workgroups'); ?></a>
				<a href="index.php?option=com_maqmahelpdesk&task=wkfields"><?php echo JText::_('cfield_assign_menu'); ?></a>
				<span><?php echo JText::_('manage'); ?></span>
			</div>
			<div class="contentarea">
				<table id="contentTable" class="table table-striped table-bordered" cellspacing="0">
					<thead>
					<tr>
						<th width="20"></th>
						<th class="algcnt valgmdl" width="20">#</th>
						<th class="algcnt valgmdl" width="20">
							<input type="checkbox" id="checkall-toggle" name="checkall-toggle" value=""
								   onClick="Joomla.checkAll(this);"/>
						</th>
						<th class="valgmdl"><?php echo JText::_('workgroup'); ?></th>
						<th class="valgmdl"><?php echo JText::_('category'); ?></th>
						<th class="valgmdl"><?php echo JText::_('section'); ?></th>
						<th class="valgmdl"><?php echo JText::_('field_name'); ?></th>
						<th class="algcnt valgmdl"><?php echo JText::_('field_type'); ?></th>
						<th class="algcnt valgmdl" width="70"><?php echo JText::_('support_only_short'); ?></th>
						<th class="algcnt valgmdl" width="70"><?php echo JText::_('new_only_short'); ?></th>
						<th class="algcnt valgmdl" width="70"><?php echo JText::_('required'); ?></th>
					</tr>
					</thead>
					<tbody>
						<?php
						if (count($rows) == 0) {
							?>
							<tr>
								<td colspan="11"><?php echo JText::_('register_not_found'); ?></td>
							</tr><?php
						} else {
							for ($i = 0, $n = count($rows); $i < $n; $i++)
							{
								$row = &$rows[$i];
								$img = $row->required ? 'ok' : 'remove';
								$alt = $row->required ? JText::_('required_tooltip') : JText::_('not_required_tooltip');
								if ($row->id_category != '') {
									$sql = "SELECT `name`
									FROM `#__support_category`
									WHERE `id` IN (" . $row->id_category . ")";
									$database->setQuery($sql);
									$categories = $database->loadObjectList();
								} else {
									$categories = null;
								} ?>
								<tr id="contentTable-row-<?php echo ($row->id);?>">
									<td width="20" class="dragHandle"></td>
									<td width="20" class="algcnt valgmdl"><span class="lbl"><?php echo $row->id; ?></span></td>
									<td width="20" class="algcnt valgmdl"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
									<td class="valgmdl"><?php echo $row->wkdesc; ?></td>
									<td class="valgmdl"><?php
										if (isset($categories)) {
											foreach ($categories as $category) {
												echo $category->name . ", ";
											}
										} ?>
									</td>
									<td class="valgmdl"><?php echo $row->section; ?></td>
									<td class="valgmdl">
										<a href="#wkfields_edit"
										   onClick="return listItemTask('cb<?php echo $i;?>','wkfields_edit')">
											<?php echo $row->caption; ?>
										</a>
									</td>
									<td class="algcnt valgmdl"><?php echo JText::_('formfield_' . $row->ftype); ?></td>
									<td class="algcnt valgmdl">
										<?php
										$img_support_only = $row->support_only ? 'ok' : 'remove';
										?>
										<span class="btn btn-<?php echo ($img_support_only=='ok' ? 'success' : 'danger');?>"><i class="ico-<?php echo $img_support_only;?>-sign ico-white"></i></span>
									</td>
									<td class="algcnt valgmdl">
										<?php
										$img_new_only = $row->new_only ? 'ok' : 'remove';
										?>
										<span class="btn btn-<?php echo ($img_new_only=='ok' ? 'success' : 'danger');?>"><i class="ico-<?php echo $img_new_only;?>-sign ico-white"></i></span>
									</td>
									<td class="algcnt valgmdl">
										<span class="btn btn-<?php echo ($row->required ? 'success' : 'danger');?>"><i class="ico-<?php echo $img;?>-sign ico-white"></i></span>
									</td>
								</tr><?php
							} // for loop
						} // if ?>
					</tbody>
					<tfoot>
					<tr>
						<td colspan="11">
							<?php echo $pageNav->getListFooter(); ?>
						</td>
					</tr>
					</tfoot>
				</table>

				<input type="hidden" name="option" value="com_maqmahelpdesk"/>
				<input type="hidden" id="task" name="task" value="wkfields"/>
				<input type="hidden" name="boxchecked" value="0"/>
		</form>

		<script type="text/javascript">
		$jMaQma(document).ready(function () {
			$jMaQma('#contentTable').tableDnD({
				onDrop:function (table, row) {
					var rows = table.tBodies[0].rows;
					for (var i=0; i<rows.length; i++) {
						var RowID = rows[i].id;
						$jMaQma('#adminForm').append($jMaQma('<input/>', {
							type: 'hidden',
							name: 'contentTable[]',
							value: RowID.replace('contentTable-row-', '')
						}));
					}
					$jMaQma("#task").val('wkfields_saveorder');
					$jMaQma("#adminForm").submit();
				},
				dragHandle:"dragHandle"
			});

			$jMaQma("#contentTable tr").hover(function () {
				$jMaQma(this.cells[0]).addClass('showDragHandle');
			}, function () {
				$jMaQma(this.cells[0]).removeClass('showDragHandle');
			});
		});
		</script><?php
	}
}
