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

	JFactory::getDocument()->addScript('components/com_cmgroupbuying/assets/js/popup.js');

	// You can start customize the popup's style from here
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

	function submit_subscription_form()
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
	<div class="popupbox" id="popuprel">
		<div id="top_header">
			<?php echo JText::_('COM_CMGROUPBUYING_SUBSCRIPTION_POPUP_HEADER'); ?>
		</div>
		<div id="subscription_form">
			<form id="new_subscription" class="new_subscription" method="post" action="index.php" onsubmit="return submit_subscription_form()">
				<input type="hidden" name="option" id="option" value="com_cmgroupbuying" />
				<input type="hidden" name="controller" id="controller" value="subscription" />
				<input type="hidden" name="task" id="task" value="subscribe" />
				<table id="subscription_table">
					<tr>
						<td>
							<label>
								<?php echo JText::_('COM_CMGROUPBUYING_SUBSCRIPTION_POPUP_LOCATION_LABEL'); ?>
							</label>
						</td>
						<td>
							<select id="subscription_location" name="subscription_location">
								<?php foreach($this->locations as $location): ?>
								<option value="<?php echo $location['id']; ?>"><?php echo $location['name']; ?></option>
								<?php endforeach; ?> 
							</select>
						</td>
					</tr>
					<tr>
						<td>
							<label>
								<?php echo JText::_('COM_CMGROUPBUYING_SUBSCRIPTION_POPUP_NAME_LABEL'); ?>
							</label>
						</td>
						<td>
							<input type="text" name="subscription_name" id="subscription_name">
							<div id="name_error_message" class="subscription_form_error"></div>
						</td>
					</tr>
					<tr>
						<td>
							<label>
								<?php echo JText::_('COM_CMGROUPBUYING_SUBSCRIPTION_POPUP_EMAIL_LABEL'); ?>
							</label>
						</td>
						<td>
							<input type="text" name="subscription_email" id="subscription_email">
							<div id="email_error_message" class="subscription_form_error"></div>
						</td>
					</tr>
					<tr>
						<td colspan="2">
							<input type="submit" value="<?php echo JText::_('COM_CMGROUPBUYING_SUBSCRIPTION_POPUP_SUBSCRIBE_BUTTON'); ?>" name="submit_subscription_popup" id="submit_subscription_popup">
							<br />
							<div onClick="skip_form()" id="skip_subscription_popup"><?php echo JText::_('COM_CMGROUPBUYING_SUBSCRIPTION_POPUP_SKIP'); ?></div>
						</td>
					</tr>
				</table>
			</form>
		</div>
	</div>
	<div id="fade"></div>
	<form id="skip_form" method="get" action="index.php">
		<input type="hidden" name="option" id="option" value="com_cmgroupbuying" />
		<input type="hidden" name="controller" id="controller" value="subscription" />
		<input type="hidden" name="task" id="task" value="skip" />
	</form>
<?php
	// End style customizing here
else:
	JFactory::getApplication()->redirect($skipLink);
endif;
?>