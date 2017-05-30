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

jimport('joomla.application.component.model');

class MarketingsModelMarketing extends JModelLegacy
{

    function __construct()
    {
        parent::__construct();

        $array = JRequest::getVar('cid',  0, '', 'array');
        $this->setId((int)$array[0]);

        $this->params = JComponentHelper::getParams( 'com_affiliatetracker' );

    }

    function setId($id)
    {
        // Set id and wipe data
        $this->_id		= $id;
        $this->_data	= null;

    }

    function &getData()
    {
        // Load the data
        if (empty( $this->_data )) {
            $query = ' SELECT mm.* FROM #__affiliate_tracker_marketing_material as mm '.
                ' WHERE mm.id = '.$this->_id;
            $this->_db->setQuery( $query );
            $this->_data = $this->_db->loadObject();


        }

        if (!$this->_data) {
            $this->_data = new stdClass();
            $this->_data->id = 0;
            $this->_data->title = "";
            $this->_data->description = "";
            $this->_data->html_code = JText::_('MARKETING_DEFAULT_HTML');
            $this->_data->publish = 0;
        }

        return $this->_data;
    }

    function store()
    {
        $row = $this->getTable();

        $data = JRequest::get( 'post' );

        $data['html_code'] = JRequest::getVar('html_code', '', 'post', 'string', JREQUEST_ALLOWRAW);

        if (!$row->bind($data)) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        if (!$row->check()) {
            $this->setError($this->_db->getErrorMsg());
            return false;
        }

        if (!$row->store()) {

            $this->setError( $this->_db->getErrorMsg() );
            return false;
        }

        return $row->id;
    }

    function delete()
    {
        $cids = JRequest::getVar( 'cid', array(0), 'post', 'array' );

        $row = $this->getTable();

        if (count( $cids )) {
            foreach($cids as $cid) {

                if (!$row->delete( $cid )) {
                    $this->setError( $row->getErrorMsg() );
                    return false;
                }
            }
        }
        return true;
    }

    function publish()
    {
        $cids = JRequest::getVar( 'cid', array(0), 'default', 'array' );

        if (count( $cids )) {
            foreach($cids as $cid) {
                $query = ' UPDATE #__affiliate_tracker_marketing_material SET publish = 1 WHERE id = '. $cid . ' LIMIT 1 ';
                $this->_db->setQuery($query);
                $this->_db->query();

            }
        }
        return true;
    }

    function unpublish()
    {
        $cids = JRequest::getVar( 'cid', array(0), 'default', 'array' );

        if (count( $cids )) {
            foreach($cids as $cid) {
                $query = ' UPDATE #__affiliate_tracker_marketing_material SET publish = 0 WHERE id = '. $cid . ' LIMIT 1 ';
                $this->_db->setQuery($query);
                $this->_db->query();
            }
        }
        return true;
    }

}