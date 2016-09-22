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
class plgHikaserialGroupconsumer extends hikaserialPlugin {

	protected $type = 'consumer';
	protected $multiple = true;
	protected $name = 'groupconsumer';

	protected $pluginConfig = array(
		'pack_id' => array('PACK', 'pack'),
		'group_id' => array('GROUP', 'input')
	);

	public function __construct(&$subject, $config) {
		parent::__construct($subject, $config);
	}

	public function onBeforeSerialConsume(&$serial, $user_id, &$do) {
		$user = hikaserial::loadUser(true);
		$ids = array();

		parent::listPlugins('groupconsumer', $ids, false);

		foreach($ids as $id) {
			parent::pluginParams($id);
			if($this->plugin_params->pack_id = $serial->serial_pack_id) {
				if(empty($user) || empty($user->user_cms_id)) {
					$do = false;
				}
				return;
			}
		}
	}

	public function onAfterSerialConsume(&$serial) {
		$user = hikaserial::loadUser(true);
		$cms_id = $user->user_cms_id;
		$ids = array();

		parent::listPlugins('groupconsumer', $ids, false);

		foreach($ids as $id) {
			parent::pluginParams($id);
			if($this->plugin_params->pack_id == $serial->serial_pack_id) {
				$this->updateGroup($cms_id, $this->plugin_params->group_id);
			}
		}
	}

	public function configurationHead() {
		return array();
	}

	public function configurationLine($id = 0, $conf = null) {
		return null;
	}

	private function updateGroup($user_id, $new_group_id, $remove_group_id = 0) {
		$user = clone(JFactory::getUser($user_id));
		if(version_compare(JVERSION,'1.6.0','<')) {
			if($user->gid != 25) {
				$user->set('gid', $new_group_id);
				$acl = JFactory::getACL();
				$user->set('usertype', $acl->get_group_name($new_group_id));
			}
		} else {
			jimport('joomla.access.access');
			$userGroups = JAccess::getGroupsByUser($user_id, true);
			$userGroups[] = $new_group_id;
			if(!empty($remove_group_id)) {
				$key = array_search($remove_group_id, $userGroups);
				if(is_int($key)) {
					unset($userGroups[$key]);
				}
			}
			$user->set('groups', $userGroups);
		}
		$user->save();
	}
}
