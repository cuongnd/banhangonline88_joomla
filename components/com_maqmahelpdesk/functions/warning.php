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

$user = JFactory::getUser();
$supportConfig = HelpdeskUtility::GetConfig();
$Itemid = JRequest::getInt('Itemid', 0);

// Activities logger
HelpdeskUtility::ActivityLog('site', 'warning', $task);

// Work out which template to get based on the current user and anonymous access settings
if ($is_support) {
	$tmplfile = 'departments/support';
} elseif ($user->id > 0) {
	$tmplfile = 'departments/customer';
} elseif ($user->id == 0 && $supportConfig->unregister) {
	$tmplfile = 'departments/anonymous';
} else {
	$tmplfile = 'general/access_denied';
}
// Get the template for the page
$tmpl_code->htmlcode = HelpdeskTemplate::Get('', $id_workgroup, $tmplfile);

if ($task == 'noaccess') {
	$title = JText::_('access_denied_title');

} elseif ($task == 'nocontract') {
	$title = JText::_('no_contract_active_title');

}

echo $tmpl_code->htmlcode;
