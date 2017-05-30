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
class importController extends hikaserialController {
	protected $type = 'import';
	public $helper = null;
	protected $rights = array(
		'display' => array('display','show','listing','cancel'),
		'add' => array(),
		'edit' => array(),
		'modify' => array('import'),
		'delete' => array()
	);

	public function __construct($config = array())	{
		parent::__construct($config);
		$this->registerDefaultTask('show');
	}

	public function display($tpl = null, $params = null) {
		JRequest::setVar('layout', 'show');
		return parent::display($tpl, $params);
	}

	public function import() {
		JRequest::checkToken('request') || die('Invalid Token');
		$importFrom = JRequest::getCmd('importfrom');

		$this->helper = hikaserial::get('helper.import');
		switch($importFrom) {
			case 'csv':
				$this->importCsvFile();
				break;
			case 'textarea':
				$this->importTextarea();
				break;
		}

		JRequest::setVar('layout', 'show');
		return parent::display();
	}

	private function importCsvFile() {
		$importFile = JRequest::getVar('csvimport_file', array(), 'files','array');

		$this->helper->charset = JRequest::getVar('csvimport_charsetconvert', '');
		$this->helper->pack_id = JRequest::getInt('csvimport_pack');

		$options = array();
		if(JRequest::getInt('csvimport_checkduplicates', 0))
			$options['check_duplicates'] = true;

		$ret = $this->helper->importFromFile($importFile, $options);

		$productClass = hikaserial::get('class.product');
		$productClass->refreshQuantities();
		return $ret;
	}

	private function importTextarea() {
		$content = JRequest::getVar('textareaimport_content', '', '', 'string', JREQUEST_ALLOWRAW);
		$this->helper->pack_id = JRequest::getInt('textareaimport_pack');
		$productClass = hikaserial::get('class.product');

		$options = array();
		if(JRequest::getInt('textareaimport_checkduplicates', 0))
			$options['check_duplicates'] = true;

		$importAsCsv = JRequest::getInt('textareaimport_as_csv', 0);
		if($importAsCsv == 0) {
			if($this->helper->pack_id == 0) {
				return false;
			}
			$ret = $this->helper->handleTextContent($content, $options);
			$productClass->refreshQuantities();
			return $ret;
		}
		$ret = $this->helper->handleCsvContent($content, $options);
		$productClass->refreshQuantities();
		return $ret;
	}
}
