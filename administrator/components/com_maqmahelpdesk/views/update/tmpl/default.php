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

// Include helpers
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/file.php';

class update_html
{
	static function showStep1($rowsAddOns)
	{
		global $dbtables, $dbfields;

		$CONFIG = new JConfig();
		$supportConfig = HelpdeskUtility::GetConfig();

		// Frontend permissions
		$oDir = new HelpdeskFile();
		$oDir->Read(JPATH_SITE . '/components/com_maqmahelpdesk/', "/\.(php|html|swf)*$/", true, false, true, "", "", true);
		$oDir->Sort('Fullname', true);
		$frontend = '';
		foreach ($oDir->aFiles as $aFile)
		{
			$sFullname = $oDir->FullName($aFile);
			$bWritable = $oDir->IsWritable($aFile);
			$frontend .= '<p><img src="../media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/' . ($bWritable ? 'ok' : 'no') . '.png" /> ' . JPATH_SITE . '/components/com_maqmahelpdesk/' . $sFullname . '</p>';
		}
		unset($oDir);

		// Administration permissions
		$oDir = new HelpdeskFile();
		$oDir->Read(JPATH_SITE . '/administrator/components/com_maqmahelpdesk/', "/\.(php|html|swf)*$/", true, false, true, "", "", true);
		$oDir->Sort('Fullname', true);
		$admin = '';
		foreach ($oDir->aFiles as $aFile)
		{
			$sFullname = $oDir->FullName($aFile);
			$bWritable = $oDir->IsWritable($aFile);
			$admin .= '<p><img src="../media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/' . ($bWritable ? 'ok' : 'no') . '.png" /> ' . JPATH_SITE . '/administrator/components/com_maqmahelpdesk/' . $sFullname . '</p>';
		}
		unset($oDir); ?>

		<div class="breadcrumbs">
			<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
			<span><?php echo JText::_('tools'); ?></span>
		</div>
		<div class="tabbable tabs-left contentarea">
			<ul class="nav nav-tabs equalheight">
				<li class="active"><a href="#tab1" data-toggle="tab"><img
					src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/addons.png"
					border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('addons');?></a></li>
				<li><a href="#tab2" data-toggle="tab"><img
					src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/lock.png"
					border="0" align="absmiddle"/>&nbsp; <?php echo JText::_('permissions');?></a></li>
			</ul>
			<div class="tab-content contentbar withleft">
				<div id="tab1" class="tab-pane active equalheight">
					<table class="table table-striped table-bordered ontop" cellspacing="0">
						<thead>
						<tr>
							<th class="title"><?php echo JText::_('name'); ?></th>
							<th class="title" width="100" align="center"><?php echo JText::_('type'); ?></th>
							<th class="title" width="50">
								<div align="center"><?php echo JText::_('version'); ?></div>
							</th>
							<th class="title" width="50">
								<div align="center"><?php echo JText::_('published'); ?></div>
							</th>
						</tr>
						</thead>
						<tbody><?php
							if (count($rowsAddOns) == 0) {
								print '<tr><td colspan="5">' . JText::_('no_addons_installed') . '</td></tr>';
							}

							$k = 0;
							for ($i = 0, $n = count($rowsAddOns); $i < $n; $i++) {
								$row = &$rowsAddOns[$i];
								$img = $row->publish ? 'ok' : 'no';
								$alt = $row->publish ? JText::_('published') : JText::_('unpublished');
								switch ($row->execution) {
									case 0:
										$exec = '-';
										break;
									case 1:
										$exec = JText::_('cron_manual');
										break;
									case 2:
										$exec = JText::_('new_ticket');
										break;
									case 3:
										$exec = JText::_('new_ticket_customer');
										break;
									case 4:
										$exec = JText::_('new_ticket_staff');
										break;
									case 5:
										$exec = JText::_('new_reply');
										break;
									case 6:
										$exec = JText::_('new_reply_customer');
										break;
									case 7:
										$exec = JText::_('new_reply_staff');
										break;
								} ?>

							<tr class="<?php echo "row$k"; ?>">
								<td>
									<p style="font-size:16px;font-weight:bold;"><?php echo $row->lname; ?></p>

									<p><i><?php echo $row->description; ?></i></p>

									<p style="margin-left:20px;"><span class="lbl"><?php echo JText::_('url');?></span>
										<span style="font-size:11px;"><a
											href="../index.php?option=com_maqmahelpdesk&task=addon&addon=<?php echo $row->sname; ?>&SecretWord=<?php echo $CONFIG->secret; ?>&format=raw&tmpl=component"
											target="_blank"><?php echo JURI::root();?>index.php?option=com_maqmahelpdesk&task=addon&addon=<?php echo $row->sname; ?>&SecretWord=<?php echo $CONFIG->secret; ?>&format=raw&tmpl=component</a></span>
									</p>

									<p style="margin-left:20px;"><span class="lbl"><?php echo JText::_('path');?></span>
										<span style="font-size:11px;"><?php echo JPATH_SITE . DS;?>index.php?option=com_maqmahelpdesk&task=addon&addon=<?php echo $row->sname; ?>&SecretWord=<?php echo $CONFIG->secret; ?>&format=raw&tmpl=component</span></p>
								</td>
								<td width="100" align="center"><?php echo $exec; ?></td>
								<td width="50">
									<div align="center"><?php echo $row->version; ?></div>
								</td>
								<td width="50">
									<div align="center">
										<a href="index.php?option=com_maqmahelpdesk&task=update_publish&id=<?php echo $row->id; ?>&value=<?php echo ($row->publish == 1 ? 0 : 1); ?>">
											<img
												src="../media/com_maqmahelpdesk/images/themes/<?php echo $supportConfig->theme_icon;?>/16px/<?php echo $img;?>.png"
												border="0" alt="<?php echo $alt;?>"/>
										</a>
									</div>
								</td>
							</tr><?php
								$k = 1 - $k;
							} // for loop ?>
						</tbody>
					</table>
				</div>
				<div id="tab2" class="tab-pane equalheight">
					<table class="table table-striped table-bordered ontop" cellspacing="0">
						<tr>
							<td><h4><?php echo JText::_('frontend_folders'); ?></h4></td>
						</tr>
						<tr>
							<td valign="top"><?php echo $frontend; ?></td>
						</tr>
						<tr>
							<td><h4><?php echo JText::_('admin_folders'); ?></h4></td>
						</tr>
						<tr>
							<td valign="top"><?php echo $admin; ?></td>
						</tr>
						</tbody>
					</table>
				</div>
			</div>
		</div>

		<script type='text/javascript'>
			$jMaQma(document).ready(function () {
				$jMaQma(".equalheight").equalHeights(2150);
			});
		</script><?php
	}
}
