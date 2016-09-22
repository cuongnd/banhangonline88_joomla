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

$CONFIG = new JConfig();
$supportConfig = HelpdeskUtility::GetConfig();
$addon = JRequest::getCmd('addon', '', '', 'string');
$SecretWord = JRequest::getVar('SecretWord', '', '', 'string');

// Activities logger
HelpdeskUtility::ActivityLog('site', 'addon', $addon);

// Checks if the secret word match to continue
if ($CONFIG->secret == $SecretWord) {
	include_once(JPATH_SITE . "/components/com_maqmahelpdesk/addon/" . $addon . "/" . $addon . ".php");
}
