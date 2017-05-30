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
				<a href="index.php?option=com_maqmahelpdesk&task=holidays"><?php echo JText::_('holidays'); ?></a>
				<span><?php echo JText::_('manager'); ?></span>
			</div>
			<div class="contentarea">
				<div id="contentbox" class="mqmclear">
					<table class="table table-striped table-bordered" cellspacing="0">
						<thead>
						<tr>
							<th class="algcnt valgmdl" width="10">#</th>
							<th class="algcnt valgmdl" width="10">
								<input type="checkbox" id="checkall-toggle" name="checkall-toggle" value=""
									   onClick="Joomla.checkAll(this);"/>
							</th>
							<th class="algcnt valgmdl" width="100">
								<div align="center"><?php echo JText::_('date'); ?></div>
							</th>
							<th><?php echo JText::_('name'); ?></th>
						</tr>
						</thead>
						<tbody><?php
							if (count($rows) == 0) { ?>
								<tr>
									<td colspan="4"><?php echo JText::_('register_not_found'); ?></td>
								</tr><?php
							} else {
								$k = 0;
								for ($i = 0, $n = count($rows); $i < $n; $i++)
								{
									$row = &$rows[$i]; ?>
									<tr>
										<td class="algcnt valgmdl" width="10"><?php echo $pageNav->getRowOffset($i); ?></td>
										<td class="algcnt valgmdl" width="10"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
										<td class="algcnt valgmdl" width="100">
											<a href="#holidays_edit"
											   onclick="return listItemTask('cb<?php echo $i;?>','holidays_edit')">
												<?php echo $row->holiday_date; ?>
											</a>
										</td>
										<td>
											<a href="#holidays_edit"
											   onclick="return listItemTask('cb<?php echo $i;?>','holidays_edit')">
												<?php echo $row->name; ?>
											</a>
										</td>
									</tr><?php
								} // for loop
							} // if ?>
						</tbody>
						<tfoot>
						<tr>
							<td colspan="4"><?php echo $pageNav->getListFooter(); ?></td>
						</tr>
						</tfoot>
					</table>
				</div>
				<div id="infobox">
					<span id="infoarrow"></span>
					<dl class="first">
						<dd class="title"><?php echo JText::_('INFO_HOLIDAYS_TITLE');?></dd>
						<dd class="last">
							<?php echo JText::_('INFO_HOLIDAYS_DESC');?>
                            <div class="btn-group">
                                <a href="#" target="_blank" class="btn btn-small"><i class="ico-book"></i> <?php echo JText::_('more_information');?></a>
                                &nbsp;
                                <a id="mqmCloseHelp" href="javascript:;" class="btn btn-small btn-inverse"><i class="ico-off ico-white"></i> <?php echo JText::_('close');?></a>
                            </div>
						</dd>
					</dl>
				</div>
				<div class="clr"></div>
			</div>

			<input type="hidden" name="option" value="com_maqmahelpdesk"/>
			<input type="hidden" name="task" value="holidays"/>
			<input type="hidden" name="boxchecked" value="0"/>
		</form>

		<script type="text/javascript">
        Joomla.submitbutton = function (pressbutton) {
            var form = document.adminForm;
            if (pressbutton == 'show_help') {
                $jMaQma("#infobox").show();
                return;
            }

            Joomla.submitform(pressbutton, document.getElementById('adminForm'));
        }
		</script><?php
	}
}
