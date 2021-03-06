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
class pluginsController extends hikaauctionController {

	protected $type = 'plugins';
	protected $rights = array(
		'display' => array('show','listing','trigger'),
		'add' => array('add'),
		'edit' => array('edit','toggle'),
		'modify' => array('save','apply'),
		'delete' => array('delete')
	);

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->registerDefaultTask('listing');
	}

	public function trigger() {
		$cid = JRequest::getInt('cid', 0);
		$function = JRequest::getString('function', '');
		if(empty($cid) || empty($function)){
			return false;
		}
		$pluginsClass = hikaauction::get('class.plugins');
		$plugin = $pluginsClass->get($cid);
		if(empty($plugin)) {
			return false;
		}
		$plugin = hikaauction::import($plugin->folder, $plugin->element);
		if(method_exists($plugin, $function))
			return $plugin->$function();
		return false;
	}

	public function store() {
		$this->plugin = JRequest::getCmd('name', '');
		$this->plugin_type = JRequest::getCmd('plugin_type', 'plugin');
		if(empty($this->plugin) || !in_array($this->plugin_type, array('plugin'))) {
			return false;
		}
		$data = hikaauction::import('hikaauction'.$this->plugin_type, $this->plugin);

		$element = null;
		$id = hikaauction::getCID($this->plugin_type.'_id');
		$formData = JRequest::getVar('data', array(), '', 'array');

		$params_name = $this->plugin_type.'_params';
		if(!empty($formData[$this->plugin_type])) {
			$plugin_id = $this->plugin_type.'_id';
			$element->$plugin_id = $id;
			foreach($formData[$this->plugin_type] as $column => $value) {
				hikaauction::secureField($column);
				if(is_array($value)) {
					if($column == $params_name) {
						$element->$params_name = null;
						foreach($formData[$this->plugin_type][$column] as $key=>$val) {
							hikaauction::secureField($key);
							$element->$params_name->$key = strip_tags($val);
						}
					}
				}else{
					$element->$column = strip_tags($value);
				}
			}

			$plugin_description = $this->plugin_type.'_description';
			$plugin_description_data = JRequest::getVar($plugin_description, '', '', 'string', JREQUEST_ALLOWRAW);
			$element->$plugin_description = $plugin_description_data;
		}
		$function = 'on'.ucfirst($this->plugin_type).'ConfigurationSave';
		if(method_exists($data, $function)) {
			$data->$function($element);
		}

		if(!empty($element)) {
			$pluginClass = hikaauction::get('class.'.$this->plugin_type);
			if(isset($element->$params_name)) {
				$element->$params_name = serialize($element->$params_name);
			}
			$status = $pluginClass->save($element);

			if(!$status) {
				JRequest::setVar('fail', $element);
			} else {
				$app = JFactory::getApplication();
				$app->enqueueMessage(JText::_('HIKASHOP_SUCC_SAVED'), 'message');
				if(empty($id)) {
					JRequest::setVar($this->plugin_type.'_id', $status);
				}
			}
		}
	}
}
