<?php 
/** 
 * @package JCHAT::CONFIG::administrator::components::com_jchat
 * @subpackage views
 * @subpackage config
 * @subpackage tmpl
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html 
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' ); 
?> 
<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-validate">  
	<?php 
	$fieldSets = $this->params_form->getFieldsets();
	$tabs = array();
	$contents = array();
	foreach ($fieldSets as $name => $fieldSet) :
		$label = empty($fieldSet->label) ? JText::_('COM_JCHAT_'. strtoupper($name) .'_FIELDSET_LABEL') : JText::_($fieldSet->label);
		$tabs[] = "<li><a href='#$fieldSet->id' data-toggle='tab' data-element='$fieldSet->id'>$label</a></li>";
		ob_start(); ?>
		<div id="<?php echo $fieldSet->id;?>" class="tab-pane">
		<?php  
		foreach ($this->params_form->getFieldset($name) as $field):?>
			<div class="control-group <?php echo $field->class != 'btn-group' ? str_replace(array('label', 'label-info', 'btn-group'), '', $field->class) : null;?>">
				<div class="control-label"><?php echo $field->label; ?></div>
				<div class="controls"><?php echo $field->input; ?></div>
			</div>
		<?php endforeach; ?>
		</div>
		<?php $contents[] = ob_get_clean();?>
	<?php endforeach; ?>
	
	<ul id="tab_configuration" class="nav nav-tabs"><?php echo implode('', $tabs);?></ul>
	<div id="config-responsivizer" class="tab-content current"><?php echo implode('', $contents);?></div> 
	<input type="hidden" name="option" value="<?php	echo $this->option;?>" /> 
	<input type="hidden" name="task" value="" />
</form> 