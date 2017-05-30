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
class importViewImport extends hikaserialView {

	const ctrl = 'import';
	const name = 'IMPORT';
	const icon = 'import';

	public function display($tpl = null, $params = null) {
		$this->paramBase = HIKASERIAL_COMPONENT.'.'.$this->getName();
		$fct = $this->getLayout();
		if(method_exists($this, $fct))
			$this->$fct($params);
		parent::display($tpl);
	}

	public function show($tpl = null) {
		hikaserial::setTitle(JText::_(self::name), self::icon, self::ctrl);

		$config = hikaserial::config();
		$this->assignRef('config', $config);

		$importData = array(
			array(
				'text' => JText::_('IMPORT_FROM_CSV'),
				'key' => 'csv'
			),
			array(
				'text' => JText::_('IMPORT_FROM_TEXTAREA'),
				'key' => 'textarea'
			)
		);
		$this->assignRef('importData', $importData);

		$defaultValue = $importData[0]['key'];
		$this->assignRef('defaultValue', $defaultValue);

		$importValues = array();
		foreach($importData as $data) {
			$importValues[] = JHTML::_('select.option', $data['key'], $data['text']);
		}
		$this->assignRef('importValues', $importValues);

		$this->toolbar = array(
			'|',
			array('name' => 'custom', 'icon' => 'upload', 'alt' => JText::_('IMPORT'), 'task' => 'import', 'check' => false),
			'|',
			array('name' => 'pophelp', 'target' => self::ctrl),
			'dashboard'
		);
	}

	public function import($tpl = null) {
		hikaserial::setTitle(JText::_(self::name), self::icon, self::ctrl);

		$this->toolbar = array(
			'|',
			array('name' => 'custom', 'icon' => 'upload', 'alt' => JText::_('IMPORT'), 'task' => 'import', 'check' => false),
			'|',
			array('name' => 'pophelp', 'target' => self::ctrl),
			'dashboard'
		);
	}
}
