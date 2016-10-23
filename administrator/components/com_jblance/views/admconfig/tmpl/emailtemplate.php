<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	20 March 2012
 * @file name	:	views/admconfig/tmpl/emailtemplate.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Edit the email templates (jblance)
 */
 defined('_JEXEC') or die('Restricted access'); 

 JHtml::_('behavior.framework', true);
 JHtml::_('behavior.formvalidation');

 $editor = JFactory::getEditor();
 $app  	 = JFactory::getApplication();
 $tempFor = $app->input->get('tempfor', 'subscr-pending', 'string');
 
 $availableTags = array();
 
 $availableTags['subscr-approved-auto'] 	=
 $availableTags['subscr-pending'] 			= array(
	 											"[NAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_USER'), 
	 											"[PLANNAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_PLAN_SUBSCRIBE'), 
	 											"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'), 
	 											"[SITEURL]" => JText::_('COM_JBLANCE_SITE_URL'), 
	 											"[ADMINEMAIL]" => JText::_('COM_JBLANCE_EMAIL_OF_THE_ADMIN')
	 											);
 $availableTags['subscr-approved-admin'] 	= array(
	 											"[NAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_USER'), 
	 											"[PLANNAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_PLAN_SUBSCRIBE'), 
	 											"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'), 
	 											"[SITEURL]" => JText::_('COM_JBLANCE_SITE_URL'), 
	 											);
 $availableTags['subscr-details'] 			= array(
	 											"[NAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_USER'),
	 											"[USEREMAIL]" => JText::_('COM_JBLANCE_EMAIL_ID_OF_THE_USER'),
	 											"[USERNAME]" => JText::_('COM_JBLANCE_USERNAME_OF_THE_USER'),
	 											"[SUBSCRID]" => JText::_('COM_JBLANCE_SUBSCR_ID'),
	 											"[PLANNAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_PLAN_SUBSCRIBE'), 
	 											"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'), 
	 											"[GATEWAY]" => JText::_('COM_JBLANCE_PAYMENT_GATEWAY'), 
	 											"[PLANSTATUS]" => JText::_('COM_JBLANCE_STATUS_PLAN'), 
	 											);
 $availableTags['newuser-facebook-signin'] = 
 $availableTags['newuser-pending-approval'] = 
 $availableTags['newuser-activate'] 		= 
 $availableTags['newuser-login'] 			= 
 $availableTags['newuser-details'] 			= array(
	 											"[NAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_USER'), 
	 											"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'), 
	 											"[ACTLINK]" => JText::_('COM_JBLANCE_ACTIVATION_LINK'),
	 											"[SITEURL]" => JText::_('COM_JBLANCE_SITE_URL'),
	 											"[ADMINURL]" => JText::_('COM_JBLANCE_SITE_BACKEND_URL'),
	 											"[USERNAME]" => JText::_('COM_JBLANCE_USERNAME_OF_THE_USER'),
	 											"[PASSWORD]" => JText::_('COM_JBLANCE_PASSWORD_OF_THE_USER'), 
	 											"[USEREMAIL]" => JText::_('COM_JBLANCE_EMAIL_ID_OF_THE_USER'), 
	 											"[USERTYPE]" => JText::_('COM_JBLANCE_USERGROUP_OF_THE_USER'),
	 											"[STATUS]" => JText::_('COM_JBLANCE_STATUS'),
	 											);
$availableTags['newuser-account-approved'] 	= array(
 												"[NAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_USER'),
 												"[EMAIL]" => JText::_('COM_JBLANCE_EMAIL_ID_OF_THE_USER'),
 												"[USERNAME]" => JText::_('COM_JBLANCE_USERNAME_OF_THE_USER'),
 												"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'),
 												"[SITEURL]" => JText::_('COM_JBLANCE_SITE_URL')
 												);
