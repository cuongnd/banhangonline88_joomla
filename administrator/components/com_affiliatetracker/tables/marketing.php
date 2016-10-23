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



jimport('joomla.filesystem.file');

class TableMarketing extends JTable
{

    var $id = null;

    var $title = null;
    var $description = null;
    var $html_code = null;
    var $publish = null;


    function TableMarketing(& $db) {
        parent::__construct('#__affiliate_tracker_marketing_material', 'id', $db);
    }

    function check(){

        return true;
    }

}