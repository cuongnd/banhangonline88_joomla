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
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/client.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/validation.php';

$id_workgroup = JRequest::getInt('id_workgroup', 0);

// Activities logger
HelpdeskUtility::ActivityLog('site', 'workgroup', $task, $id_workgroup);

switch ($task)
{
	case "view":
		isset($workgroupSettings) ? showWorkgroup() : HelpdeskValidation::NoAccessQuit();
		break;

	default:
		showList();
		break;
}

function showWorkgroup()
{
	global $supportOptions, $clientOptions, $is_manager, $usertype;

	$Itemid = JRequest::getInt('Itemid', 0);
	$id_workgroup = JRequest::getInt('id_workgroup', 0);
	$session = JFactory::getSession();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$url = JURI::getInstance();
	$supportConfig = HelpdeskUtility::GetConfig();
	$workgroupSettings = HelpdeskDepartment::GetSettings();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();
	HelpdeskUtility::AppendResource('equalheights.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	// Validate if it's a support only contract and it's not a support user
	if ($workgroupSettings->support_only && !$is_support)
	{
		HelpdeskValidation::NoAccessQuit();
	}

	// Sets the title
	HelpdeskUtility::PageTitle('showWorkgroup');

	// Get announcements
	$sql = "SELECT id, `date`, introtext as intro, bodytext as body, frontpage, urgent, sent, id_workgroup, DAY(`date`) AS date_day, MONTH(`date`) AS date_month, YEAR(`date`) AS date_year, CONCAT('index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&id_workgroup=" . $id_workgroup . "&task=announce_view&id=',`id`) AS link, SUBSTRING(`bodytext`,1,50) AS body_resume
			FROM #__support_announce 
			WHERE frontpage='1' " . ($is_support ? "" : "AND (id_client='0' OR id_client='" . $is_client . "') ") . ($id_workgroup == 0 ? "AND id_workgroup='0'" : "AND (id_workgroup='0' OR id_workgroup='" . $id_workgroup . "')") . "
			ORDER BY date DESC";
	$database->setQuery($sql);
	$announcements = $database->loadObjectList();

	#	support = 1
	#		usertype
	#			7: Support manager
	#			6: Support team leader
	#			5: Basic support user
	#	client = 1
	#		manager = 1

	// Build Options Array
	$i = 0;
	$wk_options = array();

	if ($workgroupSettings->wkticket && (($supportConfig->unregister && !$user->id && $supportConfig->anonymous_tickets && !$workgroupSettings->contract) || $user->id))
	{
		if (!$is_client || ($is_client && HelpdeskClient::AccessApplication($id_workgroup, $is_client, 'ticket')))
		{
			$wk_options[$i]['title'] = JText::_('wk_addticket');
			$wk_options[$i]['icon'] = 'tickets_add.png';
			$wk_options[$i]['link'] = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_new');
			$wk_options[$i]['description'] = JText::_('wk_addticket_desc');
			$i++;

			$wk_options[$i]['title'] = JText::_('wk_tickets');
			$wk_options[$i]['icon'] = 'tickets.png';
			$wk_options[$i]['link'] = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=ticket_my');
			$wk_options[$i]['description'] = JText::_('wk_tickets_desc');
			$i++;
		}
	}

	if ($workgroupSettings->enable_discussions)
	{
		if (!$is_client || (HelpdeskClient::AccessApplication($id_workgroup, $is_client, 'discussions')))
		{
			$wk_options[$i]['title'] = JText::_('discussions');
			$wk_options[$i]['icon'] = 'discussions.png';
			$wk_options[$i]['link'] = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=discussions');
			$wk_options[$i]['description'] = JText::_('discussions_desc');
			$i++;
		}
	}

	if ($workgroupSettings->wkkb)
	{
		if (!$is_client || (HelpdeskClient::AccessApplication($id_workgroup, $is_client, 'kb')))
		{
			// If there's only one category link goes direct
			$sql = "SELECT c.`id`
					FROM `#__support_category` AS c
						 INNER JOIN `#__support_kb_category` AS kc ON kc.`id_category` = c.`id`
					WHERE c.`id_workgroup`=" . $id_workgroup;
			$database->setQuery($sql);
			$categories = $database->loadObjectList();
			$link = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_list' . (!$categories || count($categories) > 1 ? '' : '&parent=' . $categories[0]->id));
			$wk_options[$i]['title'] = JText::_('wk_kb');
			$wk_options[$i]['icon'] = 'kb.png';
			$wk_options[$i]['link'] = $link;
			$wk_options[$i]['description'] = JText::_('wk_kb_desc');
			$i++;
		}
	}

	if ($workgroupSettings->wkfaq)
	{
		if (!$is_client || (HelpdeskClient::AccessApplication($id_workgroup, $is_client, 'faq')))
		{
			$wk_options[$i]['title'] = JText::_('wk_faq');
			$wk_options[$i]['icon'] = 'replies.png';
			$wk_options[$i]['link'] = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=kb_faq');
			$wk_options[$i]['description'] = JText::_('wk_faq_desc');
			$i++;
		}
	}

	if ($workgroupSettings->bugtracker)
	{
		if (!$is_client || (HelpdeskClient::AccessApplication($id_workgroup, $is_client, 'bugtracker')))
		{
			$wk_options[$i]['title'] = JText::_('bugtracker');
			$wk_options[$i]['icon'] = 'bug.png';
			$wk_options[$i]['link'] = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=bugtracker');
			$wk_options[$i]['description'] = JText::_('bugtracker_desc');
			$i++;
		}
	}

	if ($workgroupSettings->wkglossary)
	{
		if (!$is_client || (HelpdeskClient::AccessApplication($id_workgroup, $is_client, 'glossary')))
		{
			$wk_options[$i]['title'] = JText::_('wk_glossary');
			$wk_options[$i]['icon'] = 'glossary.png';
			$wk_options[$i]['link'] = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=glossary';
			$wk_options[$i]['description'] = JText::_('wk_glossary_desc');
			$i++;
		}
	}

	if ($workgroupSettings->trouble)
	{
		if (!$is_client || (HelpdeskClient::AccessApplication($id_workgroup, $is_client, 'trouble')))
		{
			$wk_options[$i]['title'] = JText::_('wk_troubleshooter');
			$wk_options[$i]['icon'] = 'troubleshooter.png';
			$wk_options[$i]['link'] = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=troubleshooter';
			$wk_options[$i]['description'] = JText::_('wk_troubleshooter_desc');
			$i++;
		}
	}

	if ($workgroupSettings->wkannounces)
	{
		if (!$is_client || (HelpdeskClient::AccessApplication($id_workgroup, $is_client, 'announcements')))
		{
			$wk_options[$i]['title'] = JText::_('wk_announces');
			$wk_options[$i]['icon'] = 'announcements.png';
			$wk_options[$i]['link'] = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=announce_list';
			$wk_options[$i]['description'] = JText::_('wk_announces_desc');
			$i++;
		}
	}

	if ($workgroupSettings->wkdownloads)
	{
		if (!$is_client || (HelpdeskClient::AccessApplication($id_workgroup, $is_client, 'downloads')))
		{
			$wk_options[$i]['title'] = JText::_('wk_downloads');
			$wk_options[$i]['icon'] = 'files.png';
			$wk_options[$i]['link'] = 'index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=downloads';
			$wk_options[$i]['description'] = JText::_('wk_downloads_desc');
			$i++;
		}
	}

	if ($is_support)
	{
		// Get Links
		$sql = "SELECT COUNT(*)
				FROM `#__support_client`";
		$database->setQuery($sql);
		$clients = $database->loadResult();

		if ($clients)
		{
			$wk_options[$i]['title'] = JText::_('wk_clients');
			$wk_options[$i]['icon'] = 'clients.png';
			$wk_options[$i]['link'] = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=client_list');
			$wk_options[$i]['description'] = JText::_('wk_clients_desc');
			$i++;
		}
	}

	// Get Links
	$sql = "SELECT `id`, `name` as `title`, `description`, `link`, `image` as `icon`
			FROM #__support_links 
			WHERE id_workgroup='" . $id_workgroup . "' AND `section`='F' AND `published`='1'" . (!$is_support ? " AND `public`='1'" : '') . "
			ORDER BY `ordering` DESC";
	$database->setQuery($sql);
	$links = $database->loadObjectList();

	// Dashboard - Pending tickets
	$sql = "SELECT COUNT(*) AS TOTAL
			FROM #__support_ticket AS t 
				 INNER JOIN #__support_status AS s ON s.id=t.id_status 
			WHERE s.status_group='O' 
			  AND t.id_workgroup='" . $id_workgroup . "'" .
		(!$is_support ? ($is_client ? "AND t.id_client=" . $is_client : "AND t.id_user=" . $user->id) : "");
	$database->setQuery($sql);
	$tickets_pending = (int) $database->loadResult();

	// Dashboard - Overdued tickets
	$sql = "SELECT COUNT(*) AS TOTAL
			FROM #__support_ticket AS t 
				 INNER JOIN #__support_status AS s ON s.id=t.id_status 
			WHERE s.status_group='O' AND t.duedate<'" . date("Y-m-d") . "'
			  AND t.id_workgroup='" . $id_workgroup . "'" .
		(!$is_support ? ($is_client ? "AND t.id_client=" . $is_client : "AND t.id_user=" . $user->id) : "");
	$database->setQuery($sql);
	$tickets_overdue = (int) $database->loadResult();

	// Dashboard - Tickets today
	$sql = "SELECT COUNT(*) AS TOTAL
			FROM #__support_ticket AS t 
			WHERE SUBSTRING(t.date,1,10)='" . date("Y-m-d") . "'
			  AND t.id_workgroup='" . $id_workgroup . "'" .
		(!$is_support ? ($is_client ? "AND t.id_client=" . $is_client : "AND t.id_user=" . $user->id) : "");
	$database->setQuery($sql);
	$tickets_today = (int) $database->loadResult();

	// Display toolbar
	HelpdeskToolbar::Create();

	// Output
	$tmplfile = HelpdeskTemplate::GetFile('departments/' . ($user->id == 0 ? 'anonymous' : ($is_support ? 'support' : 'customer')));
	include $tmplfile;
}

function showList()
{
	$session = JFactory::getSession();
	$mainframe = JFactory::getApplication();
	$database = JFactory::getDBO();
	$user = JFactory::getUser();
	$supportConfig = HelpdeskUtility::GetConfig();
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();
	$Itemid = JRequest::getInt('Itemid', 0);
	$id_group = JRequest::getVar('id_group', 0, '', 'int');
	$tickets_pending = 0;
	$tickets_overdue = 0;
	$tickets_today = 0;
	HelpdeskUtility::AppendResource('equalheights.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	// Sets the title
	HelpdeskUtility::PageTitle('showWorkgroups');

	// Verify permissions to the workgroups
	$wkids = $session->get('wkids', '', 'maqmahelpdesk');
	$sql = "SELECT w.`id`, w.`wkdesc`, w.`wkabout`, w.`logo`, w.`shortdesc`, g.`title` AS group_title, g.`description` AS group_description, w.`contract`
			FROM `#__support_workgroup` AS w
				 LEFT JOIN `#__support_department_group` AS g ON g.`id`=w.`id_group`
			WHERE w.`show`='1'
              AND w.id IN (" . $wkids . ") " .
			  ( $id_group ? "AND w.`id_group`=" . $id_group : "" ) . "
			ORDER BY g.`title`, w.`ordering`, w.`wkdesc`";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();
	/*$wkids = '';
	if ($is_support)
	{
		if ($supportConfig->support_workgroup_only)
		{
			$sql = "SELECT w.`id`, w.`wkdesc`, w.`wkabout`, w.`logo`, w.`shortdesc`, g.`title` AS group_title, g.`description` AS group_description, w.`contract`
					FROM `#__support_workgroup` AS w
						 LEFT JOIN `#__support_department_group` AS g ON g.`id`=w.`id_group`
					WHERE w.`show`='1'
					  AND w.id IN (SELECT p.`id_workgroup` FROM `#__support_permission` AS p WHERE p.`id_user`=" . $user->id . ") " .
				      ( $id_group ? "AND w.`id_group`=" . $id_group : "" ) . "
					ORDER BY g.`title`, w.`ordering`, w.`wkdesc`";
			$database->setQuery($sql);
			$rows = $database->loadObjectList();
		}
		else
		{
			// Get workgroups where the user is as support staff
			$sql = "SELECT DISTINCT w.`id`, w.`wkdesc`, w.`wkabout`, w.`logo`, w.`shortdesc`, g.`title` AS group_title, g.`description` AS group_description, w.`contract`
					FROM `#__support_workgroup` AS w
						 LEFT JOIN `#__support_department_group` AS g ON g.`id`=w.`id_group`
					WHERE w.`show`='1'
					  AND w.id IN (SELECT p.`id_workgroup` FROM `#__support_permission` AS p WHERE p.`id_user`=" . $user->id . ") " .
					  ( $id_group ? "AND w.`id_group`=" . $id_group : "" ) . "
					ORDER BY g.`title`, w.`ordering`, w.`wkdesc`";
			$database->setQuery($sql);
			$rows1 = $database->loadObjectList();

			// Get workgroups where the user is not as support staff (if any)
			$sql = "SELECT DISTINCT w.`id`, w.`wkdesc`, w.`wkabout`, w.`logo`, w.`shortdesc`, g.`title` AS group_title, g.`description` AS group_description, w.`contract`
					FROM `#__support_workgroup` AS w
						 LEFT JOIN `#__support_department_group` AS g ON g.`id`=w.`id_group`
					WHERE w.`contract`=0
					  AND w.`show`='1'
					  AND w.id NOT IN (SELECT p.`id_workgroup` FROM `#__support_permission` AS p WHERE p.`id_user`=" . $user->id . ") " .
					  ( $id_group ? "AND w.`id_group`=" . $id_group : "" ) . "
					ORDER BY g.`title`, w.`ordering`, w.`wkdesc`";
			$database->setQuery($sql);
			$rows2 = $database->loadObjectList();

			// Merge both arrays
			$rows = array_merge($rows1, $rows2);
		}

		// Set session access permissions
		for ($i = 0; $i < count($rows); $i++)
		{
			$row = $rows[$i];
			$sql = "SELECT COUNT(*)
					FROM #__support_permission
					WHERE id_workgroup='" . $row->id . "'
					  AND id_user='" . $user->id . "'";
			$database->setQuery($sql);
			if ($database->loadResult() > 0)
			{
				$wkids .= $row->id . ',';
			}
		}
		$wkids = substr($wkids, 0, strlen($wkids) - 1);
	}
	elseif ($is_client > 0)
	{
		// Get workgroups
		$sql = "SELECT w.`id`, w.`wkdesc`, w.`wkabout`, w.`logo`, w.`shortdesc`, g.`title` AS group_title, g.`description` AS group_description, w.`contract`
				FROM `#__support_workgroup` AS w
					 LEFT JOIN `#__support_department_group` AS g ON g.`id`=w.`id_group`
				WHERE w.`show`='1' ".( !$is_support ? "AND w.`support_only`=0 " : "" ).( $id_group ? "AND w.`id_group`=".$id_group : "" )."
				ORDER BY g.`title`, w.`ordering`, w.`wkdesc`";
		$database->setQuery($sql);
		$rows = $database->loadObjectList();
		// w.id IN (" . $wkids . ") AND

		// Set session access permissions
		for ($i = 0; $i < count($rows); $i++)
		{
			$row = $rows[$i];
			$sql = "SELECT COUNT(*)
					FROM #__support_client_wk
					WHERE (id_workgroup='" . $row->id . "' OR id_workgroup='0') AND id_client='" . $is_client . "'";
			$database->setQuery($sql);
			if ($database->loadResult() > 0)
			{
				if (HelpdeskDepartment::UserGroupRestricted($row->id))
				{
					$wkids .= $row->id . ',';
				}
			}
		}
		$wkids = substr($wkids, 0, strlen($wkids) - 1);

		// Get workgroups
		$sql = "SELECT w.`id`, w.`wkdesc`, w.`wkabout`, w.`logo`, w.`shortdesc`, g.`title` AS group_title, g.`description` AS group_description, w.`contract`
				FROM `#__support_workgroup` AS w
					 LEFT JOIN `#__support_department_group` AS g ON g.`id`=w.`id_group`
				WHERE w.`show`='1' AND w.`id` IN (" . $wkids . ")
				ORDER BY g.`title`, w.`ordering`, w.`wkdesc`";
		$database->setQuery($sql);
		$rows = $database->loadObjectList();
	}
	else
	{
		$sql = "SELECT w.`id`, w.`wkdesc`, w.`wkabout`, w.`logo`, w.`shortdesc`, g.`title` AS group_title, g.`description` AS group_description, w.`contract`
				FROM `#__support_workgroup` AS w
					 LEFT JOIN `#__support_department_group` AS g ON g.`id`=w.`id_group`
				WHERE w.`show`='1' " . ( !$is_support ? "AND w.`support_only`=0 " : "" ) . ( $id_group ? "AND w.`id_group`=".$id_group : "" ) . "
				ORDER BY g.`title`, w.`ordering`, w.`wkdesc`";
		$database->setQuery($sql);
		$rows = $database->loadObjectList();

		// Set session access permissions
		for ($i = 0; $i < count($rows); $i++)
		{
			$row = $rows[$i];
			if (HelpdeskDepartment::UserGroupRestricted($row->id))
			{
				$wkids .= $row->id . ',';
			}
		}
		$wkids = substr($wkids, 0, strlen($wkids) - 1);
	}*/

	if (count($rows) > 0)
	{
		$i = 1;
		foreach ($rows as $key2 => $value2)
		{
			if (is_object($value2))
			{
				foreach ($value2 as $key3 => $value3)
				{
					$workgroups[$i][$key3] = $value3;

					if ($key3 == 'id')
					{
						$workgroups[$i]['link'] = JRoute::_("index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&id_workgroup=" . $value3 . "&task=workgroup_view");
					}

					if ($key3 == 'logo')
					{
						$workgroups[$i]['image'] = ($value3 != '' ? 'media/com_maqmahelpdesk/images/logos/' . $value3 : 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/48px/workgroup.png');
					}

					if ($key3 == 'wkdesc')
					{
						$workgroups[$i]['title'] = str_replace("\'", "'", $value3);
					}

					if ($key3 == 'wkabout')
					{
						$workgroups[$i]['description'] = str_replace("\'", "'", $value3);
					}

					if ($key3 == 'contract')
					{
						if ($value3 && !$user->id)
						{
							unset($workgroups[$i]);
							$i--;
						}
					}
				}
			}

			$i++;
		}
	}

	// Get featured KB articles from the possible workgroups - $wkids
	$sql = "SELECT k.id, k.kbcode AS code, k.kbtitle AS title, k.content AS question, (SUM(r.rate)/COUNT(r.id)) AS rating
			FROM #__support_kb AS k 
				 LEFT JOIN #__support_rate AS r ON r.id_table=k.id AND r.source='K'
				 INNER JOIN #__support_kb_category AS kc ON kc.id_kb=k.id
				 INNER JOIN #__support_category AS c ON c.id=kc.id_category AND kc.id_kb=k.id
			WHERE k.publish=1
			  AND c.show=1
			  AND c.id_workgroup IN (" . $wkids . ")
			  AND k.anonymous_access<=" . (!$user->id ? '0' : ($is_support ? '2' : '1')) . "
			GROUP BY k.id, k.kbcode, k.kbtitle, k.content 
			ORDER BY k.views DESC
			LIMIT 0, 5";
	$database->setQuery($sql);
	$featured_kb = $database->loadAssocList();

	for ($i = 0; $i < count($featured_kb); $i++)
	{
		// Get the article categories
		$sql = "SELECT c.id_workgroup FROM #__support_category AS c INNER JOIN #__support_kb_category AS kc ON kc.id_category=c.id WHERE kc.id_kb='" . $featured_kb[$i]["id"] . "' LIMIT 0, 1";
		$database->setQuery($sql);
		$featured_kb[$i]["link"] = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $database->loadResult() . '&task=kb_view&id=' . $featured_kb[$i]["id"]);
		$featured_kb[$i]["rating"] = number_format($featured_kb[$i]["rating"], 0);
		$featured_kb[$i]["title"] = (strlen($featured_kb[$i]['title']) > 30 ? substr($featured_kb[$i]["title"], 0, 30) . '...' : $featured_kb[$i]["title"]);
	}

	// Get viewed KB articles from the possible workgroups - $wkids
	$sql = "SELECT k.id, k.kbcode AS code, k.kbtitle AS title, k.content AS question, k.views
			FROM #__support_kb AS k
				 INNER JOIN #__support_kb_category AS kc ON kc.id_kb=k.id
				 INNER JOIN #__support_category AS c ON c.id=kc.id_category AND kc.id_kb=k.id
			WHERE k.publish='1'
			  AND c.show=1
			  AND c.id_workgroup IN (" . $wkids . ")
			  AND k.anonymous_access<=" . (!$user->id ? '0' : ($is_support ? '2' : '1')) . "
			GROUP BY k.id, k.kbcode, k.kbtitle, k.content 
			ORDER BY k.views DESC
			LIMIT 0, 5";
	$database->setQuery($sql);
	$viewed_kb = $database->loadAssocList();

	for ($i = 0; $i < count($viewed_kb); $i++)
	{
		// Get the article categories
		$sql = "SELECT c.id_workgroup FROM #__support_category AS c INNER JOIN #__support_kb_category AS kc ON kc.id_category=c.id WHERE kc.id_kb='" . $viewed_kb[$i]["id"] . "' LIMIT 0, 1";
		$database->setQuery($sql);
		$viewed_kb[$i]["link"] = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $database->loadResult() . '&task=kb_view&id=' . $viewed_kb[$i]["id"]);
		$viewed_kb[$i]["title"] = (strlen($viewed_kb[$i]['title']) > 30 ? substr($viewed_kb[$i]["title"], 0, 30) . '...' : $viewed_kb[$i]["title"]);
	}

	if (count($rows) == 0)
	{
		$task = 'noaccess';
		$function = 'warning';
		HelpdeskUtility::ShowTplMessage(JText::_('client_as_no_workgroup'), 0);
	}
	elseif (count($rows) == 1)
	{
		$row = $rows[0];
		$link = JRoute::_('index.php?option=com_maqmahelpdesk&amp;Itemid=' . $Itemid . '&amp;id_workgroup=' . $row->id . '&amp;task=workgroup_view', false);
		header("Location: $link");
	}
	else
	{
		if ($is_support)
		{
			// Dashboard - Pending tickets
			$sql = "SELECT COUNT(*) AS TOTAL
					FROM #__support_ticket AS t 
						 INNER JOIN #__support_status AS s ON s.id=t.id_status 
					WHERE s.status_group='O' 
					  AND t.id_workgroup IN (" . $wkids . ") " .
				(!$is_support ? ($is_client ? "AND t.id_client=" . $is_client : "AND t.id_user=" . $user->id) : "");
			$database->setQuery($sql);
			$tickets_pending = $database->loadResult();

			// Dashboard - Overdued tickets
			$sql = "SELECT COUNT(*) AS TOTAL
					FROM #__support_ticket AS t 
						 INNER JOIN #__support_status AS s ON s.id=t.id_status 
					WHERE s.status_group='O' 
					  AND t.id_workgroup IN (" . $wkids . ")
					  AND t.duedate<'" . date("Y-m-d") . "' " .
				(!$is_support ? ($is_client ? "AND t.id_client=" . $is_client : "AND t.id_user=" . $user->id) : "");
			$database->setQuery($sql);
			$tickets_overdue = $database->loadResult();

			// Dashboard - Tickets today
			$sql = "SELECT COUNT(*) AS TOTAL
					FROM #__support_ticket AS t 
					WHERE t.id_workgroup IN (" . $wkids . ")
					  AND SUBSTRING(t.date,1,10)='" . date("Y-m-d") . "' " .
				(!$is_support ? ($is_client ? "AND t.id_client=" . $is_client : "AND t.id_user=" . $user->id) : "");
			$database->setQuery($sql);
			$tickets_today = $database->loadResult();
		}

		// Display toolbar
		HelpdeskToolbar::Create();

		// Output
		$tmplfile = HelpdeskTemplate::GetFile($supportConfig->departments_template);
		include $tmplfile;
	}
}
