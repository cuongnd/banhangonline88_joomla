<?php
/**
* @package		EasyBlog
* @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasyBlog is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Restricted access');
?>
<table class="admintable">
	<tr>
		<td width="50%" valign="top">
			<fieldset class="adminform">
			<legend><?php echo JText::_('COM_EASYBLOG_CATEGORIES_EDIT_FORM_TITLE'); ?></legend>
			<table class="admintable">
				<tr>
					<td width="30%" class="key">
						<label class="label" for="title"><?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT_TITLE'); ?></label>
					</td>
					<td valign="top" class="value">
						<input class="inputbox input" type="text" id="title" name="title" size="45" value="<?php echo $this->escape( $this->comment->title );?>" />
						<span class="small" style="line-height: 22px;">(<?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT_REQUIRED'); ?>)</span>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label class="label" for="name"><?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT_AUTHOR_NAME'); ?></label>
					</td>
					<td>
						<input class="inputbox" type="text" id="name" name="name" size="45" value="<?php echo $this->escape( $this->comment->name );?>" />
						<span class="small" style="line-height: 22px;">(<?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT_REQUIRED'); ?>)</span>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label class="label" for="email"><?php echo JText::_('COM_EASYBLOG_EMAIL'); ?></label>
					</td>
					<td valign="top">
						<input class="inputbox" type="text" id="email" name="email" size="45" value="<?php echo $this->escape( $this->comment->email );?>" />
						<span class="small" style="line-height: 22px;">(<?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT_REQUIRED'); ?>)</span>
					</td>
				</tr>
				<tr>
					<td class="key"><label class="label" for="url"><?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT_AUTHOR_WEBSITE'); ?></label></td>
					<td valign="top">
						<input class="inputbox" type="text" id="url" name="url" size="45" value="<?php echo $this->escape( $this->comment->url );?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label class="label" for="comment"><?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT'); ?></label><br />
					</td>
					<td valign="top">
						<textarea id="comment" name="comment" class="inputbox" cols="50" rows="5"><?php echo $this->escape( $this->comment->comment );?></textarea>
						<div class="small" style="line-height: 22px;">(<?php echo JText::_('COM_EASYBLOG_COMMENTS_COMMENT_REQUIRED'); ?>)</div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="created"><?php echo JText::_( 'COM_EASYBLOG_COMMENTS_COMMENT_CREATED' ); ?></label>
					</td>
					<td valign="top">
						<?php echo JHTML::_('calendar', $this->comment->created , "created", "created", '%Y-%m-%d %H:%M:%S', array('size'=>'30')); ?>
					</td>
				</tr>
				<tr>
					<td class="key"><label for="published"><?php echo JText::_( 'COM_EASYBLOG_PUBLISHED' ); ?></label></td>
					<td>
						<?php echo $this->renderCheckbox( 'published' , $this->comment->published ); ?>
					</td>
				</tr>
			</table>
			</fieldset>
		</td>
		<td valign="top">&nbsp;</td>
	</tr>
</table>