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
		$supportConfig = HelpdeskUtility::GetConfig(); ?>

		<div class="breadcrumbs">
			<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
			<a href="index.php?option=com_maqmahelpdesk&task=product"><?php echo JText::_('downloads'); ?></a>
			<a href="index.php?option=com_maqmahelpdesk&task=customer"><?php echo JText::_('clients_access'); ?></a>
			<span><?php echo JText::_('manage'); ?></span>
		</div>

		<form action="index.php" method="POST" id="adminForm" name="adminForm">
			<?php echo JHtml::_('form.token'); ?>
			<div id="filtersarea">
				<?php echo JString::strtoupper(JText::_('filters'));?> <img src="../media/com_maqmahelpdesk/images/ui/separator.png"
																   style="padding:5px;" align="absmiddle"/>
				<input type="text" id="filter" name="filter" value="<?php echo $filter;?>"/>

				<div class="btn-group" style="float:right;">
					<a href="javascript:;" class="btn"
					   onclick="document.adminForm.getElementById('filter').value='';document.adminForm.submit();"><?php echo JText::_('reset');?></a>
					<a href="javascript:;" class="btn btn-success"
					   onclick="document.adminForm.submit();"><?php echo JText::_('filter');?></a>
				</div>
			</div>

			<div class="contentarea">
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
						<th class="algcnt valgmdl" width="20"><input type="checkbox" id="checkall-toggle" name="checkall-toggle" value="" onclick="Joomla.checkAll(<?php echo count($rows); ?>);"/></th>
						<th class="valgmdl" nowrap><?php echo JText::_('client'); ?></th>
						<th class="valgmdl" nowrap><?php echo JText::_('dl_product'); ?></th>
						<th class="algcnt valgmdl" width="50"><?php echo JText::_('active'); ?></th>
						<th class="algcnt valgmdl" width="70"><?php echo JText::_('start'); ?></th>
						<th class="algcnt valgmdl" width="70"><?php echo JText::_('end'); ?></th>
					</tr>
					</thead>
					<tbody><?php
						for ($i = 0, $n = count($rows); $i < $n; $i++)
						{
							$row = &$rows[$i]; ?>
							<tr id="contentTable-row-<?php echo ($row->id);?>">
								<td class="algcnt valgmdl" width="20"><span class="lbl"><?php echo $row->id; ?></span></td>
								<td class="algcnt valgmdl" width="20">
									<input type="checkbox" id="cb<?php echo $i;?>" name="cid[]" value="<?php echo $row->id; ?>"
										   onClick="isChecked(this.checked);"/>
								</td>
								<td class="valgmdl"><a href="#edit" onclick="return listItemTask('cb<?php echo $i; ?>','customer_edit')"><?php echo $row->clientname; ?></a></td>
								<td class="valgmdl"><?php echo $row->category . ' &raquo; ' . $row->product;?></td>
								<td class="algcnt valgmdl" width="50"><?php
									$img = $row->isactive ? 'ok' : 'remove'; ?>
									<span class="btn btn-<?php echo ($img=='ok' ? 'success' : 'danger');?>"><i class="ico-<?php echo $img;?>-sign ico-white"></i></span>
								</td>
								<td class="algcnt valgmdl" width="70"><?php echo HelpdeskDate::DateOffset($supportConfig->dateonly_format,strtotime($row->servicefrom));?></td>
								<td class="algcnt valgmdl" width="70"><?php echo HelpdeskDate::DateOffset($supportConfig->dateonly_format,strtotime($row->serviceuntil));?></td>
							</tr><?php
						} ?>
					</tbody>
					<tfoot>
					<tr>
						<td colspan="8">
							<?php echo $pageNav->getListFooter(); ?>
						</td>
					</tr>
					</tfoot>
				</table>
				<?php endif; ?>
				<div class="clr"></div>
			</div>

			<input type="hidden" name="option" value="com_maqmahelpdesk"/>
			<input type="hidden" name="task" value="customer"/>
			<input type="hidden" name="boxchecked" value="0"/>
		</form>

		<script type="text/javascript">
		$jMaQma(document).ready(function () {
			$jMaQma("#filter").css("width", $jMaQma("#filtersarea").width() - $jMaQma("#filter").offset().left - $jMaQma(".btn-group").width());
			$jMaQma(window).resize(function () {
				$jMaQma("#filter").css("width", $jMaQma("#filtersarea").width() - $jMaQma("#filter").offset().left - $jMaQma(".btn-group").width());
			});
		});
		</script><?php
	}
}
