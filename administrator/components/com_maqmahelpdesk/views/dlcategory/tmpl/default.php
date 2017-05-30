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
		$supportConfig = HelpdeskUtility::GetConfig(); ?>

		<form action="index.php" method="POST" id="adminForm" name="adminForm">
			<?php echo JHtml::_('form.token'); ?>
			<div class="breadcrumbs">
				<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
				<a href="index.php?option=com_maqmahelpdesk&task=product"><?php echo JText::_('downloads'); ?></a>
				<a href="index.php?option=com_maqmahelpdesk&task=dlcategory"><?php echo JText::_('categories'); ?></a>
				<span><?php echo JText::_('manage'); ?></span>
			</div>
			<div class="contentarea">
				<table id="contentTable" class="table table-striped table-bordered" cellspacing="0">
					<thead>
					<tr>
						<th class="algcnt valgmdl" width="20">&nbsp;</th>
						<th class="algcnt valgmdl" width="20">#</th>
						<th class="algcnt valgmdl" width="20"><input type="checkbox" id="checkall-toggle" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/></th>
						<th nowrap><?php echo JText::_('name'); ?></th>
						<th nowrap><?php echo JText::_('description'); ?></th>
						<th class="algcnt valgmdl" nowrap><?php echo JText::_('published'); ?></th>
					</tr>
					</thead>
					<tbody><?php
						if (count($rows) == 0) { ?>
							<tr>
								<td colspan="6"><?php echo JText::_('register_not_found'); ?></td>
							</tr><?php
						} else {
							$k = 0;
							for ($i = 0, $n = count($rows); $i < $n; $i++)
							{
								$row = &$rows[$i]; ?>
								<tr id="contentTable-row-<?php echo ($row->id);?>" class="<?php echo "row$k"; ?>">
									<td class="dragHandle" width="20"></td>
									<td class="algcnt valgmdl" width="20"><span class="lbl"><?php echo $row->id; ?></span></td>
									<td width="20" class="algcnt valgmdl"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
									<td align="left"><?php
										if ($row->level > 1) {
											?>
											<img src="../media/com_maqmahelpdesk/images/dtree/joinbottom.gif" alt="" align="absmiddle"/><?php
											if ($row->level > 2) {
												for ($x = 2; $x < $row->level; $x++)
												{ ?>
													<img src="../media/com_maqmahelpdesk/images/dtree/line2.gif" alt="" align="absmiddle"/><?php
												}
											}
										} ?>
										<a href="#edit"
										   onclick="return listItemTask('cb<?php echo $i; ?>','dlcategory_edit')"><?php echo $row->title; ?></a><br/>
										<?php echo JText::_('slug'); ?>: <span class="lbl"><?php echo $row->slug; ?></span>
									</td>
									<td align="left"><?php echo strip_tags($row->description);?></td><?php
									$task = $row->published ? 'dlcategory_unpublish' : 'dlcategory_publish';
                                    $alt = $row->published ? JText::_('send_again') : JText::_('dont_send');
									$img = $row->published ? 'eye-open' : 'eye-close'; ?>
									<td class="algcnt valgmdl">
										<a class="btn btn-<?php echo ($row->published ? 'success' : 'danger');?>" href="javascript:;" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')" title="<?php echo $alt;?>"><i class="ico-<?php echo $img;?> ico-white"></i></a>
								</tr><?php
							} // for loop
						} // if ?>
					</tbody>
					<tfoot>
					<tr>
						<td colspan="6">
							<?php echo $pageNav->getListFooter(); ?>
						</td>
					</tr>
					</tfoot>
				</table>
				<div class="clr"></div>
			</div>

			<input type="hidden" name="option" value="com_maqmahelpdesk"/>
			<input type="hidden" id="task" name="task" value="dlcategory"/>
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
					$jMaQma("#task").val('dlcategory_saveorder');
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