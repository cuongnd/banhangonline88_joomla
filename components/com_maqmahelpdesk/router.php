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

require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/utility.php');

/**
 * Method to build Route
 * @param array $query
 */
function MaQmaHelpdeskBuildRoute(&$query)
{
	$segments = array();

	if (isset($query['view']))
	{
		if (empty($query['Itemid']))
		{
			$segments[] = $query['view'];
		}
		else
		{
			$menu = &JSite::getMenu();
			$menuItem = &$menu->getItem($query['Itemid']);

			if (!isset($menuItem->query['view']) || $menuItem->query['view'] != $query['view'])
			{
				$segments[] = $query['view'];
			}
		}
		unset($query['view']);
	}

	if (isset($query['task']))
	{
		switch ($query['task'])
		{
			// AJAX
			case 'ajax_asreply':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'asreply';
				unset($query['id_workgroup']);
				break;
			case 'ajax_javascript':
				//$segments[] = 'ajax';
				$segments[] = 'javascript';
				break;

			// ANNOUNCEMENTS
			case 'announce_list':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'announcements';
				unset($query['id_workgroup']);
				break;
			case 'announce_view':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'announcement';
				$segments[] = ObtainAnnouncement($query['id']);
				unset($query['id_workgroup']);
				unset($query['id']);
				break;

			// PUBLIC DISCUSSIONS
			case 'discussions':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'discussions';
				unset($query['id_workgroup']);
				break;
			case 'discussions_category':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'discussions';
				$segments[] = 'category';
				$segments[] = ObtainCategory($query['id_category']);
				unset($query['id_category']);
				unset($query['id_workgroup']);
				break;
			case 'discussions_view':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'discussion';
				$segments[] = ObtainDiscussion($query['id']);
				unset($query['id_workgroup']);
				unset($query['id']);
				break;
			case 'discussions_question':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'discussion';
				$segments[] = 'post';
				unset($query['id_workgroup']);
				break;
			case 'discussions_delete':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'discussion';
				$segments[] = 'delete';
				unset($query['id_workgroup']);
				break;
			case 'discussions_leaderboard':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'discussions';
				$segments[] = 'leaderboard';
				unset($query['id_category']);
				unset($query['id_workgroup']);
				break;

			// WORKGROUPS
			case 'workgroup_view':
				$segments[] = 'department';
				$segments[] = ObtainWK($query['id_workgroup']);
				unset($query['id_workgroup']);
				break;

			// KB
			case 'kb_list':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'kb';
				if (isset($query['parent'])) {
					$segments[] = ObtainCategory($query['parent']);
					unset($query['parent']);
				}
				unset($query['id_workgroup']);
				break;
			case 'kb_new':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'kb';
				$segments[] = 'new';
				unset($query['id_workgroup']);
				break;
			case 'kb_edit':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'kb';
				$segments[] = 'edit';
				unset($query['id_workgroup']);
				break;
			case 'kb_view':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'kb';
				$segments[] = 'article';
				$segments[] = ObtainKB($query['id']);
				unset($query['id_workgroup']);
				unset($query['id']);
				break;
			case 'kb_bookmark':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'kb';
				$segments[] = 'bookmark';
				unset($query['id_workgroup']);
				break;
			case 'kb_convert':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'kb';
				$segments[] = 'convert';
				unset($query['id_workgroup']);
				break;
			case 'kb_download':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'kb';
				$segments[] = 'attachment';
				unset($query['id_workgroup']);
				break;

			// TICKETS
			case 'ticket_my':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'tickets';
				$segments[] = 'manager';
				unset($query['id_workgroup']);
				break;
			case 'ticket_new':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'ticket';
				$segments[] = 'create';
				unset($query['id_workgroup']);
				break;
			case 'ticket_duplicate':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'ticket';
				$segments[] = 'duplicate';
				unset($query['id_workgroup']);
				break;
			case 'ticket_view':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'ticket';
				$segments[] = ObtainTicket($query['id']);
				unset($query['id_workgroup']);
				unset($query['id']);
				break;
			case 'ticket_report':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'tickets';
				$segments[] = 'report';
				unset($query['id_workgroup']);
				break;
			case 'ticket_analysis':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'tickets';
				$segments[] = 'analysis';
				unset($query['id_workgroup']);
				break;
			case 'ticket_download':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'tickets';
				$segments[] = 'attachment';
				unset($query['id_workgroup']);
				break;
			case 'ticket_views':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'tickets';
				$segments[] = 'views';
				unset($query['id_workgroup']);
				break;
			case 'ticket_bookmark':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'ticket';
				$segments[] = 'bookmark';
				unset($query['id_workgroup']);
				break;
			case 'ticket_delete':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'ticket';
				$segments[] = 'delete';
				unset($query['id_workgroup']);
				break;
			case 'ticket_parent':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'ticket';
				$segments[] = 'parent';
				unset($query['id_workgroup']);
				break;
			case 'ticket_approve':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'ticket';
				$segments[] = 'approve';
				unset($query['id_workgroup']);
				break;
			case 'ticket_delattach':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'ticket';
				$segments[] = 'delattach';
				unset($query['id_workgroup']);
				break;

			// GLOSSARY
			case 'glossary':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'glossary';
				unset($query['id_workgroup']);
				break;
			case 'glossary_category':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'glossary';
				$segments[] = 'category';
				$segments[] = ObtainCategory($query['id_category']);
				unset($query['id_category']);
				unset($query['id_workgroup']);
				break;
			case 'glossary_add':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'glossary';
				$segments[] = 'add';
				unset($query['id_workgroup']);
				break;
			case 'glossary_edit':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'glossary';
				$segments[] = 'edit';
				unset($query['id_workgroup']);
				break;

			// DOWNLOADS
			case 'downloads':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'downloads';
				unset($query['id_workgroup']);
				break;
			case 'downloads_category':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'downloads';
				$segments[] = 'category';
				$segments[] = ObtainDownloadsCategory($query['id']);
				unset($query['id_workgroup']);
				unset($query['id']);
				break;
			case 'downloads_product':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'downloads';
				$segments[] = 'product';
				$segments[] = ObtainDownloads($query['id']);
				unset($query['id_workgroup']);
				unset($query['id']);
				break;
			case 'downloads_subscriptions':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'subscriptions';
				unset($query['id_workgroup']);
				break;
			case 'downloads_subscribe':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'subscribe';
				unset($query['id_workgroup']);
				break;
			case 'downloads_license':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'license';
				$segments[] = ObtainLicense($query['id']);
				unset($query['id_workgroup']);
				unset($query['id']);
				break;
			case 'downloads_getfile':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'download';
				unset($query['id_workgroup']);
				break;
			case 'downloads_unsubscribe':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'unsubscribe';
				unset($query['id_workgroup']);
				break;

			// CLIENTS
			case 'client_list':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'clients';
				unset($query['id_workgroup']);
				break;
			case 'client_view':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'client';
				$segments[] = ObtainClient($query['id']);
				unset($query['id_workgroup']);
				break;
			case 'client_download':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'clientdownload';
				unset($query['id_workgroup']);
				break;

			// TASKS
			case 'calendar_view':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'tasks';
				$segments[] = 'monthly';
				unset($query['id_workgroup']);
				break;
			case 'calendar_week':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'tasks';
				$segments[] = 'weekly';
				unset($query['id_workgroup']);
				break;
			case 'calendar_day':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'tasks';
				$segments[] = 'daily';
				unset($query['id_workgroup']);
				break;
			case 'calendar_list':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'tasks';
				$segments[] = 'list';
				unset($query['id_workgroup']);
				break;
			case 'calendar_add':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'tasks';
				$segments[] = 'add';
				unset($query['id_workgroup']);
				break;
			case 'calendar_edit':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'tasks';
				$segments[] = 'edit';
				unset($query['id_workgroup']);
				break;

			// USERS
			case 'users_profile':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'profile';
				unset($query['id_workgroup']);
				break;
			case 'users_getuserdetails':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'details';
				unset($query['id_workgroup']);
				break;

			// FAQ
			case 'kb_faq':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'faq';
				if (isset($query['parent'])) {
					$segments[] = ObtainCategory($query['parent']);
					unset($query['parent']);
				}
				unset($query['id_workgroup']);
				break;

			// TIMESHEET
			case 'timesheet':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'timesheet';
				unset($query['id_workgroup']);
				break;
			case 'timesheet_manage':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'times';
				unset($query['id_workgroup']);
				break;
			case 'timesheet_edit':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'times_edit';
				unset($query['id_workgroup']);
				break;

			// TROUBLESHOOTER
			case 'troubleshooter':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'troubleshooter';
				unset($query['id_workgroup']);
				break;

			// MY
			case 'my_kb':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'my-articles';
				unset($query['id_workgroup']);
				break;
			case 'my_bookmark':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'my-bookmarks';
				unset($query['id_workgroup']);
				break;
			case 'my_delbookmark':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'remove-bookmark';
				unset($query['id_workgroup']);
				break;
			case 'my_downloads':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'my-downloads';
				unset($query['id_workgroup']);
				break;

			// BUGTRACKER
			case 'bugtracker':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'bugtracking';
				unset($query['id_workgroup']);
				break;
			case 'bugtracker_view':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'bugtracker';
				$segments[] = ObtainBugtracker($query['id']);
				unset($query['id_workgroup']);
				unset($query['id']);
				break;
			case 'bugtracker_post':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'bugtracker';
				$segments[] = 'post';
				unset($query['id_workgroup']);
				break;
			case 'bugtracker_download':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'bugtracker';
				$segments[] = 'attachment';
				unset($query['id_workgroup']);
				break;

			// 3RD PARTY
			case 'discussions_customiit':
				$segments[] = ObtainWK($query['id_workgroup']);
				$segments[] = 'discussions';
				$segments[] = 'customiit';
				unset($query['id_category']);
				unset($query['id_workgroup']);
				break;

			default:
				$segments[] = $query['task'];
				break;
		}
		unset($query['task']);
	}

	return $segments;
}