$availableTags['proj-new-notify'] 			= array(
												"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'),
												"[SITEURL]" => JText::_('COM_JBLANCE_SITE_URL'),
 												"[PROJECTNAME]" => JText::_('COM_JBLANCE_PROJECT_TITLE'),
 												"[CATEGORYNAME]" => JText::_('COM_JBLANCE_PROJECT_CATEGORIES'),
 												"[CURRENCYSYM]" => JText::_('COM_JBLANCE_CURRENCY_SYMBOL'),
 												"[CURRENCYCODE]" => JText::_('COM_JBLANCE_CURRENCY_CODE'),
 												"[BUDGETMIN]" => JText::_('COM_JBLANCE_MINIMUM_BUDGET'),
 												"[BUDGETMAX]" => JText::_('COM_JBLANCE_MAXIMUM_BUDGET'),
 												"[STARTDATE]" => JText::_('COM_JBLANCE_PUBLISH_DATE'),
 												"[EXPIRE]" => JText::_('COM_JBLANCE_EXPIRES'),
 												"[PROJECTURL]" => JText::_(''),
 												"[CUSTOM_X_PUBLISHER]" => JText::_('COM_JBLANCE_CUSTOM_X_PUBLISHER'),
 												"[CUSTOM_X_PROJECT]" => JText::_('COM_JBLANCE_CUSTOM_X_PROJECT')
 												);
$availableTags['proj-pending-approval'] 	= array(
												"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'),
												"[ADMINURL]" => JText::_('COM_JBLANCE_SITE_BACKEND_URL'),
 												"[PROJECTNAME]" => JText::_('COM_JBLANCE_PROJECT_TITLE'),
 												"[PUBLISHERUSERNAME]" => JText::_(''),
												"[CUSTOM_X_PUBLISHER]" => JText::_('COM_JBLANCE_CUSTOM_X_PUBLISHER'),
												"[CUSTOM_X_PROJECT]" => JText::_('COM_JBLANCE_CUSTOM_X_PROJECT')
 												);
$availableTags['proj-approved'] 			= array(
												"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'),
												"[PROJECTURL]" => JText::_(''),
 												"[PROJECTNAME]" => JText::_('COM_JBLANCE_PROJECT_TITLE'),
 												"[PUBLISHERUSERNAME]" => JText::_(''),
												"[CUSTOM_X_PUBLISHER]" => JText::_('COM_JBLANCE_CUSTOM_X_PUBLISHER'),
												"[CUSTOM_X_PROJECT]" => JText::_('COM_JBLANCE_CUSTOM_X_PROJECT')
 												);
$availableTags['proj-expiry-reminder'] 			= array(
												"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'),
												"[SITEURL]" => JText::_('COM_JBLANCE_SITE_URL'),
 												"[PROJECTNAME]" => JText::_('COM_JBLANCE_PROJECT_TITLE'),
 												"[PUBLISHERNAME]" => JText::_(''),
 												"[PROJECTEXPIRYDATE]" => JText::_('COM_JBLANCE_EXPIRES_ON')
 												);
$availableTags['proj-newbid-notify'] 		= array(
												"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'),
												"[SITEURL]" => JText::_('COM_JBLANCE_SITE_URL'),
 												"[PUBLISHERNAME]" => JText::_(''),
 												"[PROJECTNAME]" => JText::_('COM_JBLANCE_PROJECT_TITLE'),
 												"[CATEGORYNAME]" => JText::_('COM_JBLANCE_PROJECT_CATEGORIES'),
 												"[CURRENCYSYM]" => JText::_('COM_JBLANCE_CURRENCY_SYMBOL'),
 												"[CURRENCYCODE]" => JText::_('COM_JBLANCE_CURRENCY_CODE'),
 												"[BUDGETMIN]" => JText::_('COM_JBLANCE_MINIMUM_BUDGET'),
 												"[BUDGETMAX]" => JText::_('COM_JBLANCE_MAXIMUM_BUDGET'),
 												"[STARTDATE]" => JText::_('COM_JBLANCE_PUBLISH_DATE'),
 												"[EXPIRE]" => JText::_('COM_JBLANCE_EXPIRES'),
 												"[BIDDERNAME]" => JText::_(''),
 												"[BIDDERUSERNAME]" => JText::_(''),
 												"[BIDAMOUNT]" => JText::_(''),
 												"[DELIVERY]" => JText::_(''),
												"[CUSTOM_X_PUBLISHER]" => JText::_('COM_JBLANCE_CUSTOM_X_PUBLISHER'),
												"[CUSTOM_X_BIDDER]" => JText::_('COM_JBLANCE_CUSTOM_X_BIDDER'),
												"[CUSTOM_X_PROJECT]" => JText::_('COM_JBLANCE_CUSTOM_X_PROJECT')
 												);
