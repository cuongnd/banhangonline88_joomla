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

class MaQmaHtmlCFDefault
{
	static function display(&$rows, &$pageNav)
	{ ?>
	    <form action="index.php" method="post" id="adminForm" name="adminForm">
			<?php echo JHtml::_('form.token'); ?>
	        <div class="breadcrumbs">
	            <a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
	            <a href="index.php?option=com_maqmahelpdesk&task=customfield"><?php echo JText::_('cfields'); ?></a>
	            <a href="index.php?option=com_maqmahelpdesk&task=contracts"><?php echo JText::_('contracts'); ?></a>
	            <span><?php echo JText::_('manage'); ?></span>
	        </div>
	        <div class="contentarea">
	            <table id="contentTable" class="table table-striped table-bordered" cellspacing="0">
	                <thead>
	                <tr>
	                    <th width="20"></th>
	                    <th class="algcnt valgmdl" width="20">#</th>
	                    <th class="algcnt valgmdl" width="20">
	                        <input type="checkbox" id="checkall-toggle" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/>
	                    </th>
	                    <th><?php echo JText::_('field_name'); ?></th>
	                    <th class="algcnt valgmdl"><?php echo JText::_('field_type'); ?></th>
	                    <th class="algcnt valgmdl" width="70"><?php echo JText::_('required'); ?></th>
	                </tr>
	                </thead>
	                <tbody>
						<?php
						if (count($rows) == 0) { ?>
	                    <tr>
	                        <td colspan="6"><?php echo JText::_('register_not_found'); ?></td>
	                    </tr><?php
						} else {
							for ($i = 0, $n = count($rows); $i < $n; $i++)
							{
								$row = &$rows[$i];
								$img = $row->required ? 'eye-open' : 'eye-close'; ?>
	                        <tr>
	                            <td width="20" class="dragHandle"></td>
	                            <td class="algcnt valgmdl" width="20"><span class="lbl"><?php echo $row->id; ?></span></td>
	                            <td class="algcnt valgmdl" width="20"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
	                            <td>
	                                <a href="#contracts_fields_edit"
	                                   onClick="return listItemTask('cb<?php echo $i;?>','contracts_fields_edit')">
										<?php echo $row->caption; ?>
	                                </a>
	                            </td>
	                            <td><?php echo JText::_('formfield_' . $row->ftype); ?></td>
	                            <td class="algcnt valgmdl">
	                                <span class="btn btn-<?php echo ($img=='ok' ? 'success' : 'danger');?>"><i class="ico-<?php echo $img;?>-sign ico-white"></i></span>
	                            </td>
	                        </tr><?php
							} // for loop
						} // if ?>
	                </tbody>
	                <tfoot>
	                <tr>
	                    <td colspan="7">
							<?php echo $pageNav->getListFooter(); ?>
	                    </td>
	                </tr>
	                </tfoot>
	            </table>
	            <div class="clr"></div>
	        </div>

	        <input type="hidden" name="option" value="com_maqmahelpdesk"/>
	        <input type="hidden" id="task" name="task" value="contracts"/>
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
	                    $jMaQma("#task").val('contracts_saveorder');
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
