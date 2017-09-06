<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	28 July 2012
 * @file name	:	views/admconfig/tmpl/editbudget.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Edit budget range (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 
 JHtml::_('behavior.framework', true);
 JHtml::_('behavior.formvalidation');
 
 $select = JblanceHelper::get('helper.select');		// create an instance of the class SelectHelper
 $config = JblanceHelper::getConfig();
 $currencysym = $config->currencySymbol;
 ?>
 <script type="text/javascript">
 <!--
	 Joomla.submitbutton = function(task){

	 	if(task != 'admconfig.cancelbudget'){
		 	var budgetmin = $('budgetmin').get('value').toFloat();
			var budgetmax = $('budgetmax').get('value').toFloat();

			if(budgetmin > budgetmax){
				alert('<?php echo JText::_('COM_JBLANCE_MIN_BUDGET_MORE_THAN_MAX_BUDGET'); ?>');
				return false;
			}
		 }
		 
		 if (task == 'admconfig.cancelbudget' || document.formvalidator.isValid(document.id('editbudget-form'))) {
		 	Joomla.submitform(task, document.getElementById('editbudget-form'));
		 }
		 else {
		 	alert('<?php echo $this->escape(JText::_('COM_JBLANCE_FIEDS_HIGHLIGHTED_RED_COMPULSORY'));?>');
		 }
	 }
 //-->
 </script>
<form action="index.php" method="post" id="editbudget-form" name="adminForm" class="form-validate form-horizontal">
	<div class="row-fluid">
		<div class="span6">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_JBLANCE_DETAILS'); ?></legend>
				<div class="control-group">
		    		<label class="control-label" for="title"><?php echo JText::_('COM_JBLANCE_TITLE'); ?>:</label>
					<div class="controls">
						<input class="input-xlarge input-large-text required" type="text" name="title" id="title" value="<?php echo $this->row->title; ?>" />
					</div>
		  		</div>
				<div class="control-group">
		    		<label class="control-label" for="budgetmin"><?php echo JText::_('COM_JBLANCE_MINIMUM_BUDGET'); ?>:</label>
					<div class="controls">
						<div class="input-prepend">
							<span class="add-on"><?php echo $currencysym; ?></span>
							<input class="input-small required" type="text" name="budgetmin" id="budgetmin" value="<?php echo number_format($this->row->budgetmin, 0, '.', ''); ?>" />
						</div>
					</div>
		  		</div>
				<div class="control-group">
		    		<label class="control-label" for="budgetmax"><?php echo JText::_('COM_JBLANCE_MAXIMUM_BUDGET'); ?>:</label>
					<div class="controls">
						<div class="input-prepend">
							<span class="add-on"><?php echo $currencysym; ?></span>
							<input class="input-small required" type="text" name="budgetmax" id="budgetmax" value="<?php echo number_format($this->row->budgetmax, 0, '.', ''); ?>" />
						</div>
					</div>
		  		</div>
				<div class="control-group">
		    		<label class="control-label" for="project_type"><?php echo JText::_('COM_JBLANCE_PROJECT_TYPE'); ?>:</label>
					<div class="controls">
						<?php 
						$default = empty($this->row->project_type) ? 'COM_JBLANCE_FIXED' : $this->row->project_type; 
						$project_type = $select->getRadioProjectType('project_type', $default);
						echo  $project_type; ?>
					</div>
		  		</div>
			</fieldset>
		</div>
	</div>
	
	<input type="hidden" name="option" value="com_jblance" />
	<input type="hidden" name="task" value="savecategory" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
    <?php echo JHtml::_('form.token'); ?>
</form>