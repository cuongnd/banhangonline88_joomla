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
<table width="100%">
<tr>
    <td width="50%" valign="top">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_EASYBLOG_FEEDS_DETAILS'); ?></legend>
			<table class="admintable">
				<tr>
					<td class="key">
						<label for="title"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_TITLE' ); ?></label>
					</td>
					<td>
						<input class="inputbox" id="title" name="title" size="55" maxlength="255" value="<?php echo $this->feed->title; ?>"/>
						<div class="small"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_TITLE_DESC' ); ?></div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="url"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_URL' ); ?></label>
					</td>
					<td>
						<input class="inputbox" id="url" name="url" size="55" value="<?php echo $this->feed->get( 'url' );?>" />
						<div class="small"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_URL_DESC' ); ?></div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="title"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_PUBLISHED' ); ?></label>
					</td>
					<td>
						<?php echo $this->renderCheckbox( 'published' , $this->feed->get( 'published' ) ); ?>
						<div class="small" style="clear:both;"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_PUBLISHED_DESC' ); ?></div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="title"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_CRON' ); ?></label>
					</td>
					<td>
						<?php echo $this->renderCheckbox( 'cron' , $this->feed->get( 'cron' ) ); ?>
						<div class="small" style="clear:both;"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_CRON_DESC' ); ?></div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="interval"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_CRON_INTERVAL' ); ?></label>
					</td>
					<td>
						<input class="inputbox" id="interval" name="interval" size="3" style="text-align: center;" value="<?php echo $this->feed->get( 'interval' );?>" /> <?php echo JText::_( 'COM_EASYBLOG_MINUTES' );?>
						<div class="small"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_CRON_INTERVAL_DESC' ); ?></div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="title"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_SHOW_AUTHOR' ); ?></label>
					</td>
					<td>
						<?php echo $this->renderCheckbox( 'author' , $this->feed->get( 'author' ) ); ?>
						<div class="small" style="clear:both;"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_SHOW_AUTHOR_DESC' ); ?></div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="title"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_COPYRIGHT_TEXT' ); ?></label>
					</td>
					<td>
						<input type="text" class="text inputbox" name="copyrights" size="55" value="<?php echo $this->params->get( 'copyrights' );?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="title"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_INCLUDE_ORIGINAL_LINK' ); ?></label>
					</td>
					<td>
						<?php echo $this->renderCheckbox( 'sourceLinks' , $this->params->get( 'sourceLinks' ) ); ?>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="title"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_AMOUNT' ); ?></label>
					</td>
					<td>
						<input type="text" class="text inputbox" name="feedamount" size="3" value="<?php echo $this->params->get( 'feedamount' );?>" />
						<div class="small" style="clear:both;"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_AMOUNT_DESC' ); ?></div>
					</td>
				</tr>
			</table>
		</fieldset>
	</td>
	<td width="50%" valign="top">
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_EASYBLOG_FEEDS_PUBLISHING_DETAILS'); ?></legend>
			<table class="admintable">
				<tr>
					<td class="key">
						<label><?php echo JText::_( 'COM_EASYBLOG_FEEDS_PUBLISH_ITEM' ); ?></label>
					</td>
					<td>
						<?php echo $this->renderCheckbox( 'item_published' , $this->feed->get( 'item_published' ) ); ?>
						<div class="small" style="clear:both;"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_PUBLISH_ITEM_DESC' ); ?></div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_( 'COM_EASYBLOG_FEEDS_PUBLISH_FRONTPAGE' ); ?></label>
					</td>
					<td>
						<?php echo $this->renderCheckbox( 'item_frontpage' , $this->feed->get( 'item_frontpage' ) ); ?>
						<div class="small" style="clear:both;"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_PUBLISH_FRONTPAGE_DESC' ); ?></div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_( 'COM_EASYBLOG_FEEDS_PUBLISH_AUTOPOST' ); ?></label>
					</td>
					<td>
						<?php echo $this->renderCheckbox( 'autopost' , $this->params->get( 'autopost' ) ); ?>
						<div class="small" style="clear:both;"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_PUBLISH_AUTOPOST_DESC' ); ?></div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_( 'COM_EASYBLOG_FEEDS_CATEGORY' ); ?></label>
					</td>
					<td>
						<span id="category_name"><?php echo ( !empty($this->categoryName) ) ? $this->categoryName : ''; ?></span>
						<a href="index.php?option=com_easyblog&amp;view=categories&amp;tmpl=component&amp;browse=1" rel="{handler: 'iframe', size: {x: 750, y: 475}}" class="modal button"><?php echo JText::_( 'COM_EASYBLOG_SELECT_CATEGORY' );?></a>
						<input type="hidden" name="item_category" value="<?php echo $this->feed->get( 'item_category' );?>" id="item_category" />
						<div class="small"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_CATEGORY_DESC' ); ?></div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_( 'COM_EASYBLOG_FEEDS_AUTHOR' ); ?></label>
					</td>
					<td>
						<span id="author_name"><?php echo ( !empty($this->authorName) ) ? $this->authorName : ''; ?></span>
						<a href="index.php?option=com_easyblog&amp;view=users&amp;tmpl=component&amp;browse=1" rel="{handler: 'iframe', size: {x: 750, y: 475}}" class="modal button"><?php echo JText::_( 'COM_EASYBLOG_SELECT_AUTHOR' );?></a>
						<input type="hidden" name="item_creator" value="<?php echo $this->feed->get( 'item_creator' );?>" id="item_creator" />
						<div class="small" style="clear:both;"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_AUTHOR_DESC' ); ?></div>
					</td>
				</tr>

				<tr>
					<td class="key">
						<label><?php echo JText::_( 'COM_EASYBLOG_FEEDS_GET_FULL_TEXT' ); ?></label>
					</td>
					<td>
						<?php echo $this->renderCheckbox( 'item_get_fulltext' , $this->feed->get( 'item_get_fulltext' ) ); ?>
						<div class="small" style="clear:both;"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_GET_FULL_TEXT_DESC' ); ?></div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_( 'COM_EASYBLOG_FEEDS_STORE_CONTENT_TYPE' ); ?></label>
					</td>
					<td>
						<select name="item_content" class="select">
							<option value="intro" <?php echo ($this->feed->item_content == 'intro') ? 'selected' : '' ; ?> ><?php echo JText::_( 'COM_EASYBLOG_FEEDS_INTROTEXT' ); ?></option>
							<option value="content" <?php echo ($this->feed->item_content == 'content') ? 'selected' : '' ; ?>><?php echo JText::_( 'COM_EASYBLOG_FEEDS_MAINTEXT' ); ?></option>
						</select>
						<div class="small" style="clear:both;"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_STORE_CONTENT_TYPE_DESC' ); ?></div>
					</td>
				</tr>
				<tr>
					<td class="key">
						<label><?php echo JText::_( 'COM_EASYBLOG_FEEDS_ALLOWED_TAGS' ); ?></label>
					</td>
					<td>
						<input type="text" name="item_allowed_tags" value="<?php echo $this->params->get( 'allowed' , '<img>,<a>,<br>,<table>,<tbody>,<th>,<tr>,<td>,<div>,<span>,<p>,<h1>,<h2>,<h3>,<h4>,<h5>,<h6>' ); ?>" class="inputbox full-width" />
						<div class="small" style="clear:both;"><?php echo JText::_( 'COM_EASYBLOG_FEEDS_ALLOWED_TAGS_DESC' ); ?></div>
					</td>
				</tr>
			</table>
		</fieldset>
	</td>
</tr>
</table>
