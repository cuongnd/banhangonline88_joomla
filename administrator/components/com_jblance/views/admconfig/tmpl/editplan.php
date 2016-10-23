<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	15 March 2012
 * @file name	:	views/admconfig/tmpl/editplan.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Shows list of Plans(jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 
 JHtml::_('behavior.framework', true);
 JHtml::_('behavior.formvalidation');
 JHtml::_('formbehavior.chosen', 'select');
 JHtml::_('bootstrap.tooltip');
  
 //$editor = JFactory::getEditor();
 $model = $this->getModel();
 $select = JblanceHelper::get('helper.select');		// create an instance of the class selectHelper
 
 $config = JblanceHelper::getConfig();
 $currencysym = $config->currencySymbol;
 
 ?>
<script type="text/javascript">
<!--
	Joomla.submitbutton = function(task){
		if (task == 'admconfig.cancelplan' || document.formvalidator.isValid(document.id('editplan-form'))) {
			Joomla.submitform(task, document.getElementById('editplan-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('COM_JBLANCE_FIEDS_HIGHLIGHTED_RED_COMPULSORY'));?>');
		}
	}
//-->
</script>
<form action="index.php" method="post" id="editplan-form" name="adminForm" class="form-validate form-horizontal">
	<div class="row-fluid">
		<div class="span6">
			<fieldset>
				<legend><?php echo JText::_('COM_JBLANCE_PLAN_SETTINGS'); ?></legend>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_PLAN_NAME_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="name" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_PLAN_NAME'); ?>:</label>
					<div class="controls">
						<input type="text" class="input-xlarge input-large-text required" name="name" id="name" value="<?php echo $this->row->name; ?>" />
					</div>
		  		</div>
				<div class="control-group">
		    		<label class="control-label" for="ug_id"><?php echo JText::_('COM_JBLANCE_USER_GROUP'); ?>:</label>
					<div class="controls">
						<?php
						$attribs = 'class="input-large required" size="1"';
		          		$group = $select->getSelectUserGroups('ug_id', $this->row->ug_id, 'COM_JBLANCE_SELECT_USERGROUP', $attribs, '');
				    	echo  $group; ?>
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_DURATION_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="days" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_DURATION'); ?>:</label>
					<div class="controls controls-row">
						<input type="text" class="input-mini required" name="days" id="days" value="<?php echo $this->row->days; ?>" />
						<?php $dur = $model->getSelectDuration('days_type', $this->row->days_type, 0, '');
					    echo  $dur; ?>
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_LIMIT_TIMES_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="time_limit" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_LIMIT'); ?>:</label>
					<div class="controls">
						<div class="input-append">
							<input type="text" class="input-mini required" id="time_limit" name="time_limit" value="<?php echo $this->row->time_limit; ?>" />
							<span class="add-on"><?php echo JText::_('COM_JBLANCE_TIMES'); ?></span>
						</div>
					</div>
		  		</div>	  		
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_PLAN_PRICE_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="price" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_PRICE'); ?>:</label>
					<div class="controls">
						<div class="input-prepend">
							<span class="add-on"><?php echo $currencysym; ?></span>
							<input type="text" class="input-small required" id="price" name="price" value="<?php echo $this->row->price; ?>" />
						</div>
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_NEXT_TIME_DISCOUNT_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="discount" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_NEXT_TIME_DISCOUNT'); ?>:</label>
					<div class="controls">
						<div class="input-append">
							<input type="text" class="input-mini required" name="discount" id="discount" value="<?php echo $this->row->discount; ?>" />
							<span class="add-on">%</span>
						</div>
					</div>
		  		</div>
				<div class="control-group">
		    		<label class="control-label" for="published"><?php echo JText::_('JPUBLISHED'); ?>:</label>
					<div class="controls">
						<?php $published = $select->YesNoBool('published', $this->row->published);
						echo  $published; ?>
					</div>
		  		</div>
				<div class="control-group">
		    		<label class="control-label" for="invisible"><?php echo JText::_('COM_JBLANCE_INVISIBLE'); ?>:</label>
					<div class="controls">
						<?php $invisible = $select->YesNoBool('invisible', $this->row->invisible);
						echo  $invisible; ?>
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_ALERT_ADMIN_ON_SUBSCRIBE_EVENT_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="alert_admin" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_ALERT_ADMIN_ON_SUBSCRIBE_EVENT'); ?>:</label>
					<div class="controls">
						<?php $alert_admin = $select->YesNoBool('alert_admin', $this->row->alert_admin);
						echo  $alert_admin; ?>
					</div>
		  		</div>
			</fieldset>
		    <fieldset class="form-vertical">
				<legend><?php echo JText::_( 'COM_JBLANCE_DESCRIPTION' ); ?></legend>
				<div class="control-group" style="display: none;">
		    		<label class="control-label" for="description"><?php echo JText::_('COM_JBLANCE_DESCRIPTION'); ?>:</label>
					<div class="controls">
						<textarea name="description" id="description" rows="3" cols="30"><?php echo $this->row->description; ?></textarea>
					</div>
		  		</div>
		  		<div class="control-group">
		    		<label class="control-label" for="finish_msg"><?php echo JText::_('COM_JBLANCE_FINAL_MESSAGE'); ?>:</label>
					<div class="controls">
						<textarea name="finish_msg" id="finish_msg" rows="3" cols="30"><?php echo $this->row->finish_msg; ?></textarea>
					</div>
		  		</div>
			</fieldset>		
		</div>
		<div class="span6">
			<fieldset>
				<legend><?php echo JText::_('COM_JBLANCE_FUND_SETTINGS'); ?></legend>
			<?php echo JHtml::_('bootstrap.startAccordion', 'credit-slider', array('active' => 'credit-general')); ?>
			
			<?php echo JHtml::_('bootstrap.addSlide', 'credit-slider', JText::_('COM_JBLANCE_GENERAL'), 'credit-general'); ?>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_BONUS_FUND_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="bonusFund" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_BONUS_FUND'); ?>:</label>
					<div class="controls">
						<div class="input-prepend">
							<span class="add-on"><?php echo $currencysym; ?></span>
							<input type="text" class="input-small required" name="bonusFund" id="bonusFund" value="<?php echo $this->row->bonusFund; ?>" />
						</div>
					</div>
		  		</div>			
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_PORTFOLIO_ITEMS_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="portfolioCount" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_PORTFOLIO_ITEMS'); ?>:</label>
					<div class="controls">
						<?php 
						$val = isset($this->params['portfolioCount']) ? $this->params['portfolioCount'] : 0; ?>
						<input type="text" class="input-mini required" name="params[portfolioCount]" id="portfolioCount" value="<?php echo $val; ?>" />
					</div>
		  		</div>			
			<?php echo JHtml::_('bootstrap.endSlide'); ?> <!-- end of general slide -->
			
			<!--  section for buyer type of user group -->
			<?php echo JHtml::_('bootstrap.addSlide', 'credit-slider', JText::_('COM_JBLANCE_FUND_SETTINGS_USERS_POSTING_PROJECTS'), 'fund-buyer'); ?>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_PROJECT_FEE_IN_AMT_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="buyFeeAmtPerProject" title="<?php echo $tip; ?>"><?php echo JText::sprintf('COM_JBLANCE_PROJECT_FEE_IN_AMT', $currencysym); ?>:</label>
					<div class="controls">
						<?php 
						$val = isset($this->params['buyFeeAmtPerProject']) ? $this->params['buyFeeAmtPerProject'] : 0; ?>
						<div class="input-prepend">
							<span class="add-on"><?php echo $currencysym; ?></span>
							<input type="text" class="input-small required" name="params[buyFeeAmtPerProject]" id="buyFeeAmtPerProject" value="<?php echo $val; ?>" />
						</div>
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_PROJECT_FEE_IN_PERCENT_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="buyFeePercentPerProject" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_PROJECT_FEE_IN_PERCENT'); ?>:</label>
					<div class="controls">
						<?php $val = isset($this->params['buyFeePercentPerProject']) ? $this->params['buyFeePercentPerProject'] : 0; ?>
						<div class="input-append">
							<input type="text" class="input-small required" name="params[buyFeePercentPerProject]" id="buyFeePercentPerProject" value="<?php echo $val; ?>" />
							<span class="add-on">%</span>
						</div>
					</div>
		  		</div>	
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_CHARGE_PER_PROJECT_IN_AMT_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="buyChargePerProject" title="<?php echo $tip; ?>"><?php echo JText::sprintf('COM_JBLANCE_CHARGE_PER_PROJECT_IN_AMT', $currencysym); ?>:</label>
					<div class="controls">
						<?php $val = isset($this->params['buyChargePerProject']) ? $this->params['buyChargePerProject'] : 0; ?>
						<div class="input-prepend">
							<span class="add-on"><?php echo $currencysym; ?></span>
							<input type="text" class="input-small required" name="params[buyChargePerProject]" id="buyChargePerProject" value="<?php echo $val; ?>" />
						</div>
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_FEATURED_PROJECT_FEE_IN_AMT_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="buyFeePerFeaturedProject" title="<?php echo $tip; ?>"><?php echo JText::sprintf('COM_JBLANCE_FEATURED_PROJECT_FEE_IN_AMT', $currencysym); ?>:</label>
					<div class="controls">
						<?php $val = isset($this->params['buyFeePerFeaturedProject']) ? $this->params['buyFeePerFeaturedProject'] : 0; ?>
						<div class="input-prepend">
							<span class="add-on"><?php echo $currencysym; ?></span>
							<input type="text" class="input-small required" name="params[buyFeePerFeaturedProject]" id="buyFeePerFeaturedProject" value="<?php echo $val; ?>" />
						</div>
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_URGENT_PROJECT_FEE_IN_AMT_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="buyFeePerUrgentProject" title="<?php echo $tip; ?>"><?php echo JText::sprintf('COM_JBLANCE_URGENT_PROJECT_FEE_IN_AMT', $currencysym); ?>:</label>
					<div class="controls">
						<?php $val = isset($this->params['buyFeePerUrgentProject']) ? $this->params['buyFeePerUrgentProject'] : 0; ?>
						<div class="input-prepend">
							<span class="add-on"><?php echo $currencysym; ?></span>
							<input type="text" class="input-small required" name="params[buyFeePerUrgentProject]" id="buyFeePerUrgentProject" value="<?php echo $val; ?>" />
						</div>
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_PRIVATE_PROJECT_FEE_IN_AMT_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="buyFeePerPrivateProject" title="<?php echo $tip; ?>"><?php echo JText::sprintf('COM_JBLANCE_PRIVATE_PROJECT_FEE_IN_AMT', $currencysym); ?>:</label>
					<div class="controls">
						<?php $val = isset($this->params['buyFeePerPrivateProject']) ? $this->params['buyFeePerPrivateProject'] : 0; ?>
						<div class="input-prepend">
							<span class="add-on"><?php echo $currencysym; ?></span>
							<input type="text" class="input-small required" name="params[buyFeePerPrivateProject]" id="buyFeePerPrivateProject" value="<?php echo $val; ?>" />
						</div>
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_SEALED_PROJECT_FEE_IN_AMT_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="buyFeePerSealedProject" title="<?php echo $tip; ?>"><?php echo JText::sprintf('COM_JBLANCE_SEALED_PROJECT_FEE_IN_AMT', $currencysym); ?>:</label>
					<div class="controls">
						<?php $val = isset($this->params['buyFeePerSealedProject']) ? $this->params['buyFeePerSealedProject'] : 0; ?>
						<div class="input-prepend">
							<span class="add-on"><?php echo $currencysym; ?></span>
							<input type="text" class="input-small required" name="params[buyFeePerSealedProject]" id="buyFeePerSealedProject" value="<?php echo $val; ?>" />
						</div>
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_NDA_PROJECT_FEE_IN_AMT_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="buyFeePerNDAProject" title="<?php echo $tip; ?>"><?php echo JText::sprintf('COM_JBLANCE_NDA_PROJECT_FEE_IN_AMT', $currencysym); ?>:</label>
					<div class="controls">
						<?php $val = isset($this->params['buyFeePerNDAProject']) ? $this->params['buyFeePerNDAProject'] : 0; ?>
						<div class="input-prepend">
							<span class="add-on"><?php echo $currencysym; ?></span>
							<input type="text" class="input-small required" name="params[buyFeePerNDAProject]" id="buyFeePerNDAProject" value="<?php echo $val; ?>" />
						</div>
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_PROJECTS_ALLOWED_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="buyProjectCount" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_PROJECTS_ALLOWED'); ?>:</label>
					<div class="controls">
						<?php $val = isset($this->params['buyProjectCount']) ? $this->params['buyProjectCount'] : 0; ?>
						<input type="text" class="input-small required" name="params[buyProjectCount]" id="buyProjectCount" value="<?php echo $val; ?>" />
					</div>
		  		</div>
			<?php echo JHtml::_('bootstrap.endSlide'); ?> <!-- end of fund-buyer slide -->
			
			<!--  section for freelancer type of user group -->
			<?php echo JHtml::_('bootstrap.addSlide', 'credit-slider', JText::_('COM_JBLANCE_FUND_SETTINGS_USERS_SEEKING_PROJECTS'), 'fund-freelancer'); ?>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_FREELANCER_PROJECT_FEE_IN_AMT_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="flFeeAmtPerProject" title="<?php echo $tip; ?>"><?php echo JText::sprintf('COM_JBLANCE_PROJECT_FEE_IN_AMT', $currencysym); ?>:</label>
					<div class="controls">
						<?php $val = isset($this->params['flFeeAmtPerProject']) ? $this->params['flFeeAmtPerProject'] : 0; ?>
						<div class="input-prepend">
							<span class="add-on"><?php echo $currencysym; ?></span>
							<input type="text" class="input-small required" name="params[flFeeAmtPerProject]" id="flFeeAmtPerProject" value="<?php echo $val; ?>" />
						</div>
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_FREELANCER_PROJECT_FEE_IN_PERCENT_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="flFeePercentPerProject" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_PROJECT_FEE_IN_PERCENT'); ?>:</label>
					<div class="controls">
						<?php $val = isset($this->params['flFeePercentPerProject']) ? $this->params['flFeePercentPerProject'] : 0; ?>
						<div class="input-append">
							<input type="text" class="input-small required" name="params[flFeePercentPerProject]" id="flFeePercentPerProject" value="<?php echo $val; ?>" />
							<span class="add-on">%</span>
						</div>
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_CHARGE_PER_BID_IN_AMT_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="flChargePerBid" title="<?php echo $tip; ?>"><?php echo JText::sprintf('COM_JBLANCE_CHARGE_PER_BID_IN_AMT', $currencysym); ?>:</label>
					<div class="controls">
						<?php $val = isset($this->params['flChargePerBid']) ? $this->params['flChargePerBid'] : 0; ?>
						<div class="input-prepend">
							<span class="add-on"><?php echo $currencysym; ?></span>
							<input type="text" class="input-small required" name="params[flChargePerBid]" id="flChargePerBid" value="<?php echo $val; ?>" />
						</div>
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_BIDS_ALLOWED_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="flBidCount" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_BIDS_ALLOWED'); ?>:</label>
					<div class="controls">
						<?php $val = isset($this->params['flBidCount']) ? $this->params['flBidCount'] : 0; ?>
						<input type="text" class="input-mini required" name="params[flBidCount]" id="flBidCount" value="<?php echo $val; ?>" />
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_CHARGE_PER_SERVICE_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="flChargePerService" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_CHARGE_PER_SERVICE'); ?>:</label>
					<div class="controls">
						<?php $val = isset($this->params['flChargePerService']) ? $this->params['flChargePerService'] : 0; ?>
						<div class="input-prepend">
							<span class="add-on"><?php echo $currencysym; ?></span>
							<input type="text" class="input-small required" name="params[flChargePerService]" id="flChargePerService" value="<?php echo $val; ?>" />
						</div>
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_SERVICE_FEE_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="flFeePercentPerProject" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_SERVICE_FEE'); ?>:</label>
					<div class="controls">
						<?php $val = isset($this->params['flFeePercentPerService']) ? $this->params['flFeePercentPerService'] : 0; ?>
						<div class="input-append">
							<input type="text" class="input-small required" name="params[flFeePercentPerService]" id="flFeePercentPerService" value="<?php echo $val; ?>" />
							<span class="add-on">%</span>
						</div>
					</div>
		  		</div>
			<?php echo JHtml::_('bootstrap.endSlide'); ?> <!-- end of fund-freelancer slide -->
			
			<?php echo JHtml::_('bootstrap.endAccordion'); ?>
			</fieldset>		
		</div>
	</div>
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="option" value="com_jblance" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="hidemainmenu" value="0" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>