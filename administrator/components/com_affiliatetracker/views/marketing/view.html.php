<?php

/*------------------------------------------------------------------------
# com_affiliatetracker - Affiliate Tracker for Joomla
# ------------------------------------------------------------------------
# author				Germinal Camps
# copyright 			Copyright (C) 2014 JoomlaThat.com. All Rights Reserved.
# @license				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.JoomlaThat.com
# Technical Support:	Forum - http://www.JoomlaThat.com/support
-------------------------------------------------------------------------*/

//no direct access
defined('_JEXEC') or die('Restricted access.');

jimport( 'joomla.application.component.view' );

class MarketingsViewMarketing extends JViewLegacy
{

    function display($tpl = null)
    {
        $document	= JFactory::getDocument();

        //get the account
        $element = $this->get('Data');

        $isNew		= ($element->id < 1);

        $text = $isNew ? JText::_( 'NEW' ) : JText::_( 'EDIT' );
        $title = $isNew ? JText::_( 'MARKETING_MATERIAL_ELEMENT' ) : $element->title;

        JToolBarHelper::title(   $title . ': <small><small>[ ' . $text.' ]</small></small>','accounts' );

        JToolBarHelper::apply();
        JToolBarHelper::save();

        if ($isNew)  {
            JToolBarHelper::cancel();
        } else {

            JToolBarHelper::cancel( 'cancel', 'Close' );

        }

        // push data into the template
        $this->assignRef('element',	$element);

        // JS
        JHtmlBehavior::framework();
        $document->addScript('components/com_affiliatetracker/assets/codemirror/lib/codemirror.js');
        $document->addScript('components/com_affiliatetracker/assets/codemirror/mode/htmlmixed/htmlmixed.js');
        $document->addScript('components/com_affiliatetracker/assets/codemirror/mode/javascript/javascript.js');
        $document->addScript('components/com_affiliatetracker/assets/codemirror/mode/xml/xml.js');
        $document->addScript('components/com_affiliatetracker/assets/codemirror/mode/css/css.js');
        //CSS
        $document->addStyleSheet('components/com_affiliatetracker/assets/codemirror/lib/codemirror.css');

        parent::display($tpl);
    }


}