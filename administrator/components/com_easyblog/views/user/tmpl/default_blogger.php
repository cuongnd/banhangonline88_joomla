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
<table  width="100%" class="paramlist admintable" cellspacing="1">
	<tr>
	    <td class="key">
			<label for="title"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_PAGE_TITLE'); ?></label>
		</td>
        <td class="paramlist_value">
			<input id="title" name="title" value="<?php echo $this->escape( $this->blogger->title );?>" style="width: 350px;" />
		</td>
	</tr>
	<tr>
	    <td class="key" style="vertical-align: top;">
			<span><?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_PAGE_DESCRIPTION'); ?></span>
		</td>
        <td class="paramlist_value">
        	<textarea name="description" style="height:110px; width:80%;"><?php echo $this->blogger->getDescription( true );?></textarea>
		</td>
	</tr>
	<tr>
		<td class="key">
			<label for="nickname"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_NICKNAME'); ?></label>
		</td>
		<td class="paramlist_value">
			<input class="inputbox" type="text" id="nickname" name="nickname" value="<?php echo $this->escape( $this->blogger->nickname );?>" size="40" style="width: 350px;"/>
		</td>
	</tr>
	<tr>
		<td class="key" style="vertical-align: top;">
			<label for="user-avatar"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_AVATAR'); ?></label>
		</td>
		<td class="paramlist_value">
			<img id="user-avatar" src="<?php echo $this->blogger->getAvatar();?>" style="border: 1px solid #eee;" alt="<?php echo $this->escape( $this->user->get('name') );?>" />
			<?php if($this->avatarIntegration == 'default') { ?>
				<input type="file" name="Filedata" id="Filedata" style="display: block;" size="65" />
			<?php } ?>
		</td>
	</tr>
	<tr>
		<td class="key" style="vertical-align: top;">
			<label for="biography"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_BIOGRAPHY_INFO'); ?></label>
		</td>
		<td class="paramlist_value">

			<?php echo $this->editor->display( 'biography', $this->blogger->getBiography( true ) , '100%', '200', '10', '10' , array('pagebreak','ninjazemanta','image','readmore' , 'article') ); ?>
		</td>
	</tr>
	
	<tr>
		<td class="key">
			<label for="url"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_WEBSITE'); ?></label>
		</td>
		<td class="paramlist_value">
			<input type="text" name="url" id="url" value="<?php echo $this->escape( $this->blogger->url );?>" size="40" style="width: 350px;" />
		</td>
	</tr>
	
	<?php if( EasyBlogHelper::isSiteAdmin() ) : ?>
	<tr>
		<td class="key">
			<label for="url"><?php echo JText::_('COM_EASYBLOG_BLOGGERS_EDIT_PERMALINK'); ?></label>
		</td>
		<td class="paramlist_value">
			<input type="text" name="user_permalink" id="user_permalink" value="<?php echo $this->escape( $this->blogger->permalink );?>" size="40" style="width: 350px;" />
			<div class="small"><?php echo JText::_( 'COM_EASYBLOG_BLOGGERS_EDIT_PERMALINK_USAGE' ); ?></div>
		</td>
	</tr>
	<?php endif; ?>
</table>