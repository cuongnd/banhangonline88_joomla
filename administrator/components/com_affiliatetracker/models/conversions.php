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

jimport( 'joomla.application.component.model' );


class ConversionsModelConversions extends JModelLegacy
{

	var $_data;
  	var $_total = null;
  	var $_pagination = null;
  	var $_keywords = null;
	var $_artists_list = null;
	var $_artist_id = null;

	function __construct(){
		parent::__construct();
	
		$mainframe = JFactory::getApplication();

		// Get pagination request variables
		$limit = $mainframe->getUserStateFromRequest('affiliate.conversions.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limitstart = $mainframe->getUserStateFromRequest('affiliate.conversions.limitstart', 'limitstart', 0, 'int');
		$keywords = $mainframe->getUserStateFromRequest('affiliate.conversions.keywords','keywords','','keywords');
		$filter_order     = $mainframe->getUserStateFromRequest('affiliate.conversions.filter_order', 'filter_order', 'at.id', 'cmd' );
        $filter_order_Dir = $mainframe->getUserStateFromRequest('affiliate.conversions.filter_order_Dir', 'filter_order_Dir', 'DESC', 'word' );
		$cal_start = $mainframe->getUserStateFromRequest('affiliate.conversions.cal_start','cal_start','','cal_start');
		$cal_end = $mainframe->getUserStateFromRequest('affiliate.conversions.cal_end','cal_end','','cal_end');
		$status_id = $mainframe->getUserStateFromRequest('affiliate.conversions.status_id','status_id','','status_id');
		$user_id = $mainframe->getUserStateFromRequest('affiliate.conversions.user_id','user_id','','user_id');
		$account_id = $mainframe->getUserStateFromRequest('affiliate.conversions.account_id','account_id','','account_id');
		
		$this->setState('filter_order', $filter_order);
        $this->setState('filter_order_Dir', $filter_order_Dir);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
		$this->setState('status_id', $status_id);
		$this->setState('user_id', $user_id);
		$this->setState('account_id', $account_id);
		
		$this->setState('keywords', $keywords);
		$this->setState('cal_start', $cal_start);
		$this->setState('cal_end', $cal_end);
		
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

function getCalstart(){
		if (empty($this->cal_start)) {
			$this->cal_start = $this->getState('cal_start')	;
		}
		return $this->cal_start;
	}
	 function getCalend(){
		if (empty($this->cal_end)) {
			$this->cal_end = $this->getState('cal_end')	;
		}
		return $this->cal_end;
	}
	
	function getStatusId(){
		if (empty($this->status_id)) {
			$this->status_id = $this->getState('status_id')	;
		}
		return $this->status_id;
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

	function getAccountId(){
		if (empty($this->account_id)) {
			$this->account_id = $this->getState('account_id')	;
		}
		return $this->account_id;
	}
	
 
	function getFilterOrder(){
		return  $this->getState('filter_order') ;
  }
  function getFilterOrderDir(){
		return  $this->getState('filter_order_Dir') ;
  }
  
	
	function getStatus(){

		if (empty( $this->status )){
			$this->status = AffiliateHelper::getStatus();
			
		}
		//print_r($this->songs);die();
		return $this->status;
	
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
		$cal_start = $this->getCalstart();
		$cal_end = $this->getCalend();
		$status_id = $this->getStatusId();
		$user_id = $this->getUserId();
		$account_id = $this->getAccountId();
		
		$where_clause = array();

		if ($keywords != ""){
			$where_clause[] = ' ( at.name LIKE "%'.$keywords.'%" OR at.extended_name LIKE "%'.$keywords.'%" OR u.username LIKE "%'.$keywords.'%" OR u.name LIKE "%'.$keywords.'%" OR u.email LIKE "%'.$keywords.'%" OR acc.account_name LIKE "%'.$keywords.'%") ';
		}
		if ($cal_start != ""){
			$where_clause[] = ' at.date_created >= "'.$cal_start.'" ';
		}
		if ($cal_end != ""){
			$where_clause[] = ' at.date_created <= "'.$cal_end.'" ';
		}
		if ($status_id != ""){
			$where_clause[] = ' at.approved = "'.$status_id.'" ';
		}
		if ($user_id != ""){
			$where_clause[] = ' acc.user_id = "'.$user_id.'" ';
		}
		if ($account_id != ""){
			$where_clause[] = ' at.atid = "'.$account_id.'" ';
		}
		
		$orderby = $this->_buildContentOrderBy();
		
		// Build the where clause of the content record query
		$where_clause = (count($where_clause) ? ' WHERE '.implode(' AND ', $where_clause) : '');

		$query = ' SELECT at.*, u.name as username, acc.user_id AS owner_id, acc.account_name, u2.name as account_owner, acc.user_id as account_user_id '
				.' FROM #__affiliate_tracker_conversions as at '
				.' LEFT JOIN #__affiliate_tracker_accounts as acc ON acc.id = at.atid '
				.' LEFT JOIN #__users as u ON u.id = at.user_id '
				.' LEFT JOIN #__users as u2 ON u2.id = acc.user_id '
				
				.$where_clause
				.$orderby
		;
		//echo $query; die;
		
		return $query;
	}
	
	function getData(){

		if (empty( $this->_data )){
			$query = $this->_buildQuery();
			$this->_data = $this->_getList($query, $this->getState('limitstart'), $this->getState('limit'));
			
			for($i = 0; $i < count($this->_data) ; $i++){
				if($this->_data[$i]->account_user_id){
					$query = ' SELECT SUM(pa.payment_amount) AS total_paid FROM #__affiliate_tracker_payments AS pa WHERE pa.user_id = '.$this->_data[$i]->account_user_id.' AND pa.payment_status = 1 ' ;
					$this->_db->setQuery($query);
					$this->_data[$i]->total_paid = $this->_db->loadResult();

					$query = ' SELECT SUM(at.comission) AS total_earned FROM #__affiliate_tracker_conversions AS at '
							.' LEFT JOIN #__affiliate_tracker_accounts as acc ON acc.id = at.atid '
							.' WHERE acc.user_id = '.$this->_data[$i]->account_user_id.' AND at.approved = 1 ' ;
					$this->_db->setQuery($query);
					$this->_data[$i]->total_earned = $this->_db->loadResult();
				}
			}
		}

 	return $this->_data;
	

	}
	
}