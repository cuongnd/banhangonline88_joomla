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

class dashboardController extends hikaserialController {
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