/**
 * Method to parse Route
 * @param array $segments
 */
function MaQmaHelpdeskParseRoute($segments)
{
	$vars = array();

	$end_segment = preg_replace('/:/', '-', end($segments), 1);

	for ($i = 0; $i < count($segments); $i++) {
		$segments[$i] = preg_replace('/:/', '-', $segments[$i], 1);
	}

	switch ($segments[1])
	{
		// AJAX
		case 'asreply':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['task'] = 'ajax_asreply';
			break;
		case 'javascript':
			$vars['task'] = 'ajax_javascript';
			break;

		// ANNOUNCEMENTS
		case 'announcements':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['task'] = 'announce_list';
			break;
		case 'announcement':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['id'] = ObtainAnnouncement($segments[2], 'title');
			$vars['task'] = 'announce_view';
			break;

		// PUBLIC DISCUSSIONS
		case 'discussions':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			if (isset($segments[2]) && $segments[2] == 'category') {
				$vars['id_category'] = ObtainCategory($segments[3], 'title');
				$vars['task'] = 'discussions_category';
			} elseif (isset($segments[2]) && $segments[2] == 'leaderboard') {
				$vars['id_category'] = ObtainCategory($segments[3], 'title');
				$vars['task'] = 'discussions_leaderboard';
			} else {
				$vars['task'] = 'discussions';
			}
			break;
		case 'discussion':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			if (isset($segments[2]) && $segments[2] != 'post' && $segments[2] != 'delete') {
				$vars['id'] = ObtainDiscussion($segments[2], 'title');
				$vars['task'] = 'discussions_view';
			}elseif (isset($segments[2]) && $segments[2] == 'delete') {
				$vars['id'] = ObtainDiscussion($segments[2], 'title');
				$vars['task'] = 'discussions_delete';
			} else {
				$vars['task'] = 'discussions_question';
			}
			break;

		// BUGTRACKER
		case 'bugtracking':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['task'] = 'bugtracker';
			break;
		case 'bugtracker':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			if (isset($segments[2]) && $segments[2] != 'post' && $segments[2] != 'attachment') {
				$vars['id'] = ObtainBugtracker($segments[2], 'title');
				$vars['task'] = 'bugtracker_view';
			} elseif (isset($segments[2]) && $segments[2] == 'attachment') {
				$vars['task'] = 'bugtracker_download';
			} else {
				$vars['task'] = 'bugtracker_post';
			}
			break;

		// KB
		case 'kb':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			if (isset($segments[2])) {
				if ($segments[2] == 'article') {
					$vars['task'] = 'kb_view';
					$vars['id'] = ObtainKB($segments[3], 'title');
				} elseif ($segments[2] == 'edit') {
					$vars['task'] = 'kb_edit';
				} elseif ($segments[2] == 'new') {
					$vars['task'] = 'kb_new';
				} elseif ($segments[2] == 'bookmark') {
					$vars['task'] = 'kb_bookmark';
				} elseif ($segments[2] == 'convert') {
					$vars['task'] = 'kb_convert';
				} elseif ($segments[2] == 'attachment') {
					$vars['task'] = 'kb_download';
				} else {
					$vars['parent'] = ObtainCategory($segments[2], 'title');
					$vars['task'] = 'kb_list';
				}
			} else {
				$vars['task'] = 'kb_list';
			}
			break;

		// TICKETS
		case 'tickets':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			if ($segments[2] == 'manager') {
				$vars['task'] = 'ticket_my';
			} elseif ($segments[2] == 'report') {
				$vars['task'] = 'ticket_report';
			} elseif ($segments[2] == 'analysis') {
				$vars['task'] = 'ticket_analysis';
			} elseif ($segments[2] == 'attachment') {
				$vars['task'] = 'ticket_download';
			} elseif ($segments[2] == 'views') {
				$vars['task'] = 'ticket_views';
			}
			break;
		case 'ticket':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			if ($segments[2] == 'create') {
				$vars['task'] = 'ticket_new';
			} elseif ($segments[2] == 'duplicate') {
				$vars['task'] = 'ticket_duplicate';
			} elseif ($segments[2] == 'bookmark') {
				$vars['task'] = 'ticket_bookmark';
			} elseif ($segments[2] == 'delete') {
				$vars['task'] = 'ticket_delete';
			} elseif ($segments[2] == 'parent') {
				$vars['task'] = 'ticket_parent';
			} elseif ($segments[2] == 'approve') {
				$vars['task'] = 'ticket_approve';
			} elseif ($segments[2] == 'delattach') {
				$vars['task'] = 'ticket_delattach';
			} else {
				$vars['id'] = ObtainTicket($segments[2], 'title');
				$vars['task'] = 'ticket_view';
			}
			break;

		// GLOSSARY
		case 'glossary':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			if (isset($segments[2])) {
				if ($segments[2] == 'add') {
					$vars['task'] = 'glossary_add';
				} elseif ($segments[2] == 'edit') {
					$vars['task'] = 'glossary_edit';
				} elseif ($segments[2] == 'category') {
					$vars['id_category'] = ObtainCategory($segments[3], 'title');
					$vars['task'] = 'glossary_category';
				} else {
					$vars['task'] = 'glossary';
				}
			} else {
				$vars['task'] = 'glossary';
			}
			break;

		// DOWNLOADS
		case 'downloads':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			if (isset($segments[2]) && $segments[2] == 'category') {
				$vars['id'] = ObtainDownloadsCategory($segments[3], 'title');
				$vars['task'] = 'downloads_category';
			} elseif (isset($segments[2]) && $segments[2] == 'product') {
				$vars['id'] = ObtainDownloads($segments[3], 'title');
				$vars['task'] = 'downloads_product';
			} else {
				$vars['task'] = 'downloads';
			}
			break;
		case 'subscriptions':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['task'] = 'downloads_subscriptions';
			break;
		case 'license':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['task'] = 'downloads_license';
			$vars['id'] = ObtainLicense($segments[2], 'title');
			break;
		case 'subscribe':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['task'] = 'downloads_subscribe';
			$vars['id'] = ObtainDownloads($segments[2], 'title');
			break;
		case 'download':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['task'] = 'downloads_getfile';
			break;
		case 'unsubscribe':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['task'] = 'downloads_unsubscribe';
			break;

		// CLIENTS
		case 'clients':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['task'] = 'client_list';
			break;
		case 'client':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['id'] = ObtainClient($segments[2], 'title');
			$vars['task'] = 'client_view';
			break;
		case 'clientdownload':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['task'] = 'client_download';
			break;

		// TASKS
		case 'tasks':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			if ($segments[2] == 'add') {
				$vars['task'] = 'calendar_add';
			} elseif ($segments[2] == 'edit') {
				$vars['task'] = 'calendar_edit';
			} elseif ($segments[2] == 'monthly') {
				$vars['task'] = 'calendar_view';
			} elseif ($segments[2] == 'weekly') {
				$vars['task'] = 'calendar_week';
			} elseif ($segments[2] == 'daily') {
				$vars['task'] = 'calendar_day';
			} else {
				$vars['task'] = 'calendar_list';
			}
			break;

		// USERS
		case 'profile':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['task'] = 'users_profile';
			break;
		case 'details':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['task'] = 'users_getuserdetails';
			break;

		// FAQ
		case 'faq':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			if (isset($segments[2])) {
				$vars['parent'] = ObtainCategory($segments[2], 'title');
			}
			$vars['task'] = 'kb_faq';
			break;

		// TIMESHEET
		case 'timesheet':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['task'] = 'timesheet';
			break;
		case 'times':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['task'] = 'timesheet_manage';
			break;
		case 'times_edit':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['task'] = 'timesheet_edit';
			break;

		// TROUBLESHOOTER
		case 'troubleshooter':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['task'] = 'troubleshooter';
			break;

		// MY
		case 'my-articles':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['task'] = 'my_kb';
			break;
		case 'my-bookmarks':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['task'] = 'my_bookmark';
			break;
		case 'remove-bookmark':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['task'] = 'my_delbookmark';
			break;
		case 'my-downloads':
			$vars['id_workgroup'] = ObtainWK($segments[0], 'title');
			$vars['task'] = 'my_downloads';
			break;

		// OTHERS (WORKGROUP OPTIONS)
		default:
			if ($segments[0] == 'department') {
				$vars['id_workgroup'] = ObtainWK($segments[1], 'title');
				$vars['task'] = 'workgroup_view';
			}
			break;
	}

	return $vars;
}

