<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	20 March 2012
 * @file name	:	views/admconfig/tmpl/editcategory.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Edit category (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 
 JHtml::_('behavior.framework', true);
 JHtml::_('behavior.formvalidation');
 JHtml::_('formbehavior.chosen', 'select');
?>
<script type="text/javascript">
<!--
	Joomla.submitbutton = function(task){
		if (task == 'admconfig.cancelcategory' || document.formvalidator.isValid(document.id('editcategory-form'))) {
			Joomla.submitform(task, document.getElementById('editcategory-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('COM_JBLANCE_FIEDS_HIGHLIGHTED_RED_COMPULSORY'));?>');
		}
	}
//-->
</script>

<form action="index.php" method="post" id="editcategory-form" name="adminForm" class="form-validate form-horizontal">
	<div class="row-fluid">
		<div class="span6">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_JBLANCE_DETAILS'); ?></legend>
				<div class="control-group">
		    		<label class="control-label" for="category"><?php echo JText::_('COM_JBLANCE_CATEGORY'); ?>:</label>
					<div class="controls">
						<input class="input-xlarge input-large-text required" type="text" name="category" id="category" value="<?php echo $this->row->category; ?>" />
					</div>
		  		</div>
				<div class="control-group">
		    		<label class="control-label" for="parent"><?php echo JText::_('COM_JBLANCE_PARENT_ITEM'); ?>:</label>
					<div class="controls">
						<?php 
						$select = JblanceHelper::get('helper.select');		// create an instance of the class SelectHelper
						$attribs = "class='input-xlarge' size='15'";
						$categtree = $select->getSelectCategoryTree('parent', $this->row->parent, 'COM_JBLANCE_ROOT_CATEGORY', $attribs, '', false, true);
						echo $categtree;
						?>
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