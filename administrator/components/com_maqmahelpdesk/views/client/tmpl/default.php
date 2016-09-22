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
	static function display(&$rows, &$pageNav, $filter)
	{
		$database = JFactory::getDBO();?>

		<div class="breadcrumbs">
			<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
			<a href="index.php?option=com_maqmahelpdesk&task=client"><?php echo JText::_('clients_manager'); ?></a>
			<span><?php echo JText::_('manage'); ?></span>
		</div>

		<form action="index.php" method="post" name="filterForm" style="margin-bottom:0;">
			<?php echo JHtml::_('form.token'); ?>
			<div id="filtersarea">
				<?php echo JString::strtoupper(JText::_('filters'));?> <img src="../media/com_maqmahelpdesk/images/ui/separator.png"
																   style="padding:5px;" align="absmiddle"/>
				<input type="text" id="filter" name="filter" value="<?php echo $filter;?>"/>

				<div class="btn-group" style="float:right;">
					<a href="javascript:;" class="btn"
					   onclick="document.filterForm.getElementById('filter').value='';document.filterForm.submit();"><?php echo JText::_('reset');?></a>
					<a href="javascript:;" class="btn btn-success"
					   onclick="document.filterForm.submit();"><?php echo JText::_('filter');?></a>
				</div>
			</div>
			<input type="hidden" name="option" value="com_maqmahelpdesk"/>
			<input type="hidden" name="task" value="client"/>
		</form>

		<form action="index.php" method="post" id="adminForm" name="adminForm">
			<?php echo JHtml::_('form.token'); ?>
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
							<th width="20" class="algcnt valgmdl">#</th>
							<th width="20" class="algcnt valgmdl"><input type="checkbox" id="checkall-toggle" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/></th>
							<th><?php echo JText::_('name'); ?></th>
							<th><?php echo JText::_('users'); ?></th>
							<th width="70" class="algcnt valgmdl"><?php echo JText::_('tickets'); ?></th>
							<th width="70" class="algcnt valgmdl"><?php echo JText::_('blocked'); ?></th>
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
								$img_blocked = $row->block ? 'remove' : 'ok'; ?>
							<tr>
								<td width="20" class="algcnt valgmdl"><span class="lbl"><?php echo $row->id; ?></span></td>
								<td width="20" class="algcnt valgmdl"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
								<td>
									<?php if ($row->logo != ''):?>
									<img src="<?php echo JURI::root();?>media/com_maqmahelpdesk/images/logos/<?php echo $row->logo; ?>" style="height:32px;" align="right" alt="" />
									<?php endif;?>
									<a href="#client_edit" onclick="return listItemTask('cb<?php echo $i;?>','client_edit')"><?php echo $row->clientname; ?></a><br/>
									<?php echo JText::_('slug'); ?>: <span class="lbl"><?php echo $row->slug; ?></span>
								</td>
								<td class="valgmdl"><?php
									$sql = "SELECT u.name
												FROM #__support_client_users AS c
													 INNER JOIN #__users AS u ON c.id_user=u.id
												WHERE c.id_client=" . $row->id;
									$database->setQuery($sql);
									$users = $database->loadObjectList();
									for ($x = 0; $x < count($users); $x++) {
										echo $users[$x]->name . ', ';
									} ?>
								</td>
								<td width="70" class="algcnt valgmdl"><?php
									$sql = "SELECT COUNT(*)
												FROM #__support_ticket
												WHERE id_client=" . $row->id;
									$database->setQuery($sql);
									echo $database->loadResult(); ?>
								</td>
								<td width="70" class="algcnt valgmdl">
									<span class="btn btn-<?php echo ($row->block ? 'danger' : 'success');?>"><i class="ico-<?php echo $img_blocked;?>-sign ico-white"></i></span>
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
						<dd class="title"><?php echo JText::_('INFO_CLIENT_TITLE');?></dd>
						<dd class="last">
							<?php echo JText::_('INFO_CLIENT_DESC');?>
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
			<input type="hidden" name="task" value="client"/>
			<input type="hidden" name="boxchecked" value="0"/>
		</form><?php
	}
}