function ObtainWK($param, $what = '')
{
	$database = JFactory::getDBO();
	$param = str_replace(':', '-', $param);

	if ($what == 'title') {
		$sql = "SELECT `id` FROM #__support_workgroup WHERE `slug`=" . $database->quote($param);
	} else {
		$sql = "SELECT `slug` FROM #__support_workgroup WHERE `id`=" . $database->quote($param);
	}
	$database->setQuery($sql);
	$return = $database->loadResult();

	// No slug created so lets do it now
	if ($return == '' && $what == '') {
		$sql = "SELECT `wkdesc`
				FROM #__support_workgroup 
				WHERE `id`=" . $database->quote($param);
		$database->setQuery($sql);
		$return = $database->loadResult();
		$return = HelpdeskUtility::CreateSlug($return);

		$sql = "UPDATE `#__support_workgroup`
				SET `slug`=" . $database->quote($return) . "
				WHERE `id`=" . $database->quote($param);
		$database->setQuery($sql);
		$database->query();
	}

	return $return;
}

function ObtainAnnouncement($param, $what = '')
{
	$database = JFactory::getDBO();
	$param = str_replace(':', '-', $param);

	if ($what == 'title') {
		$sql = "SELECT `id` FROM #__support_announce WHERE `slug`=" . $database->quote($param);
	} else {
		$sql = "SELECT `slug` FROM #__support_announce WHERE `id`=" . $database->quote($param);
	}
	$database->setQuery($sql);
	$return = $database->loadResult();

	// No slug created so lets do it now
	if ($return == '' && $what == '') {
		$sql = "SELECT `introtext`
				FROM #__support_announce 
				WHERE `id`=" . $database->quote($param);
		$database->setQuery($sql);
		$return = $database->loadResult();
		$return = HelpdeskUtility::CreateSlug($return);

		$sql = "UPDATE `#__support_announce`
				SET `slug`=" . $database->quote($return) . "
				WHERE `id`=" . $database->quote($param);
		$database->setQuery($sql);
		$database->query();
	}

	return $return;
}

