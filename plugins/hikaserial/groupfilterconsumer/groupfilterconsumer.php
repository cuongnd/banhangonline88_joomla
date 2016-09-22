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
class plgHikaserialGroupfilterconsumer extends hikaserialPlugin {

	protected $type = 'consumer';
	protected $multiple = true;
	protected $name = 'groupfilterconsumer';

	protected $pluginConfig = array(
		'packs_id' => array('PACK', 'packs'),
		'groups' => array('GROUPS', 'group')
	);

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	public function onBeforeSerialConsume(&$serial, $user_id, &$do, &$extra_data) {
		$user = hikaserial::loadUser(true);
		$ids = array();

		parent::listPlugins('groupfilterconsumer', $ids, false);

		if(!$do)
			return;

		foreach($ids as $id) {
			parent::pluginParams($id);

			if(!in_array($serial->serial_pack_id, $this->plugin_params->packs_id)) {
				continue;
			}

			if(empty($user) || empty($user->user_cms_id)) {
				$do = false;
				continue;
			}

			if(empty($this->plugin_params->groups)) {
				continue;
			}

			$valid_groups = explode(',', trim($this->plugin_params->groups, ','));
			foreach($valid_groups as &$g) {
				$g = (int)$g;
			}
			unset($g);

			if(!HIKASHOP_J16) {
				$joomla_user = clone(JFactory::getUser($user->user_cms_id));
				$userGroups = array($joomla_user->gid);
			} else {
				jimport('joomla.access.access');
				$userGroups = JAccess::getGroupsByUser($user->user_cms_id, true);
			}

			$f = false;
			foreach($userGroups as $g) {
				if(in_array($g, $valid_groups)) {
					$f = true;
					break;
				}
			}

			if(!$f)
				$do = false;
		}
	}

	public function configurationHead() {
		return array();
	}

	public function configurationLine($id = 0, $conf = null) {
		return null;
	}
}
