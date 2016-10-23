<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	14 March 2012
 * @file name	:	views/admconfig/tmpl/config.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Shows list of Users (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 
 JHtml::_('behavior.formvalidation');
 JHtml::_('bootstrap.tooltip');
 jbimport('integration.integration');
 JHtml::_('formbehavior.chosen', 'select');

 $model = $this->getModel();
 $select = JblanceHelper::get('helper.select');		// create an instance of the class SelectHelper

 $uploadLimit = ini_get('upload_max_filesize');
 $uploadLimit = str_ireplace ('M', ' MB', $uploadLimit);
 
 $user = JFactory::getUser();
 $isSuperAdmin = false;
 if(isset($user->groups[8]))
 	$isSuperAdmin = true;
 
?>
<script type="text/javascript">
<!--
	Joomla.submitbutton = function(task){
		if (task == 'admconfig.cancelconfig' || document.formvalidator.isValid(document.id('editconfig-form'))) {
			Joomla.submitform(task, document.getElementById('editconfig-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('COM_JBLANCE_FIEDS_HIGHLIGHTED_RED_COMPULSORY'));?>');
		}
	}
//-->
</script>
<form action="index.php" method="post" name="adminForm" id="editconfig-form" class="form-validate form-horizontal">
<div id="j-sidebar-container" class="span2">
	<?php include_once(JPATH_COMPONENT.'/views/configmenu.php'); ?>
</div>
<div id="j-main-container" class="span10">
<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'general')); ?>

