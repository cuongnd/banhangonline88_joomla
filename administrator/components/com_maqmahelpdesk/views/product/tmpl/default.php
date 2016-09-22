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
			<a href="index.php?option=com_maqmahelpdesk&task=product"><?php echo JText::_('files'); ?></a>
			<span><?php echo JText::_('manage'); ?></span>
		</div>
		<div class="contentarea">
			<table id="contenttable" class="table table-striped table-bordered" cellspacing="0">
				<thead>
				<tr>
					<th class="algcnt valgmdl" width="20"></th>
                    <th width="20" class="algcnt valgmdl"><input type="checkbox" id="checkall-toggle" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/></th>
					<th class="valgmdl" nowrap><?php echo JText::_('name'); ?></th>
					<th class="algcnt valgmdl" nowrap><?php echo JText::_('category'); ?></th>
					<th class="algcnt valgmdl" nowrap><?php echo JText::_('version'); ?></th>
					<th class="algcnt valgmdl" nowrap><?php echo JText::_('published'); ?></th>
				</tr>
				</thead>
				<tbody><?php
					if (count($rows) == 0) {
						?>
					<tr>
						<td colspan="6"><?php echo JText::_('register_not_found'); ?></td>
					</tr><?php
					} else {
						for ($i = 0, $n = count($rows); $i < $n; $i++)
						{
							$row = &$rows[$i]; ?>
						<tr id="contentTable-row-<?php echo ($row->id);?>">
							<td class="dragHandle" width="20"></td>
                            <td width="20" class="algcnt valgmdl"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
							<td class="valgmdl"><a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','product_edit')"><?php echo $row->pname; ?></a><br/><?php echo JText::_('slug'); ?>: <span class="lbl"><?php echo $row->slug; ?></span></td>
							<td class="algcnt valgmdl"><?php echo $row->cname; ?></td>
							<td class="algcnt valgmdl"><?php
								$database->setQuery("SELECT version FROM #__support_dl_version WHERE id_download='" . $row->id . "' ORDER BY id DESC LIMIT 0, 1");
								$version = $database->loadResult();
								if ($version=='')
								{
									echo '<span style="color:#ff0000;">n/a</span>';
								}
								echo $version; ?>
							</td><?php
							$task = $row->published ? 'product_unpublish' : 'product_publish';
							$img = $row->published ? 'eye-open' : 'eye-close'; ?>
							<td class="algcnt valgmdl" width="10%"><a class="btn btn-<?php echo ($row->published ? 'success' : 'danger');?>" href="javascript:;" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')"><i class="ico-<?php echo $img;?> ico-white"></i></a></td>
						</tr><?php
						} // for loop
					} // if ?>
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
		<input type="hidden" id="task" name="task" value="product"/>
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
					$jMaQma("#task").val('product_saveorder');
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
