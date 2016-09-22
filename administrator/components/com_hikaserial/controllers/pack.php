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
class packController extends hikaserialController {

	protected $type = 'pack';
	protected $toggle = array('pack_published' => 'pack_id');
	protected $rights = array(
		'display' => array('display','show','cancel','listing','select','generate','useselection','cancel','findList'),
		'add' => array('add'),
		'edit' => array('edit','toggle','publish','unpublish'),
		'modify' => array('save','apply'),
		'delete' => array('remove')
	);

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->registerDefaultTask('listing');
	}

	public function store() {
		return parent::adminStore();
	}

	public function generate() {
		$pack_id = hikaserial::getCID();
		$formData = JRequest::getVar('data', array(), '', 'array');

		if(!empty($formData) && !empty($formData['number_serials']) && (int)$formData['number_serials'] > 0 && !empty($pack_id)) {
			JRequest::checkToken('request') || die('Invalid Token');

			$quantity = (int)$formData['number_serials'];

			$app = JFactory::getApplication();
			$db = JFactory::getDBO();
			$config = hikaserial::config();
			$packClass = hikaserial::get('class.pack');
			$pack = $packClass->get($pack_id);

			$serial_status = @$formData['serial_status'];
			if(empty($serial_status)){
				$serial_status = $config->get('unassigned_serial_status', 'free');
			}

			$pluginId = 0;
			$pluginName = substr($pack->pack_generator, 4);
			if(strpos($pluginName,'-') !== false){
				list($pluginName,$pluginId) = explode('-', $pluginName, 2);
				$pack->$pluginName = $pluginId;
			}
			$serials = array();
			$order = new stdClass();
			$order->hikaserial = new stdClass();
			$order->hikaserial->type = 'generate';
			$order->hikaserial->formData = $formData;

			$plugin = hikaserial::import('hikaserial', $pluginName);
			if(method_exists($plugin, 'generate')) {
				ob_start();
				$plugin->generate($pack, $order, $quantity, $serials);
				ob_get_clean();
			}

			if(!empty($serials)) {
				$struct = array(
					'serial_pack_id' => (int)$pack_id,
					'serial_status' => $serial_status
				);
				$serialClass = hikaserial::get('class.serial');
				$serialClass->generate($serials, $struct);


				$app->enqueueMessage( JText::sprintf('X_SERIAL_GENERATED', count($serials)) ); // _('X_SERIAL_GENERATED')
			} else {
				$app->enqueueMessage( JText::_('ERROR_GENERATING_SERIALS'), 'error' );
			}
			JRequest::setVar('serials', $serials);
		}

		JRequest::setVar('hidemainmenu', 1);
		JRequest::setVar('layout','generate');
		return $this->display();
	}

	public function remove() {
		JRequest::checkToken() || die('Invalid Token');
		$cids = JRequest::getVar('cid', array(), '', 'array');
		$packClass = hikaserial::get('class.pack');
		$num = $packClass->delete($cids);
		if($num) {
			$app = JFactory::getApplication();
			$app->enqueueMessage(JText::sprintf('SUCC_DELETE_ELEMENTS', count($cids)), 'message');
		}
		return parent::listing();
	}

	public function select() {
		JRequest::setVar('layout', 'select');
		return parent::display();
	}

	public function useselection() {
		JRequest::setVar('layout', 'useselection');
		return parent::display();
	}

	public function findList() {
		$search = JRequest::getVar('search', '');
		$options = array();
		$nameboxType = hikashop_get('type.namebox');
		$elements = $nameboxType->getValues($search, 'plg.hikaserial.pack', $options);
		echo json_encode($elements);
		exit;
	}
}
