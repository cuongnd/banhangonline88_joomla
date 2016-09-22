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
				<a href="index.php?option=com_maqmahelpdesk&task=product"><?php echo JText::_('downloads'); ?></a>
				<a href="index.php?option=com_maqmahelpdesk&task=licenses"><?php echo JText::_('licenses'); ?></a>
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
							<th class="algcnt valgmdl" width="10">#</th>
							<th class="algcnt valgmdl" width="10"><input type="checkbox" id="checkall-toggle" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/></th>
							<th class="valgmdl"><?php echo JText::_('name'); ?></th>
						</tr>
						</thead>
						<tbody><?php
							for ($i = 0, $n = count($rows); $i < $n; $i++)
							{
								$row = &$rows[$i]; ?>
								<tr>
									<td class="algcnt valgmdl" width="10"><?php echo $pageNav->getRowOffset($i); ?></td>
									<td class="algcnt valgmdl" width="10"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
									<td class="valgmdl">
										<a href="#licenses_edit"
										   onClick="return listItemTask('cb<?php echo $i;?>','licenses_edit')">
											<?php echo $row->title; ?>
										</a><br/>
										<?php echo JText::_('slug'); ?>: <span class="lbl"><?php echo $row->slug; ?></span>
									</td>
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
						<dd class="title"><?php echo JText::_('INFO_LICENSES_TITLE');?></dd>
						<dd class="last">
							<?php echo JText::_('INFO_LICENSES_DESC');?>
							<p align="center"><a href="http://www.imaqma.com/" target="_blank" class="btn"><img
								src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/help.png"
								align="absmiddle" border="0" alt=""/> <?php echo JText::_('more_information');?></a></p>

							<p align="center"><a href="http://www.imaqma.com/" target="_blank" class="btn btn-success"><img
								src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/down.png"
								align="absmiddle" border="0" alt=""/> Download</a></p>
						</dd>
					</dl>
				</div>
				<div class="clr"></div>
			</div>

			<input type="hidden" name="option" value="com_maqmahelpdesk"/>
			<input type="hidden" name="task" value="licenses"/>
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
