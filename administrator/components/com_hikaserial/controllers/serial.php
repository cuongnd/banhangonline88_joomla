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
class serialController extends hikaserialController {

	protected $type = 'serial';
	protected $rights = array(
		'display' => array('listing','form','cancel','select','useselection','unassign','exportconf','export','applyexportconf'),
		'add' => array('add'),
		'edit' => array('edit'),
		'modify' => array('save','apply'),
		'delete' => array('remove')
	);

	public function __construct($config = array())	{
		parent::__construct($config);
		$this->registerDefaultTask('listing');
	}

	public function store() {
		return parent::adminStore();
	}

	public function select(){
		JRequest::setVar('layout', 'select');
		return parent::display();
	}

	public function useselection(){
		JRequest::setVar('layout', 'useselection');
		return parent::display();
	}

	public function unassign(){
		$serialClass = hikaserial::get('class.serial');
		$type = JRequest::getVar('type', '');
		$serial_id = hikaserial::getCID('serial_id');
		$success = $serialClass->unassign($serial_id, $type);

		if($success)
			echo '1';
		else
			echo '0';
		exit;
	}

	public function exportconf(){
		JRequest::setVar('layout', 'exportconf');
		return parent::display();
	}

	public function applyexportconf(){
		JRequest::setVar('layout', 'applyexportconf');
		return parent::display();
	}

	public function export(){
		JRequest::setVar('layout', 'export');
		return parent::display();
	}

	public function remove() {
		JRequest::checkToken() || die('Invalid Token');
		$cids = JRequest::getVar('cid', array(), '', 'array');
		$serialClass = hikaserial::get('class.serial');
		$num = $serialClass->delete($cids);
		if($num) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::sprintf('SUCC_DELETE_ELEMENTS', count($cids)), 'message');
		}
		return parent::listing();
	}
}
