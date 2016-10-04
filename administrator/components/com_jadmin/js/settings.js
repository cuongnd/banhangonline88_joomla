/**
 * @package JAdmin!
 * @version 1.5.4.3
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

var onTimezoneSelectedMenuItemChange = function (event) {
    var listValue = $('offset_value');
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

Joomla.submitbutton = function(action) {
    if(action == 'save' || action == 'apply') {
	saveSettings();
    } else if(action == 'cancel') {
	document.location.href='index.php?option=com_jadmin&view=settings';
    }
}

function saveSettings() {
    var adminForm = $('adminForm');

    adminForm.submit();
}
