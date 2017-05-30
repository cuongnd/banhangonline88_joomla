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

<div id="write_container">
	
	<form name="frmEditTag" id="frmEditTag">
		<table width="100%" border="0" cellpadding="0" cellspacing="0" class="reset-table">
			<tr>
				<td valign="top">
					<table width="100%" border="0" cellpadding="0" cellspacing="0" class="reset-table">
						<tr>
							<td class="key">
								<label for="catname">
									<?php echo JText::_( 'COM_EASYBLOG_TAG' ); ?>
								</label>
							</td>
							<td>
								<input name="title" size="55" maxlength="255" value="<?php echo $tag->title;?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="alias">
									<?php echo JText::_('COM_EASYBLOG_TAG_ALIAS'); ?>
								</label>
							</td>
							<td>
								<input name="alias" size="55" maxlength="255" value="<?php echo $tag->alias;?>" />
							</td>
						</tr>
						<tr>
							<td>
								<label for="published"><?php echo JText::_( 'COM_EASYBLOG_PUBLISHED' ); ?></label>
							</td>
							<td>
								<?php echo JHTML::_('select.booleanlist', 'published', 'class="input text"', $tag->published ); ?>
							</td>
						</tr>
						<tr>
							<td>
								<label for="created">
									<?php echo JText::_('COM_EASYBLOG_CREATED'); ?>
								</label>
							</td>
							<td>
								<?php echo JHTML::_('calendar', $tag->created , "created", "created"); ?>
							</td>
						</tr>
					</table>
				</td>
			</tr>
		</table>
		<input type="hidden" name="id" id="id" value="<?php echo $tag->id; ?>"/>
	</form>
</div>