$availableTags['proj-lowbid-notify'] 		= array(
												"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'),
												"[SITEURL]" => JText::_('COM_JBLANCE_SITE_URL'),
 												"[PROJECTNAME]" => JText::_('COM_JBLANCE_PROJECT_TITLE'),
 												"[CURRENCYSYM]" => JText::_('COM_JBLANCE_CURRENCY_SYMBOL'),
 												"[CURRENCYCODE]" => JText::_('COM_JBLANCE_CURRENCY_CODE'),
 												"[BUDGETMIN]" => JText::_('COM_JBLANCE_MINIMUM_BUDGET'),
 												"[BUDGETMAX]" => JText::_('COM_JBLANCE_MAXIMUM_BUDGET'),
 												"[STARTDATE]" => JText::_('COM_JBLANCE_PUBLISH_DATE'),
 												"[EXPIRE]" => JText::_('COM_JBLANCE_EXPIRES'),
 												"[BIDDERUSERNAME]" => JText::_(''),
 												"[BIDAMOUNT]" => JText::_(''),
 												"[DELIVERY]" => JText::_('')
 												);
$availableTags['proj-bidwon-notify'] 		= 
$availableTags['proj-lowbid-notify'] 		= array(
												"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'),
												"[SITEURL]" => JText::_('COM_JBLANCE_SITE_URL'),
 												"[PROJECTNAME]" => JText::_('COM_JBLANCE_PROJECT_TITLE'),
 												"[BIDDERNAME]" => JText::_(''),
 												"[BIDDERUSERNAME]" => JText::_(''),
 												"[CURRENCYSYM]" => JText::_('COM_JBLANCE_CURRENCY_SYMBOL'),
 												"[CURRENCYCODE]" => JText::_('COM_JBLANCE_CURRENCY_CODE'),
 												"[BIDAMOUNT]" => JText::_(''),
 												"[DELIVERY]" => JText::_(''),
												"[CUSTOM_X_PUBLISHER]" => JText::_('COM_JBLANCE_CUSTOM_X_PUBLISHER'),
												"[CUSTOM_X_BIDDER]" => JText::_('COM_JBLANCE_CUSTOM_X_BIDDER'),
												"[CUSTOM_X_PROJECT]" => JText::_('COM_JBLANCE_CUSTOM_X_PROJECT')
 												);
$availableTags['proj-accept-notify-bidder']	= 
$availableTags['proj-accept-notify'] 		= 
$availableTags['proj-denied-notify'] 		= array(
												"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'),
												"[SITEURL]" => JText::_('COM_JBLANCE_SITE_URL'),
 												"[PROJECTNAME]" => JText::_('COM_JBLANCE_PROJECT_TITLE'),
 												"[PUBLISHERNAME]" => JText::_(''),
 												"[BIDDERNAME]" => JText::_(''),
 												"[BIDDERUSERNAME]" => JText::_(''),
												"[CUSTOM_X_PUBLISHER]" => JText::_('COM_JBLANCE_CUSTOM_X_PUBLISHER'),
												"[CUSTOM_X_BIDDER]" => JText::_('COM_JBLANCE_CUSTOM_X_BIDDER'),
												"[CUSTOM_X_PROJECT]" => JText::_('COM_JBLANCE_CUSTOM_X_PROJECT')
 												);
$availableTags['proj-bid-loosers-notify'] 		= array(
												"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'),
												"[SITEURL]" => JText::_('COM_JBLANCE_SITE_URL'),
 												"[PROJECTNAME]" => JText::_('COM_JBLANCE_PROJECT_TITLE')
 												);
$availableTags['proj-payment-complete'] 	= array(
												"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'),
												"[SITEURL]" => JText::_('COM_JBLANCE_SITE_URL'),
												"[PROJECTNAME]" => JText::_('COM_JBLANCE_PROJECT_TITLE'),
												"[RECIPIENT_USERNAME]" => JText::_(''),
												"[MARKEDBY_USERNAME]" => JText::_('')
												);
$availableTags['proj-progress-notify'] 		= array(
												"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'),
												"[SITEURL]" => JText::_('COM_JBLANCE_SITE_URL'),
												"[BUYER_USERNAME]" => JText::_(''),
												"[PROJECTNAME]" => JText::_('COM_JBLANCE_PROJECT_TITLE'),
												"[PROJECTID]" => JText::_(''),
												"[STATUS]" => JText::_(''),
												"[PERCENT]" => JText::_(''),
												);
