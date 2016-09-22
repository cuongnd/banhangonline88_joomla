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
class menusController extends hikaauctionController {
	protected $type = 'menus';

	protected $rights = array(
		'display' => array('display', 'show', 'listing'),
		'add' => array('add'),
		'edit' => array('edit', 'add_module'),
		'modify' => array('save','apply'),
		'delete' => array()
	);

	public function __construct($config = array())	{
		parent::__construct($config);
		$this->registerDefaultTask('listing');
	}

	public function add_module() {
		$id = hikaauction::getCID('id');
		$menuClass = hikaauction::get('class.menus');
		$menu->attachAssocModule($id);
		$this->edit();
	}

	public function store() {
		$app = JFactory::getApplication();
		if($app->isAdmin())
			return $this->adminStore();
		return false;
	}

	public function edit() {
		if(JRequest::getInt('fromjoomla')) {
			$app = JFactory::getApplication();
			$context = 'com_menus.edit.item';
			$id = hikaauction::getCID('id');
			if($id) {
				$values = (array)$app->getUserState($context . '.id');
				$index = array_search((int)$id, $values, true);
				if(is_int($index)) {
					unset($values[$index]);
					$app->setUserState($context . '.id', $values);
				}
			}
		}
		return parent::edit();
	}
}
