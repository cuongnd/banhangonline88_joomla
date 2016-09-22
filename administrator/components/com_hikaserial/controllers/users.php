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
class usersController extends hikaserialController {
	protected $type = 'users';

	protected $rights = array(
		'display' => array('select','useselection'),
		'add' => array(),
		'edit' => array(),
		'modify' => array(),
		'delete' => array()
	);

	public function select(){
		JRequest::setVar('layout', 'select');
		return parent::display();
	}

	public function useselection(){
		JRequest::setVar('layout', 'useselection');
		return parent::display();
	}
}
