<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
<div class="fsj">

<form action="<?php echo JRoute::_('index.php?option=com_fsj_main&view=settings&admin_com=' . JRequest::getVar('admin_com')); ?>" name="adminForm" id="adminForm" method="post">
<?php if (JRequest::getVar('tmpl') == "component"): ?>
	<fieldset>
		<div class="fltrt">
			<button type="button" onclick="Joomla.submitform('apply', this.form);">
				<?php echo JText::_('JAPPLY');?></button>
			<button type="button" onclick="Joomla.submitform('save', this.form);">
				<?php echo JText::_('JSAVE');?></button>
			<button type="button" onclick="<?php echo JRequest::getBool('refresh', 0) ? 'window.parent.location.href=window.parent.location.href;' : '';?>  window.parent.SqueezeBox.close();">
				<?php echo JText::_('JCANCEL');?></button>
		</div>
		<div class="configuration" >
			<?php echo $this->title; ?>
		</div>
	</fieldset>
<?php endif; ?>

<input name="task" type="hidden">
<input name="admin_com" type="hidden" value="<?php echo JRequest::getVar('admin_com'); ?>">
<?php if ($this->set != ""): ?>
	<input name="settings" type="hidden" value="<?php echo JRequest::getVar('settings'); ?>">
<?php endif; ?>
<?php 
echo JHtml::_('fsjtabs.start', "settings", array('useCookie'=>1));
	
foreach ($this->xml->tab as $tab) : 
$tab_id = (string)$tab->attributes()->id;	
?>

<?php echo JHtml::_('fsjtabs.panel', JText::_($tab->attributes()->name), "settings-tab-" . $tab_id); ?>

	<?php foreach ($this->xml->fields as $fieldset): $tdclass = "row1" ?>
		<?php if ((string)$fieldset->attributes()->tab != $tab_id) continue; ?>
		
				<div class="settings_block">
					<h3><?php echo JText::_($fieldset->attributes()->display); ?></h3>

					<table cellpadding="0" cellspacing="0" width="100%" class="table">
						<?php foreach ($fieldset->field as $field): ?>
							<?php $tdclass = ($tdclass == "row1") ? 'row0' : 'row1'; ?>
							<tr id="jform_<?php echo $fieldset->attributes()->name; ?>_<?php echo $field->attributes()->name; ?>-tr">
								<td class="<?php echo $tdclass; ?>" width="200"><?php echo $this->settings->getLabel($field->attributes()->name, $fieldset->attributes()->name); ?></td>
								<?php if ($field->attributes()->wide): ?>
									<td class="<?php echo $tdclass; ?>" width="320" class="fsj_settings_setting" colspan="2">
										<div class="control-group">
											<div class="controls">
												<?php echo $this->settings->getInput($field->attributes()->name, $fieldset->attributes()->name); ?>
											</div>
										</div>
										<?php if ($field->attributes()->useglobal): ?>
											<!--<div class="fsj_settings_globalvalue"><?php echo JText::_('FSJ_SETTINGS_CUR_GLOBAL'); ?> : <span class="fsj_settings_globalvalue_value">???</span></div>-->
										<?php endif; ?>
										<div class="fsj_settings_description"><?php echo JText::_($field->attributes()->description); ?></div>
									</td>
								<?php else: ?>
									<td class="<?php echo $tdclass; ?>" width="320" class="fsj_settings_setting">
										<div class="control-group">
											<div class="controls">
												<?php echo $this->settings->getInput($field->attributes()->name, $fieldset->attributes()->name); ?>
											</div>
										</div>
									</td>
									<?php if ($field->attributes()->description != ""): ?>
										<td class="<?php echo $tdclass; ?>" <?php if ($field->attributes()->helprows): ?> rowspan="<?php echo (string)$field->attributes()->helprows; ?>"<?php endif;?>>
											<?php if ($field->attributes()->useglobal): ?>
												<!--<div class="fsj_settings_globalvalue"><?php echo JText::_('FSJ_SETTINGS_CUR_GLOBAL'); ?> : <span class="fsj_settings_globalvalue_value">???</span></div>-->
											<?php endif; ?>
											<div class="fsj_settings_description"><?php echo JText::_($field->attributes()->description); ?></div>
										</td>
									<?php endif; ?>
								<?php endif; ?>
							</tr>
						<?php endforeach; ?>
					</table>
			</div>

	<?php endforeach; ?>
<div style="clear: both;"></div>

<?php endforeach; ?>

<?php 
echo JHtml::_('fsjtabs.panel', JText::_("FSJ_PERMISSIONS"), "settings-tab-perms");
echo $this->perm_form->getInput("rules");
echo JHtml::_('fsjtabs.end');
?>

</form>

<script>

jQuery(document).ready(function () {
    jQuery('.reset_field').click(function (ev) {
        ev.preventDefault();
        var field = jQuery(this).attr('id').split('|')[1];
        var url = '<?php echo JRoute::_('index.php?option=com_fsj_main&view=settings&admin_com='.$this->_com.'&task=resetsetting',false); ?>&setting=' + field;
		field = field.replace(".","_");
		jQuery('#' + field).val("Please Wait...");
		jQuery.get(url, function (data) { 
			jQuery('#' + field).val(data);
		});
    });
});
</script>

</div>