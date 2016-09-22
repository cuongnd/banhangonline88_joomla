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

class dashboardController extends hikaauctionController {
	protected $rights = array(
		'display' => array('listing','cpanel'),
		'add' => array(),
		'edit' => array(),
		'modify' => array(),
		'delete' => array()
	);

	public function __construct($config = array()) {
		parent::__construct($config);
	}

	public function cpanel() {
		JRequest::setVar('layout', 'cpanel');
		return $this->display();
	}
}
