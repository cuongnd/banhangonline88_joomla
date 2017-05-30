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

// Access check
/*if (!JFactory::getUser()->authorise('core.access', 'com_maqmahelpdesk')) {
	return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
}*/

// Include helpers
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/category.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/date.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/form.php';
require_once JPATH_ADMINISTRATOR . '/components/com_maqmahelpdesk/helpers/toolbar.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/utility.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/user.php';
require_once JPATH_SITE . '/components/com_maqmahelpdesk/helpers/validation.php';

// Get config
$supportConfig = HelpdeskUtility::GetConfig();

// Load required javascript
JHTML::_('behavior.formvalidation');
// JHTML::_('behavior.tooltip');
// JHTML::_('behavior.modal', 'a.modal');
jimport('joomla.application.input');
if (HelpdeskUtility::JoomlaCheck())
{
	JHtml::_('bootstrap.framework');
}

$lang = JFactory::getLanguage();
if ($lang->isRTL())
{
	if (!HelpdeskUtility::JoomlaCheck())
	{
		HelpdeskUtility::AppendResource('bootstrap_rtl.css', '../media/com_maqmahelpdesk/css/', 'css');
	}
	HelpdeskUtility::AppendResource('admin_rtl.css', '../media/com_maqmahelpdesk/css/', 'css');
	HelpdeskUtility::AppendResource('admin_rtl.css', '../media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/css/', 'css');
}
else
{
	HelpdeskUtility::AppendResource('bootstrap.min.css', '../media/com_maqmahelpdesk/css/', 'css');
	HelpdeskUtility::AppendResource('bootstrap-responsive.min.css', '../media/com_maqmahelpdesk/css/', 'css');
	if (!HelpdeskUtility::JoomlaCheck())
	{
		HelpdeskUtility::AppendResource('bootstrap-j25.min.css', '../media/com_maqmahelpdesk/css/', 'css');
	}
	else
	{
		HelpdeskUtility::AppendResource('bootstrap-j30.min.css', JURI::root() . 'media/com_maqmahelpdesk/css/', 'css');
	}
	HelpdeskUtility::AppendResource('bootstrap-override.min.css', '../media/com_maqmahelpdesk/css/', 'css');
	HelpdeskUtility::AppendResource('admin.css', '../media/com_maqmahelpdesk/css/', 'css');
	HelpdeskUtility::AppendResource('admin.css', '../media/com_maqmahelpdesk/images/themes/' . $supportConfig->theme_icon . '/css/', 'css');
	HelpdeskUtility::AppendResource('redactor.css', '../media/com_maqmahelpdesk/css/', 'css');
}

$mainframe = JFactory::getApplication();
$document = JFactory::getDocument();
$database = JFactory::getDBO();
$tmpl = JRequest::getCmd('tmpl', '', '', 'string');
$format = JRequest::getCmd('format', '', '', 'string');
$task = JRequest::getWord('task', '', '', 'string');

$lang = JFactory::getLanguage();
$language_tag = $lang->getTag();
$lang->load('com_maqmahelpdesk', JPATH_SITE . '/administrator/languages', $language_tag, true);
$lang->load('com_maqmahelpdesk.info', JPATH_SITE . '/administrator/languages', $language_tag, true);
$lang->load('com_maqmahelpdesk', JPATH_SITE . '/administrator/components/com_maqmahelpdesk', $language_tag, true);
$lang->load('com_maqmahelpdesk', JPATH_SITE . '/components/com_maqmahelpdesk', $language_tag, true);
$lang->load('com_maqmahelpdesk.info', JPATH_SITE . '/components/com_maqmahelpdesk', $language_tag, true);

// Check version
ob_start();
include(JPATH_SITE . '/components/com_maqmahelpdesk/version.txt');
$installed = ob_get_contents();
ob_end_clean();
$current_version = $installed;
$version_updated = true;
$session = JFactory::getSession();
if ($session->get('current_version', '', 'maqmahelpdesk') == '') {
	if (function_exists('curl_init')) {
		$process = curl_init('http://versions.imaqma.com/helpdesk.txt');
		curl_setopt($process, CURLOPT_HEADER, 0);
		curl_setopt($process, CURLOPT_ENCODING, 'gzip');
		curl_setopt($process, CURLOPT_TIMEOUT, 10);
		curl_setopt($process, CURLOPT_RETURNTRANSFER, 1);
		curl_setopt($process, CURLOPT_SSL_VERIFYPEER, false);
		@curl_setopt($process, CURLOPT_FOLLOWLOCATION, 1);
		@curl_setopt($process, CURLOPT_MAXREDIRS, 20);
		$current_version = curl_exec($process);
		curl_close($process);
		$session->set('current_version', $current_version, 'maqmahelpdesk');
	}
} else {
	$current_version = $session->get('current_version', '', 'maqmahelpdesk');
}
if (version_compare($installed, $current_version) < 0) {
	$version_updated = false;
}

