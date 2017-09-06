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
<fieldset>
		<div class="control-group">
			<div class="control-label">
				<?php echo JText::_('Toolbar Assignment'); ?>
			</div>
			<div class="controls">
				<?php echo $this->lists['components']; ?>
			</div>
		</div>
</fieldset>
