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

/**
 * @package MaQma Helpdesk
 */
class tools_html
{
	static function db1($lists, $execute)
	{
		$supportConfig = HelpdeskUtility::GetConfig(); ?>

	<script language="javascript" type="text/javascript">
	Joomla.submitbutton = function (pressbutton) {
		var form = document.adminForm;
		if (pressbutton == '') {
            Joomla.submitform(pressbutton);
			return;
		}

		Joomla.submitform(pressbutton, document.getElementById('adminForm'));
	}
	</script>

	<form action="index.php" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
		<?php echo JHtml::_('form.token'); ?>
		<div class="breadcrumbs">
			<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
			<a href="javascript:;"><?php echo JText::_('tools'); ?></a>
			<span><?php echo JText::_('database_cleanup'); ?></span>
		</div>
		<div class="contentarea">
			<p><?php echo ($execute ? JText::_('dbclean_finish') : JText::_('dbclean_desc')); ?></p>

			<p>&nbsp;</p>

			<table width="100%">
				<tr>
					<td width="15%" class="actionlabel"><?php echo JText::_('tickets_status'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['tickets_status']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('categories'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['categories']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('kb_categories2'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['kb_categories']; ?></td>
				</tr>
				<tr>
					<td width="15%" class="actionlabel"><?php echo JText::_('support_staff'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['support_staff']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('client_users'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['client_users']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('ticket_replies'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['ticket_replies']; ?></td>
				</tr>
				<tr>
					<td width="15%" class="actionlabel"><?php echo JText::_('tickets_priorities'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['tickets_priorities']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('ticket_rates'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['ticket_rates']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('client_permissions'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['client_wks']; ?></td>
				</tr>
				<tr>
					<td width="15%" class="actionlabel"><?php echo JText::_('ticket_notes'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['ticket_notes']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('client_information'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['client_info']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('client_contracts'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['client_contracts']; ?></td>
				</tr>
				<tr>
					<td width="15%" class="actionlabel"><?php echo JText::_('tasks'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['tasks']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('announcements'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['announces']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('cfields'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['wk_fields']; ?></td>
				</tr>
				<tr>
					<td width="15%" class="actionlabel"><?php echo JText::_('cfields_values'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['field_values']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('kb_comments'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['kb_comments']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('kb_categories'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['kb_categories']; ?></td>
				</tr>
				<tr>
					<td width="15%" class="actionlabel"><?php echo JText::_('kb_rates'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['kb_rates']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"></td>
					<td width="15%" class="actionnumber"></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"></td>
					<td width="15%" class="actionnumber"></td>
				</tr>
			</table>
		</div>

		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="task" value=""/>
	</form><?php
	}

	static function rstickets1($lists, $execute, $ispro)
	{
		$supportConfig = HelpdeskUtility::GetConfig(); ?>

	<script language="javascript" type="text/javascript">
		Joomla.submitbutton = function (pressbutton) {
			var form = document.adminForm;
			if (pressbutton == '') {
                Joomla.submitform(pressbutton);
				return;
			}

			Joomla.submitform(pressbutton, document.getElementById('adminForm'));
		}
	</script>

	<form action="index.php" method="post" id="adminForm" name="adminForm">
		<?php echo JHtml::_('form.token'); ?>
		<div class="breadcrumbs">
			<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
			<a href="javascript:;"><?php echo JText::_('tools'); ?></a>
			<span><?php echo JText::_('import_rstickets'); ?></span>
		</div>
		<div class="contentarea">
			<h1 style="text-align:center;margin-top:0;padding-top:20px;"><?php echo $execute ? JText::_('rstickets_finish') : JText::_('rstickets_desc'); ?></h1>

			<p>&nbsp;</p>

			<?php if (!$execute): ?>
			<table width="100%">
				<tr>
					<td width="15%" class="actionlabel"><?php echo JText::_('tickets'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['tickets']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('ticket_replies'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['messages']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('attachments'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['files']; ?></td>
				</tr>
				<tr>
					<td width="15%" class="actionlabel"><?php echo JText::_('workgroups'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['departments']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('cfields'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['cfields']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('support_staff'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['staff']; ?></td>
				</tr>
				<?php if ($ispro): ?>
				<tr>
					<td width="15%" class="actionlabel"><?php echo JText::_('kb_categories'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['kb_categories']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('kb_articles'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['kb_articles']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('priority'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['priorities']; ?></td>
				</tr>
				<tr>
					<td width="15%" class="actionlabel"><?php echo JText::_('status'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['statuses']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('notes'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['notes']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('logs'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['log']; ?></td>
				</tr>
				<?php endif;?>
			</table>
			<?php endif;?>
		</div>

		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="ispro" value="<?php echo $ispro;?>"/>
		<input type="hidden" name="task" value=""/>
	</form><?php
	}

	static function billets1($lists, $execute)
	{
		$supportConfig = HelpdeskUtility::GetConfig(); ?>

	<script language="javascript" type="text/javascript">
		Joomla.submitbutton = function (pressbutton) {
			var form = document.adminForm;
			if (pressbutton == '') {
                Joomla.submitform(pressbutton);
				return;
			}

			Joomla.submitform(pressbutton, document.getElementById('adminForm'));
		}
	</script>

	<form action="index.php" method="post" id="adminForm" name="adminForm">
		<?php echo JHtml::_('form.token'); ?>
		<div class="breadcrumbs">
			<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
			<a href="javascript:;"><?php echo JText::_('tools'); ?></a>
			<span><?php echo JText::_('import_billets'); ?></span>
		</div>
		<div class="contentarea">
			<h1 style="text-align:center;margin-top:0;padding-top:20px;"><?php echo $execute ? JText::_('billets_finish') : JText::_('billets_desc'); ?></h1>

			<p>&nbsp;</p>

			<?php if (!$execute): ?>
			<table width="100%">
				<tr>
					<td width="15%" class="actionlabel"><?php echo JText::_('tickets'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['tickets']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('ticket_replies'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['messages']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('notes'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['notes']; ?></td>
				</tr>
				<tr>
					<td width="15%" class="actionlabel"><?php echo JText::_('categories'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['categories']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('status'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['status']; ?></td>
					<td width="5%"></td>
					<td width="15%" class="actionlabel"><?php echo JText::_('attachments'); ?>:</td>
					<td width="15%" class="actionnumber"><?php echo $lists['files']; ?></td>
				</tr>
			</table>
			<?php endif;?>
		</div>

		<input type="hidden" name="option" value="com_maqmahelpdesk"/>
		<input type="hidden" name="task" value=""/>
	</form><?php
	}

	static function ambrasubs1($lists, $execute)
	{
		$supportConfig = HelpdeskUtility::GetConfig(); ?>

		<script language="javascript" type="text/javascript">
			Joomla.submitbutton = function (pressbutton) {
				var form = document.adminForm;
				if (pressbutton == '') {
                    Joomla.submitform(pressbutton);
					return;
				}

				Joomla.submitform(pressbutton, document.getElementById('adminForm'));
			}
		</script>

		<form action="index.php" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
			<?php echo JHtml::_('form.token'); ?>
			<div class="breadcrumbs">
				<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
				<a href="javascript:;"><?php echo JText::_('tools'); ?></a>
				<span><?php echo JText::_('import_ambrasubs'); ?></span>
			</div>
			<div class="contentarea">
				<h1 style="text-align:center;margin-top:0;padding-top:20px;"><?php echo $execute ? JText::_('ambrasubs_finish') : JText::_('ambrasubs_desc'); ?></h1>

				<p>&nbsp;</p>

				<?php if (!$execute): ?>
				<ul>
					<li><?php echo JText::_('tickets'); ?>: <b><?php echo $lists['tickets']; ?></b></li>
					<li><?php echo JText::_('ticket_replies'); ?>: <b><?php echo $lists['messages']; ?></b></li>
					<li><?php echo JText::_('notes'); ?>: <b><?php echo $lists['notes']; ?></b></li>
					<li><?php echo JText::_('categories'); ?>: <b><?php echo $lists['categories']; ?></b></li>
					<li><?php echo JText::_('status'); ?>: <b><?php echo $lists['status']; ?></b></li>
				</ul>
				<?php endif;?>
			</div>

			<input type="hidden" name="option" value="com_maqmahelpdesk"/>
			<input type="hidden" name="task" value=""/>
		</form><?php
	}

	static function deleteTickets($execute)
	{
		$supportConfig = HelpdeskUtility::GetConfig(); ?>

		<script language="javascript" type="text/javascript">
			Joomla.submitbutton = function (pressbutton) {
				var form = document.adminForm;
				if (pressbutton == '') {
                    Joomla.submitform(pressbutton);
					return;
				}

				Joomla.submitform(pressbutton, document.getElementById('adminForm'));
			}
		</script>

		<form action="index.php" method="post" id="adminForm" name="adminForm" enctype="multipart/form-data">
			<?php echo JHtml::_('form.token'); ?>
			<div class="breadcrumbs">
				<a href="index.php?option=com_maqmahelpdesk"><?php echo JText::_('control_panel'); ?></a>
				<a href="javascript:;"><?php echo JText::_('tools'); ?></a>
				<span><?php echo JText::_('DELETE_TICKETS'); ?></span>
			</div>
			<div class="contentarea">
				<h1 style="text-align:center;margin-top:0;padding-top:20px;"><?php echo $execute ? JText::_('DELETE_TICKETS_COMPLETE') : JText::_('DELETE_TICKETS_DESC'); ?></h1>
			</div>

			<input type="hidden" name="option" value="com_maqmahelpdesk"/>
			<input type="hidden" name="task" value=""/>
		</form><?php
	}
}
