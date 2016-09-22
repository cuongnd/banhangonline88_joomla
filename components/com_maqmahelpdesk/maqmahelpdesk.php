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

JHTML::_('behavior.formvalidation');

// Include helpers
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/category.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/contract.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/date.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/department.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/mobile.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/template.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/user.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/utility.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/validation.php';

$mainframe = JFactory::getApplication();
$document = JFactory::getDocument();
$session = JFactory::getSession();
$database = JFactory::getDBO();
$user = JFactory::getUser();

$appParams = $mainframe->getParams();
$registry = new JRegistry($appParams);
$params = $registry->toArray();
$show_toolbar = isset($params["data"]["show_toolbar"]) ? (int) $params["data"]["show_toolbar"] : 1;
$id_group = isset($params["data"]["id_group"]) ? (int) $params["data"]["id_group"] : 0;
JRequest::setVar('id_group', $id_group, 'post');
JRequest::setVar('id_group', $id_group, 'get');

// Include language file
$lang = JFactory::getLanguage();
$language_tag = $lang->getTag();
$lang->load('com_maqmahelpdesk', JPATH_SITE . '/administrator/languages', $language_tag, true);
$lang->load('com_maqmahelpdesk', JPATH_SITE . '/components/com_maqmahelpdesk/language', $language_tag, true);

// Get variables
$format = JRequest::getVar('format', '', '', 'string');
$tmpl = JRequest::getWord('tmpl', '');
$Itemid = JRequest::getInt('Itemid', 0);
$task = JRequest::getCmd('task', '', '', 'string');
$view = JRequest::getCmd('view', '', '', 'string');
$id_workgroup = JRequest::getInt('id_workgroup', 0);
$msg = urldecode(JRequest::getVar('msg', '', '', 'string'));
$msgtype = JRequest::getVar('msgtype', '', '', 'string');

// Specific menu
if ($task == '' && $view != 'mainpage')
{
	JRequest::setVar('task', $view);
	$task = $view;
	$id_workgroup = (int) $params["data"]["id_workgroup"];
	JRequest::setVar('id_workgroup', $id_workgroup, 'post');
	JRequest::setVar('id_workgroup', $id_workgroup, 'get');
}

if ($task == 'rss' || $task == 'discussions_rss' || $task == 'activities_rss')
{
	require_once(JPATH_SITE . '/components/com_maqmahelpdesk/' . $task . '.php');
	return;
}

@list($function, $task) = explode('_', $task, 2);

// Get config
$supportConfig = HelpdeskUtility::GetConfig();
$GLOBALS['resources_header'] = array();

// Date localization
if ($supportConfig->date_country_code != '')
{
	setlocale(LC_ALL, $supportConfig->date_country_code);
}

// Validate if profile must be complete
if ($id_workgroup && $task != 'profile' && $task != 'saveuseredit')
{
	HelpdeskValidation::ProfileRequired();
}

