<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	25 September 2013
 * @file name	:	views/admconfig/tmpl/editduration.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Edit Project Duration (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 
 JHtml::_('behavior.framework', true);
 JHtml::_('behavior.formvalidation');
 JHtml::_('formbehavior.chosen', 'select');
 
 $model = $this->getModel();
 $select = JblanceHelper::get('helper.select');		// create an instance of the class SelectHelper
 ?>
 <script type="text/javascript">
 <!--
	 Joomla.submitbutton = function(task){
		 if (task == 'admconfig.cancelduration' || document.formvalidator.isValid(document.id('editduration-form'))) {
		 	Joomla.submitform(task, document.getElementById('editduration-form'));
		 }
		 else {
		 	alert('<?php echo $this->escape(JText::_('COM_JBLANCE_FIEDS_HIGHLIGHTED_RED_COMPULSORY'));?>');
		 }
	 }

 	window.addEvent('domready', function(){
		$$('input[name="less_great"]').addEvent('click', processLessGreatCheckbox);
		processLessGreatCheckbox();
	});

 	function processLessGreatCheckbox(){
 		var checkedval = $$('input[name="less_great"]:checked').get('value');
		if(checkedval == 'less'){
			$('duration_from').setProperty('disabled', true);
			$('duration_from_type').setProperty('disabled', true);
			$('duration_to').setProperty('disabled', false);
			$('duration_to_type').setProperty('disabled', false);
		}
		else if(checkedval == 'great'){
			$('duration_from').setProperty('disabled', false);
			$('duration_from_type').setProperty('disabled', false);
			$('duration_to').setProperty('disabled', true);
			$('duration_to_type').setProperty('disabled', true);
		}
	}
 //-->
 </script>
<form action="index.php" method="post" id="editduration-form" name="adminForm" class="form-validate form-horizontal">
 	<div class="row-fluid">
		<div class="span8">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_JBLANCE_DETAILS'); ?></legend>
				<div class="control-group">
		    		<label class="control-label" for="duration_from"><?php echo JText::_('COM_JBLANCE_DURATION_FROM'); ?>:</label>
					<div class="controls controls-row">
						<input class="input-mini required" type="text" name="duration_from" id="duration_from" value="<?php echo $this->row->duration_from; ?>" />
						<?php $dur_from = $model->getSelectDuration('duration_from_type', $this->row->duration_from_type, 0, '');
				    	echo  $dur_from; ?>
						<input type="radio" name="less_great" value="less" <?php  echo ($this->row->less_great == '<') ? 'checked' : '';?> />
  						<?php echo JText::_('COM_JBLANCE_LESS_THAN'); ?>
					</div>
		  		</div>
				<div class="control-group">
		    		<label class="control-label" for="duration_to"><?php echo JText::_('COM_JBLANCE_DURATION_TO'); ?>:</label>
					<div class="controls controls-row">
						<input class="input-mini required" type="text" name="duration_to" id="duration_to" value="<?php echo $this->row->duration_to; ?>" />
						<?php $dur_to = $model->getSelectDuration('duration_to_type', $this->row->duration_to_type, 0, '');
				    	echo  $dur_to; ?>
						<input type="radio" name="less_great" value="great" <?php  echo ($this->row->less_great == '>') ? 'checked' : '';?> />
						<?php echo JText::_('COM_JBLANCE_GREATER_THAN'); ?>
					</div>
		  		</div>
			</fieldset>
		</div>
	 </div>
	<input type="hidden" name="option" value="com_jblance" />
	<input type="hidden" name="task" value="saveduration" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
    <?php echo JHtml::_('form.token'); ?>
</form>