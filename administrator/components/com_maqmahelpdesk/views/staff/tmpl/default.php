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
	static function display(&$rows, $lists, &$pageNav)
	{
		$database = JFactory::getDBO(); ?>

		<div class="breadcrumbs">
			<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
			<a href="index.php?option=com_maqmahelpdesk&task=staff"><?php echo JText::_('support_staff'); ?></a>
			<span><?php echo JText::_('manage'); ?></span>
		</div>

		<form action="index.php" method="post" id="filterForm" name="filterForm">
			<?php echo JHtml::_('form.token'); ?>
			<div id="filtersarea">
				<?php echo JString::strtoupper(JText::_('filters'));?> <img src="../media/com_maqmahelpdesk/images/ui/separator.png"
																   style="padding:5px;"
																   align="absmiddle"/> <?php echo JText::_('workgroup') . ': ' . $lists['workgroup'];?>
			</div>
			<input type="hidden" name="option" value="com_maqmahelpdesk"/>
			<input type="hidden" name="task" value="staff"/>
		</form>
		<div class="contentarea">
			<form action="index.php" method="post" id="adminForm" name="adminForm">
				<?php echo JHtml::_('form.token'); ?>
				<?php
				if (count($rows) == 0) {
					?>
					<div class="detailmsg">
						<h1><?php echo JText::_('register_not_found'); ?></h1>
						<p><?php echo JText::_('to_add_new_record_desc'); ?></p>
					</div>
					<script type="text/javascript"> MaQmaJS.AddHelpHand('toolbar-new'); </script><?php
				} else { ?>
                    <table class="table table-striped table-bordered" cellspacing="0">
                        <thead>
                        <tr>
                            <th class="valgmdl">&nbsp;</th>
                            <th class="valgmdl"><?php echo JText::_('name'); ?></th>
                            <th class="valgmdl"><?php echo JText::_('workgroup'); ?></th>
                            <th class="algcnt valgmdl"><?php echo JText::_('bugtracker'); ?></th>
                            <th class="algcnt valgmdl"><?php echo JText::_('delete_tickets'); ?></th>
                            <th class="algcnt valgmdl"><?php echo JText::_('support_staff_type'); ?></th>
                            <th class="algcnt valgmdl">&nbsp;</th>
                        </tr>
                        </thead>
						<tbody><?php
							$previous_user = '';
							for ($i = 0, $n = count($rows); $i < $n; $i++)
							{
								$row = &$rows[$i];
								$show_user = false;
								if ($previous_user != $row->id_user)
								{
									$previous_user = $row->id_user;
									$show_user = true;
								} ?>
	                            <tr>
	                                <td width="32">
		                                <?php if($show_user):?>
                                        <img src="<?php echo HelpdeskUser::GetAvatar($row->id_user);?>" width="32" />
		                                <?php endif;?>
	                                </td>
                                    <td class="valgmdl">
                                        <input type="checkbox" id="user<?php echo $i;?>" name="cid[]" value="<?php echo $row->id_user;?>" style="display:none;" />
	                                    <?php if($show_user):?>
	                                    <a href="#users_edit"
                                           onclick="return listItemTask('user<?php echo $i;?>','users_edit')">
		                                    <?php echo $row->name ?>
	                                    </a>
		                                <?php endif;?>
	                                </td>
	                                <td class="valgmdl">
                                        <a href="#staff_edit"
                                           onclick="$jMaQma('#id').val(<?php echo $row->id;?>);Joomla.submitbutton('staff_edit');">
			                                <?php echo $row->wkdesc ?>
                                        </a>
	                                </td>
	                                <td class="algcnt valgmdl" width="120"><?php
										$img = $row->bugtracker ? 'ok' : 'remove'; ?>
	                                    <span class="btn btn-<?php echo ($img=='ok' ? 'success' : 'danger');?>"><i class="ico-<?php echo $img;?>-sign ico-white"></i></span>
	                                </td>
	                                <td class="algcnt valgmdl" width="120"><?php
										$img = $row->can_delete ? 'ok' : 'remove'; ?>
	                                    <span class="btn btn-<?php echo ($img=='ok' ? 'success' : 'danger');?>"><i class="ico-<?php echo $img;?>-sign ico-white"></i></span>
	                                </td>
                                    <td class="algcnt valgmdl">
                                        <select id="permission<?php echo $i;?>" name="permission<?php echo $i;?>" onchange="$jMaQma('#task').val('staff_permission');$jMaQma('#permission').val(this.value);$jMaQma('#id').val(<?php echo $row->id;?>);$jMaQma('#adminForm').submit();">
                                            <option value="7" <?php echo ($row->manager == 7 ? 'selected="selected"' : '');?>><?php echo JText::_('manager');?></option>
                                            <option value="6" <?php echo ($row->manager == 6 ? 'selected="selected"' : '');?>><?php echo JText::_('team_leader');?></option>
                                            <option value="5" <?php echo ($row->manager == 5 ? 'selected="selected"' : '');?>><?php echo JText::_('support_user');?></option>
                                        </select>
                                    </td>
		                            <td>
                                        <div class="btn-group" style="float:right;">
                                            <a class="btn" href="javascript:;" onclick="$jMaQma('#id').val(<?php echo $row->id;?>);Joomla.submitbutton('staff_edit');"><i class="ico-pencil"></i> <small><?php echo JText::_("edit");?></small></a>
                                            <a class="btn btn-danger" href="javascript:;" onclick="$jMaQma('#id').val(<?php echo $row->id;?>);Joomla.submitbutton('staff_remove');"><i class="ico-trash ico-white"></i>&nbsp;</a>
                                        </div>
		                            </td>
	                            </tr><?php
							} // for loop ?>
                        </tbody>
                        <tfoot>
                        <tr>
                            <td colspan="7"><?php echo $pageNav->getListFooter(); ?></td>
                        </tr>
                        </tfoot>
                    </table>
                    <div class="clr"></div><?php
				} ?>

				<div class="clr"></div>

				<input type="hidden" name="option" value="com_maqmahelpdesk" />
				<input type="hidden" id="task" name="task" value="staff" />
                <input type="hidden" name="boxchecked" value="0" />
                <input type="hidden" id="permission" name="permission" value="0" />
                <input type="hidden" id="id" name="id" value="0" />
			</form>
		</div><?php
	}
}
