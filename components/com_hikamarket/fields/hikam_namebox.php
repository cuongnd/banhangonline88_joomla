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
class JFormFieldHikam_namebox extends JFormField {
	protected $type = 'hikam_namebox';

	protected function getInput() {
		if(!defined('DS'))
			define('DS', DIRECTORY_SEPARATOR);
		if(!defined('HIKAMARKET_COMPONENT') && !include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikamarket'.DS.'helpers'.DS.'helper.php'))
			return 'This module can not work without the HikaMarket Component';

		$nameboxType = hikamarket::get('type.namebox');

		$namebox_type = 'vendor';
		if(isset($this->element['namebox_type']))
			$namebox_type = (string)$this->element['namebox_type'];

		$namebox_mode = hikamarketNameboxType::NAMEBOX_SINGLE;
		if($this->multiple)
			$namebox_mode = hikamarketNameboxType::NAMEBOX_MULTIPLE;

		$text = $nameboxType->display(
			$this->name,
			$this->value,
			$namebox_mode,
			$namebox_type,
			array(
				'delete' => true,
				'default_text' => '<em>'.JText::_('HIKA_NONE').'</em>'
			)
		);
		return $text;
	}
}

