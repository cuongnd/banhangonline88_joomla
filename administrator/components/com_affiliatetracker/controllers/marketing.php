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

class MarketingsControllerMarketing extends MarketingsController
{

    function __construct()
    {
        parent::__construct();

        // Register Extra tasks
        $this->registerTask( 'add'  , 	'edit' );
        $this->registerTask( 'apply',	'save' );
    }

    function edit()
    {
        JRequest::setVar( 'view', 'marketing' );
        JRequest::setVar( 'layout', 'form'  );
        JRequest::setVar('hidemainmenu', 1);

        parent::display();
    }

    function save()
    {
        $model = $this->getModel('marketing');

        if ($id = $model->store()) {
            $msg = JText::_( 'MARKETING_ELEMENT_SAVED' );
        } else {
            $msg = JText::_( 'ERROR_SAVING_MARKETING_ELEMENT' );
        }

        $task = JRequest::getCmd( 'task' );

        switch ($task)
        {
            case 'apply':
                $link = 'index.php?option=com_affiliatetracker&controller=marketing&task=edit&cid[]='. $id ;
                break;

            case 'save':
            default:
                $link = 'index.php?option=com_affiliatetracker&controller=marketings';
                break;
        }


        $this->setRedirect($link, $msg);
    }

    function remove()
    {
        $model = $this->getModel('marketing');
        if(!$model->delete()) {
            $msg = JText::_( 'ERROR_DELETING_MARKETING_MATERIAL' );
        } else {
            $msg = JText::_( 'MARKETING_MATERIAL_DELETED' );
        }

        $this->setRedirect( 'index.php?option=com_affiliatetracker&controller=marketings', $msg );
    }

    function cancel()
    {
        $msg = JText::_( 'OPERATION_CANCELLED' );
        $this->setRedirect( 'index.php?option=com_affiliatetracker&controller=marketings', $msg );
    }

    function publish()
    {
        $model = $this->getModel('marketing');
        if(!$model->publish()) {
            $msg = JText::_( 'ERROR_PUBLISHING_MARKETING_MATERIAL' );
        } else {
            $msg = JText::_( 'MARKETING_MATERIAL_PUBLISHED' );
        }

        $this->setRedirect( 'index.php?option=com_affiliatetracker&controller=marketings', $msg );
    }

    function unpublish()
    {
        $model = $this->getModel('marketing');
        if(!$model->unpublish()) {
            $msg = JText::_( 'ERROR_UNPUBLISHING_MARKETING_MATERIAL' );
        } else {
            $msg = JText::_( 'MARKETING_MATERIAL_UNPUBLISHED' );
        }

        $this->setRedirect( 'index.php?option=com_affiliatetracker&controller=marketings', $msg );
    }

}