function ObtainKB($param, $what = '')
{
	$database = JFactory::getDBO();
	$param = str_replace(':', '-', $param);

	if ($what == 'title') {
		$sql = "SELECT `id` FROM #__support_kb WHERE `slug`=" . $database->quote($param);
	} else {
		$sql = "SELECT `slug` FROM #__support_kb WHERE `id`=" . $database->quote($param);
	}
	$database->setQuery($sql);
	$return = $database->loadResult();

	// No slug created so lets do it now
	if ($return == '' && $what == '') {
		$sql = "SELECT `kbtitle`
				FROM #__support_kb 
				WHERE `id`=" . $database->quote($param);
		$database->setQuery($sql);
		$return = $database->loadResult();
		$return = HelpdeskUtility::CreateSlug($return);

		$sql = "UPDATE `#__support_kb`
				SET `slug`=" . $database->quote($return) . "
				WHERE `id`=" . $database->quote($param);
		$database->setQuery($sql);
		$database->query();
	}

	return $return;
}

function ObtainCategory($param, $what = '')
{
	$database = JFactory::getDBO();
	$param = str_replace(':', '-', $param);

	if ($what == 'title') {
		$sql = "SELECT `id` FROM #__support_category WHERE `slug`=" . $database->quote($param);
	} else {
		$sql = "SELECT `slug` FROM #__support_category WHERE `id`=" . $database->quote($param);
	}
	$database->setQuery($sql);
	$return = $database->loadResult();

	// No slug created so lets do it now
	if ($return == '' && $what == '') {
		$sql = "SELECT `name`
				FROM #__support_category 
				WHERE `id`=" . $database->quote($param);
		$database->setQuery($sql);
		$return = $database->loadResult();
		$return = HelpdeskUtility::CreateSlug($return);

		$sql = "UPDATE `#__support_category`
				SET `slug`=" . $database->quote($return) . "
				WHERE `id`=" . $database->quote($param);
		$database->setQuery($sql);
		$database->query();
	}

	return $return;
}

