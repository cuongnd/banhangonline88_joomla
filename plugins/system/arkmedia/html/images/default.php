<?php
/**
 * @package     Joomla.Administrator
 * @subpackage  com_media
 *
 * @copyright   Copyright (C) 2005 - 2013 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;


$user  = JFactory::getUser();
$input = JFactory::getApplication()->input;
$params = JComponentHelper::getParams('com_media');
$lang = JFactory::getLanguage();
$lang->load('com_menus',JPATH_ADMINISTRATOR);

JHtml::_('formbehavior.chosen', 'select');


// Load tooltip instance without HTML support because we have a HTML tag in the tip
JHtml::_('bootstrap.tooltip', '.noHtmlTip', array('html' => false));

$jversion = new JVersion();
if( version_compare( $jversion->getShortVersion(), '3.4.8', 'gt' ) ) 
{	
	// Include jQuery
	JHtml::_('jquery.framework');
	JHtml::_('stylesheet', 'media/popup-imagemanager.css', array(), true);
	
	if ($lang->isRtl())
	{
		JHtml::_('stylesheet', 'media/popup-imagemanager_rtl.css', array(), true);
	}
	
}	

JHtml::script(JURI::root() .'plugins/system/arkmedia/js/popup-mediamanager.js', true);


JFactory::getDocument()->addScriptDeclaration(
	"
		var image_base_path = '" . $params->get('image_path', 'images') . "/';
	"
);


?>
<form action="index.php?option=com_media&amp;asset=<?php echo $input->getCmd('asset');?>&amp;arkmedia=1&amp;author=<?php echo $input->getCmd('author'); ?>" class="form-vertical" id="imageForm" method="post" enctype="multipart/form-data">
	<div id="messages" style="display: none;">
		<span id="message"></span><?php echo JHtml::_('image', 'media/dots.gif', '...', array('width' => 22, 'height' => 12), true) ?>
	</div>
	<div class="well">
		<div class="row">
			<div class="span9 control-group">
				<div class="control-label">
					<label class="control-label" for="folder"><?php echo JText::_('COM_MENUS_ITEM_FIELD_LINK_LABEL') ?></label>
				</div>
				<div class="controls">
					<?php echo $this->folderList; ?>
					<button class="btn" type="button" id="upbutton" title="<?php echo JText::_('COM_MEDIA_DIRECTORY_UP') ?>"><?php echo JText::_('COM_MEDIA_UP') ?></button>
				</div>
			</div>
			<div class="pull-right">
				<button class="btn btn-primary" type="button" onclick="<?php if ($this->state->get('field.id')):?>window.parent.jinsertfieldvalue(document.id('f_url').value,'<?php echo $this->state->get('field.id');?>');<?php else:?>MediaManager.onok();<?php endif;?>window.parent.SqueezeBox.close();"><?php echo JText::_('COM_MEDIA_INSERT') ?></button>
				<button class="btn" type="button" onclick="window.parent.SqueezeBox.close();"><?php echo JText::_('JCANCEL') ?></button>
			</div>
		</div>
	</div>
	<iframe id="imageframe" name="imageframe" src="index.php?option=com_media&amp;view=imagesList&amp;tmpl=component&amp;arkmedia=1&amp;folder=<?php echo $this->state->folder?>&amp;asset=<?php echo $input->getCmd('asset');?>&amp;author=<?php echo $input->getCmd('author');?>"></iframe>
	<div class="well">
		<div class="row">
			<div class="span6 control-group">
				<div class="control-label">
					<label for="f_url"><?php echo JText::_('URL') ?></label>
				</div>
				<div class="controls">
					<input type="text" id="f_url" value="" />
				</div>
			</div>
			<?php if (!$this->state->get('field.id')):?>
			<div class="span6 control-group">
				<div class="control-label">
					<label for="f_target"><?php echo JText::_('COM_MENUS_ITEM_FIELD_BROWSERNAV_LABEL') ?></label>
				</div>
				<div class="controls">
					<select size="1" id="f_target">
						<option value="" selected="selected"><?php echo JText::_('COM_MEDIA_NOT_SET') ?></option>
						<option value="_blank"><?php echo JText::_('JBROWSERTARGET_NEW') ?></option>
						<option value="_parent"><?php echo JText::_('JBROWSERTARGET_PARENT') ?></option>
					</select>
				</div>
			</div>
			<?php endif;?>
		</div>
		<?php if (!$this->state->get('field.id')):?>
		<div class="row">
			<div class="span6 control-group">
				<div class="control-label">
					<label for="f_title"><?php echo JText::_('JGLOBAL_TITLE') ?></label>
				</div>
				<div class="controls">
					<input type="text" id="f_title" value="" />
				</div>
			</div>
			<div class="span6 control-group">
				<div class="control-label">
					<label for="f_rel"><?php echo JText::_('Rel') ?></label>
				</div>
				<div class="controls">
					<input type="text" id="f_rel" value="" />
				</div>
			</div>
		</div>
		<div class="row">
			<div class="span6 control-group">
				<div class="control-label">
					<label for="f_text"><?php echo JText::_('Text') ?></label>
				</div>
				<div class="controls">
					<input type="text" id="f_text" value="" />
				</div>
			</div>
			<div class="span6 control-group"></div>
		</div>
		<?php endif;?>

		<input type="hidden" id="dirPath" name="dirPath" />
		<input type="hidden" id="f_file" name="f_file" />
		<input type="hidden" id="tmpl" name="component" />

	</div>
</form>

<?php if ($user->authorise('core.create', 'com_media')) : ?>
	<form action="<?php echo JUri::base(); ?>index.php?option=com_media&amp;task=file.upload&amp;tmpl=component&amp;arkmedia=1&amp;<?php echo $this->session->getName() . '=' . $this->session->getId(); ?>&amp;<?php echo JSession::getFormToken();?>=1&amp;asset=<?php echo $input->getCmd('asset');?>&amp;author=<?php echo $input->getCmd('author');?>&amp;view=images" id="uploadForm" class="form-horizontal" name="uploadForm" method="post" enctype="multipart/form-data">
		<div id="uploadform" class="well">
			<fieldset id="upload-noflash" class="actions">
				<div class="control-group">
					<div class="control-label">
						<label for="upload-file" class="control-label"><?php echo JText::_('COM_MEDIA_UPLOAD_FILE'); ?></label>
					</div>
					<div class="controls">
						<input type="file" id="upload-file" name="Filedata[]" multiple /><button class="btn btn-primary" id="upload-submit"><i class="icon-upload icon-white"></i> <?php echo JText::_('COM_MEDIA_START_UPLOAD'); ?></button>
						<p class="help-block"><?php echo $this->config->get('upload_maxsize') == '0' ? JText::_('COM_MEDIA_UPLOAD_FILES_NOLIMIT') : JText::sprintf('COM_MEDIA_UPLOAD_FILES', $this->config->get('upload_maxsize')); ?></p>
					</div>
				</div>
			</fieldset>
			<?php JFactory::getSession()->set('com_media.return_url', 'index.php?option=com_media&view=images&tmpl=component&arkmedia=1&fieldid=' . $input->getCmd('fieldid', '') . '&e_name=' . $input->getCmd('e_name') . '&asset=' . $input->getCmd('asset') . '&author=' . $input->getCmd('author')); ?>
		</div>
	</form>
<?php endif; ?>