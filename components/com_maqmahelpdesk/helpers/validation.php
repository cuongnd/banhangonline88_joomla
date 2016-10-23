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

class HelpdeskValidation
{
	static function SystemCheck($task)
	{
		$supportConfig = HelpdeskUtility::GetConfig();
		$database = JFactory::getDBO();
		$warning_message = '';

		if ($supportConfig->docspath == '')
		{
			$warning_message .= '<p><a href="index.php?option=com_maqmahelpdesk&task=config" class="btn btn-mini btn-danger btn-small">' . JText::_('warning_fix') . '</a> ' . JText::_('warning_attach_paths') . '</p>';
		}

		if ($supportConfig->public_attach && $supportConfig->extensions == '')
		{
			$warning_message .= '<p><a href="index.php?option=com_maqmahelpdesk&task=config" class="btn btn-mini btn-danger btn-small">' . JText::_('warning_fix') . '</a> ' . JText::_('warning_attach_types') . '</p>';
		}

		$sql = "SELECT COUNT(*)
				FROM `#__support_workgroup` 
				WHERE `show`=1";
		$database->setQuery($sql);
		if (!$database->loadResult())
		{
			$warning_message .= '<p><a href="index.php?option=com_maqmahelpdesk&task=workgroup" class="btn btn-mini btn-danger btn-small">' . JText::_('warning_fix') . '</a> ' . JText::_('warning_wks_enabled') . '</p>';
		}

		$sql = "SELECT COUNT(*)
				FROM `#__support_workgroup` 
				WHERE `show`=1 AND `id` NOT IN (SELECT `id_workgroup` FROM `#__support_permission`)";
		$database->setQuery($sql);
		if ($database->loadResult())
		{
			$warning_message .= '<p><a href="index.php?option=com_maqmahelpdesk&task=staff" class="btn btn-mini btn-danger btn-small">' . JText::_('warning_fix') . '</a> ' . JText::_('warning_wks_users') . '</p>';
		}

		$sql = "SELECT COUNT(*)
				FROM `#__support_status` 
				WHERE `show`=1";
		$database->setQuery($sql);
		if (!$database->loadResult())
		{
			$warning_message .= '<p><a href="index.php?option=com_maqmahelpdesk&task=status" class="btn btn-mini btn-danger btn-small">' . JText::_('warning_fix') . '</a> ' . JText::_('warning_status') . '</p>';
		}

		$sql = "SELECT COUNT(*)
				FROM `#__support_status` 
				WHERE `show`=1 AND `isdefault`=1";
		$database->setQuery($sql);
		if (!$database->loadResult())
		{
			$warning_message .= '<p><a href="index.php?option=com_maqmahelpdesk&task=status" class="btn btn-mini btn-danger btn-small">' . JText::_('warning_fix') . '</a> ' . JText::_('warning_status_default') . '</p>';
		}

		$sql = "SELECT COUNT(*)
				FROM `#__support_priority` 
				WHERE `show`=1";
		$database->setQuery($sql);
		if (!$database->loadResult())
		{
			$warning_message .= '<p><a href="index.php?option=com_maqmahelpdesk&task=priority" class="btn btn-mini btn-danger btn-small">' . JText::_('warning_fix') . '</a> ' . JText::_('warning_priority') . '</p>';
		}

		$sql = "SELECT COUNT(*)
				FROM `#__support_priority` 
				WHERE `show`=1 AND `isdefault`=1";
		$database->setQuery($sql);
		if (!$database->loadResult())
		{
			$warning_message .= '<p><a href="index.php?option=com_maqmahelpdesk&task=priority" class="btn btn-mini btn-danger btn-small">' . JText::_('warning_fix') . '</a> ' . JText::_('warning_priority_default') . '</p>';
		}

		$sql = "SELECT COUNT(*)
				FROM `#__support_workgroup` 
				WHERE `show`=1 AND `contract`=1";
		$database->setQuery($sql);
		if ($database->loadResult())
		{
			$sql = "SELECT COUNT(*)
					FROM `#__support_contract_template`";
			$database->setQuery($sql);
			if (!$database->loadResult())
			{
				$warning_message .= '<p><a href="index.php?option=com_maqmahelpdesk&task=contracts" class="btn btn-mini btn-danger btn-small">' . JText::_('warning_fix') . '</a> ' . JText::_('warning_wks_contract') . '</p>';
			}
		}

		$sql = "SELECT COUNT(*)
				FROM `#__support_workgroup` 
				WHERE `show`=1 AND `use_activity`=1";
		$database->setQuery($sql);
		if ($database->loadResult())
		{
			$sql = "SELECT COUNT(*)
					FROM `#__support_activity_rate` 
					WHERE `published`=1";
			$database->setQuery($sql);
			if (!$database->loadResult())
			{
				$warning_message .= '<p><a href="index.php?option=com_maqmahelpdesk&task=rates" class="btn btn-mini btn-danger btn-small">' . JText::_('warning_fix') . '</a> ' . JText::_('warning_wks_rates') . '</p>';
			}

			$sql = "SELECT COUNT(*)
					FROM `#__support_activity_type` 
					WHERE `published`=1";
			$database->setQuery($sql);
			if (!$database->loadResult())
			{
				$warning_message .= '<p><a href="index.php?option=com_maqmahelpdesk&task=types" class="btn btn-mini btn-danger btn-small">' . JText::_('warning_fix') . '</a> ' . JText::_('warning_wks_types') . '</p>';
			}
		}

		$sql = "SELECT COUNT(*)
				FROM `#__support_workgroup` 
				WHERE `show`=1 AND `enable_discussions`=1 AND `wkmail_address`=''";
		$database->setQuery($sql);
		if ($database->loadResult())
		{
			$warning_message .= '<p><a href="index.php?option=com_maqmahelpdesk&task=workgroup" class="btn btn-mini btn-danger btn-small">' . JText::_('warning_fix') . '</a> ' . JText::_('warning_wks_discussions') . '</p>';
		}

		$sql = "SELECT COUNT(*)
				FROM `#__support_workgroup` 
				WHERE `show`=1 AND `enable_discussions`=1 AND `id` NOT IN (SELECT `id_workgroup` FROM `#__support_category` WHERE `discussions`=1 AND `show`=1)";
		$database->setQuery($sql);
		if ($database->loadResult())
		{
			$warning_message .= '<p><a href="index.php?option=com_maqmahelpdesk&task=category" class="btn btn-mini btn-danger btn-small">' . JText::_('warning_fix') . '</a> ' . JText::_('discussions_no_categories') . '</p>';
		}

		$sql = "SELECT COUNT(*)
				FROM `#__support_workgroup` 
				WHERE `show`=1 AND `wkglossary`=1 AND `id` NOT IN (SELECT `id_workgroup` FROM `#__support_category` WHERE `glossary`=1 AND `show`=1)";
		$database->setQuery($sql);
		if ($database->loadResult())
		{
			$warning_message .= '<p><a href="index.php?option=com_maqmahelpdesk&task=category" class="btn btn-mini btn-danger btn-small">' . JText::_('warning_fix') . '</a> ' . JText::_('glossary_no_categories') . '</p>';
		}

		$sql = "SELECT COUNT(*)
				FROM `#__support_workgroup` 
				WHERE `show`=1 AND `bugtracker`=1 AND `id` NOT IN (SELECT `id_workgroup` FROM `#__support_category` WHERE `bugtracker`=1 AND `show`=1)";
		$database->setQuery($sql);
		if ($database->loadResult())
		{
			$warning_message .= '<p><a href="index.php?option=com_maqmahelpdesk&task=category" class="btn btn-mini btn-danger btn-small">' . JText::_('warning_fix') . '</a> ' . JText::_('bugtracker_no_categories') . '</p>';
		}

		$sql = "SELECT COUNT(*)
				FROM `#__support_country`";
		$database->setQuery($sql);
		if (!$database->loadResult())
		{
			$warning_message .= '<p><a href="index.php?option=com_maqmahelpdesk&task=tools_countries" class="btn btn-mini btn-danger btn-small">' . JText::_('warning_fix') . '</a> ' . JText::_('WARNING_COUNTRIES') . '</p>';
		}

		if (!file_exists(JPATH_SITE . '/components/com_maqmahelpdesk/pdf/mpdf/ttfonts/DejaVuinfo.txt'))
		{
			$warning_message .= '<p><a href="index.php?option=com_maqmahelpdesk&task=tools_pdf" class="btn btn-mini btn-danger btn-small">' . JText::_('warning_fix') . '</a> ' . JText::_('WARNING_PDF') . '</p>';
		}

		if (!is_dir($supportConfig->docspath))
		{
			$warning_message .= '<p><a href="index.php?option=com_maqmahelpdesk&task=config" class="btn btn-mini btn-danger btn-small">' . JText::_('warning_fix') . '</a> ' . JText::_('attachs_dir_invalid') . '</p>';
		}

		if (is_dir($supportConfig->docspath) && !is_writable($supportConfig->docspath))
		{
			$warning_message .= '<p><a href="index.php?option=com_maqmahelpdesk&task=config" class="btn btn-mini btn-danger">' . JText::_('warning_fix') . '</a> ' . JText::_('attachs_dir_permissions') . '</p>';
		}

		if ($warning_message != '' && $task != 'download')
		{
			echo '<div class="alert"><h4 class="alert-heading">' . JText::_('missing_settings') . '</h4><br />' . $warning_message . '</div>';
		}
	}