$availableTags['svc-neworder-notify'] 		= array(
												"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'),
												"[SITEURL]" => JText::_('COM_JBLANCE_SITE_URL'),
												"[SELLER_USERNAME]" => JText::_(''),
												"[SERVICENAME]" => JText::_('COM_JBLANCE_SERVICE_TITLE'),
												"[SERVICEPRICE]" => JText::_('COM_JBLANCE_PRICE'),
												"[SERVICEDURATION]" => JText::_('COM_JBLANCE_DURATION'),
												"[TOTALPRICE]" => JText::_('COM_JBLANCE_TOTAL_PRICE'),
												"[TOTALDURATION]" => JText::_('COM_JBLANCE_TOTAL_DURATION'),
												"[SERVICEURL]" => JText::_(''),
												);
$availableTags['svc-progress-notify'] 		= array(
												"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'),
												"[SITEURL]" => JText::_('COM_JBLANCE_SITE_URL'),
												"[BUYER_USERNAME]" => JText::_(''),
												"[SERVICENAME]" => JText::_('COM_JBLANCE_SERVICE_TITLE'),
												"[ORDERID]" => JText::_(''),
												"[STATUS]" => JText::_(''),
												"[PERCENT]" => JText::_(''),
												);
$availableTags['svc-pending-approval'] 	= array(
												"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'),
												"[ADMINURL]" => JText::_('COM_JBLANCE_SITE_BACKEND_URL'),
												"[SERVICENAME]" => JText::_('COM_JBLANCE_SERVICE_TITLE'),
												"[SELLER_USERNAME]" => JText::_(''),
												);
$availableTags['fin-deposit-alert'] 		= array(
												"[NAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_USER'),
												"[USERNAME]" => JText::_('COM_JBLANCE_USERNAME_OF_THE_USER'),
 												"[INVOICENO]" => JText::_('COM_JBLANCE_INVOICE_NO'),
 												"[ADMINURL]" => JText::_('COM_JBLANCE_SITE_BACKEND_URL'),
 												"[GATEWAY]" => JText::_('COM_JBLANCE_PAYMENT_GATEWAY'), 
 												"[STATUS]" => JText::_('COM_JBLANCE_STATUS'),
 												"[AMOUNT]" => JText::_('COM_JBLANCE_AMOUNT'),
 												"[CURRENCYSYM]" => JText::_('COM_JBLANCE_CURRENCY_SYMBOL')
 												);
$availableTags['fin-witdrw-approved'] 		= 
$availableTags['fin-deposit-approved'] 		= array(
												"[NAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_USER'),
 												"[CURRENCYSYM]" => JText::_('COM_JBLANCE_CURRENCY_SYMBOL'),
 												"[AMOUNT]" => JText::_('COM_JBLANCE_AMOUNT'),
 												"[INVOICENO]" => JText::_('COM_JBLANCE_INVOICE_NO'),
 												"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'),
 												"[SITEURL]" => JText::_('COM_JBLANCE_SITE_URL')
 												);
$availableTags['fin-witdrw-request'] 		= array(
												"[NAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_USER'),
												"[USERNAME]" => JText::_('COM_JBLANCE_USERNAME_OF_THE_USER'),
 												"[INVOICENO]" => JText::_('COM_JBLANCE_INVOICE_NO'),
 												"[ADMINURL]" => JText::_('COM_JBLANCE_SITE_BACKEND_URL'),
 												"[GATEWAY]" => JText::_('COM_JBLANCE_PAYMENT_GATEWAY')
 												);
$availableTags['fin-escrow-accepted'] 		= 
$availableTags['fin-escrow-released'] 		= array(
												"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'),
												"[SITEURL]" => JText::_('COM_JBLANCE_SITE_URL'),
												"[PROJECTNAME]" => JText::_('COM_JBLANCE_PROJECT_TITLE'),
												"[SENDERUSERNAME]" => JText::_(''),
												"[RECEIVEUSERNAME]" => JText::_(''),
 												"[RELEASEDATE]" => JText::_('COM_JBLANCE_RELEASE_DATE'),
 												"[CURRENCYSYM]" => JText::_('COM_JBLANCE_CURRENCY_SYMBOL'),
 												"[AMOUNT]" => JText::_('COM_JBLANCE_AMOUNT'),
 												"[NOTE]" => JText::_('COM_JBLANCE_NOTE')
 												);