if ($task != 'client_download' && $task != 'tickets_download' && $task != 'product_download' && $task != 'kb_download' && $tmpl != 'component' && $format != 'raw')
{
	$jquery_url = HelpdeskUtility::GetJQueryURL();
	$document->addScriptDeclaration('var LOADJQUERY = "true";');
	$document->addScriptDeclaration('var SITEURL = "' . JURI::root() . '";');
	$document->addScriptDeclaration('var IMQM_ICON_THEME = "' . $supportConfig->theme_icon . '";');
	$document->addScriptDeclaration('var IMQM_JOOMLA_VERSION = ' . (HelpdeskUtility::JoomlaCheck() ? 'true' : 'false') . ';');
	$document->addScriptDeclaration('var IMQM_REPLIES_TITLE = "' . JText::_("PREDEFINED_REPLIES") . '";');
	if ($jquery_url != '')
	{
		HelpdeskUtility::AppendResource('', $jquery_url, 'js', true);
	}
	elseif (!HelpdeskUtility::JoomlaCheck())
	{
		HelpdeskUtility::AppendResource('jquery-1.8.3.min.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	}
	HelpdeskUtility::AppendResource('jmaqma.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	//if (!HelpdeskUtility::JoomlaCheck())
	//{
		HelpdeskUtility::AppendResource('bootstrap.min.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	//}
	HelpdeskUtility::AppendResource('maqma.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	HelpdeskUtility::AppendResource('prettify.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	HelpdeskUtility::AppendResource('replies.js', JURI::root() . 'media/com_maqmahelpdesk/js/redactor/', 'js', true);
	HelpdeskUtility::AppendResource('redactor.min.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
	HelpdeskUtility::AppendResource('maqmahelpdesk.js', JURI::root() . 'media/com_maqmahelpdesk/js/', 'js', true);
}

// Set our location as front end for the global messages, f=front end, a=administration
$GLOBALS['sysmsgs_user_location'] = 'a';

// Get task and function
$task = JRequest::getCmd('task', '', '', 'string');
@list($function, $task) = explode('_', $task, 2);

if ($task == '') {
	$task = $function;
}

// Create a variable that saves the functions folder
$base_path = JPATH_SITE . "/administrator/components/com_maqmahelpdesk";

$print = JRequest::getVar('print', 0, '', 'int');

// Show print button
if ($print) {
	print '<div align="right"><a href="javascript:;" onClick="javascript:window.print();">' . JText::_('print') . ' <img src="../images/M_images/printButton.png" align="absmiddle" border="0" /></a>&nbsp;</div>';
}
if ($format != 'raw') {
	print '<div class="maqmahelpdesk">';
}

// Display global system messages
HelpdeskUtility::GetGlobalMessage();

// System check
if ($tmpl != 'component' && $format != 'raw') {
	HelpdeskValidation::SystemCheck($task);
}

if (!$print && $task != 'download' && $tmpl != 'component' && $format != 'raw')
{
	if (!$version_updated)
	{
		echo '<div class="alert alert-info"><p><a href="http://www.imaqma.com/" class="btn btn-success">' . JText::_('download') . '</a> ' . JText::_('warning_version') . '</p></div>';
	}

	include JPATH_SITE . '/administrator/components/com_maqmahelpdesk/views/menu/view.html.php';
}

// Check if it's from an add-on or not
if (JString::substr($function, 0, 5) == 'addon')
{
	$addontask = explode('-', $function);
	$file = $task;
	// This checks if that variable exist so it can have multiple tasks in same addon file
	if (JRequest::getVar('addonfile', '', '', 'string') != '')
	{
		$file = JRequest::getVar('addonfile', '', '', 'string');
	}
	if ($task != 'download' && $tmpl != 'component' && $format != 'raw')
	{
		HelpdeskUtility::OutputResources();
	}
	require_once JPATH_SITE . '/components/com_maqmahelpdesk/addon/' . $addontask[1] . '/' . $file . '.php';
} else {
	if ($task != 'download' && $tmpl != 'component' && $format != 'raw')
	{
		HelpdeskUtility::OutputResources();
	}
	if (file_exists($base_path . '/views/' . $function . '/view.html.php'))
	{
		require_once $base_path . '/views/' . $function . '/view.html.php';
	}
	else
	{
		$function = 'cpanel';
		require_once $base_path . '/views/cpanel/view.html.php';
	}
}

if (!$print && $task != 'download' && $tmpl != 'component' && $format != 'raw')
{ ?>
	</div>
	<br style="clear:both;"/>
	<div style="float:right;margin-left:30px;margin-top:4px;height:40px;">
		Became a Fan on Facebook <a href="http://facebook.com/imaqma" title="Became a Fan on Facebook" target="_blank"><img
		src="../media/com_maqmahelpdesk/images/ui/facebook.png" border="0" align="absmiddle"/></a>
	</div>
	<div style="float:right;margin-left:30px;margin-top:4px;height:40px;">
		Follow us on Twitter <a href="http://twitter.com/imaqma" title="Follow us on Twitter" target="_blank"><img
		src="../media/com_maqmahelpdesk/images/ui/twitter.png" border="0" align="absmiddle"/></a>
	</div>
	<div style="float:right;margin-left:30px;margin-top:4px;height:40px;">
		Post a rating and a review at the Joomla! Extensions Directory <a
		href="http://extensions.joomla.org/extensions/clients/help-desk/348/details"
		title="Post a rating and a review at the Joomla! Extensions Directory." target="_blank"><img
		src="../media/com_maqmahelpdesk/images/ui/joomla.png" border="0" align="absmiddle"/></a>
	</div><?php

	include JPATH_SITE . '/administrator/components/com_maqmahelpdesk/views/about/view.html.php'; ?>

	</div><?php
}
