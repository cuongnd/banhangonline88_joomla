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
<table width="100%" cellpadding="0" cellspacing="0">
<tr>
	<td>
		<fieldset class="adminform">
			<legend><?php echo JText::_('COM_EASYBLOG_GENERAL'); ?></legend>
			<table cellspacing="0" cellpadding="0" border="0" width="100%">
				<tr>
					<td valign="top">
						<table class="admintable">
							<tr>
								<td class="key">
									<label for="keywords">
										<?php echo JText::_( 'COM_EASYBLOG_META_TYPE_TITLE' ); ?>
									</label>
								</td>
								<td>
									<strong><?php echo $this->meta->title; ?></strong>
								</td>
							</tr>
							<tr>
								<td class="key">
									<span><?php echo JText::_( 'COM_EASYBLOG_META_TAG_ALLOW_INDEXING' ); ?></span>
								</td>
								<td>
									<div class="has-tip">
										<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_META_TAG_ALLOW_INDEXING_DESC' ); ?></div>
										<?php echo $this->renderCheckbox( 'indexing' , $this->meta->indexing ); ?>
									</div>									
								</td>
							</tr>
							<tr>
								<td class="key" style="vertical-align:top;">
									<label for="keywords">
										<?php echo JText::_( 'COM_EASYBLOG_META_TAG_EDIT_KEYWORDS' ); ?>
									</label>
								</td>
								<td>
									<div class="has-tip">
										<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_META_KEYWORD_TIPS' ); ?></div>
										<textarea id="keywords" name="keywords" class="inputbox" style="width: 300px;height:150px"><?php echo $this->meta->keywords; ?></textarea>
									</div>
								</td>
							</tr>
							<tr>
								<td class="key" style="vertical-align:top;">
									<span><?php echo JText::_( 'COM_EASYBLOG_META_TAG_EDIT_DESCRIPTION' ); ?></span>
								</td>
								<td>
									<div class="has-tip">
										<div class="tip"><i></i><?php echo JText::_( 'COM_EASYBLOG_META_DESC_TIPS' ); ?></div>
										<textarea id="description" name="description" class="inputbox" style="width: 300px;height:150px"><?php echo $this->meta->description; ?></textarea>
									</div>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>
		</fieldset>
	</td>
	<td width="50%">&nbsp;</td>
</tr>
</table>