<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<p><?php echo JText::_( 'COM_EASYBLOG_ARE_YOU_SURE_YOU_WANT_TO_UNSUBSCRIBE_POST' );?></p>
<form id="dashboard" name="dashboard" method="post" action="<?php echo EasyBlogRouter::_( 'index.php?option=com_easyblog&controller=entry&task=unsubscribe' );?>">
	<input type="hidden" name="subscription_id" value="<?php echo $subscription_id; ?>" />
	<input type="hidden" name="blog_id" value="<?php echo $blog_id; ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>

	<div class="dialog-actions">
		<input type="button" value="<?php echo JText::_( 'COM_EASYBLOG_CANCEL_BUTTON' );?>" class="button" id="edialog-cancel" name="edialog-cancel" onclick="ejax.closedlg();" />
		<input type="submit" value="<?php echo JText::_( 'COM_EASYBLOG_PROCEED_BUTTON' );?>" class="button" />
	</div>
</form>
