<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	15 March 2012
 * @file name	:	views/admconfig/tmpl/editcustomfield.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Edit Custom Field (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 
 JHtml::_('behavior.framework', true);
 JHtml::_('behavior.formvalidation');
 JHtml::_('bootstrap.tooltip');
 JHtml::_('formbehavior.chosen', 'select');
 
 $app    = JFactory::getApplication();
 $type   = $app->input->get('type', '', 'string');
 $select = JblanceHelper::get('helper.select');		// create an instance of the class SelectHelper
?>
<script language="javascript" type="text/javascript">
<!--
	Joomla.submitbutton = function(task){
		if (task == 'admconfig.cancelcustomfield' || document.formvalidator.isValid(document.id('editcustomfield-form'))) {
			Joomla.submitform(task, document.getElementById('editcustomfield-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('COM_JBLANCE_FIEDS_HIGHLIGHTED_RED_COMPULSORY'));?>');
		}
	}

	window.addEvent('domready', function(){
		val = '<?php echo $this->row->value_type; ?>';
		type = '<?php echo $this->row->field_type; ?>';
		chooseVal(val);
		changeType(type);
	});
	
	var chooseVal = function(val){
		if(val == 'database'){
			document.getElementById('con_customValue').style.display = 'none';
			document.getElementById('con_databaseValue').style.display = '';
		}
		else {
			document.getElementById('con_customValue').style.display = '';
			document.getElementById('con_databaseValue').style.display = 'none';
		}
	}

	var changeType = function(type){
		if(type == 'Radio' || type == 'Checkbox' || type == 'Select' || type == 'Multiple Select'){
			$('radiocheckselect').setStyle('display', '');
			//show the searchPage and value type 'Custom/Database' only to 'Select'
			if(type == 'Select'){
				$('con_value_type').setStyle('display', '');
			}
			else {
				$('con_value_type').setStyle('display', 'none');
				chooseVal('custom');
			}
		}
		else {
			$('radiocheckselect').setStyle('display', 'none');
		}
		//show the search option only for the fg. fields
		if(type == 'Radio' || type == 'Checkbox' || type == 'Select' || type == 'Multiple Select' || type == 'Location')
			$('con_searchPage').setStyle('display', '');
		else
			$('con_searchPage').setStyle('display', 'none');
		
	}
//-->
</script>
<form action="index.php" method="post" id="editcustomfield-form" name="adminForm" class="form-validate form-horizontal">
<?php if($type != 'group'): ?><!-- Input fields to be shown while creating new "Field" -->
	<div class="row-fluid">
		<div class="span6">
			<fieldset>
			<legend><?php echo JText::_('COM_JBLANCE_FIELD_PROPERTIES'); ?></legend>
				<div class="control-group">
		    		<label class="control-label" for="field_title"><?php echo JText::_('COM_JBLANCE_FIELD_TITLE'); ?>:</label>
					<div class="controls">
						<input class="input-large input-large-text required" type="text" name="field_title" id="field_title" value="<?php echo $this->row->field_title; ?>" />
					</div>
		  		</div>
				<div class="control-group">
		    		<label class="control-label" for="field_for"><?php echo JText::_('COM_JBLANCE_FIELD_FOR'); ?>:</label>
					<div class="controls">
						<?php echo $this->lists['field_for']; ?>
					</div>
		  		</div>
				<div class="control-group">
		    		<label class="control-label" for="field_type"><?php echo JText::_('COM_JBLANCE_FIELD_TYPE'); ?>:</label>
					<div class="controls">
						<?php 
						// it is unadvisable to change the array content
						$types = array('Textbox', 'Textarea', 'Radio', 'Checkbox', 'Select', 'Multiple Select', 'URL', 'Email', 'Date', 'Birthdate', 'File');
						foreach($types as $key=>$value){
							$options[] = JHtml::_('select.option', $value, JText::_($value));
						}
						$fields = JHtml::_('select.genericlist', $options, 'field_type', "class='inputbox' size='1' onchange='changeType(this.value);'", 'value', 'text', $this->row->field_type);
						echo $fields;
						?>
					</div>
		  		</div>
				<div class="control-group">
		    		<label class="control-label" for="parent"><?php echo JText::_('COM_JBLANCE_SELECT_GROUP'); ?>:</label>
					<div class="controls">
						<?php echo $this->groups; ?>
					</div>
		  		</div>
				<div class="control-group">
		    		<label class="control-label" for="class"><?php echo JText::_('COM_JBLANCE_ADDITIONAL_CSS_CLASS'); ?>:</label>
					<div class="controls">
						<input class="input-large" type="text" name="class" id="class" value="<?php echo $this->row->class; ?>" />
					</div>
		  		</div>
				<div class="control-group">
		    		<label class="control-label" for="tips"><?php echo JText::_('COM_JBLANCE_TIPS'); ?>:</label>
					<div class="controls">
						<input class="input-large" type="text" name="tips" id="tips" value="<?php echo $this->row->tips; ?>" />
					</div>
		  		</div>
				<div class="control-group">
		    		<label class="control-label" for="published"><?php echo JText::_('JPUBLISHED'); ?>:</label>
					<div class="controls">
						<?php echo $select->YesNoBool('published', $this->row->published); ?>
					</div>
		  		</div>		  		
				<div class="control-group">
		    		<label class="control-label" for="required"><?php echo JText::_('COM_JBLANCE_REQUIRED'); ?>:</label>
					<div class="controls">
						<?php echo $select->YesNoBool('required', $this->row->required); ?>
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_CUSTOM_FIELD_VISIBLE_TIPS')); ?>
		    		<label class="control-label hasTooltip" for="visible" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_VISIBLE'); ?>:</label>
					<div class="controls">
						<?php 
						$options 	 = array();
						$attribs = array('class'=>'radio btn-group');
						$options[] = JHtml::_('select.option', 'all', JText::_('COM_JBLANCE_ALL'));
						$options[] = JHtml::_('select.option', 'personal', JText::_('COM_JBLANCE_PERSONAL'));
						$lists = $select->radiolist($options, 'visible', $attribs, 'value', 'text', $this->row->visible);
						echo $lists;
						?>
					</div>
		  		</div>		  		
				<div class="control-group" style="display:none;">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_SEARCH_PAGE')); ?>
		    		<label class="control-label" for="searchPage" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_VISIBLE'); ?>:</label>
					<div class="controls">
						<?php $searchPage = $select->YesNoBool('searchPage', $this->row->searchPage);
						echo  $searchPage; ?>
					</div>
		  		</div>		  		
			</fieldset>		
		</div>
		<div class="span6" id="radiocheckselect">
			<fieldset>
				<legend><?php echo JText::_('COM_JBLANCE_FOR_RADIO_CHECKBOX_SELECT_MULTIPLE'); ?></legend>
				<div class="control-group" style="display: none;">
		    		<label class="control-label" for="value_type"><?php echo JText::_('COM_JBLANCE_VALUE_TYPE'); ?>:</label>
					<div class="controls">
						<fieldset class="radio">
							<?php
							$put = array();
							$this->row->value_type = (!empty($this->row->value_type)) ? $this->row->value_type : 'custom';
							$put[] = JHtml::_('select.option', 'custom', JText::_('COM_JBLANCE_CUSTOM'));
							//$put[] = JHtml::_('select.option', 'database', JText::_('COM_JBLANCE_DATABASE'));
							echo JHtml::_('select.radiolist', $put, 'value_type', "onchange='chooseVal(this.value);'", 'value', 'text', $this->row->value_type);
							?>
						</fieldset>
					</div>
		  		</div>
				<div class="control-group" id="con_customValue">
		    		<label class="control-label" for="customValues"><?php echo JText::_('COM_JBLANCE_VALUES'); ?>:</label>
					<div class="controls">
						<textarea rows="4" name="customValues" id="customValues"><?php echo $this->row->value; ?></textarea>
						<span class="help-block"><?php echo JText::_('COM_JBLANCE_SEPARATED_SEMI_COLUMN'); ?></span>
					</div>
		  		</div>
				<!-- <div class="control-group" id="con_databaseValue">
		    		<label class="control-label" for="customValues"><?php echo JText::_('COM_JBLANCE_VALUES'); ?>:</label>
					<div class="controls">
						<?php 
						$select = JblanceHelper::get('helper.select');
						//echo $select->getSelectDatabaseTables('databaseValues', $this->row->value); ?>
					</div>
		  		</div> -->
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_SEARCH_PAGE')); ?>
		    		<label class="control-label" for="searchPage" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_VISIBLE'); ?>:</label>
					<div class="controls">
						<?php 
						$put = array();
						$attribs = array('class'=>'radio btn-group');
						$this->row->show_type = (!empty($this->row->show_type)) ? $this->row->show_type : 'left-to-right';
						$put[] = JHtml::_('select.option', 'left-to-right', JText::_('COM_JBLANCE_LEFT_RIGHT'));
						$put[] = JHtml::_('select.option', 'top-to-bottom', JText::_('COM_JBLANCE_TOP_BOTTOM'));
						$lists = $select->radiolist($put, 'show_type', $attribs, 'value', 'text', $this->row->show_type);
						echo $lists;
						?>
					</div>
		  		</div>
			</fieldset>		
		</div>
	</div>

	<?php else: ?><!-- Input fields to be shown while creating new "Group" -->
	<div class="">
		<div class="span12">
			<fieldset>
				<legend><?php echo JText::_('COM_JBLANCE_GROUP_PROPERTIES'); ?></legend>
				<div class="control-group">
		    		<label class="control-label" for="field_title"><?php echo JText::_('COM_JBLANCE_GROUP_TITLE'); ?>:</label>
					<div class="controls">
						<input class="input-large input-large-text required" type="text" name="field_title" id="field_title" value="<?php echo $this->row->field_title; ?>" />
					</div>
		  		</div>
				<div class="control-group">
		    		<label class="control-label" for="field_for"><?php echo JText::_('COM_JBLANCE_GROUP_FOR'); ?>:</label>
					<div class="controls">
						<?php echo $this->lists['field_for']; ?>
					</div>
		  		</div>
				<div class="control-group">
		    		<label class="control-label" for="gdesc"><?php echo JText::_('COM_JBLANCE_GROUP_DESCRIPTION'); ?>:</label>
					<div class="controls">
						<textarea id="gdesc" name="gdesc" rows="5"><?php echo $this->row->gdesc; ?></textarea>
					</div>
		  		</div>
				<div class="control-group">
		    		<label class="control-label" for="published"><?php echo JText::_('JPUBLISHED'); ?>:</label>
					<div class="controls">
						<?php echo $select->YesNoBool('published', $this->row->published); ?>
					</div>
		  		</div>
			</fieldset>
		</div>
	</div>

	<?php endif; ?>
	<input type="hidden" name="option" value="com_jblance" />
	<input type="hidden" name="view" value="admconfig" />
	<input type="hidden" name="layout" value="editcustomfield" />
	<input type="hidden" name="task" value="savefield" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="type" value="<?php echo $type; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>
