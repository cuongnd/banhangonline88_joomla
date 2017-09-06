<?php
/** 
 * @package JCHAT::FORM::components::com_jchat
 * @subpackage views
 * @subpackage form
 * @subpackage tmpl
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html
 */
defined('_JEXEC') or die('Restricted access');?>
<div class="item-page<?php echo $this->cparams->get('pageclass_sfx', null);?>">
	<?php if ($this->cparams->get('show_page_heading', 1)) : ?>
		<div class="page-header">
			<h3> <?php echo $this->escape($this->cparams->get('page_heading', $this->menuTitle)); ?> </h3>
		</div>
	<?php endif;?>
</div>
	
<form action="<?php echo JRoute::_('index.php?option=com_jchat&task=form.saveEntity&format=json');?>" method="post" name="activation_form" id="activation_form" dir="ltr">
	<fieldset class="jchat_fieldsets">
       	<div class="jchat_formfields_container">
			<div class="input-prepend">
		   		<span class="add-on"><?php echo JText::_('COM_JCHAT_NICKNAME');?></span>
				<input id="info_override_name" data-validation="required" type="text" name="override_name" value="<?php echo $this->userInfo->guestName;?>" size="18">
			</div>
			
			<?php if($this->cparams->get('show_email', true)):?>
				<div class="input-prepend">
			   		<span class="add-on"><?php echo JText::_('COM_JCHAT_EMAIL');?></span>
					<input id="info_email" data-validation="email <?php echo $this->cparams->get('validate_email', false) ? 'required' : '';?>" type="text" name="email" size="18">
				</div>
			<?php endif;?>
			
			<?php if($this->cparams->get('show_description', true)):?>
				<div class="input-prepend">
			   		<span class="add-on"><?php echo JText::_('COM_JCHAT_DESCRIPTION');?></span>
					<textarea id="info_description" data-validation="<?php echo $this->cparams->get('validate_description', false) ? 'required' : '';?>" name="description" size="18"></textarea>
				</div>
			<?php endif;?>
			
			<?php if($this->cparams->get('show_skypeid', false)):?>
				<div class="input-prepend">
			   		<span class="add-on"><?php echo JText::_('COM_JCHAT_SKYPEID');?></span>
					<input id="info_skypeid" data-validation="<?php echo $this->cparams->get('validate_skypeid', false) ? 'required' : '';?>" type="text" name="skypeid" size="18">
				</div>
			<?php endif;?>
			
			<?php if($this->cparams->get('show_antispam', false)):?>
				<div class="input-prepend">
			   		<span class="add-on"><?php echo JText::_('COM_JCHAT_SPAM_VALIDATION');?></span>
			   		<?php 
			   			$operand1 = rand(0, 10);
			   			$operand2 = rand(0, 10);
			   		?>
			   		<label class="validation_operand"><?php echo $operand1;?></label>
					<input type="hidden" data-role="validation_op1" name="validation_op1" value="<?php echo $operand1;?>" />
					<label class="validation_operand">+</label>
					<label class="validation_operand"><?php echo $operand2;?></label>
					<input type="hidden" data-role="validation_op2" name="validation_op2" value="<?php echo $operand2;?>" />
					<label class="validation_operand">=</label>
					<input type="text" data-role="validation_result" name="validation_result" size="18">
				</div>
			<?php endif;?>
		</div>
		<input type="submit" class="jchat_button button" value="<?php echo JText::_('COM_JCHAT_START_CHAT');?>"/>
		<input type="hidden" name="option" value="<?php echo $this->option;?>"/>
		<input type="hidden" name="task" value="form.saveEntity"/>
		<input type="hidden" name="format" value="json"/>
	</fieldset>
</form>