<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'general', JText::_('COM_JBLANCE_GENERAL', true)); ?>
	<div class="row-fluid">
		<div class="span6">
			<fieldset>
				<legend><?php echo JText::_('COM_JBLANCE_GENERAL'); ?></legend>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_WELCOME_TITLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsWelcomeTitle" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_WELCOME_TITLE'); ?>:</label>
					<div class="controls">						
						<input class="input-large required" type="text" name="params[welcomeTitle]" id="paramsWelcomeTitle" maxlength="100" value="<?php echo $this->params->welcomeTitle; ?>" />
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_CURRENCY_SYMBOL_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsCurrencySymbol" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_CURRENCY_SYMBOL'); ?>:</label>
					<div class="controls">						
						<input class="input-mini required" type="text" name="params[currencySymbol]" id="paramsCurrencySymbol" maxlength="10" value="<?php echo $this->params->currencySymbol; ?>" />
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_CURRENCY_CODE_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsCurrencyCode" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_CURRENCY_CODE'); ?>:</label>
					<div class="controls">						
						<input class="input-mini required" type="text" name="params[currencyCode]" id="paramsCurrencyCode" maxlength="10" value="<?php echo $this->params->currencyCode; ?>" />
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_DEFAULT_DATE_FORMAT_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsdateFormat" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_DEFAULT_DATE_FORMAT'); ?>:</label>
					<div class="controls">						
						<?php $list_dformat = $model->getselectDateFormat('params[dateFormat]', $this->params->dateFormat);
						 echo $list_dformat; ?>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_ARTICLE_ID_TNS_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsTermArticleId" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_ARTICLE_ID_TNS'); ?>:</label>
					<div class="controls">						
						<input class="input-mini required" type="text" name="params[termArticleId]" id="paramsTermArticleId" maxlength="5" value="<?php echo $this->params->termArticleId; ?>" />
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_SHOW_RSS_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsshowRss" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_SHOW_RSS'); ?>:</label>
					<div class="controls">						
						<?php $enable_rss = $select->YesNoBool('params[showRss]', $this->params->showRss);
						echo  $enable_rss; ?>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_RSS_LIMIT_RSS_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsRssLimit" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_RSS_LIMIT'); ?>:</label>
					<div class="controls">						
						<input class="input-mini required" type="text" name="params[rssLimit]" id="paramsRssLimit" maxlength="5" value="<?php echo $this->params->rssLimit; ?>" />
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_SHOW_JOOMBRI_CREDIT_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsshowJoombriCredit" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_SHOW_JOOMBRI_CREDIT'); ?>:</label>
					<div class="controls">						
						<?php $show_jbcredit = $select->YesNoBool('params[showJoombriCredit]', $this->params->showJoombriCredit);
						echo  $show_jbcredit; ?>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_REVIEW_PRIVATE_MESSAGES_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsreviewMessages" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_REVIEW_PRIVATE_MESSAGES'); ?>:</label>
					<div class="controls">						
						<?php $reviewMessages = $select->YesNoBool('params[reviewMessages]', $this->params->reviewMessages);
						echo  $reviewMessages; ?>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_PUBLIC_PROFILE_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsprofilePublic" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_PUBLIC_PROFILE'); ?>:</label>
					<div class="controls">						
						<?php $profilePublic = $select->YesNoBool('params[profilePublic]', $this->params->profilePublic);
						echo  $profilePublic; ?>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_MAX_SKILLS_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsMaxSkills" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_MAX_SKILLS'); ?>:</label>
					<div class="controls">						
						<input class="input-mini required" type="text" name="params[maxSkills]" id="paramsMaxSkills" maxlength="5" value="<?php echo $this->params->maxSkills; ?>" />
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_SOCIAL_BOOKMARKING_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsenableAddThis" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_SOCIAL_BOOKMARKING'); ?>:</label>
					<div class="controls">						
						<?php echo $select->YesNoBool('params[enableAddThis]', $this->params->enableAddThis); ?>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_ADDTHIS_PUBLISHERID_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="addThisPubid" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_ADDTHIS_PUBLISHERID'); ?>:</label>
					<div class="controls">						
						<input class="input-medium" type="text" name="params[addThisPubid]" id="addThisPubid" maxlength="25" value="<?php echo $this->params->addThisPubid; ?>" />
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_SHOW_FEEDS_DASHBOARD_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsshowFeedsDashboard" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_SHOW_FEEDS_DASHBOARD'); ?>:</label>
					<div class="controls">						
						<?php echo $select->YesNoBool('params[showFeedsDashboard]', $this->params->showFeedsDashboard); ?>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_DASHBOARD_FEEDS_LIMIT_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="feedLimitDashboard" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_DASHBOARD_FEEDS_LIMIT'); ?>:</label>
					<div class="controls">						
						<input class="input-mini required" type="text" name="params[feedLimitDashboard]" id="feedLimitDashboard" maxlength="5" value="<?php echo $this->params->feedLimitDashboard; ?>" />
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_DISPLAY_USERNAME_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsShowUsername" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_DISPLAY_USERNAME'); ?>:</label>
					<div class="controls">						
						<?php $showUsername = $select->YesNoBool('params[showUsername]', $this->params->showUsername);
						echo  $showUsername; ?>
					</div>
			  	</div>
			  	<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_LOAD_BOOTSTRAP_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsloadBootstrap" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_LOAD_BOOTSTRAP'); ?>:</label>
					<div class="controls">						
						<?php echo $select->YesNoBool('params[loadBootstrap]', $this->params->loadBootstrap); ?>
					</div>
			  	</div>
			</fieldset>
		</div>
		<div class="span6">
			<fieldset>
				<legend><?php echo JText::_('COM_JBLANCE_PROJECTS_SERVICES'); ?></legend>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_REVIEW_PROJECTS_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsreviewProjects" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_REVIEW_PROJECTS'); ?>:</label>
					<div class="controls">						
						<?php $reviewProjects = $select->YesNoBool('params[reviewProjects]', $this->params->reviewProjects);
						echo  $reviewProjects; ?>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_SEAL_PROJECT_BIDS_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramssealProjectBids" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_SEAL_PROJECT_BIDS'); ?>:</label>
					<div class="controls">						
						<?php $sealProjectBids = $select->YesNoBool('params[sealProjectBids]', $this->params->sealProjectBids);
						echo  $sealProjectBids; ?>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_REVIEW_SERVICES_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsreviewServices" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_REVIEW_SERVICES'); ?>:</label>
					<div class="controls">						
						<?php $reviewServices = $select->YesNoBool('params[reviewServices]', $this->params->reviewServices);
						echo  $reviewServices; ?>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_MIN_SERVICE_BASE_PRICE_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsMinServiceBasePrice" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_MIN_SERVICE_BASE_PRICE'); ?>:</label>
					<div class="controls">						
						<input class="input-mini required" type="text" name="params[minServiceBasePrice]" id="paramsMinServiceBasePrice" maxlength="5" value="<?php echo $this->params->minServiceBasePrice; ?>" />
					</div>
			  	</div>
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('COM_JBLANCE_REPORTINGS'); ?></legend>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_ENABLE_REPORTING_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsenableReporting" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_ENABLE_REPORTING'); ?>:</label>
					<div class="controls">						
						<?php echo $select->YesNoBool('params[enableReporting]', $this->params->enableReporting); ?>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_REPORT_EXECUTE_DEFAULT_ACTION_LIMIT_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="maxReport" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_REPORT_EXECUTE_DEFAULT_ACTION_LIMIT'); ?>:</label>
					<div class="controls">						
						<input class="input-mini required" type="text" name="params[maxReport]" id="maxReport" maxlength="5" value="<?php echo $this->params->maxReport; ?>" />&nbsp;<?php echo JText::_('COM_JBLANCE_REPORTS'); ?>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_GUEST_REPORTING_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsenableGuestReporting" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_GUEST_REPORTING'); ?>:</label>
					<div class="controls">						
						<?php echo $select->YesNoBool('params[enableGuestReporting]', $this->params->enableGuestReporting); ?>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_REPORT_CATEGORIES_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="reportCategory" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_REPORT_CATEGORIES'); ?>:</label>
					<div class="controls">						
						<textarea name="params[reportCategory]" id="reportCategory" class="required" rows="6" cols="30"><?php echo $this->params->reportCategory; ?></textarea>
					</div>
			  	</div>
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('COM_JBLANCE_SENDER_INFORMATION'); ?></legend>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_FROM_NAME_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsMailFromName" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_FROM_NAME'); ?>:</label>
					<div class="controls">						
						<input class="input-medium required" type="text" name="params[mailFromName]" id="paramsMailFromName" maxlength="100" value="<?php echo $this->params->mailFromName; ?>" />
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_FROM_ADDRESS_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsMailFromAddress" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_FROM_ADDRESS'); ?>:</label>
					<div class="controls">						
						<input class="input-medium required validate-email" type="text" name="params[mailFromAddress]" id="paramsMailFromAddress" maxlength="100" value="<?php echo $this->params->mailFromAddress; ?>" />
					</div>
			  	</div>
			</fieldset>
		</div>
	</div>
