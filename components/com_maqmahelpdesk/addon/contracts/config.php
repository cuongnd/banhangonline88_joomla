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

// Required helpers
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/contracts.php';

// Set toolbar and page title
HelpdeskContractsAdminHelper::addToolbar($task, 'contracts');
HelpdeskContractsAdminHelper::setDocument($task);

switch ($task)
{
	default:
	case 'config':
		contractsShowConfig();
		break;

	case 'saveconfig':
		contractsSaveConfig();
		break;
}

function contractsShowConfig($message = '')
{
	$database = JFactory::getDBO();

	$contract = null;
	$database->setQuery("SELECT * FROM #__support_addon_contract");
	$contract = $database->loadObject(); ?>

	<br/><?php
	if ($message != '') {
		echo HelpdeskUtility::ShowSCMessage($message) . '<br />';
	} ?>

	<div class="contentarea">
		<div id="contentbox">
			<form name="adminForm" method="POST" action="index.php" class="label-inline">
			<div class="field w50">
				<span class="label editlinktip hasTip" title="<?php echo JText::_('percs');?>"><?php echo JText::_('percs');?></span>
				<input type="text" class="medium" name="percentage" value="<?php echo $contract->percentage; ?>" size="4" maxlength="2">
			</div>
			<div class="field w50">
				<span class="label editlinktip hasTip" title="<?php echo JText::_('notify'); ?>"><?php echo JText::_('notify'); ?></span>

				<div class="controlset-pad">
					<label for="notify0"><input type="radio" id="notify0" name="notify" class="switch" value="0" <?php echo !$contract->notify ? 'checked="checked"' : ''; ?>> <?php echo JText::_('MQ_NO'); ?></label> &nbsp;
					<label for="notify1"><input type="radio" id="notify1" name="notify" class="switch" value="1" <?php echo $contract->notify ? 'checked="checked"' : ''; ?> checked=""> <?php echo JText::_('MQ_YES'); ?></label> &nbsp;					</div>
			</div>
			<input type="hidden" name="option" value="com_maqmahelpdesk"/>
			<input type="hidden" name="task" value="addon-contracts_saveconfig"/>
			<input type="hidden" name="addonfile" value="config"/>
			</form>
		</div>
	</div>
	<br class="clr" /><?php
}

function contractsSaveConfig()
{
	$database = JFactory::getDBO();

	$database->setQuery("UPDATE #__support_addon_contract SET percentage=" . $database->quote($_POST['percentage']) . ", notify=" . $database->quote($_POST['notify']));
	$database->query();

	contractsShowConfig('<p>' . JText::_('configuration_saved') . '</p>');
}
