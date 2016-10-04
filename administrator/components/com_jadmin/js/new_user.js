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

Joomla.submitbutton = function(action) {
    if(action == 'cancel') {
	document.location.href='index.php?option=com_jadmin&view=users';

	return false;
    } else if(action == 'save' || action == 'apply') {
	return saveUser();
    }
}

function saveUser() {
    var adminForm = $('adminForm');

    adminForm.submit();
    
    return true;
}

var onAdminUserMenuItemChange = function (event) {
    var oMenuItem = event.newValue;
    var altValue = $('mapped_to_admin_value');

    altValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var onMonitorPermissionMenuItemChange = function (event) {
    var oMenuItem = event.newValue;
    var altValue = $('monitor_permission_value');

    altValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var onUseSSLMenuItemChange = function (event) {
    var oMenuItem = event.newValue;
    var altValue = $('use_ssl_value');

    altValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};


var onIPBlockerMenuItemChange = function (event) {
    var oMenuItem = event.newValue;
    var altValue = $('ipblocker_value');

    altValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};
