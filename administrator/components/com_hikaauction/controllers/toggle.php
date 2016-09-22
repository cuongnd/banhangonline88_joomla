<?php
/**
 * @package	HikaShop for Joomla!
 * @version	1.2.0
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class toggleController extends hikaauctionBridgeController {
	public function __construct($config = array()) {
		parent::__construct($config);
		$this->registerDefaultTask('toggle');
		if(!headers_sent()) {
			header('Cache-Control: no-store, no-cache, must-revalidate');
			header('Cache-Control: post-check=0, pre-check=0', false);
			header('Pragma: no-cache');
		}
	}

	public function authorize($task) {
		return true;
	}

	public function toggle() {
		$completeTask = JRequest::getCmd('task');
		$task = substr($completeTask, 0, strrpos($completeTask, '-'));
		$elementPkey = substr($completeTask, strrpos($completeTask, '-') + 1);
		$value = JRequest::getVar('value', '', '', 'cmd');
		$controllerName = JRequest::getVar('table', '', '', 'word');

		$controller = hikaauction::get('controller.'.$controllerName);

		if(empty($controller)) {
			echo 'No controller';
			exit;
		}

		if(!$controller->authorize('toggle')) {
			echo 'Forbidden';
			exit;
		}

		$function = $controllerName.$task;
		if(method_exists($this, $function)) {
			$this->$function($elementPkey, $value);
		} else {
			$class = hikaauction::get('class.'.$controllerName);
			if(!$class->toggleId($task)) {
				echo 'Forbidden';
				exit;
			}
			$obj = new stdClass();
			$obj->$task = $value;
			$id = $class->toggleId($task);
			$obj->$id = $elementPkey;

			if(!$class->save($obj)) {
				if(method_exists($class,'getTable')) {
					$table = $class->getTable();
				} else {
					$table = hikaauction::table($controllerName);
				}

				$dbHelper = self::get('helper.database');
				$db = $dbHelper->get();
				$query = $dbHelper->getQuery(true);

				$query->select($task)->from( $query->quoteName($table) )->where( $query->quoteName($id) . '=' . $query->Quote($elementPkey) );
				$db->setQuery($query, 0, 1);
				$value = $db->loadResult();
			}
		}
		$toggleHelper = hikaauction::get('helper.toggle');
		$extra = JRequest::getVar('extra',array(),'','array');
		if(!empty($extra)) {
			foreach($extra as $key => $val) {
				$extra[$key] = urldecode($val);
			}
		}
		echo $toggleHelper->toggle(JRequest::getCmd('task', ''), $value, $controllerName, $extra, true);
		exit;
	}

	public function delete() {
		list($value1, $value2) = explode('-', JRequest::getCmd('value'));
		$table =  JRequest::getVar('table', '', '', 'word');
		$controller = hikaauction::get('controller.'.$table);
		if(empty($controller)) {
			echo 'No controller';
			exit;
		}
		if(!$controller->authorize('delete')) {
			echo 'Forbidden';
			exit;
		}

		$function = 'delete'.ucfirst($table);
		if(method_exists($this, $function)) {
			$this->$function($value1, $value2);
			exit;
		}
		$destClass = hikaauction::get('class.'.$table);
		$deleteToggle = $destClass->toggleDelete();
		list($key1, $key2) = reset($deleteToggle);
		$table = key($deleteToggle);
		if(empty($key1) || empty($key2) || empty($value1) || empty($value2)) {
			echo 'No value';
			exit;
		}

		$dbHelper = self::get('helper.database');
		$db = $dbHelper->get();
		$query = $dbHelper->getQuery(true);

		$query->delete( $query->quoteName(hikaauction::table($table)) )->where(array(
			$query->quoteName($key1).' = '.$query->Quote($value1),
			$query->quoteName($key2).' = '.$query->Quote($value2)
		));
		$db->setQuery($query);
		$db->query();
		exit;
	}

}
