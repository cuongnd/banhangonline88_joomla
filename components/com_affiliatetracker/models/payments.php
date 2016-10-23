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

class AffiliateModelPayments extends JModelLegacy
{
	var $query = "" ;
	var $logs = 0 ;
	  
	function __construct()
	{
		parent::__construct();
		
		$mainframe = JFactory::getApplication();
		
		$params =JComponentHelper::getParams( 'com_affiliatetracker' );
		$this->params = $params ;
		
		$this->keywords = JRequest::getVar('searchword');
		
		$this->orderby = JRequest::getVar('orderby');
		
		$date_in = $mainframe->getUserStateFromRequest('affiliate.date_in','date_in','','date_in');
		$date_out = $mainframe->getUserStateFromRequest('affiliate.date_out','date_out','','date_out');
		
		$account_id = $mainframe->getUserStateFromRequest('affiliate.account_id','account_id','','account_id');
		$type_id = $mainframe->getUserStateFromRequest('affiliate.type_id','type_id','','type_id');
		
		$timespan = $mainframe->getUserStateFromRequest('affiliate.timespan','timespan',$params->get('days',30),'timespan');
		
		$default_list_limit = 30 ;
		
		// Get pagination request variables
        $limit = $mainframe->getUserStateFromRequest('affiliate.logs.limit', 'limit', $default_list_limit, 'int');
		//$limitstart = $mainframe->getUserStateFromRequest('muscol.search.limitstart', 'limitstart', 0, 'int');
		$limitstart = JRequest::getVar('limitstart',0);
        // In case limit has been changed, adjust it
        $limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		
		//we won't use limitstart for now
		//$limitstart = 0;
		
		$this->setState('account_id', $account_id);
		$this->setState('type_id', $type_id);
		
		$this->setState('date_in', $date_in);
		$this->setState('date_out', $date_out);
		
		$this->setState('timespan', $timespan);
		
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
	
	function getDateIn(){
		if (empty($this->date_in)) {
			$this->date_in = $this->getState('date_in')	;
		}
		return $this->date_in;
	}
	
	function getDateOut(){
		if (empty($this->date_out)) {
			$this->date_out = $this->getState('date_out')	;
		}
		return $this->date_out;
	}
	
	function getAccountId(){
		if (empty($this->account_id)) {
			$this->account_id = $this->getState('account_id')	;
		}
		return $this->account_id;
	}
	
	function getTypeId(){
		if (empty($this->type_id)) {
			$this->type_id = $this->getState('type_id')	;
		}
		return $this->type_id;
	}
	
	function getTimespan(){
		if (empty($this->timespan)) {
			$this->timespan = $this->getState('timespan')	;
		}
		return $this->timespan;
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
	
		$filter_order     = $mainframe->getUserStateFromRequest( 'affiliate.items.order', 'filter_order', 'pa.payment_datetime', 'cmd' );
		$filter_order_Dir = $mainframe->getUserStateFromRequest( 'affiliate.items.order_Dir', 'filter_order_Dir', 'desc', 'word' );
		
		if(!$filter_order) $filter_order = "pa.payment_datetime";
		if(!$filter_order_Dir) $filter_order_Dir = "desc";
		
		$orderby = ' ORDER BY '.$filter_order.' '.$filter_order_Dir ;
		
		$orderby .= ' , pa.id DESC ' ;
	
		return $orderby;
	}

	function _buildQuery(){
		if(empty($this->query)){
			
			$user = JFactory::getUser();
			//echo $user->id; die();
			$keywords = $this->keywords;
			
			$date_in = $this->getDateIn();
			$date_out = $this->getDateOut();
			$account_id = $this->getAccountId();
			$type_id = $this->getTypeId();
			
			$where_clause = array();
	
			//if ($keywords != "") $where_clause[] = ' ( at.notes LIKE "%'.$keywords.'%" )';
			
			if ($date_in) {
				$where_clause[] = ' pa.payment_datetime >= "'. $date_in .'" ' ;
			}
			if ($date_out) {
				$where_clause[] = ' pa.payment_datetime <= "'. $date_out .' 23:59:59" ' ;
			}
			
			
			$where_clause[] = ' pa.user_id = ' . $user->id ;
			$where_clause[] = ' pa.payment_status = 1 ' ;
			
			// Build the where clause of the content record query
			$where = (count($where_clause) ? ' WHERE '.implode(' AND ', $where_clause) : '');
			
			$order = $this->_buildContentOrderBy();
			
			$days = $this->getTimespan();
			
			switch($days){
				case "TODAY":
				$time_clause = ' AND pa.payment_datetime >= SUBDATE(CURDATE(), 1 ) ' ;
				break;
				case "ALLTIME":
				$time_clause = '  ' ;
				break;
				default:
				$time_clause = ' AND pa.payment_datetime >= SUBDATE(CURDATE(), '.($days).' ) ' ;
				break;	
			}
			
			$this->query = ' SELECT pa.*, u.name as username '
							.' FROM #__affiliate_tracker_payments as pa '
							.' LEFT JOIN #__users as u ON u.id = pa.user_id '
							.$where 
							.$order
							;
			
			$query2 = ' SELECT SUM(pa.payment_amount) as payments '
					.' FROM #__affiliate_tracker_payments as pa '
					.' LEFT JOIN #__users as u ON u.id = pa.user_id '
					.$where 
					.$time_clause
					
					;
			$this->_db->setQuery($query2);
			$this->payments = $this->_db->loadResult();
			
		}
		return $this->query;
	}
	
	function getData(){

		if (empty( $this->_data )){
			$query = $this->_buildQuery();
			//echo $query;die;
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			//print_r($this->_db);die;		
		}

 		return $this->_data;
	}
	
	function getPayments(){
		return $this->payments ;
	}
	
	
	
}