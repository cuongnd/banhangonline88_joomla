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
<form name="edit-comment" id="edit-comment" action="<?php echo EasyBlogRouter::_('index.php?option=com_easyblog&controller=dashboard&task=saveComment');?>" method="post">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="reset-table form-layout">
	<tr>
		<td class="key" width="30%">
			<label class="label" for="title"><?php echo JText::_('COM_EASYBLOG_TITLE'); ?> <?php if($system->config->get('comment_requiretitle', 0)){ ?><small>(<?php echo JText::_('COM_EASYBLOG_REQUIRED'); ?>)</small><?php } ?></label>
		</td>
		<td>
			<input class="input text" type="text" id="title" name="title" size="45" value="<?php echo $comment->title; ?>" />
		</td>
	</tr>
	<tr>
		<td class="key" valign="top">
			<label class="label" for="name"><?php echo JText::_('COM_EASYBLOG_NAME'); ?> <small>(<?php echo JText::_('COM_EASYBLOG_REQUIRED'); ?>)</small></label>
		</td>
		<td class="input" valign="top">
			<input class="input text" type="text" id="name" name="name" size="45" value="<?php echo $comment->name; ?>" />
		</td>
	</tr>
	
	<tr>
		<td class="key" valign="top">
			<label class="label" for="email"><?php echo JText::_('COM_EASYBLOG_EMAIL'); ?> <small>(<?php echo JText::_('COM_EASYBLOG_REQUIRED'); ?>)</small></label>
		</td>
		<td class="input" valign="top">
			<input class="input text" type="text" id="email" name="email" size="45" value="<?php echo $comment->email; ?>" />
		</td>
	</tr>
	<tr>
		<td class="key" valign="top"><label class="label" for="url"><?php echo JText::_('COM_EASYBLOG_WEBSITE'); ?></label></td>
		<td class="input" valign="top">
			<input class="input text" type="text" id="url" name="url" size="45" value="<?php echo $comment->url; ?>" />
		</td>
	</tr>
	<tr>
		<td class="key" valign="top">
			<label class="label" for="comment"><?php echo JText::_('COM_EASYBLOG_COMMENT'); ?> <small>(<?php echo JText::_('COM_EASYBLOG_REQUIRED'); ?>)</small></label>
		</td>
		<td class="input" valign="top">
			<textarea id="comment" name="comment" class="input" cols="50" rows="5"><?php echo $comment->comment; ?></textarea>
		</td>
	</tr>
</table>
<input type="hidden" name="id" value="<?php echo $comment->post_id; ?>" />
<input type="hidden" name="commentId" value="<?php echo $comment->id; ?>" />
<input type="hidden" name="controller" value="dashboard" />
<input type="hidden" name="task" value="updateComment" />
<?php echo JHTML::_( 'form.token' ); ?>
<div class="dialog-actions">
	<input type="button" value="<?php echo JText::_('COM_EASYBLOG_CANCEL_BUTTON');?>" class="button" id="edialog-cancel" name="edialog-cancel" onclick="ejax.closedlg();" />
	<input type="submit" value="<?php echo JText::_('COM_EASYBLOG_PROCEED_BUTTON');?>" class="button" id="edialog-submit" name="edialog-submit" />
</div>
</form>