function ObtainClient($param, $what = '')
{
	$database = JFactory::getDBO();
	$param = str_replace(':', '-', $param);

	if ($what == 'title') {
		$sql = "SELECT `id` FROM #__support_client WHERE `slug`=" . $database->quote($param);
	} else {
		$sql = "SELECT `slug` FROM #__support_client WHERE `id`=" . $database->quote($param);
	}
	$database->setQuery($sql);
	$return = $database->loadResult();

	// No slug created so lets do it now
	if ($return == '' && $what == '') {
		$sql = "SELECT `clientname`
				FROM #__support_client 
				WHERE `id`=" . $database->quote($param);
		$database->setQuery($sql);
		$return = $database->loadResult();
		$return = HelpdeskUtility::CreateSlug($return);

		$sql = "UPDATE `#__support_client`
				SET `slug`=" . $database->quote($return) . "
				WHERE `id`=" . $database->quote($param);
		$database->setQuery($sql);
		$database->query();
	}

	return $return;
}

function ObtainDownloadsCategory($param, $what = '')
{
	$database = JFactory::getDBO();
	$param = str_replace(':', '-', $param);

	if ($what == 'title') {
		$sql = "SELECT `id` FROM #__support_dl_category WHERE `slug`=" . $database->quote($param);
	} else {
		$sql = "SELECT `slug` FROM #__support_dl_category WHERE `id`=" . $database->quote($param);
	}
	$database->setQuery($sql);
	$return = $database->loadResult();

	// No slug created so lets do it now
	if ($return == '' && $what == '') {
		$sql = "SELECT `cname`
				FROM #__support_dl_category 
				WHERE `id`=" . $database->quote($param);
		$database->setQuery($sql);
		$return = $database->loadResult();
		$return = HelpdeskUtility::CreateSlug($return);

		$sql = "UPDATE `#__support_dl_category`
				SET `slug`=" . $database->quote($return) . "
				WHERE `id`=" . $database->quote($param);
		$database->setQuery($sql);
		$database->query();
	}

	return $return;
}

