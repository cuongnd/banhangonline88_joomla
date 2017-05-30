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
					<table id="contentTable" class="table table-striped table-bordered" cellspacing="0">
						<thead>
						<tr>
							<th width="20" nowrap="nowrap"></th>
							<th width="20" class="algcnt valgmdl">#</th>
							<th width="20" class="algcnt valgmdl"><input type="checkbox" id="checkall-toggle" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/></th>
							<th><?php echo JText::_('name'); ?></th>
							<th><?php echo JText::_('email_from_name'); ?></th>
							<th><?php echo JText::_('email_from_address'); ?></th>
							<th class="algcnt valgmdl"><?php echo JText::_('contract_only'); ?></th>
							<th class="algcnt valgmdl"><?php echo JText::_('published'); ?></th>
						</tr>
						</thead>
						<tbody><?php
							for ($i = 0, $n = count($rows); $i < $n; $i++)
							{
								$row = &$rows[$i];
								$img = $row->show ? 'eye-open' : 'eye-close';
								$img_contract = $row->contract ? 'ok' : 'remove';
								$task = $row->show ? 'workgroup_unpublish' : 'workgroup_publish';
								$alt = $row->show ? JText::_('published') : JText::_('unpublished'); ?>
								<tr id="contentTable-row-<?php echo ($row->id);?>">
									<td width="20" class="dragHandle"></td>
									<td width="20" class="algcnt valgmdl"><span class="lbl"><?php echo $row->id; ?></span></td>
									<td width="20" class="algcnt valgmdl"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
									<td>
										<?php echo ($row->support_only ? '<img src="../media/com_maqmahelpdesk/images/themes/'.$supportConfig->theme_icon.'/16px/lock.png" alt="" />' : '');?>
										<a href="#workgroup_edit"
										   onClick="return listItemTask('cb<?php echo $i;?>','workgroup_edit')"><?php echo $row->wkdesc; ?></a><br/>
										<?php echo JText::_('slug'); ?>: <span class="lbl"><?php echo $row->slug; ?></span>
									</td>
									<td class="valgmdl"><?php echo $row->wkmail_address_name; ?></td>
									<td class="valgmdl"><?php echo $row->wkmail_address; ?></td>
									<td class="algcnt valgmdl"><span class="btn btn-<?php echo ($img_contract=='ok' ? 'success' : 'danger');?>"><i class="ico-<?php echo $img_contract;?>-sign ico-white"></i></span></td>
									<td class="algcnt valgmdl"><a class="btn btn-<?php echo ($row->show ? 'success' : 'danger');?>" href="javascript:;" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')" title="<?php echo $alt;?>"><i class="ico-<?php echo $img;?> ico-white"></i></a></td>
								</tr><?php
							} // for loop ?>
						<tbody>
						<tfoot>
						<tr>
							<td colspan="8">
								<?php echo $pageNav->getListFooter(); ?>
							</td>
						</tr>
						</tfoot>
					</table>
					<?php endif; ?>
				</div>
				<div id="infobox">
					<span id="infoarrow"></span>
					<dl class="first">
						<dd class="title"><?php echo JText::_('INFO_DEPARTMENT_TITLE');?></dd>
						<dd class="last">
							<?php echo JText::_('INFO_DEPARTMENT_DESC');?>
                            <p>&nbsp;</p>
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
			<input type="hidden" id="task" name="task" value="workgroup"/>
			<input type="hidden" name="boxchecked" value="0"/>
		</form><?php
	}
}
