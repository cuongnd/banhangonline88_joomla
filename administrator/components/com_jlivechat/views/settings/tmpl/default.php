<?php 
/**
 * @package JLive! Chat
 * @version 4.3.3
 * @copyright (C) Copyright 2008-2010 CMS Fruit, CMSFruit.com. All rights reserved.
 * @license GNU/LGPL http://www.gnu.org/licenses/lgpl-3.0.txt

 This program is free software; you can redistribute it and/or modify
 it under the terms of the GNU Lesser General Public License as published by
 the Free Software Foundation; either version 3 of the License, or (at your
 option) any later version.

 This program is distributed in the hope that it will be useful, but
 WITHOUT ANY WARRANTY; without even the implied warranty of MERCHANTABILITY
 or FITNESS FOR A PARTICULAR PURPOSE.  See the GNU Lesser General Public
 License for more details.

 You should have received a copy of the GNU Lesser General Public License
 along with this program.  If not, see http://www.gnu.org/licenses/.
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.file');
?>
<script language="javascript" type="text/javascript">
    prepYUI();
</script>
<form action="" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
    <div id="settings" class="yui-navset">
	<ul class="yui-nav">
	    <li class="selected"><a href="#tab1"><em><?php echo JText::_('GENERAL'); ?></em></a></li>
	    <li><a href="#tab2"><em><?php echo JText::_('POPUP'); ?></em></a></li>
	    <li><a href="#tab3"><em><?php echo JText::_('MESSAGES'); ?></em></a></li>
	    <li><a href="#tab4"><em><?php echo JText::_('PROACTIVE'); ?></em></a></li>
	    <li><a href="#tab5"><em><?php echo JText::_('AUTOPOPUP'); ?></em></a></li>
	    <li><a href="#tab6"><em><?php echo JText::_('IMAGES'); ?></em></a></li>
	    <li><a href="#tab7"><em><?php echo JText::_('CSS'); ?></em></a></li>
	    <li><a href="#tab8"><em><?php echo JText::_('LANGUAGE_TAB'); ?></em></a></li>
	    <li><a href="#tab9"><em><?php echo JText::_('HTML Integration Snippets'); ?></em></a></li>
	    <li><a href="#tab10"><em><?php echo JText::_('PROXY'); ?></em></a></li>
	    <li><a href="#tab11"><em><?php echo JText::_('Advanced'); ?></em></a></li>
	</ul>
	<div class="yui-content">
	    <div id="tab1">
		<p>
		<table cellpadding="3" cellspacing="0" border="0">
		    
		    <tr>
			<td class="label"><span class="hasTip" title="<?php echo JText::_('SITENAME_HELPTIP'); ?>"><?php echo JText::_('SITENAME'); ?></span></td>
			<td>
			    <input type="text" id="site_name" name="site_name" value="<?php echo $this->settings->getSiteName(); ?>" size="30" />
			</td>
		    </tr>
		    <tr>
			<td class="label"><span class="hasTip" title="<?php echo JText::_('EMAILS_FROM_HELPTIP'); ?>"><?php echo JText::_('EMAILS_FROM'); ?></span></td>
			<td>
			    <input type="text" size="30" id="emails_from" name="emails_from" value="<?php echo $this->settings->getSetting('emails_from'); ?>" />
			</td>
		    </tr>
		    <tr>
			<td class="label"><span class="hasTip" title="<?php echo JText::_('TIMEZONE_HELPTIP'); ?>"><?php echo JText::_('TIMEZONE_OFFSET'); ?></span></td>
			<td>
			    <label id="timezone-container">
			    <?php echo JHTML::_('select.genericlist',  $this->timeoffsets, 'offset', 'class="inputbox" size="1"', 'value', 'text', $this->settings->getSetting('timezone_offset')); ?>
			    </label>
			    <input type="hidden" id="offset_value" name="offset_value" value="<?php echo $this->settings->getSetting('timezone_offset'); ?>" />
			</td>
		    </tr>

		    <tr>
			<td class="label hasTip" title="<?php echo JText::_('LANGUAGE_HELPTIP'); ?>"><?php echo JText::_('DEFAULT_LANGUAGE'); ?></td>
			<td>
			    <label id="language-container">
			    <?php echo JHTML::_('select.genericlist',  $this->languages, 'languages', 'class="inputbox" size="1"', 'value', 'text', $this->currentlang['value']); ?>
			    </label>
			    <input type="hidden" id="language_value" name="language_value" value="<?php echo $this->currentlang['value']; ?>" />
			</td>
		    </tr>
		    
		    <tr>
			<td class="label hasTip" title="<?php echo JText::_('RING_ORDER_HELPTIP'); ?>"><?php echo JText::_('DEFAULT_RING_ORDER'); ?></td>
			<td>
			    <label id="ring-order-container">
				<select id="ring_order_selector" name="ring_order_selector">
				    <option value="ring_same_time"<?php if($this->settings->getSetting('ring_order') == 'ring_same_time') { ?> selected="selected"<?php } ?>><?php echo JText::_('RING_OPERATORS_AT_SAME_TIME'); ?></option>
				    <option value="ring_in_order"<?php if($this->settings->getSetting('ring_order') == 'ring_in_order') { ?> selected="selected"<?php } ?>><?php echo JText::_('RING_OPERATORS_IN_ORDER'); ?></option>
				</select>
			    </label>
			    <input type="hidden" id="ring_order_value" name="ring_order_value" value="<?php echo $this->settings->getSetting('ring_order'); ?>" />
			</td>
		    </tr>
		    <tr>
			<td class="label hasTip" title="<?php echo JText::_('OPERATOR2OPERATOR_HELPTIP'); ?>"><?php echo JText::_('OPERATOR_TO_OPERATOR'); ?></td>
			<td>
			    <label id="operator2operator-container">
				<select id="operator2operator_selector" name="operator2operator_selector">
				    <option value="0"<?php if($this->settings->getSetting('operator2operator') == 0) { ?> selected="selected"<?php } ?>><?php echo JText::_('DISABLED'); ?></option>
				    <option value="1"<?php if($this->settings->getSetting('operator2operator') == 1) { ?> selected="selected"<?php } ?>><?php echo JText::_('ENABLED'); ?></option>
				</select>
			    </label>
			    <input type="hidden" id="operator2operator_value" name="operator2operator_value" value="<?php echo $this->settings->getSetting('operator2operator'); ?>" />
			</td>
		    </tr>
		    <tr>
			<td class="label hasTip" title="<?php echo JText::_('ACTIVITY_MONITORING_HELPTIP'); ?>"><?php echo JText::_('ACTIVITY_MONITORING'); ?></td>
			<td>
			    <label id="activity-monitor-container">
				<select id="activity_monitor_selector" name="activity_monitor_selector">
				    <option value="0"<?php if($this->settings->getSetting('activity_monitor') == 0) { ?> selected="selected"<?php } ?>><?php echo JText::_('DISABLED'); ?></option>
				    <option value="1"<?php if($this->settings->getSetting('activity_monitor') == 1) { ?> selected="selected"<?php } ?>><?php echo JText::_('ENABLED'); ?></option>
				</select>
			    </label>
			    <input type="hidden" id="activity_monitor_value" name="activity_monitor_value" value="<?php echo $this->settings->getSetting('activity_monitor'); ?>" />
			</td>
		    </tr>
		    <tr>
			<td class="label hasTip" title="<?php echo JText::_('MONITORING_TIMEOUT_HELPTIP'); ?>"><?php echo JText::_('MONITORING_TIMEOUT'); ?></td>
			<td>
			    <input type="text" size="4" id="activity_monitor_expiration" name="activity_monitor_expiration" value="<?php echo $this->settings->getSetting('activity_monitor_expiration'); ?>" /> <?php echo JText::_('IN_MINUTES'); ?>
			</td>
		    </tr>
		</table>
		<br />
		<script language="Javascript" type="text/javascript">
		    var timezoneMenu = new YAHOO.widget.Button({
			id: "timezone-menubutton",
			name: "timezone-menubutton",
			label: "<em class=\"yui-button-label\"><?php echo $this->currentoffset->text; ?></em>",
			type: "menu",
			menu: "offset",
			container: "timezone-container"
		    });

		    var languageMenu = new YAHOO.widget.Button({
			id: "language-menubutton",
			name: "language-menubutton",
			label: "<em class=\"yui-button-label\"><?php echo $this->currentlang['text']; ?></em>",
			type: "menu",
			menu: "languages",
			container: "language-container"
		    });

		    var ringOrderMenu = new YAHOO.widget.Button({
			id: "ring-order-menubutton",
			name: "ring-order-menubutton",
			label: "<em class=\"yui-button-label\"><?php if($this->settings->getSetting('ring_order') == 'ring_same_time') { echo JText::_('RING_OPERATORS_AT_SAME_TIME'); } elseif($this->settings->getSetting('ring_order') == 'ring_in_order') { echo JText::_('RING_OPERATORS_IN_ORDER'); }; ?></em>",
			type: "menu",
			menu: "ring_order_selector",
			container: "ring-order-container"
		    });
		    
		    var operator2operatorMenu = new YAHOO.widget.Button({
			id: "operator2operator-menubutton",
			name: "operator2operator-menubutton",
			label: "<em class=\"yui-button-label\"><?php if($this->settings->getSetting('operator2operator') == 0) { echo JText::_('DISABLED'); } elseif($this->settings->getSetting('operator2operator') == 1) { echo JText::_('ENABLED'); }; ?></em>",
			type: "menu",
			menu: "operator2operator_selector",
			container: "operator2operator-container"
		    });

		    var activityMonitorMenu = new YAHOO.widget.Button({
			id: "activity-monitor-menubutton",
			name: "activity-monitor-menubutton",
			label: "<em class=\"yui-button-label\"><?php if($this->settings->getSetting('activity_monitor') == 0) { echo JText::_('DISABLED'); } elseif($this->settings->getSetting('activity_monitor') == 1) { echo JText::_('ENABLED'); }; ?></em>",
			type: "menu",
			menu: "activity_monitor_selector",
			container: "activity-monitor-container"
		    });
		    
		    timezoneMenu.on("selectedMenuItemChange", onTimezoneSelectedMenuItemChange);
		    languageMenu.on("selectedMenuItemChange", onLanguageSelectedMenuItemChange);
		    ringOrderMenu.on("selectedMenuItemChange", onRingOrderSelectedMenuItemChange);
		    operator2operatorMenu.on("selectedMenuItemChange", onOperator2OperatorSelectedMenuItemChange);
		    activityMonitorMenu.on("selectedMenuItemChange", onActivityMonitorSelectedMenuItemChange);
		</script>
		</p>
	    </div>
	    <div id="tab2">
		<p>
		<!-- POPUP WINDOW SETTINGS SECTION -->
		<table cellpadding="3" cellspacing="0" border="0">
		    <tr>
			<td class="label"><?php echo JText::_('DEFAULT_POPUP_MODE'); ?></td>
			<td>
			    <label id="popup-mode-container">
				<select id="popup_mode_selector" name="popup_mode_selector">
					<option value="popup"<?php if($this->settings->getSetting('popup_mode') == 'popup') { ?> selected="selected"<?php } ?>>Popup Window</option>
					<option value="iframe"<?php if($this->settings->getSetting('popup_mode') == 'iframe') { ?> selected="selected"<?php } ?>>iFrame Popup</option>
				</select>
			    </label>
			    <input type="hidden" id="popup_mode" name="popup_mode" value="<?php echo $this->settings->getSetting('popup_mode'); ?>" />
			</td>
		    </tr>
		    <tr>
			<td class="label hasTip" title="<?php echo JText::_('ROUTEBY_HELPTIP'); ?>"><?php echo JText::_('ROUTE_BY'); ?></td>
			<td>
			    <label id="routeby-container">
				<select id="routeby_selector" name="routeby_selector">
				    <option value="none"<?php if($this->settings->getSetting('routeby') == 'none') { ?> selected="selected"<?php } ?>><?php echo JText::_('JNONE'); ?></option>
				    <option value="department"<?php if($this->settings->getSetting('routeby') == 'department') { ?> selected="selected"<?php } ?>><?php echo JText::_('BY_DEPARTMENT'); ?></option>
				    <option value="operator"<?php if($this->settings->getSetting('routeby') == 'operator') { ?> selected="selected"<?php } ?>><?php echo JText::_('BY_OPERATOR'); ?></option>
				</select>
			    </label>
			    <input type="hidden" id="routeby" name="routeby" value="<?php echo $this->settings->getSetting('routeby'); ?>" />
			</td>
		    </tr>
		    <tr>
			<td class="label hasTip" title="<?php echo JText::_('ROUTE_DISPLAY_FORMAT_HELPTIP'); ?>"><?php echo JText::_('ROUTE_DISPLAY_FORMAT'); ?></td>
			<td>
			    <label id="route-display-format-container">
				<select id="route_display_format_selector" name="route_display_format_selector">
					<option value="all_w_status"<?php if($this->settings->getSetting('route_display_format') == 'all_w_status') { ?> selected="selected"<?php } ?>><?php echo JText::_('ROUTE_FORMAT_DISPLAY_ALL'); ?></option>
					<option value="all_wo_status"<?php if($this->settings->getSetting('route_display_format') == 'all_wo_status') { ?> selected="selected"<?php } ?>><?php echo JText::_('ROUTE_FORMAT_DISPLAY_ALL_WO_STATUS'); ?></option>
					<option value="only_online"<?php if($this->settings->getSetting('route_display_format') == 'only_online') { ?> selected="selected"<?php } ?>><?php echo JText::_('ROUTE_FORMAT_DISPLAY_ONLY_ONLINE'); ?></option>
				</select>
			    </label>
			    <input type="hidden" id="route_display_format" name="route_display_format" value="<?php echo $this->settings->getSetting('route_display_format'); ?>" />
			</td>
		    </tr>


		    <tr>
			<td class="label"><?php echo JText::_('FORCE_USE_SSL_LBL'); ?></td>
			<td>
			    <label id="popup-ssl-container">
				<select id="popup_ssl_selector" name="popup_ssl_selector">
					<option value="0"<?php if($this->settings->getSetting('popup_ssl') == 0) { ?> selected="selected"<?php } ?>><?php echo JText::_('DISABLED'); ?></option>
					<option value="1"<?php if($this->settings->getSetting('popup_ssl') == 1) { ?> selected="selected"<?php } ?>><?php echo JText::_('ENABLED'); ?></option>
				</select>
			    </label>
			    <input type="hidden" id="popup_ssl" name="popup_ssl" value="<?php echo $this->settings->getSetting('popup_ssl'); ?>" />
			</td>
		    </tr>

		    <tr>
			<td class="label hasTip" title="<?php echo JText::_('POPUP_WINDOW_PAGETITLE_HELPTIP'); ?>"><?php echo JText::_('POPUP_WINDOW_PAGETITLE'); ?></td>
			<td>
			    <input size="30" type="text" id="popup_page_title" name="popup_page_title" value="<?php echo $this->settings->getSetting('popup_page_title'); ?>" />
			</td>
		    </tr>
		</table>
		<br />
		<br />
		<div id="popup_languages" class="yui-navset" style="width: 99%; margin: 0 auto;">
		    <ul class="yui-nav">
			<?php for($a = 0; $a < count($this->languages); $a++) { ?>
			<li<?php if($this->languages[$a]['value'] == $this->currentlang['value']) { ?> class="selected"<?php } ?>><a href="#tab<?php echo $a; ?>"><em><?php echo $this->languages[$a]['text']; ?></em></a></li>
			<?php } ?>
		    </ul>
		    <div class="yui-content">
			<?php for($a = 0; $a < count($this->languages); $a++) { ?>
			<div id="tab<?php echo $a; ?>">
			    <p>
				<label class="label2"><?php echo JText::_('WINDOW_TITLE').' ('.$this->languages[$a]['value'].')'; ?></label>
				<br />
				<input style="font-size: 1.2em;" type="text" name="window_title-<?php echo $this->languages[$a]['value']; ?>" value="<?php echo $this->popup->getWindowTitle($this->languages[$a]['value']); ?>" size="70" />
				<br /><br />
				<label class="label2"><?php echo JText::_('WINDOW_INTRO').' ('.$this->languages[$a]['value'].')'; ?></label>
				<br />
				<?php
				    echo $this->editor->display('window_intro-'.$this->languages[$a]['value'], $this->popup->getWindowIntro($this->languages[$a]['value']), '580', '250', '60', '15', false);
				?>
				<br />
				<br />
				<label class="label2"><?php echo JText::_('OFFLINE_MSG').' ('.$this->languages[$a]['value'].')'; ?></label>
				<br />
				<?php
				    echo $this->editor->display('offline_msg-'.$this->languages[$a]['value'], $this->popup->getWindowOffline($this->languages[$a]['value']), '580', '250', '60', '15', false);
				?>
			    </p>
			</div>
			<?php } ?>
		    </div>
		</div>
		<script language="Javascript" type="text/javascript">
		    var oMenuButton1 = new YAHOO.widget.Button({
			id: "popup-mode-menubutton",
			name: "popup-mode-menubutton",
			label: "<em class=\"yui-button-label\"><?php if($this->settings->getSetting('popup_mode') == 'popup') { echo 'Popup Window'; } elseif($this->settings->getSetting('popup_mode') == 'iframe') { echo 'iFrame Popup'; }; ?></em>",
			type: "menu",
			menu: "popup_mode_selector",
			container: "popup-mode-container"
		    });

		    var routeByBtn = new YAHOO.widget.Button({
			id: "routeby-menubutton",
			name: "routeby-menubutton",
			label: "<em class=\"yui-button-label\"><?php if($this->settings->getSetting('routeby') == 'department') { echo JText::_('BY_DEPARTMENT'); } elseif($this->settings->getSetting('routeby') == 'operator') { echo JText::_('BY_OPERATOR'); } else { echo JText::_('JNONE'); }; ?></em>",
			type: "menu",
			menu: "routeby_selector",
			container: "routeby-container"
		    });

		    var routeDisplayFrmtBtn = new YAHOO.widget.Button({
			id: "route-display-format-menubutton",
			name: "route-display-format-menubutton",
			label: "<em class=\"yui-button-label\"><?php if($this->settings->getSetting('route_display_format') == 'all_w_status') { echo JText::_('ROUTE_FORMAT_DISPLAY_ALL'); } elseif($this->settings->getSetting('route_display_format') == 'all_wo_status') { echo JText::_('ROUTE_FORMAT_DISPLAY_ALL_WO_STATUS'); } elseif($this->settings->getSetting('route_display_format') == 'only_online') { echo JText::_('ROUTE_FORMAT_DISPLAY_ONLY_ONLINE'); }; ?></em>",
			type: "menu",
			menu: "route_display_format_selector",
			container: "route-display-format-container"
		    });

		    var popupUseSSLBtn = new YAHOO.widget.Button({
			id: "popup-ssl-menubutton",
			name: "popup-ssl-menubutton",
			label: "<em class=\"yui-button-label\"><?php if($this->settings->getSetting('popup_ssl') == 0) { echo JText::_('DISABLED'); } elseif($this->settings->getSetting('popup_ssl') == 1) { echo JText::_('ENABLED'); } ?></em>",
			type: "menu",
			menu: "popup_ssl_selector",
			container: "popup-ssl-container"
		    });
		    
		    //	Register a "selectedMenuItemChange" event handler that will sync the
		    //	Button's "label" attribute to the MenuItem that was clicked.
		    oMenuButton1.on("selectedMenuItemChange", onPopupModeSelectedMenuItemChange);
		    routeByBtn.on("selectedMenuItemChange", onRouteBySelectedMenuItemChange);
		    routeDisplayFrmtBtn.on("selectedMenuItemChange", onRouteDisplayFormatSelectedMenuItemChange);
		    popupUseSSLBtn.on("selectedMenuItemChange", onPopupUseSSLSelectedMenuItemChange);
		</script>
		</p>
	    </div>
	    <div id="tab3">
		<p>
		    <!-- MESSAGE SETTINGS SECTION -->
		    <table cellpadding="3" cellspacing="0" border="0">
			<tr>
			    <td class="label hasTip" title="<?php echo JText::_('ENABLE_MESSAGING_HELPTIP'); ?>"><?php echo JText::_('ALLOW_LEAVING_MESSAGES'); ?></td>
			    <td>
				<label id="enable-messaging-container">
				    <select id="enable_messaging_selector" name="enable_messaging_selector">
					    <option value="n"<?php if($this->settings->getSetting('enable_messaging') == 'n') { ?> selected="selected"<?php } ?>><?php echo JText::_('JNO'); ?></option>
					    <option value="y"<?php if($this->settings->getSetting('enable_messaging') == 'y') { ?> selected="selected"<?php } ?>><?php echo JText::_('JYES'); ?></option>
				    </select>
				</label>
				<input type="hidden" id="enable_messaging" name="enable_messaging" value="<?php echo $this->settings->getSetting('enable_messaging'); ?>" />
			    </td>
			</tr>
			
			<tr>
			    <td class="label"><span class="hasTip" title="<?php echo JText::_('ASK_FOR_PHONE_HELPTIP'); ?>"><?php echo JText::_('ASK_FOR_PHONE'); ?></span></td>
			    <td>
				<label id="phone-number-container">
				    <select id="ask_phone_number_selector" name="ask_phone_number_selector">
					    <option value="optional"<?php if($this->settings->getSetting('ask_phone_number') == 'optional') { ?> selected="selected"<?php } ?>><?php echo JText::_('OPTIONAL'); ?></option>
					    <option value="required"<?php if($this->settings->getSetting('ask_phone_number') == 'required') { ?> selected="selected"<?php } ?>><?php echo JText::_('REQUIRED'); ?></option>
					    <option value="none"<?php if($this->settings->getSetting('ask_phone_number') == 'none') { ?> selected="selected"<?php } ?>><?php echo JText::_('NO_PHONE_NUMBER'); ?></option>
				    </select>
				</label>
				<input type="hidden" id="ask_phone_number" name="ask_phone_number" value="<?php echo $this->settings->getSetting('ask_phone_number'); ?>" />
			    </td>
			</tr>
			<tr>
			    <td class="label"><span class="hasTip" title="<?php echo JText::_('EMAIL_MESSAGES_TO_HELPTIP'); ?>"><?php echo JText::_('EMAIL_MESSAGES_TO'); ?></span></td>
			    <td>
				<textarea id="email_messages_to" name="email_messages_to" rows="7" cols="35"><?php if($this->settings->getSetting('email_messages_to')) { echo implode("\r\n", $this->settings->getSetting('email_messages_to')); } ?></textarea>
			    </td>
			</tr>

			<tr>
			    <td class="label hasTip" title="<?php echo JText::_('Treat all operator messages universally.<br />(Default: No)'); ?>"><?php echo JText::_('Universal Messages:'); ?></td>
			    <td>
				<label id="universal-messages-container">
				    <select id="universal_messages_selector" name="universal_messages_selector">
					    <option value="0"<?php if($this->settings->getSetting('universal_messages') == 0) { ?> selected="selected"<?php } ?>><?php echo JText::_('JNO'); ?></option>
					    <option value="1"<?php if($this->settings->getSetting('universal_messages') == 1) { ?> selected="selected"<?php } ?>><?php echo JText::_('JYES'); ?></option>
				    </select>
				</label>
				<input type="hidden" id="universal_messages" name="universal_messages" value="<?php echo $this->settings->getSetting('universal_messages'); ?>" />
			    </td>
			</tr>
		    </table>
		    <br />
		<script language="Javascript" type="text/javascript">
		    var enableMessagingMenu = new YAHOO.widget.Button({
			id: "enable-messaging-menubutton",
			name: "enable-messaging-menubutton",
			label: "<em class=\"yui-button-label\"><?php if($this->settings->getSetting('enable_messaging') == 'n') { echo JText::_('JNO'); } elseif($this->settings->getSetting('enable_messaging') == 'y') { echo JText::_('JYES'); }; ?></em>",
			type: "menu",
			menu: "enable_messaging_selector",
			container: "enable-messaging-container"
		    });

		    var askNumberBtn = new YAHOO.widget.Button({
			id: "ask-phone-menubutton",
			name: "ask-phone-menubutton",
			label: "<em class=\"yui-button-label\"><?php if($this->settings->getSetting('ask_phone_number') == 'optional') { echo JText::_('OPTIONAL'); } elseif($this->settings->getSetting('ask_phone_number') == 'required') { echo JText::_('REQUIRED'); } else { echo JText::_('NO_PHONE_NUMBER'); }; ?></em>",
			type: "menu",
			menu: "ask_phone_number_selector",
			container: "phone-number-container"
		    });

		    var universalMessagesBtn = new YAHOO.widget.Button({
			id: "universal-messages-menubutton",
			name: "universal-messages-menubutton",
			label: "<em class=\"yui-button-label\"><?php if($this->settings->getSetting('universal_messages') == 0) { echo JText::_('JNO'); } elseif($this->settings->getSetting('universal_messages') == 1) { echo JText::_('JYES'); }; ?></em>",
			type: "menu",
			menu: "universal_messages_selector",
			container: "universal-messages-container"
		    });

		    //	Register a "selectedMenuItemChange" event handler that will sync the
		    //	Button's "label" attribute to the MenuItem that was clicked.
		    enableMessagingMenu.on("selectedMenuItemChange", onEnableMessagingSelectedMenuItemChange);
		    askNumberBtn.on("selectedMenuItemChange", onAskPhoneNumberSelectedMenuItemChange);
		    universalMessagesBtn.on("selectedMenuItemChange", onUniversalMessagesMenuItemChange);
		    </script>
		</p>
	    </div>
	    <div id="tab4">
		<p>
		    <!-- PROACTIVE SETTINGS SECTION -->
		    <table cellpadding="3" cellspacing="0" border="0">
			<tr>
			    <td class="label hasTip" title="<?php echo JText::_('PROACTIVE_HELPTIP'); ?>"><?php echo JText::_('PROACTIVE_CHATTING'); ?></td>
			    <td>
				<label id="proactive-chat-container">
				    <select id="proactive_chat_selector" name="proactive_chat_selector">
					    <option value="0"<?php if($this->settings->getSetting('proactive_chat') == 0) { ?> selected="selected"<?php } ?>><?php echo JText::_('PROACTIVE_DISABLED'); ?></option>
					    <option value="1"<?php if($this->settings->getSetting('proactive_chat') == 1) { ?> selected="selected"<?php } ?>><?php echo JText::_('PROACTIVE_ENABLED'); ?></option>
					    <option value="2"<?php if($this->settings->getSetting('proactive_chat') == 2) { ?> selected="selected"<?php } ?>><?php echo JText::_('PROACTIVE_ENABLED_DELAYED'); ?></option>
				    </select>
				</label>
				<input type="hidden" id="proactive_chat" name="proactive_chat" value="<?php echo $this->settings->getSetting('proactive_chat'); ?>" />
			    </td>
			</tr>
		    </table>
		    <br />
		    <script language="Javascript" type="text/javascript">
			var proactiveChatMenu = new YAHOO.widget.Button({
			    id: "proactive-chat-menubutton",
			    name: "proactive-chat-menubutton",
			    label: "<em class=\"yui-button-label\"><?php if($this->settings->getSetting('proactive_chat') == 0) { echo JText::_('PROACTIVE_DISABLED'); } elseif($this->settings->getSetting('proactive_chat') == 1) { echo JText::_('PROACTIVE_ENABLED'); } else { echo JText::_('PROACTIVE_ENABLED_DELAYED'); } ?></em>",
			    type: "menu",
			    menu: "proactive_chat_selector",
			    container: "proactive-chat-container"
			});

			//	Register a "selectedMenuItemChange" event handler that will sync the
			//	Button's "label" attribute to the MenuItem that was clicked.
			proactiveChatMenu.on("selectedMenuItemChange", onProactiveChatSelectedMenuItemChange);
		    </script>
		</p>
	    </div>
	    <div id="tab5">
		<p>
		    <!-- AUTO-POPUP SETTINGS SECTION -->
		    <table cellpadding="3" cellspacing="0" border="0">
			<tr>
			    <td class="label"><?php echo JText::_('AUTOPOPUP_LBL'); ?></td>
			    <td>
				<label id="auto-popup-container">
				    <select id="autopopup_selector" name="autopopup_selector">
					<option value="0"<?php if($this->settings->getSetting('autopopup') == 0) { ?> selected="selected"<?php } ?>><?php echo JText::_('DISABLED'); ?></option>
					<option value="1"<?php if($this->settings->getSetting('autopopup') == 1) { ?> selected="selected"<?php } ?>><?php echo JText::_('ENABLED'); ?></option>
				    </select>
				</label>
				<input type="hidden" id="autopopup" name="autopopup" value="<?php echo $this->settings->getSetting('autopopup'); ?>" />
			    </td>
			</tr>
			<tr>
			    <td class="label"><span class="hasTip" title="<?php echo JText::_('AUTOPOPUP_ONLINE_ONLY_HELPTIP'); ?>"><?php echo JText::_('AUTOPOPUP_SHOW_ONLY_ONLINE_LBL'); ?></span></td>
			    <td>
				<input type="checkbox" name="autopopup_only_online" value="1" <?php if($this->settings->getSetting('autopopup_only_online') == 1) { ?>checked="checked"<?php } ?> />
			    </td>
			</tr>
			<tr>
			    <td class="label"><span class="hasTip" title="<?php echo JText::_('DISPLAY_THE_FOLLOWING_HTML_HELPTIP'); ?>"><?php echo JText::_('DISPLAY_THE_FOLLOWING_HTML'); ?></span></td>
			    <td>
				<textarea id="display_html" name="display_html" rows="15" cols="80"><?php echo htmlentities($this->settings->getSetting('display_html'), ENT_NOQUOTES, 'UTF-8'); ?></textarea>
			    </td>
			</tr>
			<tr>
			    <td class="label"><span class="hasTip" title="<?php echo JText::_('IN_SECONDS_HELPTIP'); ?>"><?php echo JText::_('IN_THIS_MANY_SECONDS'); ?></span></td>
			    <td>
				<input type="text" size="10" name="display_html_in_seconds" value="<?php echo $this->settings->getSetting('display_html_in_seconds'); ?>" />
			    </td>
			</tr>
			<tr>
			    <td class="label"><span class="hasTip" title="<?php echo JText::_('ONLY_ON_THESE_PAGES_HELPTIP'); ?>"><?php echo JText::_('ONLY_ON_THESE_PAGES'); ?></span></td>
			    <td>
				<textarea id="display_html_on_uris" name="display_html_on_uris" rows="7" cols="50"><?php if($this->settings->getSetting('display_html_on_uris')) { echo implode("\r\n", $this->settings->getSetting('display_html_on_uris')); } ?></textarea>
			    </td>
			</tr>


			<tr>
			    <td class="label"><span class="hasTip" title="<?php echo JText::_('AUTOPOPUP_NUM_OF_TIMES_PER_SESSION_HELPTIP'); ?>"><?php echo JText::_('AUTOPOPUP_NUM_OF_TIMES_PER_SESSION'); ?></span></td>
			    <td>
				<input type="text" size="5" name="autopopup_max_display" value="<?php echo $this->settings->getSetting('autopopup_max_display'); ?>" />
			    </td>
			</tr>
		    </table>
		    <br />
		    <script language="Javascript" type="text/javascript">
			var autopopupMenu = new YAHOO.widget.Button({
			    id: "autopopup-menubutton",
			    name: "autopopup-menubutton",
			    label: "<em class=\"yui-button-label\"><?php if($this->settings->getSetting('autopopup') == 0) { echo JText::_('DISABLED'); } elseif($this->settings->getSetting('autopopup') == 1) { echo JText::_('ENABLED'); }; ?></em>",
			    type: "menu",
			    menu: "autopopup_selector",
			    container: "auto-popup-container"
			});

			//	Register a "selectedMenuItemChange" event handler that will sync the
			//	Button's "label" attribute to the MenuItem that was clicked.
			autopopupMenu.on("selectedMenuItemChange", onAutopopupSelectedMenuItemChange);
		    </script>
		</p>
	    </div>
	    <div id="tab6">
		<p>
		<h2><?php echo JText::_('ONLINE_OFFLINE_IMAGES'); ?></h2>
		<br />
		<?php if($this->module_installed) { ?>
		<table cellpadding="5" cellspacing="0" border="0">
		    <tr>
			<td align="right"><?php echo JText::_('LARGE_ONLINE_IMAGE'); ?></td>
			<td>
			    <img src="../modules/mod_jlivechat/livechat-large-online<?php echo $this->settings->getSetting('large_online_img_ext'); ?>?t=<?php echo time(); ?>" alt="Large Online Image" />
			</td>
		    </tr>
		    <tr>
			<td align="right"><?php echo JText::_('UPLOAD_NEW_IMAGE'); ?></td>
			<td>
			     <input type="file" name="large_online_img" />
			</td>
		    </tr>
		    <tr>
			<td colspan="2"><hr /></td>
		    </tr>
		    <tr>
			<td align="right"><?php echo JText::_('LARGE_OFFLINE_IMAGE'); ?></td>
			<td>
			    <img src="../modules/mod_jlivechat/livechat-large-offline<?php echo $this->settings->getSetting('large_offline_img_ext'); ?>?t=<?php echo time(); ?>" alt="Large Offline Image" />
			</td>
		    </tr>
		    <tr>
			<td align="right"><?php echo JText::_('UPLOAD_NEW_IMAGE'); ?></td>
			<td>
			     <input type="file" name="large_offline_img" />
			</td>
		    </tr>
		    <tr>
			<td colspan="2"><hr /></td>
		    </tr>
		    <tr>
			<td align="right"><?php echo JText::_('SMALL_ONLINE_IMG'); ?></td>
			<td>
			    <img src="../modules/mod_jlivechat/livechat-online<?php echo $this->settings->getSetting('small_online_img_ext'); ?>?t=<?php echo time(); ?>" alt="Small Online Image" />
			</td>
		    </tr>
		    <tr>
			<td align="right"><?php echo JText::_('UPLOAD_NEW_IMAGE'); ?></td>
			<td>
			     <input type="file" name="small_online_img" />
			</td>
		    </tr>
		    <tr>
			<td colspan="2"><hr /></td>
		    </tr>
		    <tr>
			<td align="right"><?php echo JText::_('SMALL_OFFLINE_IMG'); ?></td>
			<td>
			    <img src="../modules/mod_jlivechat/livechat-offline<?php echo $this->settings->getSetting('small_offline_img_ext'); ?>?t=<?php echo time(); ?>" alt="Small Offline Image" />
			</td>
		    </tr>
		    <tr>
			<td align="right"><?php echo JText::_('UPLOAD_NEW_IMAGE'); ?></td>
			<td>
			     <input type="file" name="small_offline_img" />
			</td>
		    </tr>
		</table>
		<?php } else { ?>
		<?php echo JText::_('MODULE_NOT_INSTALLED'); ?>
		<?php } ?>
		</p>
	    </div>
	    <div id="tab7">
		<p>
		    <?php echo JText::_('CUSTOM_CSS'); ?>
		    <br />
		    <textarea name="custom_css" rows="30" cols="100"><?php echo $this->custom_css; ?></textarea>
		</p>
	    </div>
	    <div id="tab8">
		<p>
		    <div id="language_strings" class="yui-navset" style="width: 99%; margin: 0 auto;">
			<ul class="yui-nav">
			    <?php for($a = 0; $a < count($this->languages); $a++) { ?>
			    <li<?php if($this->languages[$a]['value'] == $this->currentlang['value']) { ?> class="selected"<?php } ?>><a href="#tab<?php echo $a; ?>"><em><?php echo $this->languages[$a]['text']; ?></em></a></li>
			    <?php } ?>
			</ul>
			<div class="yui-content">
			    <?php for($a = 0; $a < count($this->languages); $a++) { ?>
			    <div id="tab<?php echo $a; ?>">
				<p>
				    <label class="label2"><?php echo JText::_('LANGUAGE_STRINGS').' ('.$this->languages[$a]['value'].')'; ?></label>
				    <br />
				    <textarea name="language_strings_<?php echo $this->languages[$a]['value']; ?>" cols="110" rows="20"><?php
				    $languageFilePath = JPATH_SITE.DIRECTORY_SEPARATOR.'language'.DIRECTORY_SEPARATOR.$this->languages[$a]['value'].DIRECTORY_SEPARATOR.$this->languages[$a]['value'].'.com_jlivechat.ini';

				    if(file_exists($languageFilePath)) echo file_get_contents($languageFilePath);
				    ?></textarea>

				    <br />
				    <br />
				    <?php echo JText::_('LANGUAGE_FILE_PATH').' '.$languageFilePath; ?>
				    <div class="clr">&nbsp;</div>
				    <span style="float: right;">
					<a href="index.php?option=com_jlivechat&view=settings&task=restore_original_language_file&restore_lang=<?php echo $this->languages[$a]['value']; ?>" id="restore_original_btn_<?php echo $this->languages[$a]['value']; ?>"><?php echo JText::_('RESTORE_ORIGINAL_LANGUAGE'); ?></a>
				    </span>
				    <div class="clr">&nbsp;</div>
				    <br />
				    <script type="text/javascript">
					<?php $varname = 'orestoreOriginalBtn'.preg_replace('@[^a-zA-Z0-9]+@', '', $this->languages[$a]['value']); ?>
					var <?php echo $varname; ?> = new YAHOO.widget.Button("restore_original_btn_<?php echo $this->languages[$a]['value']; ?>");
				    </script>
				</p>
			    </div>
			    <?php } ?>
			</div>
		    </div>
		</p>
	    </div>
	    <div id="tab9">
		<p>
		    Use the following HTML integration snippets to connect any other website or webpage to this livechat installation. You can use the following HTML snippets anywhere HTML is accepted.
		    <br />
		    <br />
		    Visitor Tracking HTML Snippet:
		    <br />
		    <textarea id="visitor_tracking_html_snippet" name="visitor_tracking_html_snippet" rows="9" cols="100"><?php echo htmlentities($this->visitor_tracking_html_snippet, ENT_NOQUOTES, 'UTF-8'); ?></textarea>

		    <br />
		    <br />
		    Online/Offline Image HTML Snippet:
		    <br />
		    <textarea id="jlc_img_html_snippet" name="jlc_img_html_snippet" rows="6" cols="100"><?php echo htmlentities($this->img_html_snippet, ENT_NOQUOTES, 'UTF-8'); ?></textarea>
		</p>
	    </div>
	    <div id="tab10">
		<p>
		    <table cellpadding="3" cellspacing="0" border="0">
			<tr>
			    <td class="label hasTip" title="<?php echo JText::_('USE_PROXY_HELPTIP'); ?>"><?php echo JText::_('USE_PROXY_LBL'); ?></td>
			    <td>
				<label id="use-proxy-container">
				    <select id="use_proxy_selector" name="use_proxy_selector">
					    <option value="0"<?php if($this->settings->getSetting('use_proxy') == 0) { ?> selected="selected"<?php } ?>><?php echo JText::_('JNO'); ?></option>
					    <option value="1"<?php if($this->settings->getSetting('use_proxy') == 1) { ?> selected="selected"<?php } ?>><?php echo JText::_('JYES'); ?></option>
				    </select>
				</label>
				<input type="hidden" id="use_proxy" name="use_proxy" value="<?php echo $this->settings->getSetting('use_proxy'); ?>" />
			    </td>
			</tr>

			<tr>
			    <td class="label hasTip" title="<?php echo JText::_('USE_SOCKS_HELPTIP'); ?>"><?php echo JText::_('USE_SOCKS_LBL'); ?></td>
			    <td>
				<label id="use-socks-container">
				    <select id="use_socks_selector" name="use_socks_selector">
					    <option value="0"<?php if($this->settings->getSetting('use_socks') == 0) { ?> selected="selected"<?php } ?>><?php echo JText::_('JNO'); ?></option>
					    <option value="1"<?php if($this->settings->getSetting('use_socks') == 1) { ?> selected="selected"<?php } ?>><?php echo JText::_('JYES'); ?></option>
				    </select>
				</label>
				<input type="hidden" id="use_socks" name="use_socks" value="<?php echo $this->settings->getSetting('use_socks'); ?>" />
			    </td>
			</tr>

			<tr>
			    <td class="label hasTip" title="<?php echo JText::_('PROXY_HOST_HELPTIP'); ?>"><?php echo JText::_('PROXY_HOST_LBL'); ?></td>
			    <td>
				<input type="text" id="proxy_uri" name="proxy_uri" value="<?php echo $this->settings->getSetting('proxy_uri'); ?>" size="30" />
			    </td>
			</tr>
			<tr>
			    <td class="label hasTip" title="<?php echo JText::_('PROXY_PORT_HELPTIP'); ?>"><?php echo JText::_('PROXY_PORT_LBL'); ?></td>
			    <td>
				<input type="text" id="proxy_port" name="proxy_port" value="<?php if($this->settings->getSetting('proxy_port') > 0) { echo $this->settings->getSetting('proxy_port'); } ?>" size="5" />
			    </td>
			</tr>
			<tr>
			    <td class="label hasTip" title="<?php echo JText::_('PROXY_AUTH_HELPTIP'); ?>"><?php echo JText::_('PROXY_AUTH_LBL'); ?></td>
			    <td>
				<input type="text" id="proxy_auth" name="proxy_auth" value="<?php echo $this->settings->getSetting('proxy_auth'); ?>" size="15" />
			    </td>
			</tr>
		    </table>
		    <br />
		    <script language="Javascript" type="text/javascript">
			var useProxyMenu = new YAHOO.widget.Button({
			    id: "use-proxy-menubutton",
			    name: "use-proxy-menubutton",
			    label: "<em class=\"yui-button-label\"><?php if($this->settings->getSetting('use_proxy') == 0) { echo JText::_('JNO'); } elseif($this->settings->getSetting('use_proxy') == 1) { echo JText::_('JYES'); }; ?></em>",
			    type: "menu",
			    menu: "use_proxy_selector",
			    container: "use-proxy-container"
			});

			var useSocksMenu = new YAHOO.widget.Button({
			    id: "use-socks-menubutton",
			    name: "use-socks-menubutton",
			    label: "<em class=\"yui-button-label\"><?php if($this->settings->getSetting('use_socks') == 0) { echo JText::_('JNO'); } elseif($this->settings->getSetting('use_socks') == 1) { echo JText::_('JYES'); }; ?></em>",
			    type: "menu",
			    menu: "use_socks_selector",
			    container: "use-socks-container"
			});

			useProxyMenu.on("selectedMenuItemChange", onUseProxySelectedMenuItemChange);
			useSocksMenu.on("selectedMenuItemChange", onUseSocksSelectedMenuItemChange);
		    </script>
		</p>
	    </div>
	    <div id="tab11">
		<p>
		    <table cellpadding="3" cellspacing="0" border="0">
			<?php if(strpos($this->mainframe->getCfg('tmp_path'), 'ultimatelivechat.com') === FALSE) { ?>
			<tr>
			    <td class="label hasTip" title="<?php echo JText::_('HOSTEDMODEAPIKEY_HELPTIP'); ?><br /><strong>* Optional</strong>"><a href="https://www.ultimatelivechat.com/my-account.html" target="_blank"><?php echo JText::_('HOSTED_MODE_APIKEY'); ?></a></td>
			    <td>
				<input type="text" id="hosted_mode_api_key" name="hosted_mode_api_key" value="<?php if($this->settings->getSetting('hosted_mode_api_key')) { echo '****************************'; } ?>" size="30" />
			    </td>
			</tr>
			<?php } ?>
			<tr>
			    <td class="label hasTip" title="Compress all data transmitted to/from operators using GZip compression.<br />(Default: Yes)">GZip Compression:</td>
			    <td>
				<label id="use-gzip-container">
				    <select id="use_gzip_selector" name="use_gzip_selector">
					    <option value="0"<?php if($this->settings->getSetting('use_gzip') == 0) { ?> selected="selected"<?php } ?>><?php echo JText::_('JNO'); ?></option>
					    <option value="1"<?php if($this->settings->getSetting('use_gzip') == 1) { ?> selected="selected"<?php } ?>><?php echo JText::_('JYES'); ?></option>
				    </select>
				</label>
				<input type="hidden" id="use_gzip" name="use_gzip" value="<?php echo $this->settings->getSetting('use_gzip'); ?>" />
			    </td>
			</tr>

			<tr>
			    <td class="label hasTip" title="Send push notifications to mobile users?<br />(Default: Yes)">Mobile Push Notifications:</td>
			    <td>
				<label id="push-notifications-container">
				    <select id="push_notifications_selector" name="push_notifications_selector">
					    <option value="0"<?php if($this->settings->getSetting('use_pushservice') == 0) { ?> selected="selected"<?php } ?>><?php echo JText::_('JNO'); ?></option>
					    <option value="1"<?php if($this->settings->getSetting('use_pushservice') == 1) { ?> selected="selected"<?php } ?>><?php echo JText::_('JYES'); ?></option>
				    </select>
				</label>
				<input type="hidden" id="use_pushservice" name="use_pushservice" value="<?php echo $this->settings->getSetting('use_pushservice'); ?>" />
			    </td>
			</tr>
		    </table>
		    <br />
		    <script language="Javascript" type="text/javascript">
			var useGZipMenu = new YAHOO.widget.Button({
			    id: "use-gzip-menubutton",
			    name: "use-gzip-menubutton",
			    label: "<em class=\"yui-button-label\"><?php if($this->settings->getSetting('use_gzip') == 0) { echo JText::_('JNO'); } elseif($this->settings->getSetting('use_gzip') == 1) { echo JText::_('JYES'); }; ?></em>",
			    type: "menu",
			    menu: "use_gzip_selector",
			    container: "use-gzip-container"
			});

			var usePushNotificationsMenu = new YAHOO.widget.Button({
			    id: "push-notifications-menubutton",
			    name: "push-notifications-menubutton",
			    label: "<em class=\"yui-button-label\"><?php if($this->settings->getSetting('use_pushservice') == 0) { echo JText::_('JNO'); } elseif($this->settings->getSetting('use_pushservice') == 1) { echo JText::_('JYES'); }; ?></em>",
			    type: "menu",
			    menu: "push_notifications_selector",
			    container: "push-notifications-container"
			});
			
			useGZipMenu.on("selectedMenuItemChange", onUseGZipMenuItemChange);
			usePushNotificationsMenu.on("selectedMenuItemChange", onPushNotificationsItemChange);
		    </script>
		</p>
	    </div>
	</div>
    </div>
    <input type="hidden" name="boxchecked" value="0" />
    <input type="hidden" name="task" value="save" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
<script language="javascript" type="text/javascript">
    var tabView = new YAHOO.widget.TabView('settings');
    var popupLanguagesTabView = new YAHOO.widget.TabView('popup_languages');
    var languagesTabView = new YAHOO.widget.TabView('language_strings');
</script>