function ObtainDownloads($param, $what = '')
{
	$database = JFactory::getDBO();
	$param = str_replace(':', '-', $param);

	if ($what == 'title') {
		$sql = "SELECT `id` FROM #__support_dl WHERE `slug`=" . $database->quote($param);
	} else {
		$sql = "SELECT `slug` FROM #__support_dl WHERE `id`=" . $database->quote($param);
	}
	$database->setQuery($sql);
	$return = $database->loadResult();

	// No slug created so lets do it now
	if ($return == '' && $what == '') {
		$sql = "SELECT d.`pname` , c.`cname`
				FROM #__support_dl AS d
					 INNER JOIN #__support_dl_category AS c ON c.`id`=d.`id_category`
				WHERE d.`id`=" . $database->quote($param);
		$database->setQuery($sql);
		$return = $database->loadObject();
		$return = HelpdeskUtility::CreateSlug($return->cname . '-' . $return->pname);

		$sql = "UPDATE `#__support_dl`
				SET `slug`=" . $database->quote($return) . "
				WHERE `id`=" . $database->quote($param);
		$database->setQuery($sql);
		$database->query();
	}

	return $return;
}

function ObtainDiscussion($param, $what = '')
{
	$database = JFactory::getDBO();
	$param = str_replace(':', '-', $param);

	if ($what == 'title') {
		$sql = "SELECT `id` FROM #__support_discussions WHERE `slug`=" . $database->quote($param);
	} else {
		$sql = "SELECT `slug` FROM #__support_discussions WHERE `id`=" . $database->quote($param);
	}
	$database->setQuery($sql);
	$return = $database->loadResult();

	// No slug created so lets do it now
	if ($return == '' && $what == '') {
		$sql = "SELECT `title`
				FROM #__support_discussions 
				WHERE `id`=" . $database->quote($param);
		$database->setQuery($sql);
		$return = $database->loadResult();
		$return = HelpdeskUtility::CreateSlug($return);
		if ($return == '')
		{
			$return = $param;
		}

		$sql = "UPDATE `#__support_discussions`
				SET `slug`=" . $database->quote($return) . "
				WHERE `id`=" . $database->quote($param);
		$database->setQuery($sql);
		$database->query();
	}

	return $return;
}