	static function ProfileRequired()
	{
		$mainframe = JFactory::getApplication();
		$database = JFactory::getDBO();
		$user = JFactory::getUser();
		$supportConfig = HelpdeskUtility::GetConfig();
		$Itemid = JRequest::getInt('Itemid', 0);
		$id_workgroup = JRequest::getInt('id_workgroup', 0);

		// Verify if there are required fields to be filled
		if ($supportConfig->profile_required && $user->id && ($supportConfig->rf_phone || $supportConfig->rf_fax || $supportConfig->rf_mobile || $supportConfig->rf_address1 || $supportConfig->rf_address2 || $supportConfig->rf_zipcode || $supportConfig->rf_location || $supportConfig->rf_country || $supportConfig->rf_city))
		{
			$where = '';
			$where .= ($supportConfig->rf_phone ? " `phone`='' OR" : '');
			$where .= ($supportConfig->rf_fax ? " `fax`='' OR" : '');
			$where .= ($supportConfig->rf_mobile ? " `mobile`='' OR" : '');
			$where .= ($supportConfig->rf_address1 ? " `address1`='' OR" : '');
			$where .= ($supportConfig->rf_address2 ? " `address2`='' OR" : '');
			$where .= ($supportConfig->rf_zipcode ? " `zipcode`='' OR" : '');
			$where .= ($supportConfig->rf_location ? " `location`='' OR" : '');
			$where .= ($supportConfig->rf_country ? " `country`='' OR" : '');
			$where .= ($supportConfig->rf_city ? " `city`='' OR" : '');
			$where = ($where != '' ? ' AND (' . substr($where, 0, strlen($where) - 3) . ')' : '');

			// Check required fields
			$sql = "SELECT COUNT(*)
					FROM `#__support_users`
					WHERE `id_user`=" . $user->id . $where;
			$database->setQuery($sql);
			$require = $database->loadResult();

			// Make a test because user may not have profile yet and in that case must fail
			$sql = "SELECT COUNT(*)
					FROM `#__support_users`
					WHERE `id_user`=" . $user->id;
			$database->setQuery($sql);
			$require = ($database->loadResult() ? $require : false);

			if ($require)
			{
				$mainframe->redirect(JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=users_profile&msgtype=w&msg=' . JText::_('profile_required_access')));
			}
		}
	}

	static function ValidPermissions($task, $check_type = 'T')
	{
		global $supportOptions, $usertype, $client_id;
		$database = JFactory::getDBO();
		$user = JFactory::getUser();
		$supportConfig = HelpdeskUtility::GetConfig();
		$workgroupSettings = HelpdeskDepartment::GetSettings();
		$is_support = HelpdeskUser::IsSupport();
		$is_client = HelpdeskUser::IsClient();
		$id_workgroup = JRequest::getInt('id_workgroup', 0);
		$Itemid = JRequest::getInt('Itemid', 0);
		$id = JRequest::getInt('id', 0);
		$extid = JRequest::getInt('extid', 0);
		$usertype = (int) $usertype;

		$valid_access = 0;
		$permissioncheck_query = 0;
		$id = JRequest::getVar('id', 0, '', 'int');

		// Check applies to ticket tasks (default)
		if ($check_type == 'T') {
			if ($task == 'replyapply' || $task == 'reply' || $task == 'view' || $task == 'print' || $task == 'bookmark' || $task == 'rate' || $task == 'download' || $task == 'delattach' || $task == 'quickreply' || $task == 'setstatus') {

				// Fix for different variables names in files table
				if ($task == 'download' || $task == 'delattach') {
					$tmp_id = $id;
					$id = $extid;
				}

				switch ($usertype) {
					case 1: // Client user, Check if the ticket belongs to current user
						$permissioncheck_query = "SELECT COUNT(*) FROM #__support_ticket WHERE id='" . $id . "' AND id_user='" . $user->id . "'";
						break;

					case 2: // Client Manager, Check if the ticket belongs to current users client company
						$permissioncheck_query = "SELECT COUNT(*) FROM #__support_client_users c, #__support_ticket t WHERE t.id='" . $id . "' AND c.id_user=t.id_user AND c.id_client= '" . $client_id . "'";
						break;

					case 5: // Support user, Check if the ticket is assigned to the current user and he has access to the tickets workgroup
						$permissioncheck_query = "SELECT COUNT(*) FROM #__support_ticket t, #__support_permission p WHERE t.id='" . $id . "' AND t.id_workgroup=p.id_workgroup AND (p.id_user='" . $user->id . "' OR t.assign_to='" . $user->id . "')";
						break;

					case 6: // Support team leader, Check if the ticket is assigned to the current user or unassigned and he has access to the tickets workgroup
						$permissioncheck_query = "SELECT COUNT(*) FROM #__support_ticket t, #__support_permission p WHERE t.id='" . $id . "' AND (t.assign_to='0' OR t.assign_to='" . $user->id . "' OR t.id_user='" . $user->id . "') AND t.id_workgroup=p.id_workgroup AND p.id_user='" . $user->id . "'";
						break;

					case 7: // Support manager, Check if the current Support Manager has access to tickets workgroup
						$permissioncheck_query = "SELECT COUNT(*) FROM  #__support_ticket t, #__support_permission p WHERE t.id_workgroup=p.id_workgroup AND p.id_user='" . $user->id . "' AND t.id='" . $id . "'";
						break;

					default:
						$permissioncheck_query = '';
						break;
				}

				// Fix for quickreply
				if ($task == 'quickreply') {
					$id = JRequest::getVar('ticket_reply_id', 0, '', 'int');
					$permissioncheck_query = '';
					$valid_access = 1;
				}

				// Fix for setstatus
				if ($task == 'setstatus') {
					$id = JRequest::getVar('ticket', 0, '', 'int');
					$permissioncheck_query = '';
					$valid_access = 1;
				}

				// Fix for different variables names in files table
				if ($task == 'download' || $task == 'delattach') {
					$id = $tmp_id;
				}

			} elseif ($task == 'apply' || $task == 'save' || $task == 'new' || $task == 'duplicate') {
				// Client user & Client manager: Check if the user has rights to the workgroup
				if ($is_client && ($usertype == 1 || $usertype == 2)) {
					$permissioncheck_query = "SELECT COUNT(*) AS total FROM #__support_client_wk WHERE (id_workgroup='" . JRequest::getVar('id_workgroup', 0, '', 'int') . "' OR id_workgroup='0') AND id_client='$client_id'";

					// Check if Workgroup is only for clients with contracts
					$database->setQuery("SELECT contract FROM #__support_workgroup WHERE id='" . JRequest::getVar('id_workgroup', 0, '', 'int') . "'");
					$wk_contractonly = $database->loadResult();

					if ($wk_contractonly && HelpdeskContract::IsValid($user->id) == false) {
						$permissioncheck_query = '';
						$msg = JText::_('wkcontractonly');
						$valid_access = 0;
					}
				} elseif ($usertype == 5 || $usertype == 6 || $usertype == 7) {
					// Support user, Team Leader, Manager: Check if the user has rights to the workgroup
					$permissioncheck_query = "SELECT COUNT(*) FROM #__support_permission p WHERE p.id_workgroup='" . JRequest::getVar('id_workgroup', 0, '', 'int') . "' AND p.id_user='" . $user->id . "'";
				} elseif (!$is_client && $usertype == 1) { // Non client user so no need to check for workgroup permissions
					// Check if Workgroup is only for clients with contracts
					$database->setQuery("SELECT contract FROM #__support_workgroup WHERE id='" . JRequest::getVar('id_workgroup', 0, '', 'int') . "'");
					$wk_contractonly = $database->loadResult();
					if ($wk_contractonly && HelpdeskContract::IsValid($user->id) == false) {
						$msg = JText::_('wkcontractonly');
						$valid_access = 0;
					} else {
						$permissioncheck_query = '';
						$valid_access = 1;
					}
				}

			} elseif ($task == 'changewk' || $task == 'analysis' || $task == 'opentickets' || $task == 'sticky' || $task == 'parent') {
				if ($is_support)
				{
					$valid_access = 1;
				}
				elseif (!$is_support && $task == 'analysis')
				{
					$valid_access = 1;
				}

			} elseif ($task == 'my' || $task == 'cancel') {

				if (!$is_support && $user->id > 0) {

					// Check if Workgroup is only for clients with contracts
					$database->setQuery("SELECT contract FROM #__support_workgroup WHERE id='" . JRequest::getVar('id_workgroup', 0, '', 'int') . "'");
					$wk_contractonly = $database->loadResult();
					//if( $wk_contractonly && HelpdeskContract::IsValid($user->id) == false ) {
					//	$msg = JText::_('wkcontractonly');
					//	$valid_access = 0;
					//}else{
					$valid_access = 1;
					//}
				} elseif ($usertype == 5 || $usertype == 6 || $usertype == 7) {
					// Support user, Team Leader, Manager: Check if the user has rights to the workgroup
					$permissioncheck_query = "SELECT COUNT(*) FROM #__support_permission p WHERE '" . JRequest::getVar('id_workgroup', 0, '', 'int') . "'=p.id_workgroup AND p.id_user='" . $user->id . "'";
				}
			}
			if ($permissioncheck_query != '') {
				$database->setQuery($permissioncheck_query);
				($database->loadResult() > 0) ? $valid_access = 1 : $valid_access = 0;
			}
			if (!$workgroupSettings->wkticket) {
				$valid_access = 0;
			}

			// Anonymous access check
			$filter_email = JRequest::getVar('filter_email', null, 'REQUEST', 'string');
			$filter_ticketid = JRequest::getVar('filter_ticketid', null, 'REQUEST', 'string');
			$database->setQuery("select count(*) from #__support_ticket where `ticketmask`='" . $filter_ticketid . "' AND an_mail=" . $database->quote($filter_email));
			$ticketcheck = $database->loadResult();
			if (!$user->id && $supportConfig->anonymous_tickets && ($task == 'new' || $task == 'my' || $task == 'save')) {
				$valid_access = 1;
			}
			if (!$user->id && $supportConfig->anonymous_tickets && ($task == 'view' || $task == 'print' || $task == 'reply') && $ticketcheck) {
				$valid_access = 1;
			}

			// Check applies to kb tasks
		} elseif ($check_type == 'K') {
			$valid_access = $workgroupSettings->wkkb;
			if (($task == 'new' || $task == 'edit' || $task == 'save') && !$is_support) {
				$valid_access = 0;
			}elseif ($task == 'view' || $task == 'print'){
				$permissioncheck_query = "SELECT COUNT(*)
										  FROM #__support_kb
										  WHERE id=" . $id . "
										    AND anonymous_access<=" . (!$user->id ? '0' : ($is_support ? '2' : '1'));
				$database->setQuery($permissioncheck_query);
				$valid_access = $database->loadResult();
			}

			// Check applies to faq tasks
		} elseif ($check_type == 'FAQ') {
			$valid_access = $workgroupSettings->wkfaq;

			// Check applies to troubleshooter tasks
		} elseif ($check_type == 'TR') {
			$valid_access = $workgroupSettings->trouble;

			// Check applies to discussions tasks
		} elseif ($check_type == 'DI') {
			$valid_access = $workgroupSettings->enable_discussions;
			if (!$user->id && ($task == 'vote' || $task == 'new' || $task == 'save')) {
				$valid_access = 0;
			}
			if (!$is_support && ($task == 'publish' || $task == 'delete')) {
				$valid_access = 0;
			}

			// Check applies to bugtracker tasks
		} elseif ($check_type == 'BUG') {
			$valid_access = $workgroupSettings->bugtracker;
			if (!$user->id && ($task == 'post' || $task == 'save' || $task == 'reply')) {
				$valid_access = 0;
			}
			if ($is_support){
				$sql = "SELECT `bugtracker`
						FROM #__support_permission
						WHERE id_user='" . $user->id . "' AND id_workgroup='$id_workgroup'";
				$database->setQuery($sql);
				$manage_bugtracker = $database->loadResult();
				if (!$manage_bugtracker && ($task == 'post' || $task == 'save')) {
					$valid_access = 0;
				}
			}

			// Check applies to client tasks
		} elseif ($check_type == 'C') {
			if ($task == 'download') {
				// Get the client related with the document
				$database->setQuery("SELECT id_client FROM #__support_client_docs WHERE id='" . JRequest::getVar('id', 0, '', 'int') . "'");
				$doc_client = $database->loadResult();

				switch ($usertype) {
					case 2: // Client Manager, Check if the ticket belongs to current users client company
						if ($doc_client == $is_client) {
							$valid_access = 1;
						} else {
							$valid_access = 0;
						}
						break;
					case 5: // Support user, Check if the ticket is assigned to the current user and he has access to the tickets workgroup
						$valid_access = 1;
						break;
					case 6: // Support team leader, Check if the ticket is assigned to the current user or unassigned and he has access to the tickets workgroup
						$valid_access = 1;
						break;
					case 7: // Support manager, Check if the current Support Manager has access to tickets workgroup
						$valid_access = 1;
						break;
					default:
						$valid_access = 0;
						break;
				}
			}

			// User is trying to register / edit profile
		} elseif ($check_type == 'U' && $user->id && ($task == 'profile' || $task == 'saveuseredit')) { // $task=='new' $task=='saveregistration' $task=='activate'
			$valid_access = 1;

			// Downloads
		} elseif ($check_type == 'D') {
			$valid_access = $workgroupSettings->wkdownloads;

			// Glossary
		} elseif ($check_type == 'G') {
			$valid_access = $workgroupSettings->wkglossary;
			if (($task == 'addglossary' || $task == 'editglossary' || $task == 'saveglossary') && !$is_support) {
				$valid_access = 0;
			}

			// Announcements
		} elseif ($check_type == 'A') {
			$valid_access = $workgroupSettings->wkannounces;

		} elseif ($check_type == 'TM') {
			switch ($usertype)
			{
				case 5: // Support user, Check if the ticket is assigned to the current user and he has access to the tickets workgroup
				case 6: // Support team leader, Check if the ticket is assigned to the current user or unassigned and he has access to the tickets workgroup
				case 7: // Support manager, Check if the current Support Manager has access to tickets workgroup
					$valid_access = 1;
					break;
				case 2: // Client Manager, Check if the ticket belongs to current users client company
					$valid_access = 0;
					break;
			}
		}

		!$valid_access ? $task = 'noaccess' : '';
		$valid_access=true;
		return $valid_access;
	}

	static function NoAccessQuit()
	{
		global $Itemid, $task, $id_workgroup;

		$database = JFactory::getDBO();
		$supportConfig = HelpdeskUtility::GetConfig();

		// Initialize Template Engine
		if ($task == 'nocontract') {
			$tmplfile = 'no_contract_active';
		} else {
			$tmplfile = 'access_denied';
		}

		$htmlcode = HelpdeskTemplate::Get('', $id_workgroup, 'general/' . $tmplfile);

		$title = JText::_('access_denied');
		echo $htmlcode;
	}

	static function CheckStopSpam($email = null, $ip = null)
	{
		if (!function_exists('json_decode')) {
			return true;
		}

		$url = 'http://www.stopforumspam.com/api?f=json' . ($ip != '' ? '&ip=' . $ip : '') . ($email != '' ? '&email=' . $email : '');
		$process = curl_init($url);
		curl_setopt($process, CURLOPT_HEADER, 0);
		curl_setopt($process, CURLOPT_TIMEOUT, 10);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
		@curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
		@curl_setopt($process, CURLOPT_MAXREDIRS, 20);
		$data = curl_exec($process);
		curl_close($process);
		$data = json_decode($data);

		// IP check
		if ($ip != '' && isset($data->ip) && $data->ip->appears)
		{
			return false;
		}

		// E-mail check
		if ($email != '' && isset($data->email) && $data->email->appears)
		{
			return false;
		}

		return true;
	}
}
