<?php
// namespace administrator\components\com_jchat\models;
/**
 *
 * @package JCHAT::ROOMS::administrator::components::com_jchat
 * @subpackage models
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Rooms model
 *
 * @package JCHAT::ROOMS::administrator::components::com_jchat
 * @subpackage models
 * @since 1.0
 */
class JChatModelRooms extends JChatModel {
	/**
	 * Build list entities query
	 *
	 * @access protected
	 * @return string
	 */
	protected function buildListQuery() {
		// WHERE
		$where = array ();
		$order = array();
		$whereString = null;
		$orderString = null;
		// STATE FILTER
		if ($filter_state = $this->state->get ( 'state' )) {
			if ($filter_state == 'P') {
				$where [] = 's.published = 1';
			} else if ($filter_state == 'U') {
				$where [] = 's.published = 0';
			}
		}
		
		// TEXT FILTER
		if ($this->state->get ( 'searchword' )) {
			$where [] = "s.name LIKE " . $this->_db->quote("%" . $this->state->get ( 'searchword' ) . "%");
		}
		
		if (count ( $where )) {
			$whereString = "\n WHERE " . implode ( "\n AND ", $where );
		}
		
		// ORDERBY
		if ($this->state->get ( 'order' )) {
			$order[] = $this->state->get ( 'order' );
		}
		
		if(count($order)) {
			$orderString = "\n ORDER BY " . implode ( ", ", $order );
		}
		
		// ORDERDIR
		if ($this->state->get ( 'order_dir' )) {
			$orderString .= " " . $this->state->get ( 'order_dir' );
		}
		
		$query = "SELECT s.*, levels.title AS accesslevel" .
				 "\n FROM #__jchat_rooms AS s" . 
				 "\n LEFT JOIN #__users AS u" . 
				 "\n ON s.checked_out = u.id" . 
				 "\n LEFT JOIN #__viewlevels AS levels" .
				 "\n ON s.access = levels.id" .
				 $whereString . 
				 $orderString;
		return $query;
	}
	
	/**
	 * Return select lists used as filter for editEntity
	 *
	 * @access public
	 * @param Object $record
	 * @return array
	 */
	public function getLists($record) {
		$lists = parent::getLists($record);
		
		// Add access levels list
		$lists['access'] = JHtml::_('access.level', 'access', $record->access, '', false);
	
		return $lists;
	}
}