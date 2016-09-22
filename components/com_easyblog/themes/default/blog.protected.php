<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *  
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<div id="ezblog-protected">
	<?php if( !empty( $errmsg ) ){ ?>
	<div class="eblog-message warning"><?php echo $errmsg; ?></div>
	<?php } ?>
    
    <div id="blog-protected">
		<form method="POST" action="index.php?">
			<div class="eblog-message warning"><?php echo JText::_('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_AUTHENTICATION_REQUIRE'); ?></div>
			<div class="blog-password-inst small"><?php echo JText::_('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_AUTHENTICATION_INSTRUCTION'); ?></div>
			<div class="blog-password-input ptm">
				<input type="password" name="blogpassword_<?php echo $id; ?>" id="blogpassword_<?php echo $id; ?>" value="">
				<input type="submit" value="<?php echo JText::_('COM_EASYBLOG_PASSWORD_PROTECTED_BLOG_READ');?>">
				<input type="hidden" name="option" value="com_easyblog">
				<input type="hidden" name="controller" value="entry">
				<input type="hidden" name="task" value="setProtectedCredentials">
				<input type="hidden" name="id" value="<?php echo $id; ?>">
				<input type="hidden" name="return" value="<?php echo $return; ?>">
			</div>
		</form>
    </div>
</div>