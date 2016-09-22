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
<form name="frmEditTag" id="frmEditTag">
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="reset-table">
	<tr>
		<td class="key" width="30%">
			<label for="title">
				<?php echo JText::_( 'COM_EASYBLOG_TAG' ); ?>
			</label>
		</td>
		<td>
			<input name="title" id="title" size="55" maxlength="255" value="<?php echo $this->escape( $tag->title );?>" />
		</td>
	</tr>
	<tr>
		<td class="key">
			<label for="alias">
				<?php echo JText::_('COM_EASYBLOG_TAG_ALIAS'); ?>
			</label>
		</td>
		<td>
			<input name="alias" id="alias" size="55" maxlength="255" value="<?php echo $this->escape( $tag->alias );?>" />
		</td>
	</tr>
	<tr>
		<td class="key">
			<label for="published"><?php echo JText::_( 'COM_EASYBLOG_PUBLISHED' ); ?></label>
		</td>
		<td>
			<?php echo JHTML::_('select.booleanlist', 'published', 'class="input text"', $tag->published ); ?>
		</td>
	</tr>
	<tr>
		<td class="key">
			<label for="created">
				<?php echo JText::_('COM_EASYBLOG_CREATED'); ?>
			</label>
		</td>
		<td>
			<?php
			    $createdDate    = EasyBlogDateHelper::getDate( $tag->created );
			?>
			<input type="text" name="created" id="created" value="<?php echo $createdDate->toFormat( $system->config->get( 'layout_systemdateformat' ) ); ?>" class="calendar"/>
		</td>
	</tr>
</table>
<input type="hidden" name="id" id="id" value="<?php echo $tag->id; ?>"/>

<div class="dialog-actions">
	<input type="button" value="<?php echo JText::_('COM_EASYBLOG_CANCEL');?>" class="button" id="edialog-cancel" name="edialog-cancel" onclick="ejax.closedlg();" />
	<input type="button" value="<?php echo JText::_('COM_EASYBLOG_SAVE');?>" class="button" id="edialog-submit" name="edialog-submit" onclick="eblog.tag.save();" />
</div>

</form>