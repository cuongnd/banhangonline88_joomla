<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 * $Id: helpdesk_replies.php 646 2012-05-22 08:20:58Z pdaniel $
 * $LastChangedDate: 2012-05-22 09:20:58 +0100 (Ter, 22 Mai 2012) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$lang = JFactory::getLanguage();
$language_tag = $lang->getTag();
$lang->load('com_maqmahelpdesk', JPATH_SITE, $language_tag, true);

jimport('joomla.plugin.plugin');

/**
 * Editor Image buton
 *
 * @package Editors-xtd
 * @since 1.5
 */
class plgButtonHelpdesk_Replies extends JPlugin
{
    /**
     * Constructor
     *
     * For php4 compatability we must not use the __constructor as a constructor for plugins
     * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
     * This causes problems with cross-referencing necessary for the observer design pattern.
     *
     * @param     object $subject The object to observe
     * @param     array  $config  An array that holds the plugin configuration
     * @since 1.5
     */
    function plgButtonHelpdesk_Replies(& $subject, $config)
    {
        parent::__construct($subject, $config);
    }

    /**
     * Display the button
     *
     * @return array A two element array of ( imageName, textToInsert )
     */
    function onDisplay($name)
    {
        $mainframe = JFactory::getApplication();
        $doc = JFactory::getDocument();

        $declaration = ".button2-left .maqmahelpdesk_replies{ background: url(" . JURI::root() . "media/com_maqmahelpdesk/images/xtd-editor/replies.png) 100% 0 no-repeat; }";

        $doc->addStyleDeclaration($declaration);

        $link = ($mainframe->isAdmin() ? '../' : '') . 'index.php?option=com_maqmahelpdesk&amp;id_workgroup=1&amp;task=ticket_replieseditor&amp;tmpl=component&amp;e_name=' . $name;

        JHTML::_('behavior.modal');

        $button = new JObject();
        $button->set('modal', true);
        $button->set('link', $link);
        $button->set('text', JText::_('Helpdesk Pre-Defined Replies'));
        $button->set('name', 'maqmahelpdesk_replies');
        $button->set('options', "{handler: 'iframe', size: {x: 650, y: 400}}");

        return $button;
    }
}