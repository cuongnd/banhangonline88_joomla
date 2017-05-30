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
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/form.php');
require_once(JPATH_SITE . '/components/com_maqmahelpdesk/helpers/ticket.php');

$CONFIG = new JConfig();
$supportConfig = HelpdeskUtility::GetConfig();
$lang = JFactory::getLanguage();

$id = JRequest::getVar('id', '', '', 'string');

// Activities logger
HelpdeskUtility::ActivityLog('site', 'pdf', $task, $id);

include_once(JPATH_SITE . "/components/com_maqmahelpdesk/pdf/mpdf/mpdf.php");
include_once(JPATH_SITE . "/components/com_maqmahelpdesk/pdf/" . $task . ".php");
