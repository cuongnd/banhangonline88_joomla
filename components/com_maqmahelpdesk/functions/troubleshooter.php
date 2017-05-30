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

$database = JFactory::getDBO();
$id = JRequest::getVar('id', 0, '', 'int');
$id_workgroup = JRequest::getInt('id_workgroup', 0);
$Itemid = JRequest::getInt('Itemid', 0);

// Activities logger
HelpdeskUtility::ActivityLog('site', 'troubleshooter', $task, $id);

if (HelpdeskValidation::ValidPermissions($task, 'TR')) {
    // Get troubleshooter question
    if ($id > 0) {
        $database->setQuery("SELECT * FROM #__support_troubleshooter WHERE id='" . $id . "' AND `show`='1'");
        $trouble = null;
        $trouble = $database->loadObject();
    }

    // Set title
    HelpdeskUtility::PageTitle('Troubleshooter', ($id ? $trouble->title : ''));

    // Get troubleshooter options
    $database->setQuery("SELECT * FROM #__support_troubleshooter WHERE parent='" . $id . "' AND `show`='1' ORDER BY id");
    $rows = $database->loadObjectList();

    $i = 1;
    foreach ($rows as $key2 => $value2) {
        if (is_object($value2)) {
            foreach ($value2 as $key3 => $value3) {
                $troubles[$i][$key3] = $value3;

                if ($key3 == 'id')
                    $troubles[$i]['link'] = JRoute::_('index.php?option=com_maqmahelpdesk&Itemid=' . $Itemid . '&id_workgroup=' . $id_workgroup . '&task=troubleshooter&id=' . $value3);
            }
        }

        $i++;
    }

    // Display toolbar
    HelpdeskToolbar::Create();

    $tmplfile = HelpdeskTemplate::GetFile('troubleshooter/troubleshooter');
    include $tmplfile;
} else {
    HelpdeskValidation::NoAccessQuit();
}
