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
class documentationController extends hikaauctionController {
	const name = 'DOCUMENTATION';
	const ctrl = 'documentation';
	const icon = 'help_header';

	protected $rights = array(
		'display' => array('listing'),
		'add' => array(),
		'edit' => array(),
		'modify' => array(),
		'delete' => array()
	);

	public function __construct($config = array()) {
		parent::__construct($config);
		$this->registerDefaultTask('listing');
	}

	function listing() {
		hikaauction::setTitle(JText::_(self::name), self::icon, self::ctrl);

		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', HIKAAUCTION_LNAME, JText::_('HIKASHOP_CPANEL'), hikaauction::completeLink('dashboard'));
		$config = hikaauction::config();
		$level = $config->get('level');
		$url = HIKAAUCTION_HELPURL.'documentation&level='.$level;
		echo '<div id="hikaauction_div"><iframe allowtransparency="true" scrolling="auto" height="450px" frameborder="0" width="100%" name="hikaauction_frame" id="hikaauction_frame" src="'.$url.'"></iframe></div>';
	}
}
