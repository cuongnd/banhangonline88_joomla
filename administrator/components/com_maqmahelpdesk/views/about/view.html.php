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

defined('_JEXEC') or die('Direct Access to this location is not allowed.'); ?>

<div id="AboutInfo" style="display:none;width:750px;">
	<div class="modal">
		<div class="modal-header">
			<a class="close" data-dismiss="modal">&times;</a>

			<h3><?php echo JText::_('about'); ?></h3>
		</div>
		<div class="modal-body">
			<table width="100%" cellspacing="0">
				<tbody>
				<tr>
					<td valign="top">
						<h3><?php echo JText::_('component_info');?></h3>
						<table width="100%" border="0" cellpadding="5" cellspacing="5">
							<tr>
								<td nowrap valign="top" class="key"><?php echo JText::_('name'); ?></td>
								<td><b>MaQma Helpdesk</b></td>
							</tr>
							<tr>
								<td nowrap valign="top" class="key"><?php echo JText::_('dl_version'); ?></td>
								<td><b><?php include(JPATH_SITE . '/components/com_maqmahelpdesk/version.txt'); ?></b>
								</td>
							</tr>
							<tr>
								<td nowrap valign="top" class="key"><?php echo JText::_('developed_by'); ?> </td>
								<td><b><a href="http://www.imaqma.com" target="_blank">Components Lab</a></b></td>
							</tr>
						</table>

						<h3><?php echo JText::_('extrainfo');?></h3>
						<table width="100%" border="0" cellpadding="5" cellspacing="5">
							<tr>
								<td nowrap valign="top" class="key">
									<span class="editlinktip hasTip"
										  title="<?php echo htmlspecialchars(JText::_('gdlibraries') . '::' . JText::_('gdlibraries_desc')); ?>"><?php echo JText::_('gdlibraries'); ?></span>
								</td>
								<td align="left">
									<b><?php (extension_loaded('gd') ? print JText::_('installed') : print JText::_('notinstalled')); ?></b>
								</td>
							</tr>
							<tr>
								<td nowrap valign="top" class="key">
									<span class="editlinktip hasTip"
										  title="<?php echo htmlspecialchars(JText::_('timeoffset_tooltip')) . '::' . JText::_('timeoffset_desc'); ?>"><?php echo JText::_('timeoffset'); ?></span>
								</td>
								<td align="left">
									<b><?php echo ($supportConfig->offset <> '0' ? str_replace('%1', HelpdeskDate::DateOffset('%e %B %Y, %H:%M'), JText::_('timeoffset_config')) : JText::_('timeoffset_off')); ?></b>
								</td>
							</tr>
						</table>
					</td>
					<td valign="top">
						<img src="components/com_maqmahelpdesk/images/logo_helpdesk.png" alt="MaQma Helpdesk"
							 border="0"/>
					</td>
				</tr>
				<tr>
					<td colspan="2">
						<h3><?php echo JText::_('contact_us');?></h3>
						<table border="0" cellpadding="5" cellspacing="5">
							<tr>
								<td><?php echo JText::_('phone'); ?>: (+351) 938 477 950 &bull; </td>
								<td><a href="http://www.imaqma.com/"
									   target="_blank"><?php echo JText::_('request_support'); ?></a> &bull; </td>
								<td><a href="mailto:sales@imaqma.com">Sales</a> &bull; </td>
								<td><a href="mailto:support@imaqma.com">Support</a></td>
								</td>
							</tr>
						</table>
					</td>
				</tr>
				</tbody>
			</table>
		</div>
		<div class="modal-footer">
			<a href="javascript:;" data-dismiss="modal" onclick="$jMaQma('#AboutInfo').modal('hide');"
			   class="btn"><?php echo JText::_('close');?></a>
		</div>
	</div>
</div>