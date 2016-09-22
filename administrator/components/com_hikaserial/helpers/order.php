<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class hikaserialOrderHelper {
	public $table = '';
	public $pkey = '';
	public $groupMap = '';
	public $groupVal = '';
	public $orderingMap = '';

	public function order($down = true) {
		$db = JFactory::getDBO();

		$sign = '<';
		$dir = 'DESC';
		if($down){
			$sign = '>';
			$dir = 'ASC';
		}

		$ids = JRequest::getVar('cid', array(), '', 'array');
		$orders = JRequest::getVar('order', array(), '', 'array');

		$orderingMap = $this->orderingMap;
		$id = (int) $ids[0];
		$pkey = $this->pkey;

		$main = $pkey;
		if(!empty($this->main_pkey)) {
			$main = $this->main_pkey;
		}

		$query = 'SELECT a.'.$orderingMap.',a.'.$pkey.' FROM '.hikaserial::table($this->table).' as b, '.hikaserial::table($this->table).' as a'.
				' WHERE a.'.$orderingMap.' '.$sign.' b.'.$orderingMap.' AND b.'.$main.' = '.$id.$this->group(false,'a').
				' ORDER BY a.'.$orderingMap.' '.$dir.' LIMIT 1';
		$db->setQuery($query);
		$secondElement = $db->loadObject();
		if(empty($secondElement))
			return false;

		$firstElement = null;
		if($main == $pkey) {
			$firstElement->$pkey = $id;
		} else {
			$db->setQuery('SELECT '.$pkey.' FROM '.hikaserial::table($this->table).' WHERE '.$main.' = '.$id.$this->group(false));
			$firstElement->$pkey = (int)$db->loadResult();
		}
		$firstElement->$orderingMap = $secondElement->$orderingMap;
		if($down)
			$secondElement->$orderingMap--;
		else
			$secondElement->$orderingMap++;

		$status1 = $db->updateObject(hikaserial::table($this->table), $firstElement, $pkey);
		$status2 = $db->updateObject(hikaserial::table($this->table), $secondElement, $pkey);
		$status = $status1 && $status2;
		if($status) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::_('NEW_ORDERING_SAVED'), 'message');
		}
		return $status;
	}

	public function save() {
		$app = JFactory::getApplication();
		$db = JFactory::getDBO();

		$pkey = $this->pkey;
		$orderingMap = $this->orderingMap;
		$main = $pkey;
		if(!empty($this->main_pkey)) {
			$main = $this->main_pkey;
		}
		$order	= JRequest::getVar('order', array(), 'post', 'array');
		$cid = JRequest::getVar('cid', array(), 'post', 'array');
		JArrayHelper::toInteger($cid);
		if(!empty($this->groupMap)) {
			$query = 'SELECT `'.$main.'` FROM '.hikaserial::table($this->table).' WHERE `'.$main.'` IN ('.implode(',',$cid).') '. $this->group();
			$db->setQuery($query);
			if(!HIKASHOP_J25) {
				$results = $db->loadResultArray();
			} else {
				$result = $db->loadColumn();
			}

			$newcid = array();
			$neworder = array();
			foreach($cid as $key => $val) {
				if(in_array($val, $results)) {
					$newcid[] = $val;
					$neworder[] = $order[$key];
				}
			}
			$cid = $newcid;
			$order = $neworder;
			if($main != $pkey) {
				$query = 'SELECT `'.$main.'`,`'.$pkey.'` FROM '.hikaserial::table($this->table).' WHERE `'.$main.'` IN ('.implode(',',$cid).') '. $this->group();
				$db->setQuery($query);
				$results = $db->loadObjectList($main);
				$newcid = array();
				foreach($cid as $id) {
					$newcid[] = $results[$id]->$pkey;
				}
				$cid = $newcid;
			}
		}

		$query = 'SELECT `'.$orderingMap.'`,`'.$pkey.'` FROM '.hikashop_table($this->table).' WHERE `'.$pkey.'` NOT IN ('.implode(',',$cid).') ' . $this->group().' ORDER BY `'.$orderingMap.'` ASC';
		$db->setQuery($query);
		$results = $db->loadObjectList($pkey);
		$oldResults = $results;
		asort($order);
		$newOrder = array();
		while(!empty($order) || !empty($results)){
			$dbElement = reset($results);
			if(!empty($order) && empty($dbElement->$orderingMap) || (!empty($order) && reset($order) <= $dbElement->$orderingMap)) {
				$newOrder[] = $cid[(int)key($order)];
				unset($order[key($order)]);
			}else{
				$newOrder[] = $dbElement->$pkey;
				unset($results[$dbElement->$pkey]);
			}
		}
		$i = 1;
		$status = true;
		$element = null;
		foreach($newOrder as $val) {
			$element->$pkey = $val;
			$element->$orderingMap = $i;
			if(!isset($oldResults[$val]) || $oldResults[$val]->$orderingMap != $i) {
				$status = $db->updateObject(hikaserial::table($this->table), $element, $pkey) && $status;
			}
			$i++;
		}
		if($status) {
			$app->enqueueMessage(JText::_('NEW_ORDERING_SAVED'), 'message');
		} else {
			$app->enqueueMessage(JText::_('ERROR_ORDERING'), 'error');
		}
		return $status;
	}

	public function reOrder(){
		$db = JFactory::getDBO();
		$orderingMap = $this->orderingMap;
		$query = 'SELECT MAX(`'.$orderingMap.'`) FROM '.hikaserial::table($this->table) . $this->group(true);
		$db->setQuery($query);
		$max = $db->loadResult() + 1;
		$query = 'UPDATE '.hikaserial::table($this->table).' SET `'.$orderingMap.'` ='.$max.' WHERE `'.$orderingMap.'`=0' . $this->group();
		$db->setQuery($query);
		$db->query();
		$query = 'SELECT `'.$orderingMap.'`,`'.$this->pkey.'` FROM '.hikaserial::table($this->table) . $this->group(true);
		$query .= ' ORDER BY `'.$orderingMap.'` ASC';
		$db->setQuery($query);
		$results = $db->loadObjectList();
		$i = 1;
		if(!empty($results)) {
			foreach($results as $oneResult) {
				if($oneResult->$orderingMap != $i) {
					$oneResult->$orderingMap = $i;
					$db->updateObject(hikaserial::table($this->table), $oneResult, $this->pkey);
				}
				$i++;
			}
		}
	}

	public function group($addWhere = false, $table = '') {
		$groups = '';
		if(!empty($this->groupMap)) {
			$db = JFactory::getDBO();
			if(is_array($this->groupMap)) {
				$groups = array();
				foreach($this->groupMap as $k => $group) {
					if(!empty($table)){
						$group = $table.'.'.$group;
					}
					$groups[]= $group.' = '.$db->Quote($this->groupVal[$k]);
				}
				$groups = ' ' . implode(' AND ',$groups);
			} else {
				$groups = ' ' .(!empty($table)?$table.'.':''). $this->groupMap.' = '.$db->Quote($this->groupVal);
			}
			if($addWhere) {
				$groups = ' WHERE'.$groups;
			}else{
				$groups = ' AND'.$groups;
			}
		}
		return $groups;
	}
}
