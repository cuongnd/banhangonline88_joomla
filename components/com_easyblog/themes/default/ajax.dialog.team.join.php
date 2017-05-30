<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<form name="frmJoin" id="frmJoin">
<div class="clearfix">
	<img class="float-l avatar mrm" src="<?php echo $team->getAvatar();?>" width="60" height="60" />
	<div><?php echo JText::_('COM_EASYBLOG_TEAMBLOG_JOIN_REQUEST_DESC'); ?></div>
</div>
<div class="dialog-actions">
	<input type="button" value="<?php echo JText::_('COM_EASYBLOG_CLOSE_BUTTON');?>" class="button" id="edialog-cancel" name="edialog-cancel" onclick="ejax.closedlg();" />
	<input type="button" value="<?php echo JText::_('COM_EASYBLOG_PROCEED_BUTTON');?>" class="button" id="edialog-submit" name="edialog-submit" onclick="eblog.teamblog.send();" />
	<input class="inputbox" type="hidden" name="id" value="<?php echo $team->id; ?>" />
	<input class="inputbox" type="hidden" name="userid" value="<?php echo $system->my->id; ?>" />
	<span id="eblog_loader"></span>
</div>