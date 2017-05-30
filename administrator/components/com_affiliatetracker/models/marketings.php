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

// No direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

jimport( 'joomla.application.component.model' );


class MarketingsModelMarketings extends JModelLegacy
{

    var $_data;
    var $_pagination = null;

    function __construct(){
        parent::__construct();

        $mainframe = JFactory::getApplication();

        // Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('affiliate.marketings.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
        $limitstart = $mainframe->getUserStateFromRequest('affiliate.marketings.limitstart', 'limitstart', 0, 'int');
        $keywords = $mainframe->getUserStateFromRequest('affiliate.marketings.keywords','keywords','','keywords');
        $filter_order     = $mainframe->getUserStateFromRequest('affiliate.marketings.filter_order', 'filter_order', 'mm.id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest('affiliate.marketings.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word' );
        $status_id = $mainframe->getUserStateFromRequest('affiliate.marketings.status_id','status_id','','status_id');

        $this->setState('filter_order', $filter_order);
        $this->setState('filter_order_Dir', $filter_order_Dir);
        $this->setState('limit', $limit);
        $this->setState('limitstart', $limitstart);
        $this->setState('status_id', $status_id);

        $this->setState('keywords', $keywords);

    }


    function getTotal()
    {
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



    function getKeywords(){
        if (empty($this->_keywords)) {
            $this->_keywords = $this->getState('keywords')	;
        }
        return $this->_keywords;
    }

    function getStatus(){

        if (empty( $this->status )){
            $this->status = AffiliateHelper::getStatus();

        }
        //print_r($this->songs);die();
        return $this->status;

    }
    function getStatusId(){
        if (empty($this->status_id)) {
            $this->status_id = $this->getState('status_id')	;
        }
        return $this->status_id;
    }

    function getFilterOrder(){
        return  $this->getState('filter_order') ;
    }
    function getFilterOrderDir(){
        return  $this->getState('filter_order_Dir') ;
    }


    function _buildContentOrderBy()
    {

        $filter_order     = $this->getState('filter_order' ) ;
        $filter_order_Dir = $this->getState('filter_order_Dir') ;

        $orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir . ' ';

        return $orderby;
    }

    function _buildQuery()
    {

        $keywords = $this->getKeywords();
        $status_id = $this->getStatusId();

        $where_clause = array();

        if ($keywords != ""){
            $where_clause[] = ' ( mm.title LIKE "%'.$keywords.'%" ) ';
        }
        if ($status_id != ""){
            $where_clause[] = ' mm.publish = "'.$status_id.'" ';
        }

        $orderby = $this->_buildContentOrderBy();

        // Build the where clause of the content record query
        $where_clause = (count($where_clause) ? ' WHERE '.implode(' AND ', $where_clause) : '');

        $query = ' SELECT mm.* '
            .' FROM #__affiliate_tracker_marketing_material as mm '
            .$where_clause
            .$orderby
        ;

        return $query;
    }

    function getData(){

        if (empty( $this->_data )){
            $query = $this->_buildQuery();
            $this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
        }

        return $this->_data;

    }

}