<?php echo JHtml::_('bootstrap.endTab'); ?>		<!-- end of general tab -->

<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'payment', JText::_('COM_JBLANCE_PAYMENT', true)); ?>
	<div class="row-fluid">
		<div class="span6">
			<fieldset>
				<legend><?php echo JText::_('COM_JBLANCE_INVOICE_DETAILS'); ?></legend>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_MY_INVOICE_DETAILS_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsInvoiceDetails" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_MY_INVOICE_DETAILS'); ?>:</label>
					<div class="controls">						
						<textarea name="params[invoiceDetails]" id="paramsInvoiceDetails" class="required" rows="5" cols="30"><?php echo $this->params->invoiceDetails; ?></textarea>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_INVOICE_FORMAT_FUND_DEPOSIT')); ?>
		    		<label class="control-label hasTooltip" for="paramsInvoiceFormatDeposit" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_INVOICE_FORMAT_FUND_DEPOSIT'); ?>:</label>
					<div class="controls">						
						<input class="input-medium required" type="text" name="params[invoiceFormatDeposit]" id="paramsInvoiceFormatDeposit" maxlength="30" value="<?php echo $this->params->invoiceFormatDeposit; ?>" />
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_INVOICE_FORMAT_PLANS')); ?>
		    		<label class="control-label hasTooltip" for="paramsInvoiceFormatPlan" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_INVOICE_FORMAT_PLANS'); ?>:</label>
					<div class="controls">						
						<input class="input-medium required" type="text" name="params[invoiceFormatPlan]" id="paramsInvoiceFormatPlan" size="20" maxlength="30" value="<?php echo $this->params->invoiceFormatPlan; ?>" />
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_INVOICE_FORMAT_FUND_WITHDRAWAL')); ?>
		    		<label class="control-label hasTooltip" for="paramsInvoiceFormatWithdraw" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_INVOICE_FORMAT_FUND_WITHDRAWAL'); ?>:</label>
					<div class="controls">						
						<input class="input-medium required" type="text" name="params[invoiceFormatWithdraw]" id="paramsInvoiceFormatWithdraw" size="20" maxlength="30" value="<?php echo $this->params->invoiceFormatWithdraw; ?>" />
					</div>
			  	</div>
			  	
			  	<div class="well well-small span9">
			  		<h2 class="module-title nav-header"><?php echo JText::_('COM_JBLANCE_AVAILABLE_TAGS'); ?></h2>
				  	<div class="row-striped">
						<div class="row-fluid">
							<div class="span3">
								<strong class="row-title">[ID]</strong>
							</div>
							<div class="span9">
								<?php echo JText::_('COM_JBLANCE_ID_FUNDTRANSFER_OR_SUBSCRIPTION'); ?>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span3">
								<strong class="row-title">[TIME]</strong>
							</div>
							<div class="span9">
								<?php echo JText::_('COM_JBLANCE_UNIX_TIME_OF_PURCHASE'); ?>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span3">
								<strong class="row-title">[YYYY]</strong>
							</div>
							<div class="span9">
								<?php echo JText::_('COM_JBLANCE_YEAR_OF_DATE_PURCHASE'); ?>
							</div>
						</div>
						<div class="row-fluid">
							<div class="span3">
								<strong class="row-title">[USERID]</strong>
							</div>
							<div class="span9">
								<?php echo JText::_('COM_JBLANCE_USER_ID_OF_THE_BUYER'); ?>
							</div>
						</div>
					</div>
				</div>
			</fieldset>	
		</div>
		<div class="span6">
			<fieldset>
				<legend><?php echo JText::_('COM_JBLANCE_FUND_TAX'); ?></legend>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_MIN_FUND_DEPOSIT_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsfundDepositMin" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_MIN_FUND_DEPOSIT'); ?>:</label>
					<div class="controls">						
						<input class="input-mini required" type="text" name="params[fundDepositMin]" id="paramsfundDepositMin" maxlength="5" value="<?php echo $this->params->fundDepositMin; ?>" />
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_MIN_WITHDRAW_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsWithdrawMin" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_MIN_WITHDRAW'); ?>:</label>
					<div class="controls">						
						<input class="input-mini required" type="text" name="params[withdrawMin]" id="paramsWithdrawMin" maxlength="5" value="<?php echo $this->params->withdrawMin; ?>" />
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_TAX_NAME_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsTaxName" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_TAX_NAME'); ?>:</label>
					<div class="controls">						
						<input class="input-small required" type="text" name="params[taxName]" id="paramsTaxName" size="25" maxlength="30" value="<?php echo $this->params->taxName; ?>" />
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_TAX_PERCENT_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsTaxpercent" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_TAX_IN_PERCENT'); ?>:</label>
					<div class="controls">
						<div class="input-append">
							<input class="input-mini required" type="text" name="params[taxPercent]" id="paramsTaxpercent" maxlength="8" value="<?php echo $this->params->taxPercent; ?>" />
							<span class="add-on">%</span>
						</div>
					</div>
			  	</div>
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('COM_JBLANCE_ENABLE_DISABLE_PAYMENT_OPTIONS'); ?></legend>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_CHECKFUND_PICKUSER_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramscheckfundPickuser" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_CHECKFUND_PICKUSER'); ?>:</label>
					<div class="controls">
						<?php $checkfundPickuser = $select->YesNoBool('params[checkfundPickuser]', $this->params->checkfundPickuser);
						echo  $checkfundPickuser; ?>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_ENABLE_ESCROW_PAYMENT_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsenableEscrowPayment" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_ENABLE_ESCROW_PAYMENT'); ?>:</label>
					<div class="controls">
						<?php $enableEscrowPayment = $select->YesNoBool('params[enableEscrowPayment]', $this->params->enableEscrowPayment);
						echo  $enableEscrowPayment; ?>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_ENABLE_WITHDRAW_FUND_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsenableWithdrawFund" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_ENABLE_WITHDRAW_FUND'); ?>:</label>
					<div class="controls">
						<?php $enableWithdrawFund = $select->YesNoBool('params[enableWithdrawFund]', $this->params->enableWithdrawFund);
						echo  $enableWithdrawFund; ?>
					</div>
			  	</div>
			</fieldset>
		</div>
	</div>