$availableTags['pm-new-notify'] 			= array(
												"[RECIPIENT_USERNAME]" => JText::_(''),
												"[SENDER_USERNAME]" => JText::_(''),
												"[MSG_SUBJECT]" => JText::_(''),
 												"[MSG_BODY]" => JText::_(''),
 												"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'),
												"[SITEURL]" => JText::_('COM_JBLANCE_SITE_URL')
 												);
$availableTags['pm-pending-approval'] 			= array(
												"[RECIPIENT_USERNAME]" => JText::_(''),
												"[SENDER_USERNAME]" => JText::_(''),
												"[MSG_SUBJECT]" => JText::_(''),
 												"[MSG_BODY]" => JText::_(''),
												"[ADMINURL]" => JText::_('COM_JBLANCE_SITE_BACKEND_URL'),
 												"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'),
												"[SITEURL]" => JText::_('COM_JBLANCE_SITE_URL')
 												);
$availableTags['report-default-action'] 	= array(
												"[TYPE]" => JText::_(''),
												"[COUNT]" => JText::_('COM_JBLANCE_COUNT'),
												"[ACTION]" => JText::_(''),
 												"[ITEMLINK]" => JText::_('COM_JBLANCE_ITEM_LINK'),
 												"[SITENAME]" => JText::_('COM_JBLANCE_NAME_OF_THE_SITE'),
 												);
