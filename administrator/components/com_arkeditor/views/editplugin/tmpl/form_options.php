<?php
/*------------------------------------------------------------------------
# Copyright (C) 2005-2012 WebxSolution Ltd. All Rights Reserved.
# @license - GPLv2.0
# Author: WebxSolution Ltd
# Websites:  http://webx.solutions
# Terms of Use: An extension that is derived from the Ark Editor will only be allowed under the following conditions: http://arkextensions.com/terms-of-use
# ------------------------------------------------------------------------*/ 

defined( '_JEXEC' ) or die();

?>
<?php foreach ($this->params->getFieldsets('params') as $name => $fieldset) : ?>
<fieldset>
	<legend><?php echo ( $fieldset->label ) ?: JText::_('COM_PLUGINS_'.$name.'_FIELDSET_LABEL'); ?></legend>
	<?php foreach ($this->params->getFieldset($name) as $field) : ?>
		<div class="control-group">
			<?php if (!$field->hidden) : ?>
				<div class="control-label">
					<?php echo $field->label; ?>
				</div>
				<div class="controls">
					<?php echo $field->input; ?>
				</div>
			<?php else : ?>
				<div class="controls">
					<?php echo $field->input; ?>
				</div>
			<?php endif; ?>
		</div>
	<?php endforeach; ?>
</fieldset>
<?php endforeach; ?>