function ObtainTicket($param, $what = '')
{
	$database = JFactory::getDBO();
	$param = str_replace(':', '-', $param);

	if ($what == 'title') {
		$sql = "SELECT `id` FROM #__support_ticket WHERE `ticketmask`=" . $database->quote($param);
	} else {
		$sql = "SELECT `ticketmask` FROM #__support_ticket WHERE `id`=" . $database->quote($param);
	}
	$database->setQuery($sql);
	$return = $database->loadResult();

	return $return;
}

function ObtainLicense($param, $what = '')
{
	$database = JFactory::getDBO();
	$param = str_replace(':', '-', $param);

	if ($what == 'title') {
		$sql = "SELECT `id` FROM #__support_dl_license WHERE `slug`=" . $database->quote($param);
	} else {
		$sql = "SELECT `slug` FROM #__support_dl_license WHERE `id`=" . $database->quote($param);
	}
	$database->setQuery($sql);
	$return = $database->loadResult();

	// No slug created so lets do it now
	if ($return == '' && $what == '') {
		$sql = "SELECT `title`
				FROM #__support_dl_license 
				WHERE `id`=" . $database->quote($param);
		$database->setQuery($sql);
		$return = $database->loadResult();
		$return = HelpdeskUtility::CreateSlug($return);

		$sql = "UPDATE `#__support_dl_license`
				SET `slug`=" . $database->quote($return) . "
				WHERE `id`=" . $database->quote($param);
		$database->setQuery($sql);
		$database->query();
	}

	return $return;
}

function ObtainBugtracker($param, $what = '')
{
	$database = JFactory::getDBO();
	$param = str_replace(':', '-', $param);

	if ($what == 'title') {
		$sql = "SELECT `id` FROM #__support_bugtracker WHERE `slug`=" . $database->quote($param);
	} else {
		$sql = "SELECT `slug` FROM #__support_bugtracker WHERE `id`=" . $database->quote($param);
	}
	$database->setQuery($sql);
	$return = $database->loadResult();

	// No slug created so lets do it now
	if ($return == '' && $what == '') {
		$sql = "SELECT CONCAT(`type`, '-', `id`)
				FROM #__support_bugtracker 
				WHERE `id`=" . $database->quote($param);
		$database->setQuery($sql);
		$return = $database->loadResult();
		$return = HelpdeskUtility::CreateSlug($return);

		$sql = "UPDATE `#__support_bugtracker`
				SET `slug`=" . $database->quote($return) . "
				WHERE `id`=" . $database->quote($param);
		$database->setQuery($sql);
		$database->query();
	}

	return $return;
}
