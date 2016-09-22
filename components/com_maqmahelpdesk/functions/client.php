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
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/file.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/priority.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/status.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/ticket.php');

$id_workgroup = JRequest::getVar('id_workgroup', 0, '', 'int');
$id = JRequest::getVar('id', 0, '', 'int');
$searchby = JRequest::getVar('searchby', '', '', 'string');
$searchfor = JRequest::getVar('searchfor', '', '', 'string');
$limit = JRequest::getVar('limit', '', '', 'string');
$limitstart = JRequest::getVar('limitstart', 0, '', 'int');
$print = JRequest::getVar('print', 0, '', 'int');

// Activities logger
HelpdeskUtility::ActivityLog('site', 'client', $task, $id);

switch ($task)
{
	case "download":
		HelpdeskValidation::ValidPermissions($task, 'C') ? HelpdeskFile::Download($id, 0, 'C') : HelpdeskValidation::NoAccessQuit();
		break;
	case "list":
		showClients($searchby, $searchfor, $limit, $limitstart);
		break;
	case "view":
		viewClient($id, $limit, $limitstart, $print);
		break;
	case "edit":
		editClient();
		break;
}

function editClient()
{
	// Display toolbar
	HelpdeskToolbar::Create();

	$tmplfile = HelpdeskTemplate::GetFile('clients/edit');
	include $tmplfile;
}

function showClients($searchby, $searchfor, $limit, $limitstart)
{
	global $supportOptions;

	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$CONFIG = new JConfig();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();
	$page = JRequest::getVar('page', 0, '', 'int');
	$limit = JRequest::getVar('limit', 20, '', 'int');
	$limitstart = ($page * $limit);

	// Filter
	$where = '';
	switch ($searchby) {
		case '0':
			$where = '';
			break;
		case 'name':
			$where = "WHERE clientname LIKE '%" . $database->escape($searchfor) . "%'";
			break;
		case 'phone':
			$where = "WHERE phone LIKE '%" . $database->escape($searchfor) . "%'";
			break;
		case 'zip':
			$where = "WHERE zipcode LIKE '%" . $database->escape($searchfor) . "%'";
			break;
		case 'city':
			$where = "WHERE city LIKE '%" . $database->escape($searchfor) . "%'";
			break;
	}

	// Get clients
	$database->setQuery("SELECT id, date_created, clientname as `name`, address, zipcode, city, state, country, phone, fax, mobile, email, contactname, website, description, travel_time, rate as hour_rate, manager as send_to_manager, block FROM #__support_client $where ORDER BY clientname ASC LIMIT " . $limitstart . ", " . $limit);
	$clients = $database->loadObjectList();

	// Get total clients
	$database->setQuery("SELECT COUNT(*) FROM #__support_client $where");
	$total = $database->loadResult();

	if ($total <= $limit) $limitstart = 0;
	jimport('joomla.html.pagination');
	$pageNav = new JPagination($total, $limitstart, $limit);

	if ($is_support) {
		HelpdeskUtility::PageTitle('showClients'); // Sets the title

		$i = 1;
		foreach ($clients as $key2 => $value2) {
			if (is_object($value2)) {
				foreach ($value2 as $key3 => $value3) {
					$clients_rows[$i][$key3] = $value3;

					if ($key3 == 'id') {
						$clients_rows[$i]['link'] = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=client_view&id=' . $value3);
					}
				}
			}

			$i++;
		}

		// Search By list
		$mitems = array();
		$mitems[] = JHTML::_('select.option', '0', JText::_('selectlist'));
		$mitems[] = JHTML::_('select.option', 'name', JText::_('name'));
		$mitems[] = JHTML::_('select.option', 'phone', JText::_('phone'));
		$mitems[] = JHTML::_('select.option', 'zip', JText::_('zipcode'));
		$mitems[] = JHTML::_('select.option', 'city', JText::_('city'));
		$searchby = JHTML::_('select.genericlist', $mitems, 'searchby', 'class="inputbox" size="1"', 'value', 'text', $searchby ? $searchby : 'name');

		// Takes care of pagination
		$plink = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=client_list';
		$pages = ceil($total / $limit);

		// Display toolbar
		HelpdeskToolbar::Create();

		$tmplfile = HelpdeskTemplate::GetFile('clients/search');
		include $tmplfile;
	} else {
		$msg = JText::_('no_permition');
		HelpdeskUtility::ShowTplMessage($msg, $id_workgroup);
	}
}

