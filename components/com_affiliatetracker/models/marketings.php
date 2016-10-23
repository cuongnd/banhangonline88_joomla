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

class AffiliateModelMarketings extends JModelLegacy
{
    var $query = "" ;
    var $logs = 0 ;

    function __construct()
    {
        parent::__construct();

        $mainframe = JFactory::getApplication();

        $params = JComponentHelper::getParams( 'com_affiliatetracker' );
        $this->params = $params ;

        $this->keywords = JRequest::getVar('searchword');
        $this->orderby = JRequest::getVar('orderby');

        $default_list_limit = 30 ;

        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('affiliate.marketings.limit', 'limit', $default_list_limit, 'int');
        $limitstart = JRequest::getVar('limitstart', 0);
        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);

        //we won't use limitstart for now
        //$limitstart = 0;


        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);

    }

    function getTotal(){
        // Load the content if it doesn't already exist
        if (empty($this->_total)) {
            $query = $this->_buildQuery();
            $this->_total = $this->_getListCount($query);
        }
        return $this->_total;
    }

    function getPagination()
    {
        // Load the content if it doesn't already exist
        if (empty($this->_pagination)) {
            jimport('joomla.html.pagination');
            $this->_pagination = new JPagination($this->getTotal(), $this->getState('limitstart'), $this->getState('limit') );
        }
        return $this->_pagination;
    }

    function _buildContentOrderBy(){

        $mainframe = JFactory::getApplication();

        $filter_order     = $mainframe->getUserStateFromRequest( 'affiliate.marketings.order', 'filter_order', 'mm.id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest( 'affiliate.marketings.order_Dir', 'filter_order_Dir', 'desc', 'word' );

        if(!$filter_order) $filter_order = "mm.id";
        if(!$filter_order_Dir) $filter_order_Dir = "desc";

        $orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir ;

        return $orderby;
    }

    function _buildQuery(){
        if(empty($this->query)){

            $keywords = $this->keywords;

            $where_clause = array();

            //if ($keywords != "") $where_clause[] = ' ( at.notes LIKE "%'.$keywords.'%" )';

            if (!empty($keywords)) {
                $where_clause[] = ' ( mm.title LIKE "%'.$keywords.'%" ) ';
            }

            $where_clause[] = ' mm.publish = 1 ' ;

            // Build the where clause of the content record query
            $where = (count($where_clause) ? ' WHERE '.implode(' AND ', $where_clause) : '');

            $order = $this->_buildContentOrderBy();

            $this->query = ' SELECT mm.* '
                .' FROM #__affiliate_tracker_marketing_material as mm '
                .$where
                .$order
            ;

        }
        return $this->query;
    }

    function getData(){

        if (empty( $this->_data )){
            $query = $this->_buildQuery();
            $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_data;
    }

}