?>
<script type="text/javascript">
<!--
	Joomla.submitbutton = function(task){
		if(document.formvalidator.isValid(document.id('emailtemp-form'))) {
			Joomla.submitform(task, document.getElementById('emailtemp-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('COM_JBLANCE_FIEDS_HIGHLIGHTED_RED_COMPULSORY'));?>');
		}
	}
//-->
</script>
<?php 
$link = JRoute::_('index.php?option=com_jblance&view=admconfig&layout=emailtemplate&tempfor=');

?>
<div id="j-sidebar-container" class="span2">
	<?php include_once(JPATH_COMPONENT.'/views/configmenu.php'); ?>
</div>
<div id="j-main-container" class="span10">
	<div id="navbar-example" class="navbar navbar-static navbar-inverse" style="margin-top: 5px;">
		<div class="navbar-inner">
			<div class="container">
				<a class="btn btn-navbar" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                  </a>
			<div class="nav-collapse navbar-responsive-collapse">
				<ul class="nav" role="navigation">
					<li class="dropdown">
						<a id="drop1" href="#" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php echo JText::_('COM_JBLANCE_SUBSCRIPTION'); ?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="<?php echo $link.'subscr-pending'; ?>"><?php echo JText::_('COM_JBLANCE_SUBSCRIPTION_PENDING'); ?></a></li>
							<li><a href="<?php echo $link.'subscr-approved-auto'; ?>"><?php echo JText::_('COM_JBLANCE_SUBSCRIPTION_AUTO_APPROVED'); ?></a></li>
							<li><a href="<?php echo $link.'subscr-approved-admin'; ?>"><?php echo JText::_('COM_JBLANCE_SUBSCRIPTION_ADMIN_APPROVED'); ?></a></li>
							<li><a href="<?php echo $link.'subscr-details'; ?>"><?php echo JText::_('COM_JBLANCE_SUBSCRIPTION_DETAILS'); ?></a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" id="drop2" role="button" class="dropdown-toggle" data-toggle="dropdown"><?php echo JText::_('COM_JBLANCE_REGISTRATION'); ?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="<?php echo $link.'newuser-details'; ?>"><?php echo JText::_('COM_JBLANCE_NEW_USER_DETAILS'); ?></a></li>
							<li><a href="<?php echo $link.'newuser-login'; ?>"><?php echo JText::_('COM_JBLANCE_NEW_USER_LOGIN'); ?></a></li>
							<li><a href="<?php echo $link.'newuser-activate'; ?>"><?php echo JText::_('COM_JBLANCE_NEW_USER_ACTIVATE'); ?></a></li>
							<li><a href="<?php echo $link.'newuser-pending-approval'; ?>"><?php echo JText::_('COM_JBLANCE_NEW_USER_PENDING_APPROVAL'); ?></a></li>
							<li><a href="<?php echo $link.'newuser-account-approved'; ?>"><?php echo JText::_('COM_JBLANCE_NEW_USER_ACCOUNT_APPROVED'); ?></a></li>
							<li><a href="<?php echo $link.'newuser-facebook-signin'; ?>"><?php echo JText::_('COM_JBLANCE_NEW_USER_FACEBOOK_SIGNIN'); ?></a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo JText::_('COM_JBLANCE_PROJECT_BIDDING'); ?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="<?php echo $link.'proj-new-notify'; ?>"><?php echo JText::_('COM_JBLANCE_NEW_PROJECT'); ?></a></li>
							<li><a href="<?php echo $link.'proj-pending-approval'; ?>"><?php echo JText::_('COM_JBLANCE_PROJECT_PENDING_APPROVAL'); ?></a></li>
							<li><a href="<?php echo $link.'proj-approved'; ?>"><?php echo JText::_('COM_JBLANCE_PROJECT_APPROVED'); ?></a></li>
							<li><a href="<?php echo $link.'proj-newbid-notify'; ?>"><?php echo JText::_('COM_JBLANCE_NEW_BID'); ?></a></li>
							<li><a href="<?php echo $link.'proj-lowbid-notify'; ?>"><?php echo JText::_('COM_JBLANCE_LOWER_BID'); ?></a></li>
							<li><a href="<?php echo $link.'proj-bidwon-notify'; ?>"><?php echo JText::_('COM_JBLANCE_BID_WON'); ?></a></li>
							<li><a href="<?php echo $link.'proj-denied-notify'; ?>"><?php echo JText::_('COM_JBLANCE_BID_DENIED'); ?></a></li>
							<li><a href="<?php echo $link.'proj-accept-notify'; ?>"><?php echo JText::_('COM_JBLANCE_BID_ACCEPTED_TO_PUBLISHER'); ?></a></li>
							<li><a href="<?php echo $link.'proj-accept-notify-bidder'; ?>"><?php echo JText::_('COM_JBLANCE_BID_ACCEPTED_TO_BIDDER'); ?></a></li>
							<li><a href="<?php echo $link.'proj-bid-loosers-notify'; ?>"><?php echo JText::_('COM_JBLANCE_BID_LOST_TO_LOOSERS'); ?></a></li>
							<li><a href="<?php echo $link.'proj-newforum-notify'; ?>"><?php echo JText::_('COM_JBLANCE_NEW_FORUM_MESSAGE'); ?></a></li>
							<li><a href="<?php echo $link.'proj-expiry-reminder'; ?>"><?php echo JText::_('COM_JBLANCE_PROJECT_EXPIRY_REMINDER'); ?></a></li>
							<li><a href="<?php echo $link.'proj-payment-complete'; ?>"><?php echo JText::_('COM_JBLANCE_PROJECT_PAYMENT_COMPLETE'); ?></a></li>
							<li><a href="<?php echo $link.'proj-private-invite'; ?>"><?php echo JText::_('COM_JBLANCE_PRIVATE_PROJECT_INVITATION'); ?></a></li>
							<li><a href="<?php echo $link.'proj-progress-notify'; ?>"><?php echo JText::_('COM_JBLANCE_PROJECT_PROGRESS_UPDATE'); ?></a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo JText::_('COM_JBLANCE_SERVICE'); ?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li class="nav-header"><?php echo JText::_('COM_JBLANCE_TO_FREELANCER'); ?></li>
							<li><a href="<?php echo $link.'svc-neworder-notify'; ?>"><?php echo JText::_('COM_JBLANCE_NEW_SERVICE_ORDER'); ?></a></li>
							<li><a href="<?php echo $link.'svc-approval_status'; ?>"><?php echo JText::_('COM_JBLANCE_SERVICE_APPROVAL_STATUS'); ?></a></li>
							<li class="nav-header"><?php echo JText::_('COM_JBLANCE_TO_BUYER'); ?></li>
							<li><a href="<?php echo $link.'svc-progress-notify'; ?>"><?php echo JText::_('COM_JBLANCE_SERVICE_PROGRESS_UPDATE'); ?></a></li>
							<li class="nav-header"><?php echo JText::_('COM_JBLANCE_TO_ADMIN'); ?></li>
							<li><a href="<?php echo $link.'svc-pending-approval'; ?>"><?php echo JText::_('COM_JBLANCE_SERVICE_PENDING_APPROVAL'); ?></a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo JText::_('COM_JBLANCE_FINANCE'); ?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="<?php echo $link.'fin-deposit-alert'; ?>"><?php echo JText::_('COM_JBLANCE_DEPOSIT_FUND_DETAILS'); ?></a></li>
							<li><a href="<?php echo $link.'fin-deposit-approved'; ?>"><?php echo JText::_('COM_JBLANCE_DEPOSIT_FUND_APPROVED'); ?></a></li>
							<li><a href="<?php echo $link.'fin-witdrw-request'; ?>"><?php echo JText::_('COM_JBLANCE_WITHDRAW_FUND_REQUEST'); ?></a></li>
							<li><a href="<?php echo $link.'fin-witdrw-approved'; ?>"><?php echo JText::_('COM_JBLANCE_WITHDRAW_REQUEST_APPROVED'); ?></a></li>
							<li><a href="<?php echo $link.'fin-escrow-released'; ?>"><?php echo JText::_('COM_JBLANCE_ESCROW_PAYMENT_RELEASED'); ?></a></li>
							<li><a href="<?php echo $link.'fin-escrow-accepted'; ?>"><?php echo JText::_('COM_JBLANCE_ESCROW_PAYMENT_ACCEPTED'); ?></a></li>
						</ul>
					</li>
					<li class="dropdown">
						<a href="#" class="dropdown-toggle" data-toggle="dropdown"><?php echo JText::_('COM_JBLANCE_PRIVATE_MESSAGE_REPORTING'); ?> <b class="caret"></b></a>
						<ul class="dropdown-menu">
							<li><a href="<?php echo $link.'pm-new-notify'; ?>"><?php echo JText::_('COM_JBLANCE_NEW_PM_NOTIFICATION'); ?></a></li>
							<li><a href="<?php echo $link.'pm-pending-approval'; ?>"><?php echo JText::_('COM_JBLANCE_PM_PENDING_APPROVAL'); ?></a></li>
							<li><a href="<?php echo $link.'report-default-action'; ?>"><?php echo JText::_('COM_JBLANCE_REPORTING_DEFAULT_ACTION'); ?></a></li>
						</ul>
					</li>
				</ul>
				</div>
			</div>
		</div>
	</div>
	<div class="clearfix"></div><br>
	<div class="row-fluid">
		<div class="span12">
			<form action="index.php" method="post" id="emailtemp-form" name="adminForm" class="form-validate">
				<div class="control-group">
		    		<label class="control-label" for="title"><?php echo JText::_('COM_JBLANCE_TITLE'); ?>:</label>
					<div class="controls">
						<span class="readonly"><?php echo $this->template->title; ?></span>
					</div>
		  		</div>
				<div class="control-group">
		    		<label class="control-label" for="subject"><?php echo JText::_('COM_JBLANCE_SUBJECT'); ?>:</label>
					<div class="controls">
						<input class="input-xxlarge required" type="text" name="subject" id="subject" value="<?php echo $this->template->subject; ?>" />
					</div>
		  		</div>
				<div class="control-group">
		    		<label class="control-label" for="body"><?php echo JText::_('COM_JBLANCE_MESSAGE_BODY'); ?>:</label>
					<div class="controls">
						<?php 
						$editor = JFactory::getEditor();
						echo $editor->display('body', $this->template->body, '100%', '300', '60', '20', false);
						?>	
					</div>
		  		</div>
			  	<div class="well well-small span8">
			  		<h2 class="module-title nav-header"><?php echo JText::_('COM_JBLANCE_AVAILABLE_TAGS'); ?></h2>
				  	<div class="row-striped">
				  	<?php 
					if(isset($availableTags[$tempFor])){
						foreach($availableTags[$tempFor] as $key=>$value) { ?>
						<div class="row-fluid">
							<div class="span4">
								<strong class="row-title"><?php echo $key; ?></strong>
							</div>
							<div class="span8">
								<?php echo $value; ?>
							</div>
						</div>
					<?php 
						}
					} ?>
					</div>
				</div>
			
				<input type="hidden" name="id" value="<?php echo $this->template->id; ?>" />
				<input type="hidden" name="templatefor" value="<?php echo $this->template->templatefor; ?>" />
				<input type="hidden" name="option" value="com_jblance" />
				<input type="hidden" name="task" value="saveemailtemplate" />
				<input type="hidden" name="check" value="post" />
				<?php echo JHtml::_('form.token'); ?>
			</form>	
		</div>
	</div>
</div>				