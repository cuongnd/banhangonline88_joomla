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
	static function display($rows, $lists)
	{
		$database = JFactory::getDBO(); ?>

		<div class="breadcrumbs">
			<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
			<a href="index.php?option=com_maqmahelpdesk&task=workgroup"><?php echo JText::_('workgroups'); ?></a>
			<a href="index.php?option=com_maqmahelpdesk&task=category"><?php echo JText::_('categories'); ?></a>
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
			<input type="hidden" name="task" value="category"/>
		</form>

		<form action="index.php" method="post" id="adminForm" name="adminForm">
		<?php echo JHtml::_('form.token'); ?>
		<div class="contentarea">
			<table id="contentTable" class="table table-striped table-bordered" cellspacing="0">
				<thead>
				<tr>
					<th width="20">&nbsp;</th>
					<th width="20" class="algcnt valgmdl">#</th>
					<th width="20" class="algcnt valgmdl">
						<input type="checkbox" id="checkall-toggle" name="checkall-toggle" value="" onclick="Joomla.checkAll(this);"/>
					</th>
					<th class="valgmdl"><?php echo JText::_('name'); ?></th>
					<th class="algcnt valgmdl"><?php echo JText::_('tickets'); ?></th>
					<th class="algcnt valgmdl"><?php echo JText::_('kb'); ?></th>
					<th class="algcnt valgmdl"><?php echo JText::_('discussions'); ?></th>
					<th class="algcnt valgmdl"><?php echo JText::_('bugtracker'); ?></th>
					<th class="algcnt valgmdl"><?php echo JText::_('glossary'); ?></th>
					<th class="algcnt valgmdl" width="70"><?php echo JText::_('published'); ?></th>
				</tr>
				</thead>
				<tbody><?php
					if (count($rows) == 0) { ?>
						<tr>
							<td colspan="10"><?php echo JText::_('register_not_found'); ?></td>
						</tr><?php
					} else {
						$i = 0;
						$prev_department = '';
						//$rows = getCategoryChildren(true, 0, 1);
						foreach ($rows as $row)
						{
							// Update category level
							$sql = "UPDATE `#__support_category`
									SET `level`=" . $row->level . "
									WHERE `id`=" . $row->id;
							$database->setQuery($sql);
							$database->query();

							$img = $row->show ? 'eye-open' : 'eye-close';
							$task = $row->show ? 'category_unpublish' : 'category_publish';
							$alt = $row->show ? JText::_('published') : JText::_('unpublished');
							if ($row->workgroup != $prev_department)
							{
								$prev_department = $row->workgroup; ?>
								<tr>
									<td class="ui-app-menu" colspan="10"><strong><?php echo $row->workgroup; ?></strong></td>
								</tr><?php
							} ?>
							<tr id="contentTable-row-<?php echo ($row->id);?>">
								<td width="20" class="dragHandle"></td>
								<td width="20" class="algcnt valgmdl"><span class="lbl"><?php echo $row->id; ?></span></td>
								<td width="20" class="algcnt valgmdl"><?php echo JHTML::_('grid.id', $i, $row->id, 0); ?></td>
								<td><?php
									if ($row->level > 1) { ?>
										<img src="../media/com_maqmahelpdesk/images/dtree/joinbottom.gif" alt="" style="float:left;"/><?php
										if ($row->level > 2) {
											for ($x = 2; $x < $row->level; $x++) { ?>
												<img src="../media/com_maqmahelpdesk/images/dtree/line2.gif" alt="" style="float:left;"/><?php
											}
										}
									} ?>
									<a href="#category_edit" onclick="return listItemTask('cb<?php echo $i;?>','category_edit')"><?php echo $row->title; ?></a><br style="clear:both;"/><?php
									if ($row->level > 1) {
										for ($x=2; $x<=$row->level; $x++)
										{
											echo '&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
										}
									}
									echo JText::_('slug'); ?>: <span class="lbl"><?php echo $row->slug; ?></span>
								</td>
								<td width="70" class="algcnt valgmdl">
									<span class="btn btn-<?php echo ($row->tickets ? 'success' : 'danger');?>"><i class="ico-<?php echo ($row->tickets ? 'ok' : 'remove');?>-sign ico-white"></i></span>
								</td>
								<td width="70" class="algcnt valgmdl">
									<span class="btn btn-<?php echo ($row->kb ? 'success' : 'danger');?>"><i class="ico-<?php echo ($row->kb ? 'ok' : 'remove');?>-sign ico-white"></i></span>
								</td>
								<td width="70" class="algcnt valgmdl">
									<span class="btn btn-<?php echo ($row->discussions ? 'success' : 'danger');?>"><i class="ico-<?php echo ($row->discussions ? 'ok' : 'remove');?>-sign ico-white"></i></span>
								</td>
								<td width="70" class="algcnt valgmdl">
									<span class="btn btn-<?php echo ($row->bugtracker ? 'success' : 'danger');?>"><i class="ico-<?php echo ($row->bugtracker ? 'ok' : 'remove');?>-sign ico-white"></i></span>
								</td>
								<td width="70" class="algcnt valgmdl">
									<span class="btn btn-<?php echo ($row->glossary ? 'success' : 'danger');?>"><i class="ico-<?php echo ($row->glossary ? 'ok' : 'remove');?>-sign ico-white"></i></span>
								</td>
								<td width="70" class="algcnt valgmdl">
									<a class="btn btn-<?php echo ($row->show ? 'success' : 'danger');?>" href="javascript:;" onclick="return listItemTask('cb<?php echo $i;?>','<?php echo $task;?>')" title="<?php echo $alt;?>"><i class="ico-<?php echo $img;?> ico-white"></i></a>
								</td><?php
								$i++; ?>
							</tr><?php
						} // for loop
					} // if ?>
				<tbody>
			</table>
			<div class="clr"></div>
		</div>

		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" id="task" name="task" value="category"/>
		<input type="hidden" name="boxchecked" value="0"/>
		</form><?php
	}
}
