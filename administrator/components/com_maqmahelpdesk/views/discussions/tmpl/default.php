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
		$database = JFactory::getDBO();?>

		<form action="index.php" method="post" id="adminForm" name="adminForm">
			<?php echo JHtml::_('form.token'); ?>
			<div class="breadcrumbs">
				<a href="index.php?option=com_maqmahelpdesk&task=discussions"><?php echo JText::_('discussions'); ?></a>
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
							<th class="valgmdl"><?php echo JText::_('title'); ?></th>
							<th class="valgmdl"><?php echo JText::_('user'); ?></th>
							<th class="valgmdl"><?php echo JText::_('workgroup'); ?></th>
							<th class="valgmdl"><?php echo JText::_('category'); ?></th>
							<th class="algcnt valgmdl" width="80"><?php echo JText::_('date'); ?></th>
							<th class="algcnt valgmdl"><?php echo JText::_('messages'); ?></th>
							<th class="algcnt valgmdl"><?php echo JText::_('published'); ?></th>
						</tr>
						</thead>
						<tfoot>
						<tr>
							<td colspan="9"><?php echo $pageNav->getListFooter(); ?></td>
						</tr>
						</tfoot>
						<tbody><?php
							$k = 0;
							for ($i = 0, $n = count($rows); $i < $n; $i++)
							{
								$row = &$rows[$i];
								$img = $row->published ? 'eye-open' : 'eye-close';
								$task = $row->published ? 'discussions_unpublish' : 'discussions_publish';
								$alt = $row->published ? JText::_('published') : JText::_('unpublished'); ?>
								<tr class="<?php echo "row$k"; ?>">
									<td class="algcnt valgmdl" width="20"><span class="lbl"><?php echo $row->id; ?></span></td>
									<td class="algcnt valgmdl" width="20"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
									<td valign="top" class="valgmdl showPopover"
										data-original-title="<?php echo strip_tags(JText::_('details'));?>"
										data-content="<?php echo addslashes($row->content);?>">
										<a href="<?php echo JRoute::_("../index.php?option=com_maqmahelpdesk&Itemid=" . HelpdeskUtility::GetItemid() . "&id_workgroup=" . $row->id_workgroup . "&task=discussions_view&id=" . $row->id);?>"
										   target="_blank">
											<?php echo $row->title; ?>
										</a>
									</td>
									<td class="valgmdl"><?php echo $row->user; ?></td>
									<td class="valgmdl"><?php echo $row->workgroup; ?></td>
									<td class="valgmdl"><?php echo $row->category; ?></td>
									<td class="algcnt valgmdl"><?php echo $row->date_created; ?></td>
									<td class="algcnt valgmdl" style="font-size:16px;"><span
										class="lbl"><?php echo $row->messages; ?></span> &bull; <span
										class="lbl lbl-important"><?php echo $row->pending;?></span></td>
									<td class="algcnt valgmdl">
										<a class="btn btn-<?php echo ($row->published ? 'success' : 'danger');?>" href="javascript:;" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')" title="<?php echo $alt;?>"><i class="ico-<?php echo $img;?> ico-white"></i></a>
									</td>
								</tr><?php
								$k = 1 - $k;
							} // for ?>
						</tbody>
					</table>
					<?php endif; ?>
				</div>
				<div id="infobox">
					<span id="infoarrow"></span>
					<dl class="first">
                        <dd class="title"><?php echo JText::_('info_discussions_title');?></dd>
						<dd class="last">
							<?php echo JText::_('INFO_DISCUSSIONS_DESC');?>
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
			$jMaQma('.showPopover').popover({'html':true, 'placement':'right', 'trigger':'hover'});
		});
		</script><?php
	}
}
