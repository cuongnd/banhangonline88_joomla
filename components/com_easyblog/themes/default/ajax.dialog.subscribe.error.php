<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<p><?php echo $message; ?></p>
<div class="dialog-actions">
	<input type="button" value="<?php echo JText::_( 'COM_EASYBLOG_BACK_BUTTON' );?>" class="button" id="edialog-submit" name="edialog-submit" onclick="eblog.subscription.show('<?php echo $type;?>','<?php echo $id;?>');" />
	<span id="eblog_loader"></span>
</div>