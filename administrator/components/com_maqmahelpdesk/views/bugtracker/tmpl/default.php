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
	{ ?>
		<form action="index.php" method="post" id="adminForm" name="adminForm">
			<?php echo JHtml::_('form.token'); ?>
			<div class="breadcrumbs">
				<a href="index.php?option=com_maqmahelpdesk&task=bugtracker"><?php echo JText::_('bugtracker'); ?></a>
				<span><?php echo JText::_('manage'); ?></span>
			</div>
			<div class="contentarea">
				<div id="contentbox">
					<?php if (count($rows) == 0) : ?>
					<div class="detailmsg">
						<h1><?php echo JText::_('register_not_found'); ?></h1>
					</div>
					<?php else: ?>
					<table class="table table-striped table-bordered" cellspacing="0">
						<thead>
						<tr>
							<th class="algcnt valgmdl" width="20">#</th>
							<th class="algcnt valgmdl" width="20"><input type="checkbox" id="checkall-toggle" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/></th>
							<th class="algcnt valgmdl"><?php echo JText::_('priority'); ?></th>
							<th class="algcnt valgmdl"><?php echo JText::_('type'); ?></th>
							<th class="valgmdl"><?php echo JText::_('title'); ?></th>
							<th class="valgmdl"><?php echo JText::_('user'); ?></th>
							<th class="algcnt valgmdl" width="80"><?php echo JText::_('date'); ?></th>
							<th class="algcnt valgmdl"><?php echo JText::_('messages'); ?></th>
							<th class="algcnt valgmdl"><?php echo JText::_('status'); ?></th>
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
								$row = &$rows[$i]; ?>
								<tr>
									<td class="algcnt valgmdl" width="20"><span class="lbl"><?php echo $row->id; ?></span></td>
									<td class="algcnt valgmdl" width="20"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
									<td class="algcnt valgmdl priority_<?php echo $row->priority;?>"><?php echo JText::_('bug_priority_' . $row->priority); ?></td>
									<td class="algcnt valgmdl"><?php echo JText::_('bug_type_' . $row->type); ?></td>
									<td class="valgmdl showPopover"
										data-original-title="<?php echo strip_tags(JText::_('details'));?>"
										data-content="<?php echo ($row->content);?>">
										<a href="<?php echo JRoute::_("../index.php?option=com_maqmahelpdesk&Itemid=" . HelpdeskUtility::GetItemid() . "&id_workgroup=" . $row->id_workgroup . "&task=bugtracker_view&id=" . $row->id);?>"
										   target="_blank">
											<?php echo $row->title; ?>
										</a><br />
										<?php echo JText::_('category') . ': ' . $row->category; ?>
									</td>
									<td class="valgmdl"><?php echo $row->user; ?></td>
									<td class="algcnt valgmdl"><?php echo $row->date_created; ?></td>
									<td class="algcnt valgmdl" style="font-size:16px;">
										<?php echo $row->messages; ?> / <span style="color:#f00000;font-weight:bold;"><?php echo $row->pending;?></span>
									</td>
									<td class="algcnt valgmdl">
										<?php echo JText::_('bug_status_' . $row->status); ?>
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
						<dd class="title"><?php echo JText::_('INFO_BUGTRACKER_TITLE');?></dd>
						<dd class="last">
							<?php echo JText::_('INFO_BUGTRACKER_DESC');?>
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
			<input type="hidden" name="task" value="kb_moderate"/>
			<input type="hidden" name="boxchecked" value="0"/>
		</form><?php
	}
}
