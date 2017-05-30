<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><fieldset class="adminform respuserinfo">
	<legend><?php echo JText::_('EXTRA_INFORMATION'); ?></legend>
	<table class="admintable">
	<?php foreach($this->extraFields as $fieldName => $oneExtraField) {
		echo '<tr id="tr'.$fieldName.'"><td class="key">'.$this->fieldsClass->getFieldName($oneExtraField).'</td><td>'.$this->fieldsClass->display($oneExtraField,@$this->subscriber->$fieldName,'data[subscriber]['.$fieldName.']').'</td></tr>';
	}
	 ?>
	</table>
</fieldset>
