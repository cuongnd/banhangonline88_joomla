<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 * $Id: config.php 646 2012-05-22 08:20:58Z pdaniel $
 * $LastChangedDate: 2012-05-22 09:20:58 +0100 (Ter, 22 Mai 2012) $
 *
 */

/** ensure this file is being included by a parent file */
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

switch ($task) {
	default:
	case 'config':
		smsShowConfig();
		break;

	case 'saveconfig':
		smsSaveConfig();
		break;
}

function smsShowConfig()
{
	$database = JFactory::getDBO();

	$database->setQuery("SELECT * FROM #__support_sms_config WHERE id='1'");
	$row = $database->loadObject();

	$sourcelist[] = JHTML::_('select.option', 'ipipi', 'iPipi');
	$lists['gateway'] = JHTML::_('select.radiolist', $sourcelist, 'gateway', 'class="inputbox"', 'value', 'text', $row->gateway);

	$lists['assigned'] = JHTML::_('select.booleanlist', 'assigned', 'class="inputbox" size="2"', $row->assigned, JText::_('MQ_YES'), JText::_('MQ_NO'));
	$lists['creation'] = JHTML::_('select.booleanlist', 'creation', 'class="inputbox" size="2"', $row->creation, JText::_('MQ_YES'), JText::_('MQ_NO'));
	$lists['customer_activ'] = JHTML::_('select.booleanlist', 'customer_activ', 'class="inputbox" size="2"', $row->customer_activ, JText::_('MQ_YES'), JText::_('MQ_NO'));
	$lists['support_activ'] = JHTML::_('select.booleanlist', 'support_activ', 'class="inputbox" size="2"', $row->support_activ, JText::_('MQ_YES'), JText::_('MQ_NO'));  ?>

<table class="adminheading">
	<tr>
		<th class="config"><h3>SMS Add-on Configuration</h3></th>
		<td>
			<table cellpadding="0" cellspacing="0" border="0" id="toolbar">
				<tr valign="middle" align="center">
					<td>&nbsp;</td>
					<td>
						<a class="toolbar" href="javascript:submitbutton('addon-sms_saveconfig');">
							<img src="images/save_f2.png" alt="Save" align="middle" name="config_save" border="0"/>
							<br/>Save</a>
					</td>
					<td>&nbsp;</td>
					<td>
						<a class="toolbar" href="javascript:submitbutton('');">
							<img src="images/cancel_f2.png" alt="Cancel" align="middle" border="0"/>
							<br/>Cancel</a>
					</td>
					<td>&nbsp;</td>
				</tr>
			</table>
		</td>
	</tr>
</table>

<br/>

<form name="adminForm" method="POST" action="index.php">
	<table class="adminform" width="100%" style="margin-bottom:2px;">
		<tr>
			<td>
				<table width="100%" border="0" cellspacing="0" cellpadding="5">
					<tr>
						<td colspan="3"><b>Notification Configuration</b></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	<div id="gen_conf">
		<div style="width:100%;">
			<table class="adminform">
				<tr>
					<td width="100"><?php echo $lists['creation']; ?></td>
					<td>
						<span class="editlinktip hasTip"
							  title="Set if you want to send a SMS message to customer when a ticket is created by him or by a support member by him.<br /><br /><b>Default: Yes</b>"><img
							src="media/com_maqmahelpdesk/images/16px/info.png" align="absmiddle" border="0" hspace="5"
							style="cursor:help; cursor:hand;"/></span>
						Send to customer when ticket is created?
					</td>
				</tr>
				<tr>
					<td width="100"><?php echo $lists['assigned']; ?></td>
					<td>
						<span class="editlinktip hasTip"
							  title="Set if you want to send a SMS message to support member when a ticket is assigned to him.<br /><br /><b>Default: Yes</b>"><img
							src="media/com_maqmahelpdesk/images/16px/info.png" align="absmiddle" border="0" hspace="5"
							style="cursor:help; cursor:hand;"/></span>
						Send to support member when ticket is assigned?
					</td>
				</tr>
				<tr>
					<td width="100"><?php echo $lists['customer_activ']; ?></td>
					<td>
						<span class="editlinktip hasTip"
							  title="Set if you want to send a SMS message to customer when a support member adds a new activity message.<br /><br /><b>Default: Yes</b>"><img
							src="media/com_maqmahelpdesk/images/16px/info.png" align="absmiddle" border="0" hspace="5"
							style="cursor:help; cursor:hand;"/></span>
						Send to customer when support member adds a new activity?
					</td>
				</tr>
				<tr>
					<td width="100"><?php echo $lists['support_activ']; ?></td>
					<td>
						<span class="editlinktip hasTip"
							  title="Set if you want to send a SMS message to support member when customer adds a new activity message.<br /><br /><b>Default: Yes</b>"><img
							src="media/com_maqmahelpdesk/images/16px/info.png" align="absmiddle" border="0" hspace="5"
							style="cursor:help; cursor:hand;"/></span>
						Send to support member when support member adds a new activity?
					</td>
				</tr>
			</table>
			<br/><br/>
		</div>
	</div>

	<table class="adminform" width="100%" style="margin-bottom:2px;">
		<tr>
			<td>
				<table width="100%" border="0" cellspacing="0" cellpadding="5">
					<tr>
						<td colspan="3"><b>Gateway Configuration</b></td>
					</tr>
				</table>
			</td>
		</tr>
	</table>

	<div id="gen_gateway">
		<div style="width:100%;">
			<table class="adminform">
				<tr>
					<td width="100">Gateway:</td>
					<td><?php echo $lists['gateway']; ?></td>
				</tr>
				<tr>
					<td width="100">Username:</td>
					<td><input type="text" class="inputbox" name="username" value="<?php echo $row->username;?>"
							   size="50" maxlength="100"></td>
				</tr>
				<tr>
					<td width="100">Password:</td>
					<td><input type="password" class="inputbox" name="password" value="<?php echo $row->password;?>"
							   size="50" maxlength="100"></td>
				</tr>
				<tr>
					<td width="100">Host:</td>
					<td><input type="text" class="inputbox" name="host" value="<?php echo $row->host;?>" size="50"
							   maxlength="100"></td>
				</tr>
				<tr>
					<td width="100">Port:</td>
					<td><input type="text" class="inputbox" name="port" value="<?php echo $row->port;?>" size="3"
							   maxlength="5"></td>
				</tr>
				<tr>
					<td width="100">From:</td>
					<td><input type="text" class="inputbox" name="from" value="<?php echo $row->from;?>" size="50"
							   maxlength="100"></td>
				</tr>
				<tr>
					<td width="100">From Name:</td>
					<td><input type="text" class="inputbox" name="fromname" value="<?php echo $row->fromname;?>"
							   size="50" maxlength="100"></td>
				</tr>
			</table>
		</div>
	</div>

	<br/>

	<input type="hidden" name="clients" value="">
	<input type="hidden" name="staff" value="">
	<input type="hidden" name="option" value="com_maqmahelpdesk">
	<input type="hidden" name="task" value="addon-sms_saveconfig">
	<input type="hidden" name="addonfile" value="config">
</form><?php
}

function smsSaveConfig()
{
	$database = JFactory::getDBO();

	$database->setQuery("UPDATE #__support_sms_config SET `assigned`=" . $database->quote($_POST['assigned']) . ", `creation`=" . $database->quote($_POST['creation']) . ", `customer_activ`=" . $database->quote($_POST['customer_activ']) . ", `support_activ`=" . $database->quote($_POST['support_activ']) . ", `gateway`=" . $database->quote($_POST['gateway']) . ", `username`=" . $database->quote($_POST['username']) . ", `password`=" . $database->quote($_POST['password']) . ", `host`=" . $database->quote($_POST['host']) . ", `port`=" . $database->quote($_POST['port']) . ", `from`=" . $database->quote($_POST['from']) . ", `fromname`=" . $database->quote($_POST['fromname']) . " WHERE id='1'");
	$database->query();

	echo 'Settings updated!';

	smsShowConfig();
}

?>