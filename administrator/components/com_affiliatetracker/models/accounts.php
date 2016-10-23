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


class AccountsModelAccounts extends JModelLegacy
{

	var $_data;
  	var $_total = null;
  	var $_pagination = null;
  	var $_keywords = null;
	
	function __construct(){
		parent::__construct();
	
		$mainframe = JFactory::getApplication();

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('affiliate.accounts.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest('affiliate.accounts.limitstart', 'limitstart', 0, 'int');
		$keywords = $mainframe->getUserStateFromRequest('affiliate.accounts.keywords','keywords','','keywords');
		$filter_order     = $mainframe->getUserStateFromRequest('affiliate.accounts.filter_order', 'filter_order', 'acc.id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest('affiliate.accounts.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word' );
		$cal_start = $mainframe->getUserStateFromRequest('affiliate.accounts.cal_start','cal_start','','cal_start');
		$cal_end = $mainframe->getUserStateFromRequest('affiliate.accounts.cal_end','cal_end','','cal_end');
		$status_id = $mainframe->getUserStateFromRequest('affiliate.accounts.status_id','status_id','','status_id');
		$user_id = $mainframe->getUserStateFromRequest('affiliate.accounts.user_id','user_id','','user_id');
	
		$this->setState('filter_order', $filter_order);
        $this->setState('filter_order_Dir', $filter_order_Dir);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		$this->setState('user_id', $user_id);
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
	
	function getUserId(){
		if (empty($this->user_id)) {
			$this->user_id = $this->getState('user_id')	;
		}
		return $this->user_id;
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
		$user_id = $this->getUserId();
		
		$where_clause = array();

		if ($keywords != ""){
			$where_clause[] = ' ( acc.account_name LIKE "%'.$keywords.'%" OR u.username LIKE "%'.$keywords.'%" OR u.name LIKE "%'.$keywords.'%" OR u.email LIKE "%'.$keywords.'%" ) ';
		}
		if ($status_id != ""){
			$where_clause[] = ' acc.publish = "'.$status_id.'" ';
		}
		if ($user_id != ""){
			$where_clause[] = ' acc.user_id = "'.$user_id.'" ';
		}
		
		$orderby = $this->_buildContentOrderBy();
		
		// Build the where clause of the content record query
		$where_clause = (count($where_clause) ? ' WHERE '.implode(' AND ', $where_clause) : '');

		$query = ' SELECT acc.*, u.name as username '
				.' FROM #__affiliate_tracker_accounts as acc '
				.' LEFT JOIN #__users as u ON u.id = acc.user_id '
				
				.$where_clause
				.$orderby
		;
		
		return $query;
	}
	
	function getData(){

		if (empty( $this->_data )){
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));

			for($i = 0; $i < count($this->_data) ; $i++){
				$query = ' SELECT SUM(pa.payment_amount) AS total_paid FROM #__affiliate_tracker_payments AS pa WHERE pa.user_id = '.$this->_data[$i]->user_id.' AND pa.payment_status = 1 ' ;
				$this->_db->setQuery($query);
				$this->_data[$i]->total_paid = $this->_db->loadResult();

				$query = ' SELECT SUM(at.comission) AS total_earned FROM #__affiliate_tracker_conversions AS at '
						.' LEFT JOIN #__affiliate_tracker_accounts as acc ON acc.id = at.atid '
						.' WHERE acc.user_id = '.$this->_data[$i]->user_id.' AND at.approved = 1 ' ;
				$this->_db->setQuery($query);
				$this->_data[$i]->total_earned = $this->_db->loadResult();

				$query = ' SELECT SUM(at.comission) AS total_earned FROM #__affiliate_tracker_conversions AS at '
						.' LEFT JOIN #__affiliate_tracker_accounts as acc ON acc.id = at.atid '
						.' WHERE acc.id = '.$this->_data[$i]->id.' AND at.approved = 1 ' ;
				$this->_db->setQuery($query);
				$this->_data[$i]->total_account = $this->_db->loadResult();
			}
			
		}

 	return $this->_data;
	

	}
	
}