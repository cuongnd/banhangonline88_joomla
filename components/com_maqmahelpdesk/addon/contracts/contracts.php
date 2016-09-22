<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 * $Id: contracts.php 646 2012-05-22 08:20:58Z pdaniel $
 * $LastChangedDate: 2012-05-22 09:20:58 +0100 (Ter, 22 Mai 2012) $
 *
 */

// Ensure this file is being included by a parent file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

$CONFIG = new JConfig();
$database = JFactory::getDBO();

$SecretWord = JRequest::getCmd('SecretWord', '', '', 'string');

if ($CONFIG->secret != $SecretWord) {
    return false;
}

// Get config	
$supportConfig = HelpdeskUtility::GetConfig();
$database->setQuery("SELECT publish FROM #__support_addon WHERE sname='contracts'");
$published = $database->loadResult();

// Get the template from the file
$tmplfile = HelpdeskTemplate::GetFile('mail/contracts_list');
$fp = fopen($tmplfile, 'rb');
$tmpl_code = fread($fp, filesize($tmplfile));
fclose($fp);

if ($published) {
    $add_config = null;
    $sql = "SELECT percentage, notify FROM #__support_addon_contract LIMIT 0, 1";
    $database->setQuery($sql);
    $add_config = $database->loadObject();
    echo $database->getErrorMsg();
    $sql = "SELECT id_client, id_user, contract_number, date_start, date_end, actual_value, value, unit FROM #__support_contract WHERE status='A' AND notified='0' AND ((actual_value*100)/value)>=" . $add_config->percentage;
    $database->setQuery($sql);
    $rows = $database->loadObjectList();
    echo $database->getErrorMsg();
    $feed_summary = '';
    $email_body = '';

    if (count($rows) > 0) {
        for ($i = 0; $i < count($rows); $i++) {
            $row = &$rows[$i];
            $feed_summary .= '<tr>
			<td bgcolor="#FFFFFF" valign="top" align="left" class="bodytext">' . $row->contract_number . '</td>
			<td bgcolor="#FFFFFF" valign="top" align="left" class="bodytext">' . $row->unit . '</td>
			<td bgcolor="#FFFFFF" valign="top" align="left" class="bodytext">' . $row->value . '</td>
			<td bgcolor="#FFFFFF" valign="top" align="left" class="bodytext">' . $row->actual_value . '</td>
			<td bgcolor="#FFFFFF" valign="top" align="left" class="bodytext">' . JString::substr($row->date_start, 0, 10) . '</td>
			<td bgcolor="#FFFFFF" valign="top" align="left" class="bodytext">' . JString::substr($row->date_end, 0, 10) . '</td>
			</tr>';

            /** EMAIL variables */
            $email_subject = JText::_('contracts_addon_title');
            $email_body = str_replace('$feed_summary', $feed_summary, $tmpl_code);
            if ($row->id_user > 0) {
                // Get the maintainer of the contract
                $user_support = null;
                $sql = "SELECT name, email FROM #__users WHERE id='" . $row->id_user . "'";
                $database->setQuery($sql);
                $user_support = $database->loadObject();
                echo $database->getErrorMsg();
                // Send mail to maintainer
                $email_body = str_replace('%1', $user_support->name, $email_body);
                print "<p>" . JText::_('e_mail') . ": " . $user_support->email . " - " . $user_support->name;
                print "<br>" . JText::_('mail_to_support_maintain') . ": " . JUtility::sendMail($CONFIG->mailfrom, JText::_('contracts_mail_from') . " <" . $CONFIG->mailfrom . ">", $user_support->email, $email_subject, $email_body, 1);
            }

            // Get the client managers
            $sql = "SELECT u.name, u.email FROM #__users u INNER JOIN #__support_client_users c ON c.id_user=u.id WHERE c.id_client='" . $row->id_client . "'";
            $database->setQuery($sql);
            $client_managers = $database->loadObjectList();
            if (count($client_managers) > 0) {
                for ($i = 0; $i < count($client_managers); $i++) {
                    $client_manager = $client_managers[$i];
                    // Send mail to manager
                    $email_body = str_replace('%1', $client_manager->name, $email_body);
                    print "<p>" . JText::_('e_mail') . ": " . $client_manager->email . " - " . $client_manager->name;
                    print "<br>" . JText::_('mail_to_cli_manager') . ": " . JUtility::sendMail($CONFIG->mailfrom, JText::_('contracts_mail_from') . " <" . $CONFIG->mailfrom . ">", $client_manager->email, $email_subject, $email_body, 1);
                }
                // Re-start
                $feed_summary = '';
                $email_body = '';
            }
        }
    }
}
