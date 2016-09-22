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
class plgHikaserialPointsconsumer extends hikaserialPlugin {

	protected $type = 'consumer';
	protected $multiple = true;
	protected $name = 'pointsconsumer';
	protected $doc_form = 'pointsconsumer-';

	protected $pluginConfig = array(
		'packs_id' => array('PACK', 'packs'),
		'mode' => array('POINTS_MODE', 'list', array()),
		'value' => array('POINTS_VALUE', 'input'),
		'dynamic_value' => array('POINTS_DYNAMIC_VALUE', 'boolean', 0),
		'target_serial_user' => array('TARGET_SERIAL_USER', 'boolean', 0)
	);

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	public function onPluginConfiguration(&$elements) {
		$ret = parent::onPluginConfiguration($elements);

		if(hikaserial::initShop()) {
			$this->pluginConfig['mode'][2]['hks'] = JText::_('HIKASHOP_USER_POINTS');
		}
		if(hikaserial::initPoints()) {
			$this->pluginConfig['mode'][2]['hkp'] = 'HikaPoints';
		}
		if($this->getAUP(false)) {
			$this->pluginConfig['mode'][2]['aup'] = 'AlphaUserPoints';
		}

		return $ret;
	}

	public function onBeforeSerialConsume(&$serial, $user_id, &$do, &$extra_data) {
		$user = hikaserial::loadUser(true);
		$ids = array();

		parent::listPlugins('pointsconsumer', $ids, false);

		foreach($ids as $id) {
			parent::pluginParams($id);
			if(in_array($serial->serial_pack_id, $this->plugin_params->packs_id)) {
				if(empty($this->plugin_params->target_serial_user) && (empty($user) || empty($user->user_cms_id)))
					$do = false;
				if(!empty($this->plugin_params->target_serial_user) && empty($serial->serial_user_id))
					$do = false;

				if(!empty($extra_data) && !empty($extra_data['points_value']))
					$do = false;
			}
		}
	}

	public function onAfterSerialConsume(&$serial) {
		$user = hikaserial::loadUser(true);
		$cms_id = (int)@$user->user_cms_id;
		$serial_user = null;
		$ids = array();

		if(!empty($serial->serial_user_id)) {
			$userClass = hikaserial::get('shop.class.user');
			$serial_user = $userClass->get($serial->serial_user_id);
		}

		parent::listPlugins('pointsconsumer', $ids, false);

		foreach($ids as $id) {
			parent::pluginParams($id);
			if(in_array($serial->serial_pack_id, $this->plugin_params->packs_id)) {

				$points = (int)$this->plugin_params->value;
				if(!empty($this->plugin_params->dynamic_value) && !empty($serial->serial_extradata['points_value']))
					$points += (int)$serial->serial_extradata['points_value'];

				$user_id = $cms_id;
				if(!empty($this->plugin_params->target_serial_user) && !empty($serial_user->serial_user_id))
					$user_id = $serial_user->user_cms_id;

				$this->givePoints($cms_id, $points);
			}
		}
	}

	public function configurationHead() {
		return array();
	}

	public function configurationLine($id = 0, $conf = null) {
		return null;
	}

	private function givePoints($user_id, $points, $data = null, $mode = null) {
		if($points === 0)
			return true;

		$points_mode = @$this->plugin_params->mode;
		if($mode !== null)
			$points_mode = $mode;

		if($points_mode == 'aup') {
			if($this->getAUP(true)) {
				$aupid = AlphaUserPointsHelper::getAnyUserReferreID($user_id);
				AlphaUserPointsHelper::newpoints('plgaup_orderValidation', $aupid, '', $data, $points);
				return true;
			}
			return false;
		}

		if($points_mode == 'hks') {
			if(hikaserial::initShop()) {
				$app = JFactory::getApplication();
				$ret = true;

				$userClass = hikaserial::get('shop.class.user');
				$oldUser = $userClass->get($user_id, 'cms');

				if(!isset($oldUser->user_points) && !in_array('user_points', array_keys(get_object_vars($oldUser))))
					return false;
				if(empty($oldUser->user_points))
					$oldUser->user_points = 0;

				$user = new stdClass();
				$user->user_id = $oldUser->user_id;
				$user->user_points = (int)$oldUser->user_points + $points;
				if($user->user_points < 0) {
					$app->enqueueMessage(JText::_('CANT_HAVE_NEGATIVE_POINTS'), 'error');
					$points = -$oldUser->user_points;
					$user->user_points = 0;
					$ret = false;
				} else {
					$app->enqueueMessage(JText::sprintf('HIKAPOINTS_EARN_X_POINTS', $points), 'success');
				}
				$userClass->save($user);
				return $ret;
			}
			return false;
		}

		if($points_mode == 'hkp') {
			if(hikaserial::initPoints()) {
				return hikapoints::trigger($user_id, 'hikaserial_points_consumer', $points);
			}
			return false;
		}
		if(substr($points_mode, 0, 4) == 'hkp.') {
			if(hikaserial::initPoints()) {
				$category_id = (int)substr($points_mode, 4);
				$pointsClass = hikapoints::get('class.points');
				return $pointsClass->add($user_id, $category_id, $points);
			}
			return false;
		}

		return false;
	}

	private function getAUP($warning = false) {
		static $aup = null;
		if(!isset($aup)) {
			$aup = false;
			$api_AUP = JPATH_SITE.DS.'components'.DS.'com_alphauserpoints'.DS.'helper.php';
			if(file_exists($api_AUP)) {
				require_once ($api_AUP);
				if(class_exists('AlphaUserPointsHelper'))
					$aup = true;
			}
			if(!$aup && $warning) {
				$app = JFactory::getApplication();
				if($app->isAdmin())
					$app->enqueueMessage('The HikaShop UserPoints plugin requires the component AlphaUserPoints to be installed. If you want to use it, please install the component or use another mode.');
			}
		}
		return $aup;
	}
}
