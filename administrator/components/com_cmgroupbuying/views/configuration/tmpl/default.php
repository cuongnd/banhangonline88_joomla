<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

// Load the tooltip behavior.
JHtml::_('behavior.tooltip');
JHtml::_('behavior.formvalidation');
JHtml::_('behavior.keepalive');
jimport('joomla.html.html');

if(version_compare(JVERSION, '3.0.0', 'ge'))
{
	JHtml::_('formbehavior.chosen', 'select');
}

require_once(JPATH_SITE. "/components/com_cmgroupbuying/helpers/common.php");

// $configuration = $this->configuration;
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task)
	{
		if(task == 'configuration.cancel' || document.formvalidator.isValid(document.id('configuration-form')))
		{
			Joomla.submitform(task, document.getElementById('configuration-form'));
		}
	}

	function getLink()
	{
		elements = jQuery("input#jform_coupon_elements").val();
		elements = elements.replace(/\s/g, "");
		elements = elements.replace(/fontsize/g,"f");
		elements = elements.replace(/size/g, "s");
		elements = elements.replace(/width/g,"w");
		elements = elements.replace(/height/g,"h");
		elements = elements.replace(/top/g,"t");
		elements = elements.replace(/left/g,"l");
		elements = elements.replace(/align/g,"a");
		elements = elements.replace(/visible/g,"v");
		elements = elements.replace(/:{/g,"--");
		elements = elements.replace(/},/g,"+");
		elements = elements.replace(/{/g,"");
		elements = elements.replace(/}/g,"");
		elements = elements.replace(/"/g,"");
		jQuery("a#designLink").attr("href", '');
		var link = 'index.php?option=com_cmgroupbuying&view=coupondesign&tmpl=component';
		link += '&elements=' + encodeURIComponent(elements);
		link += '&coupon=' + encodeURIComponent(jQuery("input#jform_coupon_background").val());
		jQuery("a#designLink").attr("href", link);
	}
</script>
<div class="cmgroupbuying">
	<form action="index.php" method="post" name="adminForm" id="configuration-form" class="form-horizontal">
		<input type="hidden" name="option" value="com_cmgroupbuying" />
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="controller" value="configuration" />
		<input type="hidden" name="id" value="1" />
	<?php if(!empty($this->sidebar)): ?>
		<div id="j-sidebar-container" class="span2">
			<?php echo $this->sidebar; ?>
		</div>
		<div id="j-main-container" class="span10">
	<?php else: ?>
		<div id="j-main-container">
	<?php endif; ?>
			<div id="com_cmgroupbuying_configuration" class="tabbable tabs-left">
				<ul class="nav nav-tabs">
					<li>
						<a href="#general" data-toggle="tab">
							<?php echo JText::_('COM_CMGROUPBUYING_CONFIGURATION_GENERAL_LABEL'); ?>
						</a>
					</li>
					<li>
						<a href="#currency" data-toggle="tab">
							<?php echo JText::_('COM_CMGROUPBUYING_CONFIGURATION_CURRENCY_LABEL'); ?>
						</a>
					</li>
					<li>
						<a href="#coupon" data-toggle="tab">
							<?php echo JText::_('COM_CMGROUPBUYING_CONFIGURATION_COUPON_LABEL'); ?>
						</a>
					</li>
					<li>
						<a href="#payment_methods" data-toggle="tab">
							<?php echo JText::_('COM_CMGROUPBUYING_CONFIGURATION_PAYMENT_METHODS_LABEL'); ?>
						</a>
					</li>
					<li>
						<a href="#layout" data-toggle="tab">
							<?php echo JText::_('COM_CMGROUPBUYING_CONFIGURATION_LAYOUT_LABEL'); ?>
						</a>
					</li>
					<li>
						<a href="#email_notification" data-toggle="tab">
							<?php echo JText::_('COM_CMGROUPBUYING_CONFIGURATION_EMAIL_NOTIFICATION_LABEL'); ?>
						</a>
					</li>
					<li>
						<a href="#facebook_comment" data-toggle="tab">
							<?php echo JText::_('COM_CMGROUPBUYING_CONFIGURATION_FACEBOOK_COMMENT_LABEL'); ?>
						</a>
					</li>
					<li>
						<a href="#disqus_comment" data-toggle="tab">
							<?php echo JText::_('COM_CMGROUPBUYING_CONFIGURATION_DISQUS_COMMENT_LABEL'); ?>
						</a>
					</li>
					<li>
						<a href="#plugin_template" data-toggle="tab">
							<?php echo JText::_('COM_CMGROUPBUYING_CONFIGURATION_PLG_CMDEALARTICLE_TEMPLATE'); ?>
						</a>
					</li>
					<li>
						<a href="#acymailing_integration" data-toggle="tab">
							<?php echo JText::_('COM_CMGROUPBUYING_CONFIGURATION_ACYMAILING_INTEGRATION_LABEL'); ?>
						</a>
					</li>
					<li>
						<a href="#point_system_integration" data-toggle="tab">
							<?php echo JText::_('COM_CMGROUPBUYING_CONFIGURATION_POINT_SYSTEM_INTEGRATION_LABEL'); ?>
						</a>
					</li>
					<li>
						<a href="#jomsocial_integration" data-toggle="tab">
							<?php echo JText::_('COM_CMGROUPBUYING_CONFIGURATION_JOMSOCIAL_INTEGRATION_LABEL'); ?>
						</a>
					</li>
					<li>
						<a href="#sh404sef_integration" data-toggle="tab">
							<?php echo JText::_('COM_CMGROUPBUYING_CONFIGURATION_SH404SEF_INTEGRATION_LABEL'); ?>
						</a>
					</li>
					<li>
						<a href="#geotargeting" data-toggle="tab">
							<?php echo JText::_('COM_CMGROUPBUYING_CONFIGURATION_GEOTARGETING_LABEL'); ?>
						</a>
					</li>
				</ul>
				<div class="tab-content">
					<div class="tab-pane" id="general">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('jquery_loading'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('jquery_loading'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('partner_folder'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('partner_folder'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('buy_as_guest'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('buy_as_guest'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('admin_email'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('admin_email'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('tos'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('tos'); ?>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="currency">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('currency_prefix'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('currency_prefix'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('currency_postfix'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('currency_postfix'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('currency_code'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('currency_code'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('currency_dec_point'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('currency_dec_point'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('currency_thousands_sep'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('currency_thousands_sep'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('currency_decimals'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('currency_decimals'); ?>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="coupon">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('coupon_format'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('coupon_format'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('qr_code_generator'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('qr_code_generator'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('coupon_background'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('coupon_background'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('coupon_elements'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('coupon_elements'); ?>
								<a id="designLink" onclick="getLink()" rel="{handler: 'iframe', size: {x: 800, y: 500}}" href="" class="modal btn"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_DESIGN_COUPON_BUTTON'); ?></a>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="payment_methods">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('payment_method_pretext'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('payment_method_pretext'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('payment_method_posttext'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('payment_method_posttext'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('payment_method_type'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('payment_method_type'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('direct_payment_method'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('direct_payment_method'); ?>
							</div>
						</div>
						<div class="control-group">
							<?php echo JText::_('COM_CMGROUPBUYING_CONFIGURATION_PAYMENT_METHOD_INSTRUCTION'); ?>
						</div>
					</div>
					<div class="tab-pane" id="layout">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('layout'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('layout'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('mobile_template'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('mobile_template'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('background_override'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('background_override'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('slideshow_switch_time'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('slideshow_switch_time'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('slideshow_fade_time'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('slideshow_fade_time'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('deal_map_width'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('deal_map_width'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('deal_map_height'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('deal_map_height'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('deal_map_zoom'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('deal_map_zoom'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('deal_map_latitude'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('deal_map_latitude'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('deal_map_longitude'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('deal_map_longitude'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('pagination_limit'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('pagination_limit'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('deal_list_effect'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('deal_list_effect'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('deal_list_slideshow_timing'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('deal_list_slideshow_timing'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('max_displayed_quantity'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('max_displayed_quantity'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('datetime_format'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('datetime_format'); ?>
							</div>
						</div>
						<div class="control-group">
							<?php echo JText::_('COM_CMGROUPBUYING_CONFIGURATION_DATETIME_FORMAT_INSTRUCTION'); ?>
						</div>
					</div>
					<div class="tab-pane" id="email_notification">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('mail_pay_buyer'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('mail_pay_buyer'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('mail_pay_partner'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('mail_pay_partner'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('mail_tip_partner'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('mail_tip_partner'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('mail_void_buyer'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('mail_void_buyer'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('mail_void_partner'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('mail_void_partner'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('mail_cash_buyer'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('mail_cash_buyer'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('mail_cash_admin'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('mail_cash_admin'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('mail_approve_partner'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('mail_approve_partner'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('mail_pending_admin'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('mail_pending_admin'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('mail_approve_coupon_partner'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('mail_approve_coupon_partner'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('mail_pending_coupon_admin'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('mail_pending_coupon_admin'); ?>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="facebook_comment">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('facebook_comment'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('facebook_comment'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('facebook_admin_user_id'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('facebook_admin_user_id'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('facebook_app_id'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('facebook_app_id'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('facebook_comment_num_posts'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('facebook_comment_num_posts'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('facebook_comment_width'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('facebook_comment_width'); ?>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="plugin_template">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('plg_cmdealarticle_template'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('plg_cmdealarticle_template'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('cmgbvariable'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('cmgbvariable'); ?>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="acymailing_integration">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('subscription_redirect'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('subscription_redirect'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('subscription_cookie_lifetime'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('subscription_cookie_lifetime'); ?>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="point_system_integration">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('point_system'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('point_system'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('pay_with_point'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('pay_with_point'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('purchase_bonus'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('purchase_bonus'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('deal_referral'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('deal_referral'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('exchange_rate'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('exchange_rate'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('point_cookie_lifetime'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('point_cookie_lifetime'); ?>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="disqus_comment">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('disqus_comment'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('disqus_comment'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('disqus_shortname'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('disqus_shortname'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('disqus_multilanguage'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('disqus_multilanguage'); ?>
							</div>
						</div>
						<div class="control-group">
							<?php echo JText::_('COM_CMGROUPBUYING_CONFIGURATION_DISQUS_COMMENT_INSTRUCTION'); ?>
						</div>
					</div>
					<div class="tab-pane" id="jomsocial_integration">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('jomsocial_activity'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('jomsocial_activity'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('jomsocial_activity_title'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('jomsocial_activity_title'); ?>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="sh404sef_integration">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('sh404sef_deal_alias'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('sh404sef_deal_alias'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('sh404sef_category_alias'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('sh404sef_category_alias'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('sh404sef_partner_alias'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('sh404sef_partner_alias'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('sh404sef_free_coupon_alias'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('sh404sef_free_coupon_alias'); ?>
							</div>
						</div>
					</div>
					<div class="tab-pane" id="geotargeting">
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('geotargeting'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('geotargeting'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('maxmind_path'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('maxmind_path'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('ipinfodb_key'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('ipinfodb_key'); ?>
							</div>
						</div>
						<div class="control-group">
							<div class="control-label">
								<?php echo $this->form->getLabel('geotargeting_cookie_lifetime'); ?>
							</div>
							<div class="controls">
								<?php echo $this->form->getInput('geotargeting_cookie_lifetime'); ?>
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<script type="text/javascript">
			jQuery('#com_cmgroupbuying_configuration a:first').tab('show');
		</script>
	</form>
</div>