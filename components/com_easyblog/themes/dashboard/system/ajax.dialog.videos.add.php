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
<p><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_INSERT_VIDEO_DESC' );?> <?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_INSERT_VIDEO_SUPPORTED' );?></p>
<div class="eblog-nbsp"></div>
<table width="100%" border="0" cellpadding="0" cellspacing="0" class="reset-table">
	<tr>
		<td class="key" width="30%">
			<label for="title"><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_INSERT_VIDEO_SOURCE' ); ?></label>
		</td>
		<td>
			<input type="text" name="video-source" id="video-source" class="full" />
		</td>
	</tr>
	<tr>
		<td class="key"><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_INSERT_VIDEO_WIDTH' );?></td>
		<td><input type="text" name="video-width" id="video-width" style="width: 30px;" value="<?php echo $system->config->get( 'dashboard_video_width' );?>" /> <?php echo JText::_( 'COM_EASYBLOG_PIXELS' );?></td>
	</tr>
	<tr>
		<td class="key"><?php echo JText::_( 'COM_EASYBLOG_DASHBOARD_WRITE_INSERT_VIDEO_HEIGHT' );?></td>
		<td><input type="text" name="video-height" id="video-height" style="width: 30px;" value="<?php echo $system->config->get( 'dashboard_video_height' );?>" /> <?php echo JText::_( 'COM_EASYBLOG_PIXELS' );?></td>
	</tr>
</table>

<?php echo JHTML::_( 'form.token' ); ?>	
<div class="dialog-actions">
	<input type="button" value="<?php echo JText::_( 'COM_EASYBLOG_CANCEL_BUTTON' );?>" class="button" id="edialog-cancel" name="edialog-cancel" onclick="ejax.closedlg();" />
	<input type="button" value="<?php echo JText::_( 'COM_EASYBLOG_INSERT_BUTTON' );?>" class="button" onclick="eblog.dashboard.videos.insert( '<?php echo $editorName;?>' );" />
</div>
