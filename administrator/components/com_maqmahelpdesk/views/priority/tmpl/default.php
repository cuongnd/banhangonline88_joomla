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

		<form action="index.php" method="post" id="adminForm" name="adminForm">
			<?php echo JHtml::_('form.token'); ?>
			<div class="breadcrumbs">
				<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
				<a href="index.php?option=com_maqmahelpdesk&task=priority"><?php echo JText::_('priorities'); ?></a>
				<span><?php echo JText::_('manage'); ?></span>
			</div>
			<div class="contentarea">
				<div id="contentbox">
					<?php if (count($rows) == 0) : ?>
					<div class="detailmsg">
						<h1><?php echo JText::_('register_not_found'); ?></h1>
						<p><?php echo JText::_('to_add_new_record_desc'); ?></p>
					</div>
					<script type="text/javascript"> MaQmaJS.AddHelpHand('toolbar-new'); </script>
					<?php else: ?>
					<table class="table table-striped table-bordered" cellspacing="0">
						<thead>
						<tr>
							<th class="algcnt valgmdl" width="20">#</th>
							<th class="algcnt valgmdl" width="20"> <input type="checkbox" id="checkall-toggle" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/></th>
							<th><?php echo JText::_('description'); ?></th>
							<th class="algcnt valgmdl"><?php echo JText::_('time'); ?></th>
							<th class="algcnt valgmdl" width="50"><?php echo JText::_('default'); ?></th>
							<th class="algcnt valgmdl" width="70"><?php echo JText::_('published'); ?></th>
						</tr>
						</thead>
						<tbody><?php
							for ($i = 0, $n = count($rows); $i < $n; $i++) {
								$row = &$rows[$i];
								$img = $row->show ? 'eye-open' : 'eye-close';
								$task = $row->show ? 'priority_unpublish' : 'priority_publish';
								$alt = $row->show ? JText::_('published') : JText::_('unpublished');
								$default = $row->isdefault ? 'ok' : 'remove'; ?>
								<tr>
									<td class="algcnt valgmdl" width="20"><span class="lbl"><?php echo $row->id; ?></span></td>
									<td class="algcnt valgmdl" width="20"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
									<td class="valgmdl">
										<a href="#priority_edit"
										   onClick="return listItemTask('cb<?php echo $i;?>','priority_edit')">
											<?php echo $row->description; ?>
										</a>
									</td>
									<td class="algcnt valgmdl"><?php echo $row->timevalue . " " . ($row->timeunit == "D" ? JText::_('days') : JText::_('hours')); ?></td>
									<td class="algcnt valgmdl" width="50"><span class="btn btn-<?php echo ($default=='ok' ? 'success' : 'danger');?>"><i class="ico-<?php echo $default;?>-sign ico-white"></i></span></td>
									<td class="algcnt valgmdl" width="70"><a class="btn btn-<?php echo ($row->show ? 'success' : 'danger');?>" href="javascript:;" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')" title="<?php echo $alt;?>"><i class="ico-<?php echo $img;?> ico-white"></i></a></td>
								</tr><?php
							} // for loop ?>
						</tbody>
						<tfoot>
						<tr>
							<td colspan="6"><?php echo $pageNav->getListFooter(); ?></td>
						</tr>
						</tfoot>
					</table>
					<?php endif; ?>
				</div>
				<div id="infobox">
					<span id="infoarrow"></span>
					<dl class="first">
						<dd class="title"><?php echo JText::_('INFO_PRIORITY_TITLE');?></dd>
						<dd class="last">
							<?php echo JText::_('INFO_PRIORITY_DESC');?>
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
			<input type="hidden" name="task" value="priority"/>
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
