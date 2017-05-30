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

window.addEvent('domready', function() {
    
});

function toggleMessages()
{
    var enableMessageCheckbox = $('enable_messages');
    var emailMessagesTo = $('email_messages_to');
    var enableMessages = parseInt(enableMessageCheckbox.get('value'));
    
    if(enableMessages == 1)
    {
	emailMessagesTo.setProperty('disabled', false);
    }
    else
    {
	emailMessagesTo.setProperty('disabled', true);
    }
}

var onPopupModeSelectedMenuItemChange = function (event) {
    var popupModeValue = $('popup_mode');
    var oMenuItem = event.newValue;

    popupModeValue.setProperty('value', oMenuItem.value);
    
    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var onTimezoneSelectedMenuItemChange = function (event) {
    var listValue = $('offset_value');
    var oMenuItem = event.newValue;

    listValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var onLanguageSelectedMenuItemChange = function (event) {
    var listValue = $('language_value');
    var oMenuItem = event.newValue;

    listValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var onRingOrderSelectedMenuItemChange = function (event) {
    var listValue = $('ring_order_value');
    var oMenuItem = event.newValue;

    listValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var onOperator2OperatorSelectedMenuItemChange = function (event) {
    var listValue = $('operator2operator_value');
    var oMenuItem = event.newValue;

    listValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var onActivityMonitorSelectedMenuItemChange = function (event) {
    var listValue = $('activity_monitor_value');
    var oMenuItem = event.newValue;

    listValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var onRouteBySelectedMenuItemChange = function (event) {
    var listValue = $('routeby');
    var oMenuItem = event.newValue;

    listValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var onEnableMessagingSelectedMenuItemChange = function (event) {
    var listValue = $('enable_messaging');
    var oMenuItem = event.newValue;

    listValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var onAskPhoneNumberSelectedMenuItemChange = function (event) {
    var listValue = $('ask_phone_number');
    var oMenuItem = event.newValue;

    listValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var onProactiveChatSelectedMenuItemChange = function (event) {
    var listValue = $('proactive_chat');
    var oMenuItem = event.newValue;

    listValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var onAutopopupSelectedMenuItemChange = function (event) {
    var listValue = $('autopopup');
    var oMenuItem = event.newValue;

    listValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var onRouteDisplayFormatSelectedMenuItemChange = function (event) {
    var listValue = $('route_display_format');
    var oMenuItem = event.newValue;

    listValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var onPopupUseSSLSelectedMenuItemChange = function (event) {
    var oMenuItem = event.newValue;
    var listValue = $('popup_ssl');
    
    listValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var onUseProxySelectedMenuItemChange = function (event) {
    var oMenuItem = event.newValue;
    var listValue = $('use_proxy');

    listValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var onUseSocksSelectedMenuItemChange = function (event) {
    var oMenuItem = event.newValue;
    var listValue = $('use_socks');

    listValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var onUseGZipMenuItemChange = function (event) {
    var oMenuItem = event.newValue;
    var listValue = $('use_gzip');

    listValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var onPushNotificationsItemChange = function (event) {
    var oMenuItem = event.newValue;
    var listValue = $('use_pushservice');

    listValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var onUniversalMessagesMenuItemChange = function (event) {
    var oMenuItem = event.newValue;
    var listValue = $('universal_messages');

    listValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

Joomla.submitbutton = function(action) {
    if(action == 'save' || action == 'apply') {
	saveSettings();
    } else if(action == 'cancel') {
	document.location.href='index.php?option=com_jlivechat&view=settings';
    }
}

function saveSettings()
{
    var adminForm = $('adminForm');

    adminForm.submit();
}