<?php echo JHtml::_('bootstrap.endTab'); ?>		<!-- end of payment tab -->

<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'uploads', JText::_('COM_JBLANCE_UPLOADS', true)); ?>
	<div class="row-fluid">
		<div class="span6">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_JBLANCE_PROJECT_FILES'); ?></legend>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_LEGAL_MIME_TYPES_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsProjectFileType" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_LEGAL_MIME_TYPES'); ?>:</label>
					<div class="controls">
						<?php if($isSuperAdmin) : ?>
							<textarea name="params[projectFileType]" id="paramsProjectFileType" rows="5" class="input-xlarge required"><?php echo $this->params->projectFileType; ?></textarea>
						<?php else : ?>
							<?php echo $this->params->projectFileType; ?>
							<input type="hidden" name="params[projectFileType]" value="<?php echo $this->params->projectFileType; ?>" />
						<?php endif; ?>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_TEXT_UPLOAD_FIELD_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsProjectFileText" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_TEXT_UPLOAD_FIELD'); ?>:</label>
					<div class="controls">
						<input class="input-large required" type="text" name="params[projectFileText]" id="paramsProjectFileText" maxlength="100" value="<?php echo $this->params->projectFileText; ?>" />
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::sprintf('COM_JBLANCE_MAX_SIZE_IN_KB_EXAMPLE', $uploadLimit)); ?>
		    		<label class="control-label hasTooltip" for="paramsProjectMaxsize" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_MAX_SIZE_IN_KB'); ?>:</label>
					<div class="controls">
						<div class="input-append">
							<input class="input-mini required" type="text" name="params[projectMaxsize]" id="paramsProjectMaxsize" size="10" maxlength="10" value="<?php echo $this->params->projectMaxsize; ?>" />
							<span class="add-on">kB</span>
						</div>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_MAX_FILES_PER_PROJECT_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsProjectMaxfileCount" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_MAX_FILES_PER_PROJECT'); ?>:</label>
					<div class="controls">
						<input class="input-mini required" type="text" name="params[projectMaxfileCount]" id="paramsProjectMaxfileCount" maxlength="10" value="<?php echo $this->params->projectMaxfileCount; ?>" />
					</div>
			  	</div>
			</fieldset>
		</div>
		<div class="span6">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_JBLANCE_IMAGES'); ?></legend>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_LEGAL_MIME_TYPES_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsImgFileType" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_LEGAL_MIME_TYPES'); ?>:</label>
					<div class="controls">
						<?php if($isSuperAdmin) : ?>
							<textarea name="params[imgFileType]" id="paramsImgFileType" class="input-xlarge required" rows="5"><?php echo $this->params->imgFileType; ?></textarea>
						<?php else : ?>
							<?php echo $this->params->imgFileType; ?>
							<input type="hidden" name="params[imgFileType]" value="<?php echo $this->params->imgFileType; ?>" />
						<?php endif; ?>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_TEXT_UPLOAD_FIELD_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsImgFileText" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_TEXT_UPLOAD_FIELD'); ?>:</label>
					<div class="controls">
						<input class="input-large required" type="text" name="params[imgFileText]" id="paramsImgFileText" maxlength="100" value="<?php echo $this->params->imgFileText; ?>" />
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_WIDTH')); ?>
		    		<label class="control-label hasTooltip" for="paramsImgwidth" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_WIDTH'); ?>:</label>
					<div class="controls">
						<div class="input-append">
							<input class="input-mini required" type="text" name="params[imgWidth]" id="paramsImgwidth" size="10" maxlength="10" value="<?php echo $this->params->imgWidth; ?>" />
							<span class="add-on">px</span>
						</div>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_HEIGHT')); ?>
		    		<label class="control-label hasTooltip" for="paramsImgHeight" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_HEIGHT'); ?>:</label>
					<div class="controls">
						<div class="input-append">
							<input class="input-mini required" type="text" name="params[imgHeight]" id="paramsImgHeight" size="10" maxlength="10" value="<?php echo $this->params->imgHeight; ?>" />&nbsp;px
							<span class="add-on">px</span>
						</div>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::sprintf('COM_JBLANCE_MAX_SIZE_IN_KB_EXAMPLE', $uploadLimit)); ?>
		    		<label class="control-label hasTooltip" for="imgMaxsize" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_MAX_SIZE_IN_KB'); ?>:</label>
					<div class="controls">
						<div class="input-append">
							<input class="input-mini required" type="text" name="params[imgMaxsize]" id="imgMaxsize" size="10" maxlength="10" value="<?php echo $this->params->imgMaxsize; ?>" />&nbsp;KB
							<span class="add-on">kB</span>
						</div>
					</div>
			  	</div>			  	
			</fieldset>		
		</div>
	</div>