// Set our location as front end for the global messages, f=front end, a=administration
$GLOBALS['sysmsgs_user_location'] = 'f';
if ($task != 'download' && $task != 'getfile' && $format != 'pdf' && $function != 'ajax' && $task != 'checkticket' && $format != 'raw')
{
	$jquery_url = HelpdeskUtility::GetJQueryURL();
	HelpdeskUtility::AppendResource('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&task=ajax_javascript&format=raw&tmpl=component', JURI::root() . '', 'js', true);
	if ($tmpl == 'component' && $jquery_url == '')
	{
		HelpdeskUtility::AppendResource('jquery-1.8.3.min.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	}
	elseif ($jquery_url != '')
	{
		HelpdeskUtility::AppendResource('', $jquery_url, 'js', true);
	}
	HelpdeskUtility::AppendResource('jmaqma.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	HelpdeskUtility::AppendResource('prettify.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	HelpdeskUtility::AppendResource('replies.js', JURI::root() . 'media/com_maqmahelpdesk/js/redactor/', 'js', true);
	HelpdeskUtility::AppendResource('redactor.min.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	HelpdeskUtility::AppendResource('maqmahelpdesk.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);

	if ($supportConfig->include_bootstrap)
	{
		HelpdeskUtility::AppendResource('bootstrap.min.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
		if ($lang->isRTL())
		{
			HelpdeskUtility::AppendResource('bootstrap_rtl.css', JURI::root() . 'media/com_maqmahelpdesk/css/', 'css');
		}
		else
		{
			HelpdeskUtility::AppendResource('bootstrap.min.css', JURI::root() . 'media/com_maqmahelpdesk/css/', 'css');
			HelpdeskUtility::AppendResource('bootstrap-responsive.min.css', JURI::root() . 'media/com_maqmahelpdesk/css/', 'css');
			HelpdeskUtility::AppendResource('bootstrap-override.min.css', JURI::root() . 'media/com_maqmahelpdesk/css/', 'css');
		}
	}

	if (!HelpdeskUtility::JoomlaCheck())
	{
		HelpdeskUtility::AppendResource('bootstrap-j25.min.css', JURI::root() . 'media/com_maqmahelpdesk/css/', 'css');
	}
	else
	{
		HelpdeskUtility::AppendResource('bootstrap-j30.min.css', JURI::root() . 'media/com_maqmahelpdesk/css/', 'css');
	}

	HelpdeskUtility::AppendResource('redactor.css', JURI::root() . 'media/com_maqmahelpdesk/css/', 'css');
}

$is_support = 0; // Indicates if the user is from the support staff
$is_manager = 0; // Indicates if the user if manager from Support or from a Client
$is_client = 0; // Indicates the ID of the client if the user belongs to a Client
$clientWK = 0; // Indicates if the client user has access to the workgroup
$supportWK = 0; // Indicates if the support user has access to the workgroup
$supportOptions = null; // Gets the user permissions for the workgroup
$clientOptions = null; // Gets the user info
$usertype = ''; // Client user = 1; Client Mgr = 2; Support user = 5; Support team leader = 6; Support mgr = 7;

if ($user->id == 0 && $supportConfig->unregister == 0 && $function != 'forms' && $function != 'ajax' && $function != 'addon')
{
	$task = 'noaccess';
	$function = 'warning';
	HelpdeskValidation::NoAccessQuit();
}
else
{
	$is_support = HelpdeskUser::IsSupport();
	$is_client = HelpdeskUser::IsClient();

	if ($is_support > 0)
	{
		if ($is_client > 0)
		{
			if ($task != 'download' && $task != 'getfile' && $format != 'pdf' && $function != 'ajax' && $task != 'checkticket' && $format != 'raw')
			{
				HelpdeskUtility::ShowSCMessage(JText::_('client_and_support'), 'e');
			}
			$is_client = 0;
		}
	}
}

// Workgroup permissions
$workgroupSettings = null;
if ($id_workgroup)
{
	// Get the settings of the Workgroup
	$workgroupSettings = HelpdeskDepartment::GetSettings();

	// If user is from support gets his permissions to the workgroup
	if ($is_support)
	{
		$supportWK = 0;
		$sql = "SELECT COUNT(*)
				FROM #__support_permission
				WHERE id_user=" . $user->id . "
				  AND id_workgroup=" . $id_workgroup;
		$database->setQuery($sql);
		$supportWK = $database->loadResult();
		if (!$supportWK)
		{
			$task = 'noaccess';
			$function = 'warning';
			HelpdeskValidation::NoAccessQuit();
		}

		$sql = "SELECT p.manager
				FROM #__support_permission p, #__support_workgroup w
				WHERE p.id_user=" . $user->id . "
				  AND p.id_workgroup=" . $id_workgroup . "
				  AND p.id_workgroup=w.id";
		$database->setQuery($sql);
		$supportOptions = null;
		$supportOptions = $database->loadObject();
		if ($supportOptions)
		{
			$is_manager = $supportOptions->manager;
			$usertype = $is_manager; //Set support staff user type (Support user = 5; Support team leader = 6; Support mgr = 7;)
		}
	}

	// If user is a client gets his permissions to the workgroup and his info
	if ($is_client)
	{
		// Get the user information
		$sql = "SELECT cu.id_client, cu.manager, u.phone, u.fax, u.mobile
				FROM #__support_client_users cu
					 INNER JOIN #__support_client c ON c.id=cu.id_client
					 LEFT JOIN #__support_users u ON u.id_user=cu.id_user
				WHERE cu.id_user=" . (int) $user->id;
		$database->setQuery($sql);
		$clientOptions = null;
		$clientOptions = $database->loadObject();
		$is_manager = $clientOptions->manager;
		$client_id = $clientOptions->id_client;
		$GLOBALS['client_id'] = $client_id;

		$is_manager ? $usertype = 2 : $usertype = 1; //Set usetype to Client manager or Client user

		// Check if the client have access to the workgroup
		$sql = "SELECT COUNT(*)
				FROM `#__support_client_wk` c
					 LEFT JOIN `#__support_workgroup` w ON w.`id`=c.`id_workgroup` 
				WHERE c.`id_client`=" . (int)$client_id . "
				  AND ( c.`id_workgroup`=" . (int)$id_workgroup . " OR c.`id_workgroup`=0 )";
		$database->setQuery($sql);
		$clientWK = $database->loadResult();
		if (!$clientWK)
		{
			$task = 'noaccess';
			$function = 'warning';
			HelpdeskValidation::NoAccessQuit();
		}
		
		// Check if client is blocked
		$database->setQuery("SELECT block FROM #__support_client WHERE id='" . $is_client . "'");
		if ($database->loadResult() == 1 && $task!='noaccess')
		{
			$task = 'noaccess';
			$function = 'warning';
			HelpdeskValidation::NoAccessQuit();
		}

		// The user doesn't belong to any client and the access to the workgroup
		// will be checked by returning if there are records in the restriction of this
		// workgroup with clients, if there is then the user don't have access to the
		// workgroup, if there's no records then the access is allowed except if it's only for
		// contracts. Only users from clients can have contracts.
	}
	else
	{
		if ($workgroupSettings)
		{
			if ($workgroupSettings->contract && $is_client > 0)
			{
				$task = 'nocontract';
				$function = 'warning';
				HelpdeskValidation::NoAccessQuit();
			}

			// Check if there is joomla group restriction
			// ...
		}
	}
}

// Check the contract information
if (isset($workgroupSettings) && $workgroupSettings->contract && $task!='noaccess' && !$is_support)
{
	// Not logged don't allow access
	if (!$user->id && $workgroupSettings->contract_total_disable)
	{
		$task = 'nocontract';
		$function = 'warning';
		HelpdeskValidation::NoAccessQuit();
	}
	// Try to auto-enable a contract
	if (!HelpdeskContract::IsValid($user->id))
	{
		HelpdeskContract::MakeActive($is_client, 0);
	}
	// Shows a warning message if no valid contract but still have access
	if (!HelpdeskContract::IsValid($user->id) && !$workgroupSettings->contract_total_disable)
	{
		if ($task != 'download' && $task != 'getfile' && $task != 'ticket_replieseditor' && $task != 'downloads_editor' && $format != 'pdf' && $function != 'ajax' && $task != 'checkticket' && $format != 'raw')
		{
			HelpdeskUtility::ShowSCMessage(JText::_('wkcontractwarn'), 'w');
		}
	}
	// If access is completelly blocked block it
	if (!HelpdeskContract::IsValid($user->id) && $workgroupSettings->contract_total_disable && $task!='noaccess' && $task!='nocontract' && !$is_support)
	{
		$task = 'nocontract';
		$function = 'warning';
		HelpdeskValidation::NoAccessQuit();
	}
	// Show contract progress
	if ($task != 'download' && $task != 'getfile' && $format != 'pdf' && $function != 'ajax' && $task != 'checkticket' && $format != 'raw')
	{
		HelpdeskContract::ShowProgressbar();
	}
}

// Globals
$GLOBALS['is_manager'] = $is_manager;

HelpdeskDepartment::Get();

// Set usertype to 1 for users who do not belong to a client or support 
if (!$is_client && !$is_support && $user->id > 0)
{
	$usertype = 1;
}

// Check mobile
$mcheck = new HelpdeskMobile();
if ($supportConfig->mobile_interface && $mcheck->isMobile() && !$mcheck->isTablet())
{
	$session->set("helpdesk_mobile", true);
	// May need to recall the page with format=raw
	if ($format != 'raw')
	{
		//$mainframe->redirect("index.php?option=com_maqmahelpdesk&Itemid=" . $Itemid . "&format=raw");
	}
}
else
{
	$session->set("helpdesk_mobile", false);
}

$GLOBALS['clientOptions'] = $clientOptions;
$GLOBALS['usertype'] = $usertype;
$GLOBALS['show_toolbar'] = $show_toolbar;

// Shows message if there's any
if ($msg != '' && $msgtype != '')
{
	HelpdeskUtility::ShowSCMessage($msg, $msgtype);
}
elseif ($msg != '' && $msgtype == '')
{
	HelpdeskUtility::ShowTplMessage($msg, $id_workgroup);
}

// Display global system messages
if ($function != 'ajax')
{
	HelpdeskUtility::GetGlobalMessage();
}

if (isset($supportOptions)) $GLOBALS['supportOptions'] = $supportOptions;
if (isset($id_workgroup)) $GLOBALS['id_workgroup'] = $id_workgroup;
if (isset($userInfo)) $GLOBALS['userInfo'] = $userInfo;

// Toolbar check
if (isset($workgroupSettings->theme))
{
	if (file_exists(JPATH_SITE . '/media/com_maqmahelpdesk/templates/' . $workgroupSettings->theme . '/toolbar.php'))
	{
		require_once JPATH_SITE . '/media/com_maqmahelpdesk/templates/' . $workgroupSettings->theme . '/toolbar.php';
	}
	else
	{
		require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/toolbar.php';
	}
}
else
{
	require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/toolbar.php';
}

// Calls the required function page
if ($task != 'noaccess' && $task != 'nocontract')
{
	require_once JPATH_SITE . "/components/com_maqmahelpdesk/functions/" . ($function != '' ? $function : 'workgroup') . ".php";
}
elseif($task == 'nocontract' && !$workgroupSettings->contract_total_disable)
{
	require_once JPATH_SITE . "/components/com_maqmahelpdesk/functions/" . ($function != '' ? $function : 'workgroup') . ".php";
}

if (!$supportConfig->hide_powered && $task != 'getfile' && $function != 'ajax' && $tmpl != 'component' && $format != 'raw')
{
	$powered_msg = '<p style="text-align:center; width:100%; font-size:10px; margin-top:20px;"><a href="http://www.imaqma.com/joomla/helpdesk-component.html" title="MaQma Helpdesk component for Joomla!" target="_blank">Powered by <strong>MaQma Helpdesk</strong></a></p>';
	echo $powered_msg;
}

if (isset($workgroupSettings) && $task != 'getfile' && $function != 'ajax' && $task != 'checkticket' && $format != 'raw' && $task != 'noaccess')
{
	if ($lang->isRTL())
	{
		if (file_exists(JPATH_SITE . '/media/com_maqmahelpdesk/templates/' . $workgroupSettings->theme . '/css/style_rtl.css'))
		{
			HelpdeskUtility::AppendResource('style_rtl.css', JURI::root() . 'media/com_maqmahelpdesk/templates/' . $workgroupSettings->theme . '/css/', 'css');
		}
		else
		{
			HelpdeskUtility::AppendResource('style_rtl.css', JURI::root() . 'media/com_maqmahelpdesk/templates/default/css/', 'css');
		}
		if ($mcheck->isMobile() && !$mcheck->isTablet() && $supportConfig->mobile_interface)
		{
			if (file_exists(JPATH_SITE . '/media/com_maqmahelpdesk/templates/' . $workgroupSettings->theme . '/css/mobile_rtl.css'))
			{
				HelpdeskUtility::AppendResource('mobile_rtl.css', JURI::root() . 'media/com_maqmahelpdesk/templates/' . $workgroupSettings->theme . '/css/', 'css');
			}
			else
			{
				HelpdeskUtility::AppendResource('mobile_rtl.css', JURI::root() . 'media/com_maqmahelpdesk/templates/default/css/', 'css');
			}
		}
		if (file_exists(JPATH_SITE . '/media/com_maqmahelpdesk/templates/' . $workgroupSettings->theme . '/css/site_rtl.css'))
		{
			HelpdeskUtility::AppendResource('site_rtl.css', JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/css/', 'css');
		}
		else
		{
			HelpdeskUtility::AppendResource('site_rtl.css', JURI::root() . 'media/com_maqmahelpdesk/images/themes/default/css/', 'css');
		}
	}
	else
	{
		if (file_exists(JPATH_SITE . '/media/com_maqmahelpdesk/templates/' . $workgroupSettings->theme . '/css/style.css'))
		{
			HelpdeskUtility::AppendResource('style.css', JURI::root() . 'media/com_maqmahelpdesk/templates/' . $workgroupSettings->theme . '/css/', 'css');
		}
		else
		{
			HelpdeskUtility::AppendResource('style.css', JURI::root() . 'media/com_maqmahelpdesk/templates/default/css/', 'css');
		}
		if ($mcheck->isMobile() && !$mcheck->isTablet() && $supportConfig->mobile_interface)
		{
			if (file_exists(JPATH_SITE . '/media/com_maqmahelpdesk/templates/' . $workgroupSettings->theme . '/css/mobile.css'))
			{
				HelpdeskUtility::AppendResource('mobile.css', JURI::root() . 'media/com_maqmahelpdesk/templates/' . $workgroupSettings->theme . '/css/', 'css');
			}
			else
			{
				HelpdeskUtility::AppendResource('mobile.css', JURI::root() . 'media/com_maqmahelpdesk/templates/default/css/', 'css');
			}
		}
		if (file_exists(JPATH_SITE . '/media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/css/site.css'))
		{
			HelpdeskUtility::AppendResource('site.css', JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/css/', 'css');
		}
		else
		{
			HelpdeskUtility::AppendResource('site.css', JURI::root() . 'media/com_maqmahelpdesk/images/themes/default/css/', 'css');
		}
	}
	HelpdeskUtility::OutputResources();

} elseif (!isset($workgroupSettings) && $task != 'getfile' && file_exists(JPATH_SITE . '/media/com_maqmahelpdesk/templates/default/css/style.css') && $function != 'ajax' && $task != 'checkticket' && $format != 'raw' && $task != 'noaccess') {
	if ($lang->isRTL()) {
		HelpdeskUtility::AppendResource('style_rtl.css', JURI::root() . 'media/com_maqmahelpdesk/templates/default/css/', 'css');
		HelpdeskUtility::AppendResource('site_rtl.css', JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/css/', 'css');
		if ($mcheck->isMobile() && !$mcheck->isTablet())
		{
			HelpdeskUtility::AppendResource('mobile_rtl.css', JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/css/', 'css');
		}
	} else {
		HelpdeskUtility::AppendResource('style.css', JURI::root() . 'media/com_maqmahelpdesk/templates/default/css/', 'css');
		HelpdeskUtility::AppendResource('site.css', JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/css/', 'css');
		if ($mcheck->isMobile() && !$mcheck->isTablet() && $supportConfig->mobile_interface)
		{
			HelpdeskUtility::AppendResource('mobile.css', JURI::root() . 'media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/css/', 'css');
		}
	}
	HelpdeskUtility::OutputResources();
}
