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
class articlesViewArticles extends hikaserialView {

	const ctrl = 'articles';
	const name = HIKASERIAL_NAME;
	const icon = HIKASERIAL_LNAME;

	public function display($tpl = null, $params = null) {
		$this->paramBase = HIKASERIAL_COMPONENT.'.'.$this->getName();
		$fct = $this->getLayout();
		if(method_exists($this, $fct))
			$this->$fct($params);
		parent::display($tpl);
	}

	public function serialprivatecontent() {
		hikaserial::setTitle(JText::_(self::name), self::icon, self::ctrl);

		$this->loadRef(array(
			'nameboxType' => 'shop.type.namebox'
		));
	}
}
