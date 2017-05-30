<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 * $Id: helpdesk_mtree.php 646 2012-05-22 08:20:58Z pdaniel $
 * $LastChangedDate: 2012-05-22 09:20:58 +0100 (Ter, 22 Mai 2012) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted index access');

$lang = JFactory::getLanguage();
$language_tag = $lang->getTag();
$lang->load('com_maqmahelpdesk', JPATH_SITE, $language_tag, true);

jimport('joomla.plugin.plugin');

class plgSystemHelpdesk_Mtree extends JPlugin
{
    function plgSystemHelpdesk_Mtree(& $subject, $config)
    {
        parent::__construct($subject, $config);
    }

    function onAfterRender()
    {
        $mainframe = JFactory::getApplication();

        // Don't run in administration
        if ($mainframe->isAdmin())
            return;

        // Get variables
        $option = JRequest::getCmd('option', '', '', 'string');
        $task = JRequest::getCmd('task', '', '', 'string');
        $link_id = JRequest::getCmd('link_id', 0, '', 'int');

        // Exit if it's not the Mosets Listing details page
        if ($option != 'com_mtree' || $task != 'viewlink')
            return;

        // Include required files
        if (!class_exists('simple_html_dom_node')) {
            include_once('plugins/system/helpdesk_mtree/helpdesk_mtree/simplehtmldom.class.php');
        }

        // Get the parameters
        $id_workgroup = $this->params->get('id_workgroup', 0);

        // Add the link
        $output = JResponse::getBody();
        $html = str_get_html($output);
        $listing_details = $html->find('div.actions', 0);
        $listing_details->outertext = str_replace('</div>', '<a href="index.php?option=com_maqmahelpdesk&amp;task=ticket_new&amp;id_workgroup=' . $id_workgroup . '&amp;id_directory=' . $link_id . '">' . JText::_("wk_addticket") . '</a></div>', $listing_details->outertext);

        // Outputs the changed HTML
        JResponse::setBody($html);
    }
}