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
				<a href="javascript:;"><?php echo JText::_('actitivity_options'); ?></a>
				<a href="index.php?option=com_maqmahelpdesk&task=rates"><?php echo JText::_('activity_rates'); ?></a>
				<span><?php echo JText::_('manage'); ?></span>
			</div>
			<div class="contentarea">
				<div id="contentbox">
					<table class="table table-striped table-bordered" cellspacing="0">
						<thead>
						<tr>
							<th class="algcnt valgmdl" width="20">#</th>
							<th class="algcnt valgmdl" width="20">
								<input type="checkbox" id="checkall-toggle" name="checkall-toggle" value=""
									   onClick="Joomla.checkAll(this);"/>
							</th>
							<th><?php echo JText::_('description'); ?></th>
							<th class="algcnt valgmdl" width="70"><?php echo JText::_('multiplier'); ?></th>
							<th class="algcnt valgmdl" width="70"><?php echo JText::_('default'); ?></th>
							<th class="algcnt valgmdl" width="70"><?php echo JText::_('published'); ?></th>
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
									$row = &$rows[$i];
									$img = $row->published ? 'eye-open' : 'eye-close';
									$img_def = $row->isdefault ? 'ok' : 'remove';
									$task = $row->published ? 'rates_unpublish' : 'rates_publish';
									$alt = $row->published ? JText::_('published') : JText::_('unpublished'); ?>
									<tr>
										<td class="algcnt valgmdl" width="20"><span class="lbl"><?php echo $row->id; ?></span></td>
										<td class="algcnt valgmdl" width="20"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
										<td>
											<a href="#rates_edit"
											   onClick="return listItemTask('cb<?php echo $i;?>','rates_edit')">
												<?php echo $row->description; ?>
											</a>
										</td>
										<td class="algcnt valgmdl" width="70"><?php echo $row->multiplier; ?></td>
										<td class="algcnt valgmdl" width="70">
											<span class="btn btn-<?php echo ($img_def=='ok' ? 'success' : 'danger');?>"><i class="ico-<?php echo $img_def;?>-sign ico-white"></i></span>
										</td>
										<td class="algcnt valgmdl" width="70">
											<a class="btn btn-<?php echo ($row->published ? 'success' : 'danger');?>" href="javascript:;" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')" title="<?php echo $alt;?>"><i class="ico-<?php echo $img;?> ico-white"></i></a>
										</td>
									</tr><?php
								} // for loop
							} // if ?>
						<tbody>
						<tfoot>
						<tr>
							<td colspan="6"><?php echo $pageNav->getListFooter(); ?></td>
						</tr>
						</tfoot>
					</table>
				</div>
				<div id="infobox">
					<span id="infoarrow"></span>
					<dl class="first">
						<dd class="title"><?php echo JText::_('INFO_RATES_TITLE');?></dd>
						<dd class="last">
							<?php echo JText::_('INFO_RATES_DESC');?>
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
			<input type="hidden" name="task" value="rates"/>
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
