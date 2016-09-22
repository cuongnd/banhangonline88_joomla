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
				<a href="index.php?option=com_maqmahelpdesk&task=announce"><?php echo JText::_('announcements'); ?></a>
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
							<th class="algcnt valgmdl" width="20"><input type="checkbox" id="checkall-toggle" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/></th>
							<th><?php echo JText::_('title'); ?></th>
							<th class="algcnt valgmdl"><?php echo JText::_('date'); ?></th>
							<th class="algcnt valgmdl"><?php echo JText::_('workgroup'); ?></th>
							<th class="algcnt valgmdl"><?php echo JText::_('client'); ?></th>
							<th class="algcnt valgmdl" width="70"><?php echo JText::_('frontpage'); ?></th>
							<th class="algcnt valgmdl" width="70"><?php echo JText::_('urgent'); ?></th>
							<th class="algcnt valgmdl" width="70"><?php echo JText::_('sent'); ?></th>
						</tr>
						</thead>
						<tfoot>
						<tr>
							<td colspan="9"><?php echo $pageNav->getListFooter(); ?></td>
						</tr>
						</tfoot>
						<tbody><?php
							for ($i = 0, $n = count($rows); $i < $n; $i++)
							{
								$row = &$rows[$i];
								$img1 = $row->frontpage ? 'ok' : 'remove';
								$img2 = $row->urgent ? 'ok' : 'remove';
								$img3 = $row->sent ? 'ok' : 'remove';
								$task = $row->sent ? 'announce_publish' : 'announce_unpublish';
								$alt = $row->sent ? JText::_('send_again') : JText::_('dont_send'); ?>
								<tr>
									<td width="20" class="algcnt valgmdl"><span class="lbl"><?php echo $row->id; ?></span></td>
									<td width="20" class="algcnt valgmdl"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
									<td>
										<a href="#announce_edit" onclick="return listItemTask('cb<?php echo $i;?>','announce_edit')"> <?php echo $row->introtext; ?></a><br/>
										<?php echo JText::_('slug'); ?>: <span class="lbl"><?php echo $row->slug; ?></span>
									</td>
									<td class="algcnt valgmdl"><?php echo HelpdeskDate::DateOffset($supportConfig->dateonly_format,strtotime($row->date)); ?></td>
									<td class="algcnt valgmdl"><?php
										if ($row->id_workgroup == 0) {
											echo JText::_('all');
										} else {
											$database->setQuery("SELECT wkdesc FROM #__support_workgroup WHERE id='" . $row->id_workgroup . "'");
											echo $database->loadResult();
										} ?>
									</td>
									<td class="algcnt valgmdl"><?php
										if ($row->id_client == 0) {
											echo JText::_('all');
										} else {
											$database->setQuery("SELECT clientname FROM #__support_client WHERE id='" . $row->id_client . "'");
											echo $database->loadResult();
										} ?>
									</td>
									<td width="70" class="algcnt valgmdl">
										<span class="btn btn-<?php echo ($row->frontpage ? 'success' : 'danger');?>"><i class="ico-<?php echo $img1;?>-sign ico-white"></i></span>
									</td>
									<td width="70" class="algcnt valgmdl">
										<span class="btn btn-<?php echo ($row->urgent ? 'success' : 'danger');?>"><i class="ico-<?php echo $img2;?>-sign ico-white"></i></span>
									</td>
									<td width="70" class="algcnt valgmdl">
										<a class="btn btn-<?php echo ($img3=='ok' ? 'success' : 'danger');?>" href="javascript:;" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')" title="<?php echo $alt;?>"><i class="ico-<?php echo $img3;?> ico-white"></i></a>
									</td>
								</tr><?php
							} // for ?>
						</tbody>
					</table>
					<?php endif; ?>
				</div>
				<div id="infobox">
					<span id="infoarrow"></span>
					<dl class="first">
						<dd class="title"><?php echo JText::_('info_announcements_title');?></dd>
						<dd class="last">
							<?php echo JText::_('info_announcements_desc');?>
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
			<input type="hidden" name="task" value="announce"/>
			<input type="hidden" name="boxchecked" value="0"/>
		</form><?php
	}
}
