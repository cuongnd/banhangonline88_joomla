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
				<a href="index.php?option=com_maqmahelpdesk&task=workgroup"><?php echo JText::_('workgroups'); ?></a>
				<a href="index.php?option=com_maqmahelpdesk&task=groups"><?php echo JText::_('groups'); ?></a>
				<span><?php echo JText::_('manager'); ?></span>
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
							<th width="10">#</th>
							<th width="10">
								<input type="checkbox" id="checkall-toggle" name="checkall-toggle" value=""
									   onClick="Joomla.checkAll(this);"/>
							</th>
							<th class="title"><?php echo JText::_('name'); ?></th>
						</tr>
						</thead>
						<tbody><?php
							$k = 0;
							for ($i = 0, $n = count($rows); $i < $n; $i++) {
								$row = &$rows[$i]; ?>
							<tr class="<?php echo "row$k"; ?>">
								<td width="10" align="right"><?php echo $pageNav->getRowOffset($i); ?></td>
								<td width="10"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
								<td>
									<a href="#groups_edit"
									   onClick="return listItemTask('cb<?php echo $i;?>','groups_edit')">
										<?php echo $row->title; ?>
									</a>
								</td><?php
								$k = 1 - $k; ?>
							</tr><?php
							} // for loop ?>
						</tbody>
						<tfoot>
						<tr>
							<td colspan="3"><?php echo $pageNav->getListFooter(); ?></td>
						</tr>
						</tfoot>
					</table>
					<?php endif; ?>
				</div>
				<div id="infobox">
					<span id="infoarrow"></span>
					<dl class="first">
						<dd class="title"><?php echo JText::_('INFO_DEPARTMENT_GROUPS_TITLE');?></dd>
						<dd class="last">
							<?php echo JText::_('INFO_DEPARTMENT_GROUPS_DESC');?>
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
			<input type="hidden" name="task" value="groups"/>
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

		$jMaQma(document).ready(function () {
			if ($jMaQma("#contentbox").height() > 400) {
				$jMaQma("#infobox").css("height", $jMaQma("#contentbox").height());
			}
		});
		</script><?php
	}
}
