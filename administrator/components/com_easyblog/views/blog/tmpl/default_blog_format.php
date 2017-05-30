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
<ul class="list-form reset-ul">
	<?php if( $this->config->get( 'main_microblog') ){ ?>
	<li>
		<label for="authorId"><?php echo JText::_( 'COM_EASYBLOG_POST_FORMAT' ); ?></label>
		<div>
			<div class="has-tip tip-below">
				<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_POST_FORMAT_DESC' );?></div>
				<select name="source" class="inputbox">
					<option value=""<?php echo $this->blog->source == '' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_STANDARD_POST' );?></option>
					<option value="photo"<?php echo $this->blog->source == 'photo' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_PHOTO' );?></option>
					<option value="video"<?php echo $this->blog->source == 'video' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_VIDEO' );?></option>
					<option value="quote"<?php echo $this->blog->source == 'quote' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_QUOTE' );?></option>
					<option value="link"<?php echo $this->blog->source == 'link' ? ' selected="selected"' : '';?>><?php echo JText::_( 'COM_EASYBLOG_MICROBLOG_LINK' );?></option>
				</select>
			</div>
		</div>
	</li>
	<?php } ?>
	
	<?php if( EasyBlogHelper::getJoomlaVersion() >= '1.6' && $this->config->get( 'main_multi_language' ) ){ ?>
	<li>
		<label for="authorId"><?php echo JText::_( 'COM_EASYBLOG_POST_LANGUAGE' ); ?></label>
		<div>
			<div class="has-tip">
				<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_POST_LANGUAGE_DESC' );?></div>
				<select name="eb_language" class="inputbox">
					<option value=""><?php echo JText::_('JOPTION_SELECT_LANGUAGE');?></option>

					<?php echo JHtml::_('select.options', JHtml::_('contentlanguage.existing', true, true), 'value', 'text' , $this->blog->language );?>
				</select>
			</div>
		</div>
	</li>
	<?php } ?>
</ul>