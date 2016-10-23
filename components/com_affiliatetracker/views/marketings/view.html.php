<?php

/*------------------------------------------------------------------------
# com_invoices - Invoices for Joomla
# ------------------------------------------------------------------------
# author				Germinal Camps
# copyright 			Copyright (C) 2012 JoomlaFinances.com. All Rights Reserved.
# @license				http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# Websites: 			http://www.JoomlaFinances.com
# Technical Support:	Forum - http://www.JoomlaFinances.com/forum
-------------------------------------------------------------------------*/

//no direct access
defined('_JEXEC') or die('Restricted access.');

jimport( 'joomla.application.component.view');

class AffiliateViewMarketings extends JViewLegacy
{

    function display($tpl = null)
    {
        $mainframe = JFactory::getApplication();
        $document	= JFactory::getDocument();

        $params = JComponentHelper::getParams( 'com_affiliatetracker' );

        $items		= $this->get( 'Data');
        $pagination = $this->get('Pagination');

        $this->assignRef('items',		$items);
        $this->assignRef('pagination', $pagination);
        $this->assignRef('params', $params);

        $filter_order     = $mainframe->getUserStateFromRequest( 'filter_order', 'filter_order', 'at.date_created', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( 'filter_order_Dir', 'filter_order_Dir', 'desc', 'word' );

        $lists['order']     = $filter_order;
        $lists['order_Dir'] = $filter_order_Dir;

        $this->assignRef( 'lists',	$lists );

        JHTML::_('behavior.modal');

        $document->addStyleSheet('components/com_affiliatetracker/assets/styles.css');

        parent::display($tpl);
    }


}
