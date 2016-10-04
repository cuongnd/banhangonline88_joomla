<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

$configuration = $this->configuration;
$locationId = JFactory::getApplication()->input->cookie->get('locationSubscription', '', 'int');

$skipLink = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=' . $configuration['subscription_redirect']);

if($locationId == null && $locationId != -1):

	if($configuration['jquery_loading'] != "" && version_compare(JVERSION, '3.0.0', 'lt'))
	{
		JFactory::getDocument()->addScript($configuration['jquery_loading']);
	}
?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style.css" type="text/css" /> 
<?php if(JFactory::getLanguage()->isRTL()): ?> 
	<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style-rtl.css" type="text/css" />
<?php endif; ?> 
<script type="text/javascript">
	function validateEmail(email)
	{ 
		var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return re.test(email);
	}

	function skip_form()
	{
		jQuery("form#skip_form").submit();
	}

	function submit_form()
	{
		var valid = true;

		if(validateEmail(jQuery("#subscription_email").val()) == false)
		{
			jQuery("#email_error_message").html("<?php echo JText::_('COM_CMGROUPBUYING_SUBSCRIPTION_POPUP_INVALID_EMAIL'); ?>");
			valid = false;
		}
		else
		{
			jQuery("#email_error_message").html("");
		}

		if(jQuery("#subscription_name").val() == "")
		{
			jQuery("#name_error_message").html("<?php echo JText::_('COM_CMGROUPBUYING_SUBSCRIPTION_POPUP_INVALID_NAME'); ?>");
			valid = false;
		}
		else
		{
			jQuery("#name_error_message").html("");
		}

		if(valid)
		{
			return true;
		}
		else
		{
			return false;
		}
	}
</script>
<div class="container">
	<form class="form-subscription" onsubmit="return submit_form()">
		<input type="hidden" name="option" id="option" value="com_cmgroupbuying" />
		<input type="hidden" name="controller" id="controller" value="subscription" />
		<input type="hidden" name="task" id="task" value="subscribe" />
		<h3 class="form-subscription-heading"><?php echo JText::_('COM_CMGROUPBUYING_SUBSCRIPTION_POPUP_HEADER'); ?></h3>
		<div class="control-group">
			<label class="control-label" for="subscription_location"><?php echo JText::_('COM_CMGROUPBUYING_SUBSCRIPTION_POPUP_LOCATION_LABEL'); ?></label>
			<div class="controls">
				<select class="input-block-level" id="subscription_location" name="subscription_location">
					<?php foreach($this->locations as $location): ?>
					<option value="<?php echo $location['id']; ?>"><?php echo $location['name']; ?></option>
					<?php endforeach; ?> 
				</select>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="subscription_name"><?php echo JText::_('COM_CMGROUPBUYING_SUBSCRIPTION_POPUP_NAME_LABEL'); ?></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="subscription_name" name="subscription_name">
				<div id="name_error_message" class="subscription_form_error"></div>
			</div>
		</div>
		<div class="control-group">
			<label class="control-label" for="subscription_email"><?php echo JText::_('COM_CMGROUPBUYING_SUBSCRIPTION_POPUP_EMAIL_LABEL'); ?></label>
			<div class="controls">
				<input class="input-block-level" type="text" id="subscription_email" name="subscription_email">
				<div id="email_error_message" class="subscription_form_error"></div>
			</div>
		</div>
		<div class="form-subscription-buttons">
			<button type="button" class="btn" onClick="skip_form()"><?php echo JText::_('COM_CMGROUPBUYING_SUBSCRIPTION_POPUP_SKIP'); ?></button>
			<button type="submit" class="btn btn-primary"><?php echo JText::_('COM_CMGROUPBUYING_SUBSCRIPTION_POPUP_SUBSCRIBE_BUTTON'); ?></button>
		</div>
	</form>
</div>
<form id="skip_form" method="get" action="index.php">
	<input type="hidden" name="option" id="option" value="com_cmgroupbuying" />
	<input type="hidden" name="controller" id="controller" value="subscription" />
	<input type="hidden" name="task" id="task" value="skip" />
</form>
<?php
else:
	JFactory::getApplication()->redirect($skipLink);
endif; 
?>