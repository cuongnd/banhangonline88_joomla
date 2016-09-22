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
class documentationController extends hikaserialBridgeController {
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
		hikaserial::setTitle(JText::_(self::name), self::icon, self::ctrl);

		$bar = JToolBar::getInstance('toolbar');
		$bar->appendButton('Link', HIKASERIAL_LNAME, JText::_('HIKASHOP_CPANEL'), hikaserial::completeLink('dashboard'));
		$config = hikaserial::config();
		$level = $config->get('level');
		$url = HIKASERIAL_HELPURL.'documentation&level='.$level;

		$shopConfig = hikaserial::config(false);
		$menu_style = $shopConfig->get('menu_style','title_bottom');
		if(HIKASHOP_J30) $menu_style = 'content_top';
		if($menu_style == 'content_top') {
			echo hikaserial::getMenu();
		}
		echo '<div id="hikaserial_div"><iframe allowtransparency="true" scrolling="auto" height="450px" frameborder="0" width="100%" name="hikaserial_frame" id="hikaserial_frame" src="'.$url.'"></iframe></div>';
	}
}
