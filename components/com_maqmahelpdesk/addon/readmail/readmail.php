<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 */

// Ensure this file is being included by a parent file
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

// Include helpers
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/email.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/imap.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/priority.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/status.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/ticket.php';
require_once JPATH_SITE . "/administrator/components/com_maqmahelpdesk/views/mail/readmail.php";

$CONFIG = new JConfig();
$database = JFactory::getDBO();
$GLOBALS['content'] = '';

$lang = JFactory::getLanguage();
$language_tag = $lang->getTag();
$lang->load('com_maqmahelpdesk', JPATH_SITE, $language_tag, true);
echo "<p>Language: $language_tag</p>";

if (php_sapi_name() != 'cli')
{
	$cmd = true;
}
else
{
	$cmd = false;
}

if (extension_loaded('imap'))
{
	$GLOBALS['content'] .= '<p style="color:#04B404;"><b>imap extension is loaded!</b></p>';

	if (extension_loaded('iconv'))
	{
		$GLOBALS['content'] .= '<p style="color:#04B404;"><b>iconv extension is loaded!</b></p>';
	}
	else
	{
		$GLOBALS['content'] .= '<p><span style="color:#f26522;">iconv extension is not loaded, body treatment maybe need to it for correct encoding</span></p>';
	}

	if (!get_cfg_var('safe_mode'))
	{
		$time_limit = ini_get('max_execution_time');
		set_time_limit(0);
	}

	// Get Configuration Options
	$supportConfig = HelpdeskUtility::GetConfig();

	$database->setQuery("SELECT publish FROM #__support_addon WHERE sname='readmail'");
	$published = $database->loadResult();

	if ($published)
	{
		$id = JRequest::getInt('id', 0);
		$database->setQuery("SELECT * FROM #__support_mail_fetch WHERE `published`=1 " . ($id ? "AND `id`=".$id : ""));
		$cat_res = $database->loadObjectList();

		if (count($cat_res) == 0)
		{
			$GLOBALS['content'] .= '<p style="#f00000"><b>No mail accounts defined!</b></p>';
		}
		else
		{
			for ($i = 0; $i < count($cat_res); $i++)
			{
				$cat_row = $cat_res[$i];

				$sql = "SELECT `add_mail_tag`
						FROM `#__support_workgroup` 
						WHERE `id`=" . $cat_row->id_workgroup;
				$database->setQuery($sql);
				$workgroupSettings = $database->loadObject();

				/*	WRITES INFO ON THE SCREEN */
				$GLOBALS['content'] .= '<h3 style="border:1px solid #d5d5d5;background:#e5e5e5;padding:5px;">E-Mail Fetch Account #' . $i . '</h3>';
				$GLOBALS['content'] .= '<table cellpadding="5">';
				$GLOBALS['content'] .= '<tr><td>E-Mail: </td><td>' . $cat_row->email . '</td></tr>';
				$GLOBALS['content'] .= '<tr><td>' . JText::_("MAIL_SERVER") . ': </td><td>' . $cat_row->server . '</td></tr>';
				$GLOBALS['content'] .= '<tr><td>Type: </td><td>' . $cat_row->type . '</td></tr>';
				$GLOBALS['content'] .= '<tr><td>Port: </td><td>' . $cat_row->port . '</td></tr>';
				$GLOBALS['content'] .= '<tr><td>ID Department: </td><td>' . $cat_row->id_workgroup . '</td></tr>';
				$GLOBALS['content'] .= '<tr><td>ID Category: </td><td>' . $cat_row->id_category . '</td></tr>';
				$GLOBALS['content'] .= '<tr><td>Delete email: </td><td>' . $cat_row->remove . '</td></tr>';
				$GLOBALS['content'] .= '<tr><td>Extra Info: </td><td>' . $cat_row->extra_info . '</td></tr>';
				$GLOBALS['content'] .= '<tr><td>Place in Queue: </td><td>' . $cat_row->queue . '</td></tr>';
				$GLOBALS['content'] .= '<tr><td>Status: </td><td>' . $cat_row->id_status . '</td></tr>';
				$GLOBALS['content'] .= '<tr><td>Folder: </td><td>' . $cat_row->label . '</td></tr>';
				$GLOBALS['content'] .= '<tr><td>Trash: </td><td>' . $cat_row->thrash . '</td></tr>';
				$GLOBALS['content'] .= '<tr><td>SSL: </td><td>' . $cat_row->ssl . '</td></tr>';
				$GLOBALS['content'] .= '<tr><td>TLS: </td><td>' . $cat_row->notls . '</td></tr>';
				$GLOBALS['content'] .= '<tr><td>Date to start: </td><td>' . date('m-d-Y', strtotime('- 1 day')) . '</td></tr>';
				$GLOBALS['content'] .= '<tr><td>Use email tag: </td><td>' . $workgroupSettings->add_mail_tag . ' - <b>' . JText::_('mail_tag_start') . ' - ' . JText::_('mail_tag') . '</b></td></tr>';
				$GLOBALS['content'] .= '</table>';

				/* CONFIGURE MAILBOX LOGIN */
				$Mail_ServerName_conf = $cat_row->server;
				$Mail_ServerAccessProtocol_conf = $cat_row->type;
				$Mail_ServerAccessPort_conf = $cat_row->port;
				$Mail_Mail_conf = $cat_row->email;
				$Mail_UserName_conf = $cat_row->username;
				$Mail_PassWord_conf = $cat_row->password;
				$Mail_Delete_conf = $cat_row->remove;
				$Mail_ExtraInfo_conf = $cat_row->extra_info;

				/* OPENING MAILBOX */
				//$obj = new HelpdeskImap("{imap.gmail.com:993/imap/ssl}INBOX", 'EMAIL', 'PASSWORD');
				$obj = new HelpdeskImap("{" . $Mail_ServerName_conf . ":" . $Mail_ServerAccessPort_conf . "/" . $Mail_ServerAccessProtocol_conf . ($cat_row->ssl ? '/ssl' : '') . ($cat_row->notls ? '' : '/notls') . $Mail_ExtraInfo_conf . "}" . $cat_row->label, $Mail_UserName_conf, $Mail_PassWord_conf);
				$obj->delete = $Mail_Delete_conf;
				$obj->thrash = $cat_row->thrash;
				$obj->parseMessagesByRecDate(date("j F Y", strtotime('- 1 day')));
				$result = $obj->getResult();
				$m = 0;
				$GLOBALS['content'] .= '<h2 style="border:1px solid #d5d5d5;background:#e5e5e5;padding:5px;">MESSAGES IN THE SERVER: ' . count($result) . '</h2>';

				// Retrieving the message information from $result
				foreach ($result as $msg)
				{
					$m++;
					$header = $msg['headers'];
					$body = $msg['body'];
					$attachments = $msg['attachments'];
					$subject = imap_utf8($header->subject);
					$to = $header->toaddress;
					$from = $header->from[0]->mailbox . '@' . $header->from[0]->host;
					$from_name = isset($header->from[0]->personal) ? $header->from[0]->personal : $header->from[0]->mailbox;
					$from_name = imap_utf8($from_name);
					$message_id = ($header->message_id != '' ? $header->message_id : md5($header->MailDate . $header->subject));

					// Email tag
					if ($workgroupSettings->add_mail_tag == 2)
					{
						$pos_start = strpos($body, JText::_('mail_tag_start'));
						$pos_end = strpos($body, JText::_('mail_tag'));

						if ($pos_start !== false && $pos_end !== false)
						{
							$body = JString::substr($body, ($pos_start + strlen(JText::_('mail_tag_start'))), ($pos_end - $pos_start - strlen(JText::_('mail_tag_start'))));
						}
					}
					elseif ($workgroupSettings->add_mail_tag == 1)
					{
						$pos = strpos($body, JText::_('mail_tag'));
						if ($pos !== false)
						{
							$body = JString::substr($body, 0, $pos);
						}
					}

					// HTML Chars treatment
					$body = strip_tags($body, '<br><p><u><i><b><pre><code><ul><li><ol>');
					$body = htmlentities($body, ENT_NOQUOTES, 'UTF-8');
					$body = str_replace('&lt;br /&gt;', '<br />', $body);
					$body = str_replace('&lt;p&gt;', '<p>', $body);
					$body = str_replace('&lt;p &gt;', '<p>', $body);
					$body = str_replace('&lt;/p&gt;', '</p>', $body);
					$body = str_replace('&lt;u&gt;', '<u>', $body);
					$body = str_replace('&lt;/u&gt;', '</u>', $body);
					$body = str_replace('&lt;i&gt;', '<i>', $body);
					$body = str_replace('&lt;/i&gt;', '</i>', $body);
					$body = str_replace('&lt;b&gt;', '<b>', $body);
					$body = str_replace('&lt;/b&gt;', '</b>', $body);
					$body = str_replace('&lt;pre&gt;', '<pre>', $body);
					$body = str_replace('&lt;/pre&gt;', '</pre>', $body);
					$body = str_replace('&lt;code&gt;', '<code>', $body);
					$body = str_replace('&lt;/code&gt;', '</code>', $body);
					$body = str_replace('&lt;ul&gt;', '<ul>', $body);
					$body = str_replace('&lt;/ul&gt;', '</ul>', $body);
					$body = str_replace('&lt;ol&gt;', '<ol>', $body);
					$body = str_replace('&lt;/ol&gt;', '</ol>', $body);
					$body = str_replace('&lt;li&gt;', '<li>', $body);
					$body = str_replace('&lt;/li&gt;', '</li>', $body);
					$body = str_replace('<p >', '<p>', $body);
					$body = str_replace('&nbsp;', " ", $body);
					$body = str_replace('&amp;', '&', $body);
					$body = str_replace('</p><br />', '</p>', $body);
					$body = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $body);
					$body = preg_replace('/(<[^>]+) class=".*?"/i', '$1', $body);

					$GLOBALS['content'] .= '<h3 style="border:1px solid #d5d5d5;background:#e5e5e5;padding:5px;">MESSAGE #' . $m . '</h3>';
					$GLOBALS['content'] .= '<table cellpadding="5">';
					$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">message id</td><td>' . $message_id . '</td></tr>';
					$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">message from</td><td>' . $from . '</td></tr>';
					$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">message from name</td><td>' . $from_name . '</td></tr>';
					$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">subject</td><td>' . $subject . '</td></tr>';
					$GLOBALS['content'] .= '</table>';

					// Verifies if StopForumSpam is to be used
					$spamcheck = true;
					if ($supportConfig->stopspam)
					{
						$spamcheck = HelpdeskValidation::CheckStopSpam($from);
					}
					$GLOBALS['content'] .= '<table cellpadding="5">';
					$GLOBALS['content'] .= '<tr><td bgcolor="#f5f5f5" width="150">Spamcheck</td><td>' . $spamcheck . '</td></tr>';
					$GLOBALS['content'] .= '</table>';

					// Verifies for a match in ignore list
					$ignore = 0;
					$sql = "SELECT `id`
							FROM `#__support_mail_fetch_ignore`
							WHERE `field`='subject' AND `value`=" . $database->quote($subject);
					$database->setQuery($sql);
					$ignore = $database->loadResult();
					if ($ignore)
					{
						$GLOBALS['content'] .= "<p><span style='color:#ff0000;'>" . sprintf(JText::_('mail_ignore_log'), $ignore) . "</span></p>";
						mailLog($cat_row->id, $from, sprintf(JText::_('mail_ignore_log'), $ignore), $message_id);
					}

					// If passed spam check and wasn't processed yet continues
					if ($spamcheck && !$ignore && !HelpdeskEmail::CheckEmailID($message_id))
					{
						// CREATES A NEW TICKET OR CREATE A NEW TICKET REPLY
						$edit_ticket = 0;
						if ($supportConfig->tickets_numbers)
						{
							$edit_ticket = preg_match("/[[][#][0-9]{7,20}[]]/", $subject);
						}
						else
						{
							$edit_ticket = preg_match("/[[][#][0-9]{1,20}[]]/", $subject);
						}
						if ($edit_ticket)
						{
							$TicketID2 = strstr($subject, "[#");
							$TicketID2 = JString::substr($TicketID2, 2, strlen($TicketID2) - (strlen($TicketID2) - strpos($TicketID2, "]")) - 2);
							if ($supportConfig->tickets_numbers)
							{
								$TicketID2 = JString::substr($TicketID2, 2, -4);
							}
							$ticket_id = Mail2Ticket($TicketID2, $cat_row->id_workgroup, $cat_row->id_category, ($subject), ($body), ($from_name), $from, $cat_row->queue, $cat_row->id_status);
						}
						else
						{
							$ticket_id = Mail2Ticket(0, $cat_row->id_workgroup, $cat_row->id_category, ($subject), ($body), ($from_name), $from, $cat_row->queue, $cat_row->id_status);
						}

						if ($attachments && $ticket_id)
						{
							foreach ($attachments as $fileName => $data)
							{
								$fileName = $ticket_id . '_' . rand(0, 999) . '_' . $fileName;
								$fh = fopen($supportConfig->docspath . $fileName, 'wb');
								fwrite($fh, $data);
								fclose($fh);
								$database->setQuery("INSERT INTO #__support_file(id, source, filename, public, `date`)
													 VALUES('" . $ticket_id . "', 'T', '" . $fileName . "', '1', '" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%M:%S") . "')");
								$database->query();
								$logmsg = str_replace('%1', $from_name, JText::_('attached_file'));
								$logmsg = str_replace('%2', $fileName, $logmsg);
								ticketLog2($ticket_id, $logmsg, JText::_('ATTACHED_FILE_HIDDEN'), HelpdeskUser::GetID($from), 0, 'add_attach.png');
							}
						}

						if ($ticket_id)
						{
							if (isset($TicketID2))
							{
								$GLOBALS['content'] .= "<p><span style='color:green;'>" . sprintf(JText::_('fetch_replied'), $ticket_id) . "</span></p>";
								mailLog($cat_row->id, $from, sprintf(JText::_('fetch_replied'), $ticket_id), $message_id);
							}
							else
							{
								$GLOBALS['content'] .= "<p><span style='color:green;'>" . sprintf(JText::_('fetch_ticket'), $ticket_id) . "</span></p>";
								mailLog($cat_row->id, $from, sprintf(JText::_('fetch_ticket'), $ticket_id), $message_id);
							}
						}
						else
						{
							$GLOBALS['content'] .= "<p><span style='color:#ff0000;'>" . JText::_('fetch_no_import') . "</span></p>";
							mailLog($cat_row->id, $from, JText::_('fetch_no_import'), $message_id);
						}
					}
					else
					{
						if (!$spamcheck)
						{
							$GLOBALS['content'] .= "<p><span style='color:#ff0000;'>" . JText::_('fetch_spam') . "</span></p>";
							mailLog($cat_row->id, $from, JText::_('fetch_spam'), $message_id);
						}
						else
						{
							$GLOBALS['content'] .= "<p><span style='color:#ff0000;'>" . JText::_('fetch_processed') . "</span></p>";
						}
					}
				}
			}
		}
	}

	/*if (!get_cfg_var('safe_mode')) {
		set_time_limit($time_limit);
	}*/

}
else
{
	$GLOBALS['content'] .= '<p><span style="color:#f00000;">imap extension is not loaded</span></p>';
}

if ($cmd)
{
	echo $GLOBALS['content'];
}