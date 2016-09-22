<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package	MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license	GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class HelpdeskUtility
{
	public static function GetItemid()
	{
		$database = JFactory::getDBO();

		$sql = "SELECT `id`
				FROM `#__menu`
				WHERE `menutype`<>'main' AND `link`='index.php?option=com_maqmahelpdesk&view=mainpage'";
		$database->setQuery($sql);
		return (int)$database->loadResult();
	}

	public static function AddLines($content)
	{
		$html = '';
		$var_pre = explode('<pre>', $content);
		for ($i = 0; $i < count($var_pre); $i++) {
			if (stripos($var_pre[$i], '</pre>') === false) {
				$html .= nl2br($var_pre[$i]);
			} else {
				$close_pre = explode('</pre>', $var_pre[$i]);
				$html .= '<pre>' . $close_pre[0] . '</pre>' . nl2br($close_pre[1]);
			}
		}
		return $html;
	}

	/**
	 * Returns if joomla installation is 3.0.0+
	 *
	 * @return int
	 */
	public static function JoomlaCheck()
	{
		$jversion = new JVersion;
		$current_version = $jversion->getShortVersion();
		return (int) (version_compare($current_version, '3.0.0') >= 0);
	}

	public static function ActivityLog($client = '', $section = '', $action = '', $id = 0)
	{
		$user = JFactory::getUser();
		$database = JFactory::getDBO();
		$uri = JURI::getInstance();
		$supportConfig = self::GetConfig();
		$is_support = HelpdeskUser::IsSupport();
		$is_client = HelpdeskUser::IsClient();
		$link = $uri->toString(array('scheme', 'host', 'port', 'path', 'query', 'fragment'));

		if (!$supportConfig->system_log)
		{
			return;
		}

		$sql = "INSERT INTO `#__support_activities`(`id_user`, `ip_address`, `client`, `section`, `action`, `id_table`, `date_created`, `link`, `is_support`, `id_client`)
				VALUES(" . $user->id . ", '" . HelpdeskUser::GetIP() . "', '$client', '$section', '$action', '$id', '" . HelpdeskDate::DateOffset("%Y-%m-%d %H:%I:%S") . "', '$link', '$is_support', '$is_client')";
		$database->setQuery($sql);
		$database->query();
	}

	public static function AppendResource($file, $path, $type, $output = false)
	{
		global $resources_header;
		
		if ($output) {
			if ($type == 'css') {
				echo '<link rel="stylesheet" type="text/css" href="' . $path . $file . '" />' . "\n";
			} else {
				echo '<script type="text/javascript" src="' . $path . $file . '"></script>' . "\n";
			}
		} else {
			$resources_header[] = array('type' => $type, 'file' => $file, 'path' => $path);
		}
	}

	public static function OutputResources()
	{
		global $resources_header;
		
		for ($i = 0; $i < count($resources_header); $i++) {
			$resource = $resources_header[$i];

			if ($resource['type'] == 'css') {
				echo '<link rel="stylesheet" type="text/css" href="' . $resource['path'] . $resource['file'] . '" />' . "\n";
			} else {
				echo '<script type="text/javascript" src="' . $resource['path'] . $resource['file'] . '"></script>' . "\n";
			}
		}
		$resources_header = null;
	}

	public static function GetConfig()
	{
		$database = JFactory::getDBO();
		$database->setQuery("SELECT * FROM #__support_config WHERE id='1'");
		$supportConfig = $database->loadObject();
		return $supportConfig;
	}

	public static function CreateSlug($title, $type = null)
	{
		$alias = JFilterOutput::stringURLSafe($title);

		// Verify if already exists
		/*switch($type)
		  {
			  case '':
				  $sql = "SELECT `slug` FROM `#__` WHERE `slug`=".$database->quote($alias);
				  break;
			  case '':
				  $sql = "SELECT `slug` FROM `#__` WHERE `slug`=".$database->quote($alias);
				  break;
			  case '':
				  $sql = "SELECT `slug` FROM `#__` WHERE `slug`=".$database->quote($alias);
				  break;
			  case '':
				  $sql = "SELECT `slug` FROM `#__` WHERE `slug`=".$database->quote($alias);
				  break;
			  case '':
				  $sql = "SELECT `slug` FROM `#__` WHERE `slug`=".$database->quote($alias);
				  break;
		  }
		  $database->setQuery( $sql );
		  $database->loadResult();*/

		return $alias;
	}

	public static function ShowTplMessage($msg, $id_workgroup = 0)
	{
		$database = JFactory::getDBO();

		$msg = str_replace("%20", " ", $msg);
		$msg = str_replace("\'", "'", $msg);
		$htmlcode = HelpdeskTemplate::Get($msg, $id_workgroup, 'general/information_message');

		echo $htmlcode;
	}

	public static function ShowSCMessage($msg, $msgtype = 'i', $title = null)
	{
		switch ($msgtype) {
			case 'i' : // Information based messages
				$type = "info";
				break;
			case 'w': // Warning based messages
				$type = "block";
				break;
			case 'e': // Error based messages
				$type = "error";
				break;
			case 's': // Success based messages
				$type = "success";
				break;
		} ?>
		<div class="maqmahelpdesk">
			<div class="alert alert-<?php echo $type;?>">
				<a class="close" data-dismiss="alert" href="javascript:;">&times;</a>
				<?php if ($title != ''): ?>
				<h4 class="alert-heading"><?php echo $title;?></h4>
				<?php endif;?>
				<p><?php echo $msg;?></p>
			</div>
		</div><?php
	}

	public static function AddGlobalMessage($message, $msgtype = 'i', $debugmsg = '', $userid = '', $username = '')
	{
		global $sysmsgs_user_location;

		$database = JFactory::getDBO();
		$user = JFactory::getUser();

		if (strlen($message) > 0)
		{
			!$userid ? $userid = $user->id : '';
			!$username ? $username = $user->username : '';
			$ipaddress = HelpdeskUser::GetIP();
			$occurred = HelpdeskDate::timestampOffset();

			$sql = "INSERT INTO #__support_sysmsgs(userid, location, username, ipaddress, msgtype, message, occurred, debugmsg)
					VALUES('$userid', '$sysmsgs_user_location', '$username', '$ipaddress', '$msgtype', " . $database->quote($message) . ", '$occurred', " . $database->quote($debugmsg) . ")";
			$database->setQuery($sql);
			!$database->query() ? self::ShowSCMessage($database->getErrorMsg(), 'e') : '';
			return $database->insertid();
		}
	}

	public static function GetGlobalMessage($userid = '', $username = '')
	{
		global $sysmsgs_user_location;

		$database = JFactory::getDBO();
		$user = JFactory::getUser();
		$supportConfig = self::GetConfig();
		!$userid ? $userid = $user->id : ''; // Get clients user id
		!$username ? $username = $user->username : ''; // Get clients username
		$ipaddress = HelpdeskUser::GetIP(); // Get clients IP address
		$display_limit = 1; // Set display number limit (starts at 0)
		$date_format = ($supportConfig->date_short != '' ? $supportConfig->date_short : 'd/m/Y H:i:s');

		// Get available mssages
		$sql = "SELECT id, msgtype, message, occurred
				FROM #__support_sysmsgs
				WHERE userid='" . $userid . "'
				  AND username='" . $username . "'
				  AND ipaddress='" . $ipaddress . "'
				  AND location='" . $sysmsgs_user_location . "'
				  AND (displayed < '" . $display_limit . "')";
		$database->setQuery($sql);
		$globalmsgs = $database->loadObjectList();

		for ($i = 0; $i < count($globalmsgs); $i++)
		{
			$globalmsg = $globalmsgs[$i];
			$globalmsg->message = '<small>' . HelpdeskDate::DateOffset($date_format, $globalmsg->occurred) . '</small><br />' . $globalmsg->message;

			// Display available messages
			self::ShowSCMessage($globalmsg->message, $globalmsg->msgtype);

			// Clean out displayed messages
			self::CleanGlobalMessage($globalmsg->id);
		}
	}

	public static function CleanGlobalMessage($id)
	{
		$database = JFactory::getDBO();

		// Keep 3 months worth of error messages
		$err_old = HelpdeskDate::timestampOffset() - 7776000;

		// Keep 1 month worth of warning messages
		$warn_old = HelpdeskDate::timestampOffset() - 2592000;

		// Keep 1 week worth if info messages
		$info_old = HelpdeskDate::timestampOffset() - 604800;

		// Updates requested global message as displayed or updated all old undisplayed messages

		$database->setQuery("UPDATE #__support_sysmsgs SET displayed=displayed+1, cleaned='1' WHERE id='$id'");
		!$database->query() ? self::ShowSCMessage($database->getErrorMsg(), 'e') : '';

		// Clean out old messages
		$database->setQuery("DELETE FROM #__support_sysmsgs WHERE (msgtype='e' AND occurred<$err_old) OR (msgtype='w' AND occurred<$warn_old) OR (msgtype='i' AND occurred<$info_old)");
		!$database->query() ? self::ShowSCMessage($database->getErrorMsg(), 'e') : '';
	}

	public static function TextHyperlinks($text)
	{
		//$text = preg_replace('/\\b(https?|ftp):\/\/[-A-Z0-9+&@#\/%?=~_|!:,.;]*[-A-Z0-9+&@#\/%=~_|]/i', '<a target="_blank" href="\\0">\\0</a>', $text);
		$text = preg_replace('/\\b(?:mailto:)?([A-Z0-9._%-]+@[A-Z0-9.-]+\\.[A-Z]{2,4})\\b/i', '<a href="mailto:\\1">\\0</a>', $text);
		return $text;
	}

	public static function String2HTML($string)
	{
		htmlentities($string, ENT_QUOTES);
		return $string;
	}

	public static function PageTitle($currlocation, $title_extension = '', $extra_details = '')
	{
		global $supportOptions, $clientOptions, $id_workgroup, $client, $print, $show_toolbar;

		$mainframe = JFactory::getApplication();
		$database = JFactory::getDBO();
		$supportConfig = self::GetConfig();
		$workgroupSettings = HelpdeskDepartment::GetSettings();
		$is_support = HelpdeskUser::IsSupport();
		$is_client = HelpdeskUser::IsClient();
		$Itemid = JRequest::getInt('Itemid', 0);
		$id_category = JRequest::getVar('id_category', 0, '', 'int');

		// Variables
		$title = '';
		$title_separator = " / ";
		$pathway = $mainframe->getPathway();

		// Generate highest level first (true for workgroup)
		$link = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid;

		// If inside a workgroup, add longer pathway links
		switch ($currlocation) {
			case "showWorkgroups":
				$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup));
				break;
			case "showWorkgroup":
				$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup));
				break;
			case "showTimesheet":
				$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				$pathway->addItem(JText::_('timesheet'));
				break;
			case "showClients":
				$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				$pathway->addItem(JText::_('pathway_clients'));
				break;
			case "viewClients":
				if (!$print && $is_support) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
					$pathway->addItem(JText::_('pathway_clients'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=client_list'));
					$pathway->addItem($client->clientname);
				}
				break;
			case "showAnnounces":
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				}
				$pathway->addItem(JText::_('pathway_announcements'));
				break;
			case "viewAnnouncements":
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
					$pathway->addItem(JText::_('pathway_announcements'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=announce_list'));
				}
				$pathway->addItem($title_extension);
				break;
			case "showKB":
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				}
				if ($title_extension == '') {
					$pathway->addItem(JText::_('pathway_kb'));
				} else {
					if ($show_toolbar) {
						$pathway->addItem(JText::_('pathway_kb'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=kb_list'));
					}
					$pathway->addItem($title_extension);
				}
				break;
			case "showFAQ":
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				}
				$pathway->addItem(JText::_('pathway_faq'));
				break;
			case "showMyKB":
				$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				$pathway->addItem(JText::_('pathway_kb'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=kb_list'));
				$pathway->addItem(JText::_('pathway_myarticles'));
				break;
			case "searchKB":
				$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				$pathway->addItem(JText::_('pathway_kb'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=kb_list'));
				$pathway->addItem(JText::_('pathway_search'));
				break;
			case "showTicketsManager":
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				}
				$pathway->addItem(JText::_('pathway_tickets_manager'));
				break;
			case 'showMyBookmarks':
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				}
				$pathway->addItem(JText::_('pathway_my_bookmarks'));
				break;
			case 'showMyDownloads':
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				}
				$pathway->addItem(JText::_('my_downloads'));
				break;
			case 'viewTicket':
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
					$pathway->addItem(JText::_('pathway_tickets'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=ticket_my'));
				}
				$pathway->addItem(JText::_('pathway_view'));
				break;
			case 'analysisTicket':
				$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				$pathway->addItem(JText::_('pathway_view'));
				break;
			case 'OpenTickets':
				$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				$pathway->addItem(JText::_('pathway_view'));
				break;
			case 'newTicket':
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
					$pathway->addItem(JText::_('pathway_tickets'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=ticket_my'));
				}
				$pathway->addItem(JText::_('pathway_new'));
				break;
			case 'userTickets':
				$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				$pathway->addItem(JText::_('pathway_tickets'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=ticket_my'));
				$pathway->addItem(JText::_('pathway_user_tickets'));
				break;
			case 'Troubleshooter':
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				}
				$pathway->addItem(JText::_('pathway_troubleshooter'));
				break;
			case 'Glossary':
				$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				if ($id_category) {
					$pathway->addItem(JText::_('pathway_glossary'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=glossary'));
					$pathway->addItem(HelpdeskCategory::GetName($id_category));
				} else {
					$pathway->addItem(JText::_('pathway_glossary'));
				}
				break;
			case 'viewKB':
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
					$pathway->addItem(JText::_('pathway_kb'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=kb_list'));
				}
				$pathway->addItem(JText::_('pathway_view'));
				break;
			case 'viewFAQ':
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
					$pathway->addItem(JText::_('pathway_faq'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=kb_faq'));
				}
				$pathway->addItem(JText::_('pathway_view'));
				break;
			case 'newKB':
				$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				$pathway->addItem(JText::_('pathway_kb'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=kb_list'));
				$pathway->addItem(JText::_('pathway_new'));
				break;
			case 'editKB':
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
					$pathway->addItem(JText::_('pathway_kb'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=kb_list'));
				}
				$pathway->addItem(JText::_('pathway_edit'));
				break;
			case 'newGlossary':
				$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				$pathway->addItem(JText::_('pathway_glossary'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=kb_glossary'));
				$pathway->addItem(JText::_('pathway_new'));
				break;
			case 'editGlossary':
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
					$pathway->addItem(JText::_('pathway_glossary'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=kb_glossary'));
				}
				$pathway->addItem(JText::_('pathway_edit'));
				break;
			case 'showCalendar':
				$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				$pathway->addItem(JText::_('pathway_tasks'));
				break;
			case 'editCalendar':
				$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				$pathway->addItem(JText::_('pathway_tasks'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=calendar_view'));
				$pathway->addItem(JText::_('pathway_edit'));
				break;
			case 'showDownloads':
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				}
				if ($title_extension == '') {
					$pathway->addItem(JText::_('pathway_downloads'));
				} else {
					if ($show_toolbar) {
						$pathway->addItem(JText::_('pathway_downloads'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=downloads'));
					}
					$pathway->addItem($title_extension);
				}
				break;
			case 'showDownload':
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
					$pathway->addItem(JText::_('pathway_downloads'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=downloads'));
					$pathway->addItem($extra_details[0], JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=downloads_category&id=' . $extra_details[1]));
				}
				$pathway->addItem($title_extension);
				break;
			case 'showSubscriptions':
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
					$pathway->addItem(JText::_('pathway_downloads'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=downloads'));
				}
				$pathway->addItem(JText::_('pathway_subscriptions'));
				break;
			case "showUserForm":
				$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				$pathway->addItem(JText::_('wk_profile'));
				break;
			case "showReportTickets":
				$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				$pathway->addItem(JText::_('report_tickets'));
				break;
			case "anonymousTicketManager":
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				}
				$pathway->addItem(JText::_('pathway_tickets_manager'));
				break;
			case "showDiscussions":
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				}
				if ($id_category) {
					if ($show_toolbar) {
						$pathway->addItem(JText::_('discussions'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=discussions'));
					}
					$pathway->addItem(HelpdeskCategory::GetName($id_category));
				} else {
					$pathway->addItem(JText::_('discussions'));
				}
				break;
			case "viewDiscussion":
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
					$pathway->addItem(JText::_('discussions'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=discussions'));
				}
				$pathway->addItem($title_extension);
				break;
			case "newDiscussion":
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
					$pathway->addItem(JText::_('discussions'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=discussions'));
				}
				$pathway->addItem(JText::_('pathway_new'));
				break;
			case "showBugtracker":
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
				}
				$pathway->addItem(JText::_('bugtracker'));
				break;
			case "viewBugtracker":
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
					$pathway->addItem(JText::_('bugtracker'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=bugtracker'));
				}
				$pathway->addItem($title_extension);
				break;
			case "newBugtracker":
				if ($show_toolbar) {
					$pathway->addItem(HelpdeskDepartment::GetName($id_workgroup), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=workgroup_view'));
					$pathway->addItem(JText::_('bugtracker'), JRoute::_($link . '&id_workgroup=' . $id_workgroup . '&task=bugtracker'));
				}
				$pathway->addItem(JText::_('pathway_new'));
				break;
		}
	}

	public static function GetJQueryURL()
	{
		$uri = JURI::getInstance();
		$supportConfig = self::GetConfig();
		$url = '';

		switch ($supportConfig->jquery_source)
		{
			case 'local':
				$url = JURI::root() . 'media/com_maqmahelpdesk/js/jquery-1.8.3.min.js';
				break;
			case 'jquery':
				$url = '//code.jquery.com/jquery-1.8.3.min.js';
				break;
			case 'google':
				$url = '//ajax.googleapis.com/ajax/libs/jquery/1.8.3/jquery.min.js';
				break;
			case 'microsoft':
				$url = '//ajax.aspnetcdn.com/ajax/jQuery/jquery-1.8.3.min.js';
				break;
		}

		return $url;
	}
}
