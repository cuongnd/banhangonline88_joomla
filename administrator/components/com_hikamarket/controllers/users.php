<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class usersMarketController extends hikamarketController {
	protected $type = 'users';

	protected $rights = array(
		'display' => array('selection','useselection','getvalues'),
		'add' => array(),
		'edit' => array(),
		'modify' => array(),
		'delete' => array()
	);

	public function selection(){
		JRequest::setVar('layout', 'selection');
		return parent::display();
	}

	public function useselection(){
		JRequest::setVar('layout', 'useselection');
		return parent::display();
	}

	public function getValues() {
		$displayFormat = JRequest::getVar('displayFormat', '');
		$search = JRequest::getVar('search', null);

		$nameboxType = hikamarket::get('type.namebox');
		$options = array(
			'displayFormat' => $displayFormat
		);
		$ret = $nameboxType->getValues($search, 'user', $options);
		if(!empty($ret)) {
			echo json_encode($ret);
			exit;
		}
		echo '[]';
		exit;
	}
}