function viewClient($id, $limit, $limitstart, $print)
{
	global $supportOptions, $client;

	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();

	// For direct linking
	if (!$is_support && !$id)
	{
		$id = $is_client;
	}

	// Get the client name
	$database->setQuery("SELECT * FROM #__support_client WHERE id='" . $id . "'");
	$client = null;
	$client = $database->loadObject();

	$database->setQuery("SELECT COUNT(*) FROM #__support_client_users WHERE id_client='" . $id . "' AND id_user='" . $user->id . "' AND manager='1'");

	if ($is_support || $database->loadResult() > 0)
	{
		// Get the client workgroups
		$sql = "SELECT w.wkdesc as `name`, w.wkabout as `description`
				FROM #__support_client_wk as c, #__support_workgroup as w 
				WHERE w.id=c.id_workgroup AND c.id_client='" . $id . "' AND w.show=1
				ORDER BY w.wkdesc";
		$database->setQuery($sql);
		$client_wks = $database->loadObjectList();

		if (!count($client_wks))
		{
			$sql = "SELECT w.wkdesc AS `name`, w.wkabout AS `description`
					FROM #__support_workgroup AS w
					WHERE w.show=1
					ORDER BY w.wkdesc";
			$database->setQuery($sql);
			$client_wks = $database->loadObjectList();
		}

		// Get the client information
		$sql = "SELECT *
				FROM #__support_client_info 
				WHERE id_client='" . $id . "'
				ORDER BY `date`";
		$database->setQuery($sql);
		$client_info = $database->loadObjectList();

		// Get the Client Downloads list
		$sql = "SELECT c.cname, d.pname, a.isactive, a.serialno, a.servicefrom, a.serviceuntil, a.id
				FROM #__support_dl d
					 INNER JOIN #__support_dl_access a ON d.id=a.id_download
					 INNER JOIN #__support_dl_category AS c ON c.id=d.id_category 
				WHERE a.id_user='" . $id . "'
				ORDER BY c.cname, d.pname";
		$database->setQuery($sql);
		$client_downloads = $database->loadObjectList();

		// Get the client documents
		$sql = "SELECT *
				FROM `#__support_client_docs` 
				WHERE `id_client`='" . $id . "' " . (!$is_support ? "AND `available`=1" : "") . "
				ORDER BY `date_created`";
		$database->setQuery($sql);
		$client_docs = $database->loadObjectList();

		// Get the client users
		$sql = "SELECT u.username, u.name, su.phone, su.fax, su.mobile, u.email, c.manager AS manager, u.id, c.id_client
				FROM #__users AS u
					 INNER JOIN #__support_client_users AS c ON u.id=c.id_user
					 LEFT JOIN #__support_users AS su ON su.id_user = u.id
				WHERE c.id_client=" . $id . "
				ORDER BY u.name";
		$database->setQuery($sql);
		$client_users = $database->loadObjectList();

		// Get the Client Contracts list
		$sql = "SELECT c.id, c.contract_number as number, c.creation_date as `date_created`, c.date_start, c.date_end, c.unit, c.value, c.actual_value as `current`, c.status, c.remarks, u.name as maintainer, t.name as `contract_tmpl`
				FROM #__support_contract_template t, #__support_client cl, #__support_contract c
					 LEFT JOIN #__users u ON c.id_user=u.id
				WHERE cl.id=c.id_client AND cl.id='" . $id . "' AND t.id=c.id_contract
				ORDER BY c.date_start DESC";
		$database->setQuery($sql);
		$client_contracts = $database->loadObjectList();

		$i = 1;
		foreach ($client_contracts as $key2 => $value2) {
			if (is_object($value2)) {
				foreach ($value2 as $key3 => $value3) {
					$contracts[$i][$key3] = $value3;

					if ($key3 == 'unit') {
						switch ($value3) {
							case 'Y':
								$contracts[$i]['unit'] = JText::_('years');
								break;

							case 'M':
								$contracts[$i]['unit'] = JText::_('months');
								break;

							case 'D':
								$contracts[$i]['unit'] = JText::_('days');
								break;

							case 'H':
								$contracts[$i]['unit'] = JText::_('hours');
								break;

							case 'T':
								$contracts[$i]['unit'] = JText::_('tickets');
								break;
						}
					}
					if ($key3 == 'current') {
						$contracts[$i]['current'] = ($contracts[$i]['unit'] == JText::_('tickets')) ? intval($value3) : $value3;
					}
					if ($key3 == 'status') {
						if ($value3 == "A")
							$contracts[$i]['status'] = JText::_('active');
						else if ($value3 == "I")
							$contracts[$i]['status'] = JText::_('inactive');
						else
							$contracts[$i]['status'] = $value3;
					}
					if ($key3 == 'date_start')
						$tickets[$i]['date_start'] = date($supportConfig->date_short, strtotime($value3));

					if ($key3 == 'date_end')
						$tickets[$i]['date_end'] = date($supportConfig->date_short, strtotime($value3));

					if ($key3 == 'creation_date')
						$tickets[$i]['date_created'] = date($supportConfig->date_short, strtotime($value3));

					if ($key3 == 'id') {
						$database->setQuery("SELECT co.name, co.description FROM #__support_contract_comp as c, #__support_components as co WHERE c.id_component=co.id AND c.id_contract=" . $value3);
						$components = $database->loadObjectList();

						$components_desc = '';
						for ($ii = 0; $ii < count($components); $ii++) {
							$row_comp = $components[$ii];
							$components_desc .= ($components_desc != '' ? '<br />' : '');
							$components_desc .= '- ' . $row_comp->name;
						}

						$contracts[$i]['components'] = ($components_desc != '' ? $components_desc : '-');
					}
				}
			}

			$i++;
		}

		// Get the client tickets
		$database->setQuery("SELECT t.subject, t.id as dbid, t.ticketmask as ticketid, t.date, t.last_update, t.assign_to, t.source, t.duedate, t.id_priority, t.id_status, t.id_user, t.id_workgroup
							  FROM #__support_ticket as t
							  WHERE t.id_client='" . $id . "'
							  ORDER BY t.date DESC");
		$client_tickets = $database->loadObjectList();

		$i = 1;
		foreach ($client_tickets as $key2 => $value2) {
			if (is_object($value2)) {
				foreach ($value2 as $key3 => $value3) {
					$tickets[$i][$key3] = $value3;

					if ($key3 == 'dbid')
					{
						$tickets[$i]['messages'] = HelpdeskTicket::GetMessages($value3);
						$tickets[$i]['link'] = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_view&id=' . $value3);
						$database->setQuery("SELECT COUNT(*) FROM #__support_file WHERE id='" . $value3 . "' AND source='T'");
						$tickets[$i]['attachs'] = $database->loadResult();
						$tickets[$i]['attachs_image'] = ($database->loadResult() > 0 ? '<img src="media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/16px/attach.png" />' : '');
					}

					if ($key3 == 'id_user')
						$tickets[$i]['user'] = HelpdeskUser::GetName($value3);

					if ($key3 == 'id_status')
					{
						$status = $value3;
						$tickets[$i]['status'] = HelpdeskStatus::GetName($value3);
					}

					if ($key3 == 'id_priority')
					{
						$priority = $value3;
						$tickets[$i]['priority'] = HelpdeskPriority::GetName($value3);
					}

					if ($key3 == 'id_workgroup')
						$tickets[$i]['workgroup'] = HelpdeskDepartment::GetName($value3);

					if ($key3 == 'assign_to')
					{
						$assign = $value3;
						$tickets[$i]['assigned_to'] = HelpdeskUser::GetName($value3);
					}

					if ($key3 == 'date')
						$tickets[$i]['date_created'] = date($supportConfig->date_short, strtotime($value3));

					if ($key3 == 'last_update')
						$tickets[$i]['date_updated'] = date($supportConfig->date_short, strtotime($value3));

					if ($key3 == 'duedate')
					{
						$duedate = $value3;
						$tickets[$i]['elapsed_time'] = HelpdeskDate::ElapsedTime(JString::substr($value3, 0, 4), JString::substr($value3, 5, 2), JString::substr($value3, 8, 2), JString::substr($value3, 11, 2), JString::substr($value3, 14, 2), HelpdeskDate::DateOffset("%Y"), HelpdeskDate::DateOffset("%m"), HelpdeskDate::DateOffset("%d"), HelpdeskDate::DateOffset("%H"), HelpdeskDate::DateOffset("%M"));
					}

					if ($key3 == 'source')
					{
						switch ($value3)
						{
							case 'P':
								$tickets[$i]['source'] = JText::_('phone');
								break;
							case 'F':
								$tickets[$i]['source'] = JText::_('fax');
								break;
							case 'M':
								$tickets[$i]['source'] = JText::_('email');
								break;
							case 'W':
								$tickets[$i]['source'] = JText::_('website');
								break;
							case 'O':
								$tickets[$i]['source'] = JText::_('other');
								break;
						}
					}
				}
			}

			$tickets[$i]['icon_duedate'] = HelpdeskTicket::IsDueDateValid($duedate, $priority, $status, 0, $assign, 0);
			$tickets[$i]['icontxt_duedate'] = HelpdeskTicket::IsDueDateValid($duedate, $priority, $status, 0, $assign, 1);

			$i++;
		}

		// Get contract custom fields
		$sql = "SELECT kf.id, kf.id_field, kf.ordering, kf.required, cf.caption, cf.ftype, cf.value, cf.size, cf.maxlength
			FROM #__support_contract_fields kf
				 INNER JOIN #__support_custom_fields cf ON cf.id=kf.id_field
			WHERE cf.cftype='C'
			ORDER BY kf.ordering";
		$database->setQuery($sql);
		$cfields = $database->loadObjectList();

		HelpdeskUtility::PageTitle('viewClients');

		// Display toolbar
		HelpdeskToolbar::Create();

		$tmplfile = HelpdeskTemplate::GetFile('clients/profile');
		include $tmplfile;
	} else {
		$msg = JText::_('no_permition');
		HelpdeskUtility::ShowTplMessage($msg, $id_workgroup);
	}
}
