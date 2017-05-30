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
class pluginsController extends hikaserialController {

	protected $type = 'plugin';
	protected $rights = array(
		'display' => array('show','listing','trigger','cancel'),
		'add' => array('add'),
		'edit' => array('edit','copy','toggle','publish','unpublish'),
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
		$pluginsClass = hikaserial::get('class.plugins');
		$plugin = $pluginsClass->get($cid);
		if(empty($plugin)) {
			return false;
		}
		$plugin = hikaserial::import($plugin->folder, $plugin->element);
		if(method_exists($plugin, $function))
			return $plugin->$function();
		return false;
	}

	public function save() {
		$this->store();
		JRequest::setVar('subtask','');
		return $this->edit();
	}

	public function copy() {
		JRequest::setVar('hidemainmenu', 1);
		JRequest::setVar('layout','form');

		$pluginIds = JRequest::getVar('cid', array(), '', 'array');
		$type = JRequest::getCmd('plugin_type');
		$result = true;
		if(!empty($pluginIds) && in_array($type, array('plugin', 'generator', 'consumer')) ) {
			JArrayHelper::toInteger($pluginIds);
			$db = JFactory::getDBO();
			$db->setQuery('SELECT * FROM '.hikaserial::table($type).' WHERE '.$type.'_id IN ('.implode(',',$pluginIds).')');
			$plugins = $db->loadObjectList();
			$pluginsClass = hikaserial::get('class.'.$this->type);
			$plugin_id = $this->type . '_id';
			foreach($plugins as $plugin) {
				unset($plugin->$plugin_id);
				if(!$pluginsClass->save($plugin)) {
					$result = false;
				}
			}

			if($result) {
				$app = JFactory::getApplication();
				if(!HIKASHOP_J30)
					$app->enqueueMessage(JText::_('HIKASHOP_SUCC_SAVED'), 'success');
				else
					$app->enqueueMessage(JText::_('HIKASHOP_SUCC_SAVED'));
			}
		}
		return $this->display();
	}

	protected function toggle($cid, $publish) {
		if(empty($cid))
			JError::raiseWarning(500, 'No items selected');

		$this->plugin = JRequest::getCmd('name', '');
		$this->plugin_type = JRequest::getCmd('plugin_type', 'generator');
		if(empty($this->plugin) || !in_array($this->plugin_type, array('generator','consumer','plugin'))) {
			return false;
		}
		$data = hikaserial::import('hikaserial', $this->plugin);

		if($data === false)
			return false;

		if(!$data->isMultiple())
			JError::raiseWarning(500, 'Not allowed');

		if(isset($data->toggle))
			$this->toggle = $data->toggle;
		else
			$this->toggle = array($this->type . '_published' => $this->type . '_id');

		JRequest::setVar('subtask', '');
		$this->publish_return_view = 'edit';
		return parent::toggle($cid, $publish);
	}

	public function store() {
		$this->plugin = JRequest::getCmd('name', '');
		$this->plugin_type = JRequest::getCmd('plugin_type', 'generator');
		if(empty($this->plugin) || !in_array($this->plugin_type, array('generator','consumer','plugin'))) {
			return false;
		}
		$data = hikaserial::import('hikaserial', $this->plugin);

		$element = new stdClass();
		$id = hikaserial::getCID($this->plugin_type.'_id');
		$formData = JRequest::getVar('data', array(), '', 'array');

		$params_name = $this->plugin_type.'_params';
		if(!empty($formData[$this->plugin_type])) {
			$element = new stdClass();
			$plugin_id = $this->plugin_type.'_id';
			$element->$plugin_id = $id;
			foreach($formData[$this->plugin_type] as $column => $value) {
				hikaserial::secureField($column);
				if(is_array($value)) {
					if($column == $params_name) {
						$element->$params_name = new stdClass();
						foreach($formData[$this->plugin_type][$column] as $key => $val) {
							hikaserial::secureField($key);
							if(!is_array($val)) {
								$element->$params_name->$key = strip_tags($val);
							} else {
								$element->$params_name->$key = $val;
							}
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
			$pluginClass = hikaserial::get('class.'.$this->plugin_type);
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
