<?php
/*------------------------------------------------------------------------
# Copyright (C) 2014-2015 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

?>
<fieldset>
	<legend><?php echo JText::_( 'COM_ARKEDITOR_BUTTONS_HEADER' ); ?></legend>
	<div class="cke_top" >&nbsp;</div>
	<?php
		$fieldSet = $this->form->getFieldset('buttons');
		$html = array();

	
		foreach ($fieldSet as $field)
		{
			$method = method_exists($field,'renderField') ? 'renderField' : 'getControlGroup';
			$html[] = $field->$method();
		}

		echo implode('', $html);
			
	?>	
</fieldset>
<fieldset>
	<legend><?php echo JText::_( 'COM_ARKEDITOR_TABS_HEADER' ); ?></legend>
	<div class="cke_top" >&nbsp;</div>
	<?php
		$fieldSet = $this->form->getFieldset('tabs');
		$html = array();

		
		
		foreach ($fieldSet as $field)
		{
			$method = method_exists($field,'renderField') ? 'renderField' : 'getControlGroup';
			$html[] = $field->$method();
		}
		echo implode('', $html);
	?>	
</fieldset>
