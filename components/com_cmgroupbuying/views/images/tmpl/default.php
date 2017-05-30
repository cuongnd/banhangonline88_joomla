<?php
/**
 * This file is taken from com_media
 * There are some changes to let partners in CMGroupBuying only have access to their own folders.
 */

/**
 * @package		Joomla.Administrator
 * @subpackage	com_media
 * @copyright	Copyright (C) 2005 - 2012 Open Source Matters, Inc. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access.
defined('_JEXEC') or die;
$user   = JFactory::getUser();
$params = JComponentHelper::getParams('com_media');
$jinput = JFactory::getApplication()->input;
if($this->configuration['partner_folder'] != '')
{
	$partnerFolder = $params->get('image_path', 'images') . '/' . $this->configuration['partner_folder'] . '/' . $user->username;
}
else
{
	$partnerFolder = $params->get('image_path', 'images') . '/' . $user->username;
}
?>
<script type='text/javascript'>
var image_base_path = '<?php echo $partnerFolder; ?>/';
</script>
	<div id="messages" style="display: none;">
		<span id="message"></span><?php echo JHtml::_('image', 'media/dots.gif', '...', array('width' =>22, 'height' => 12), true)?>
	</div>
	<fieldset>
		<div class="fltrt">
			<button type="button" onclick="<?php if ($this->state->get('field.id')):?>window.parent.jInsertFieldValue(document.id('f_url').value,'<?php echo $this->state->get('field.id');?>');<?php else:?>ImageManager.onok();<?php endif;?>window.parent.SqueezeBox.close();"><?php echo JText::_('COM_MEDIA_INSERT') ?></button>
			<button type="button" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('JCANCEL') ?></button>
		</div>
	</fieldset>

	<iframe id="imageframe" name="imageframe" src="index.php?option=com_cmgroupbuying&amp;view=imagesList&amp;tmpl=component&amp;folder=<?php echo $this->state->folder?>&amp;asset=<?php echo $jinput->getCmd('asset');?>&amp;author=<?php echo $jinput->getCmd('author');?>"></iframe>

	<fieldset>
		<table class="properties">
			<tr>
				<td><label for="f_url"><?php echo JText::_('COM_MEDIA_IMAGE_URL') ?></label></td>
				<td><input type="text" id="f_url" value="" /></td>
			</tr>
		</table>

		<input type="hidden" id="dirPath" name="dirPath" />
		<input type="hidden" id="f_file" name="f_file" />
		<input type="hidden" id="tmpl" name="component" />

	</fieldset>

<?php if ($user->authorise('core.create', 'com_media')): ?>
	<form action="<?php echo JURI::base(); ?>index.php?option=com_cmgroupbuying&amp;controller=file&amp;task=upload&amp;tmpl=component&amp;<?php echo $this->session->getName() . '=' . $this->session->getId(); ?>&amp;<?php echo JSession::getFormToken();?>=1&amp;asset=<?php echo $jinput->getCmd('asset');?>&amp;author=<?php echo $jinput->getCmd('author');?>&amp;view=images" id="uploadForm" class="form-horizontal" name="uploadForm" method="post" enctype="multipart/form-data">
		<fieldset id="uploadform">
			<legend><?php echo $this->config->get('upload_maxsize')=='0' ? JText::_('COM_MEDIA_UPLOAD_FILES_NOLIMIT') : JText::sprintf('COM_MEDIA_UPLOAD_FILES', $this->config->get('upload_maxsize')); ?></legend>
			<fieldset id="upload-noflash" class="actions">
				<label for="upload-file" class="hidelabeltxt"><?php echo JText::_('COM_MEDIA_UPLOAD_FILE'); ?></label>
				<input type="file" id="upload-file" name="Filedata[]" multiple />
				<label for="upload-submit" class="hidelabeltxt"><?php echo JText::_('COM_MEDIA_START_UPLOAD'); ?></label>
				<input type="submit" id="upload-submit" value="<?php echo JText::_('COM_MEDIA_START_UPLOAD'); ?>"/>
			</fieldset>
			<div id="upload-flash" class="hide">
				<ul>
					<li><a href="#" id="upload-browse"><?php echo JText::_('COM_MEDIA_BROWSE_FILES'); ?></a></li>
					<li><a href="#" id="upload-clear"><?php echo JText::_('COM_MEDIA_CLEAR_LIST'); ?></a></li>
					<li><a href="#" id="upload-start"><?php echo JText::_('COM_MEDIA_START_UPLOAD'); ?></a></li>
				</ul>
				<div class="clr"> </div>
				<p class="overall-title"></p>
				<?php echo JHtml::_('image', 'media/bar.gif', JText::_('COM_MEDIA_OVERALL_PROGRESS'), array('class' => 'progress overall-progress'), true); ?>
				<div class="clr"> </div>
				<p class="current-title"></p>
				<?php echo JHtml::_('image', 'media/bar.gif', JText::_('COM_MEDIA_CURRENT_PROGRESS'), array('class' => 'progress current-progress'), true); ?>
				<p class="current-text"></p>
			</div>
			<ul class="upload-queue" id="upload-queue">
				<li style="display: none"></li>
			</ul>
			<input type="hidden" name="return-url" value="<?php echo base64_encode('index.php?option=com_cmgroupbuying&view=images&tmpl=component&fieldid=' . $jinput->getCmd('fieldid', '') . '&e_name=' . $jinput->getCmd('e_name') . '&asset=' . $jinput->getCmd('asset') . '&author=' . $jinput->getCmd('author')); ?>" />
		</fieldset>
	</form>
<?php  endif; ?>