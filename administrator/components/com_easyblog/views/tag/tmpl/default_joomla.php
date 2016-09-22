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
	<td width="50%">
		<fieldset class="adminform">
		<legend><?php echo JText::_('COM_EASYBLOG_DETAILS'); ?></legend>
		<table class="admintable">
			<tr>
				<td class="key">
					<span><?php echo JText::_( 'COM_EASYBLOG_TAG_TITLE' ); ?></span>
				</td>
				<td>
					<input class="inputbox full-width" name="title" value="<?php echo $this->escape( $this->tag->title );?>" />
					<div class="small"><?php echo JText::_( 'COM_EASYBLOG_TAG_TITLE_TIPS' );?></div>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span><?php echo JText::_( 'COM_EASYBLOG_TAG_ALIAS' ); ?></span>
				</td>
				<td>
					<input class="inputbox full-width" name="alias" value="<?php echo $this->tag->alias;?>" />
					<div class="small"><?php echo JText::_( 'COM_EASYBLOG_TAG_ALIAS_TIPS' );?></div>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span><?php echo JText::_( 'COM_EASYBLOG_PUBLISHED' ); ?></span>
				</td>
				<td>
					<?php echo $this->renderCheckbox( 'published' , $this->tag->published );?>
					<div class="small"><?php echo JText::_( 'COM_EASYBLOG_TAG_PUBLISH_TIPS' );?></div>
				</td>
			</tr>
			<tr>
				<td class="key">
					<span><?php echo JText::_( 'COM_EASYBLOG_DEFAULT_TAG' ); ?></span>
				</td>
				<td>
					<?php echo $this->renderCheckbox( 'default' , $this->tag->default );?>
					<div class="small"><?php echo JText::_( 'COM_EASYBLOG_TAG_DEFAULT_TIPS' );?></div>
				</td>
			</tr>
			<tr>
				<td class="key">
					<label class="key"><?php echo JText::_('COM_EASYBLOG_AUTHOR'); ?></label>
				</td>
				<td>
					<input type="hidden" name="created_by" id="created_by" value="<?php echo $this->tag->get( 'created_by' );?>" />

					<span id="author-name" class="bubble-item"<?php if( empty($this->tag->created_by)){ ?> style="display: none;"<?php } ?>>
						<?php echo JFactory::getUser( $this->tag->get( 'created_by' , $this->my->id ) )->name; ?>
					</span>

					<a class="modal button" rel="{handler:'iframe',size:{x:650,y:375}}" href="index.php?option=com_easyblog&view=users&tmpl=component&browse=1&browsefunction=insertUser"><?php echo JText::_('COM_EASYBLOG_BROWSE_USERS');?></a>
					<div class="small"><?php echo JText::_( 'COM_EASYBLOG_TAG_AUTHOR_TIPS' );?></div>
				</td>
			</tr>
		</table>
		</fieldset>
	</td>
	<td width="50%" valign="top">&nbsp;</td>
</tr>
</table>