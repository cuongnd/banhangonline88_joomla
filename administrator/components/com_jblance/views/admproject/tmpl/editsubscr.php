<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	19 March 2012
 * @file name	:	views/admproject/tmpl/editsubscr.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Edit subscription details (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 
 JHtml::_('behavior.framework', true);
 JHtml::_('behavior.formvalidation');
 JHtml::_('formbehavior.chosen', 'select');
 
 $model = $this->getModel();
 $config = JblanceHelper::getConfig();
?>
<script type="text/javascript">
<!--
	Joomla.submitbutton = function(task){
		if (task == 'admproject.cancelsubscr' || document.formvalidator.isValid(document.id('editsubscr-form'))) {
			Joomla.submitform(task, document.getElementById('editsubscr-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('COM_JBLANCE_FIEDS_HIGHLIGHTED_RED_COMPULSORY'));?>');
		}
	}
//-->
</script>
<form action="index.php" method="post" id="editsubscr-form" name="adminForm" class="form-validate form-horizontal">
<div class="row-fluid">
	<div class="span4">
		<fieldset>
			<legend><?php echo JText::_('COM_JBLANCE_USER_INFORMATION'); ?></legend>
			<div class="control-group">
	    		<label class="control-label" for="ug_id"><?php echo JText::_('COM_JBLANCE_USER_GROUP'); ?>:</label>
				<div class="controls">
					<?php echo $this->lists['ug_id']; ?>
				</div>
	  		</div>
			<div class="control-group">
	    		<label class="control-label" for="project_title"><?php echo JText::_('COM_JBLANCE_USERNAME'); ?>:</label>
				<div class="controls">
					<?php echo  $this->users; ?>
				</div>
	  		</div>
		</fieldset>
	</div>
	<div class="span4">
		<fieldset>
			<legend><?php echo JText::_('COM_JBLANCE_SUBSCR_INFO'); ?></legend>
			<?php if($this->row->id > 0) : ?>
			<div class="control-group">
	    		<label class="control-label" for=""><?php echo JText::_('COM_JBLANCE_SUBSCR_ID'); ?>:</label>
				<div class="controls">
					<span class="readonly"><?php echo $this->row->id; ?></span>
				</div>
	  		</div>
			<div class="control-group">
	    		<label class="control-label" for=""><?php echo JText::_('COM_JBLANCE_INVOICE_NO'); ?>:</label>
				<div class="controls">
					<span class="readonly"><?php echo $this->row->invoiceNo; ?></span>
				</div>
	  		</div>
	  		<?php endif; ?>
			<div class="control-group">
	    		<label class="control-label" for=""><?php echo JText::_('COM_JBLANCE_PLAN_NAME'); ?>:</label>
				<div class="controls">
					<?php echo $this->plans; ?>
				</div>
	  		</div>
			<div class="control-group">
	    		<label class="control-label" for=""><?php echo JText::_('COM_JBLANCE_STATUS'); ?>:</label>
				<div class="controls">
					<?php echo $this->lists['status']; ?>
				</div>
	  		</div>
	  		<?php if($this->row->date_approval != '0000-00-00 00:00:00' && $this->row->id > 0){ ?>
			<div class="control-group">
	    		<label class="control-label" for=""><?php echo JText::_('COM_JBLANCE_STARTS_ON'); ?>:</label>
				<div class="controls">
					<?php 
				  	$approvalDate = $this->row->date_approval != "0000-00-00 00:00:00" ?  JHtml::_('date', $this->row->date_approval, 'Y-m-d H:i:s') :  "";
				  	echo JHtml::_('calendar', $approvalDate, 'date_approval', 'date_approval', '%Y-%m-%d %H:%M:%S', array('class'=>'input-medium')); ?>
				</div>
	  		</div>
			<div class="control-group">
	    		<label class="control-label" for=""><?php echo JText::_('COM_JBLANCE_EXPIRES_ON'); ?>:</label>
				<div class="controls">
					<?php 
				  	$expireDate = $this->row->date_expire != "0000-00-00 00:00:00" ?  JHtml::_('date', $this->row->date_expire, 'Y-m-d H:i:s') :  "";
				  	echo JHtml::_('calendar', $expireDate, 'date_expire', 'date_expire', '%Y-%m-%d %H:%M:%S', array('class'=>'input-medium')); ?>
				</div>
	  		</div>
	  		<?php } ?>
		</fieldset>
	</div>
	<div class="span4">
	<?php if($this->row->date_approval != '0000-00-00 00:00:00' && $this->row->id > 0){?>
		<fieldset>
			<legend><?php echo JText::_('COM_JBLANCE_PAYMENT_INFO'); ?></legend>
			<div class="control-group">
    			<label class="control-label" for=""><?php echo JText::_('COM_JBLANCE_TAX'); ?>:</label>
				<div class="controls">
					<div class="input-append">
						<input type="text" class="input-mini" name="tax_percent" value="<?php echo $this->row->tax_percent; ?>" />
						<span class="add-on">%</span>
					</div>
				</div>
  			</div>
			<div class="control-group">
    			<label class="control-label" for=""><?php echo JText::_('COM_JBLANCE_TOTAL_AMOUNT'); ?>:</label>
				<div class="controls">
					<div class="input-prepend">
						<span class="add-on"><?php echo $config->currencySymbol; ?></span>
						<input type="text" class="input-small" name="price" value="<?php echo JblanceHelper::formatCurrency($this->row->price, false); ?>" />
					</div>
				</div>
  			</div>
			<div class="control-group">
    			<label class="control-label" for=""><?php echo JText::_('COM_JBLANCE_FUND_ADDED'); ?>:</label>
				<div class="controls">	
					<div class="input-prepend">
						<span class="add-on"><?php echo $config->currencySymbol; ?></span>					
						<input type="text" class="input-small" name="credit" value="<?php echo JblanceHelper::formatCurrency($this->row->fund, false); ?>" />
					</div>
				</div>
  			</div>
			<div class="control-group">
    			<label class="control-label" for=""><?php echo JText::_('COM_JBLANCE_PAYMENT_GATEWAY'); ?>:</label>
				<div class="controls">
					<span class="readonly"><?php echo JblanceHelper::getGwayName($this->row->gateway); ?></span>
				</div>
  			</div>
			<div class="control-group">
    			<label class="control-label" for=""><?php echo JText::_('COM_JBLANCE_BID_LIMITS'); ?>:</label>
				<div class="controls">
					<div class="input-prepend input-append">
						<input type="text" class="input-mini" name="bids_left" value="<?php echo $this->row->bids_left; ?>" /><span class="add-on">/</span><input type="text" class="input-mini" name="bids_allowed" value="<?php echo $this->row->bids_allowed; ?>" />
					</div>
				</div>
  			</div>
			<div class="control-group">
    			<label class="control-label" for=""><?php echo JText::_('COM_JBLANCE_PROJECT_LIMITS'); ?>:</label>
				<div class="controls">
					<div class="input-prepend input-append">
						<input type="text" class="input-mini" name="projects_left" value="<?php echo $this->row->projects_left; ?>" /><span class="add-on">/</span><input type="text" class="input-mini" name="projects_allowed" value="<?php echo $this->row->projects_allowed; ?>" />
					</div>
				</div>
  			</div>
		</fieldset>
	<?php } ?>
	</div>
</div>
	<input type="hidden" name="option" value="com_jblance" />
	<input type="hidden" name="view" value="admproject" />
	<input type="hidden" name="layout" value="editsubscr" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>">
	<input type="hidden" name="gateway" value="<?php echo ($this->row->gateway ? $this->row->gateway : 'byadmin' );?>" />
	<!--<input type="hidden" name="gateway_id" value="<?php echo ($this->row->gateway_id ? $this->row->gateway_id : time() );?>" />-->
	<input type="hidden" name="hidemainmenu" value="0" />
	<?php echo JHtml::_( 'form.token' ); ?>
</form>