<?php echo JHtml::_('bootstrap.endTab'); ?>		<!-- end of uploads tab -->

<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'integration', JText::_('COM_JBLANCE_INTEGRATION', true)); ?>

	<div class="row-fluid">
		<div class="span6">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_JBLANCE_INTEGRATION_OPTIONS'); ?></legend>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_PROFILES_USER_LIST_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsintegrationProfile" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_PROFILES_USER_LIST'); ?>:</label>
					<div class="controls">
						<?php echo JoomBriIntegration::getConfigOptions('profile'); ?>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_PROFILE_PICTURE_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsintegrationAvatar" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_PROFILE_PICTURE'); ?>:</label>
					<div class="controls">
						<?php echo JoomBriIntegration::getConfigOptions('avatar'); ?>
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_REGISTRATION_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_REGISTRATION'); ?>:</label>
					<div class="controls">
						<?php 
						$link_plugin = JRoute::_('index.php?option=com_plugins&view=plugins&filter_folder=system');
						?>
						<a href="<?php echo $link_plugin; ?>"><?php echo JText::_('COM_JBLANCE_CLICK_HERE'); ?></a>
					</div>
			  	</div>
			</fieldset>		
		</div>
		<div class="span6">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_JBLANCE_FACEBOOK_CONNECT'); ?></legend>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_FACEBOOK_API_KEY_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsFbApikey" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_FACEBOOK_API_KEY'); ?>:</label>
					<div class="controls">
						<input class="input-large" type="text" name="params[fbApikey]" id="paramsFbApikey" maxlength="100" value="<?php echo $this->params->fbApikey; ?>" />
					</div>
			  	</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_FACEBOOK_APP_SECRET_EXAMPLE')); ?>
		    		<label class="control-label hasTooltip" for="paramsFbAppsecret" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_FACEBOOK_APP_SECRET'); ?>:</label>
					<div class="controls">
						<input class="input-large" type="text" name="params[fbAppsecret]" id="paramsFbAppsecret" maxlength="100" value="<?php echo $this->params->fbAppsecret; ?>" />
					</div>
			  	</div>
			</fieldset>		
		</div>
	</div>
<?php echo JHtml::_('bootstrap.endTab'); ?>		<!-- end of integration tab -->

<?php echo JHtml::_('bootstrap.endTabSet'); ?>

	<input type="hidden" name="option" value="com_jblance" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<input type="hidden" name="boxchecked" value="0" />
    <?php echo JHtml::_('form.token'); ?>
</div>
</form>