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
?>
<script language="javascript" type="text/javascript">
    prepYUI();
</script>
<form action="index.php?option=com_jlivechat&view=operators" method="post" name="adminForm" id="adminForm">
    <table cellpadding="3" cellspacing="0" border="0">
	<tr>
	    <td class="label"><span class="hasTip" title="<?php echo JText::_('OPERATOR_ALTNAME_HELPTIP'); ?>"><?php echo JText::_('OPERATOR_DESCRIPTIVE_NAME'); ?></span></td>
	    <td><input type="text" id="alt_name" name="alt_name" value="<?php echo JRequest::getVar('alt_name', '', 'method'); ?>" size="30" /></td>
	</tr>
	<tr>
	    <td class="label"><span class="hasTip" title="<?php echo JText::_('DEPARTMENT_HELPTIP'); ?>"><?php echo JText::_('OPERATOR_DEPARTMENT'); ?></span></td>
	    <td>
		<div class="department-wrapper">
		    <input type="text" id="department" name="department" value="<?php echo JRequest::getVar('department', '', 'method'); ?>" size="30" />
		    <div class="clr">&nbsp;</div>
		    <div id="department-autocomplete"></div>
		</div>
	    </td>
	</tr>
    </table>
    <br />
    <h3><?php echo JText::_('OPERATOR_PERMISSIONS'); ?></h3>
    <table cellpadding="3" cellspacing="0" border="0">
	<tr>
	    <td class="label"><span class="hasTip" title="<?php echo JText::_('WEBSITE_MONITORING_PERMISSION_HELPTIP'); ?>"><?php echo JText::_('WEBSITE_MONITORING_PERMISSION'); ?></span></td>
	    <td>
		<label id="monitor-permission-container">
		    <select id="monitor_permission_selector" name="monitor_permission_selector">
			<option value="0"><?php echo JText::_('DISABLED'); ?></option>
			<option value="1" selected="selected"><?php echo JText::_('ENABLED'); ?></option>
		    </select>
		</label>
		<input type="hidden" id="monitor_permission_value" name="monitor_permission_value" value="1" />
	    </td>
	</tr>
	<tr>
	    <td class="label"><span class="hasTip" title="<?php echo JText::_('OPERATOR2OPERATOR_PERMISSION_HELPTIP'); ?>"><?php echo JText::_('OPERATOR2OPERATOR_PERMISSION'); ?></span></td>
	    <td>
		<label id="operator2operator-container">
		    <select id="operator2operator_selector" name="operator2operator_selector">
			<option value="0"><?php echo JText::_('DISABLED'); ?></option>
			<option value="1" selected="selected"><?php echo JText::_('ENABLED'); ?></option>
		    </select>
		</label>
		<input type="hidden" id="operator2operator_permission_value" name="operator2operator_permission_value" value="1" />
	    </td>
	</tr>
	<tr>
	    <td class="label"><span class="hasTip" title="<?php echo JText::_('OPERATOR_MESSAGE_PERMISSIONS_HELPTIP'); ?>"><?php echo JText::_('OPERATOR_MESSAGE_PERMISSIONS'); ?></span></td>
	    <td>
		<label id="message-permission-container">
		    <select id="message_permission_selector" name="message_permission_selector">
			<option value="0"><?php echo JText::_('CANNOT_READ_OR_DELETE'); ?></option>
			<option value="1" selected="selected"><?php echo JText::_('READ_AND_DELETE'); ?></option>
			<option value="2"><?php echo JText::_('READ_ONLY'); ?></option>
		    </select>
		</label>
		<input type="hidden" id="message_permission_value" name="message_permission_value" value="1" />
	    </td>
	</tr>
	<tr>
	    <td class="label"><span class="hasTip" title="<?php echo JText::_('SSL_COMMUNICATION_HELPTIP'); ?>"><?php echo JText::_('SSL_COMMUNICATION'); ?></span></td>
	    <td>
		<label id="useSSL-container">
		    <select id="use_ssl_selector" name="use_ssl_selector">
			<option value="0" selected="selected"><?php echo JText::_('DISABLED'); ?></option>
			<option value="1"><?php echo JText::_('ENABLED'); ?></option>
		    </select>
		</label>
		<input type="hidden" id="use_ssl_value" name="use_ssl_value" value="0" />
	    </td>
	</tr>

	<tr>
	    <td class="label"><span class="hasTip" title="<?php echo JText::_('ALLOW_IPBLOCKER_HELPTIP'); ?>"><?php echo JText::_('ALLOW_IPBLOCKER'); ?></span></td>
	    <td>
		<label id="ipblocker-container">
		    <select id="ipblocker_selector" name="ipblocker_selector">
			<option value="0"><?php echo JText::_('DISABLED'); ?></option>
			<option value="1" selected="selected"><?php echo JText::_('ENABLED'); ?></option>
		    </select>
		</label>
		<input type="hidden" id="ipblocker_value" name="ipblocker_value" value="1" />
	    </td>
	</tr>
    </table>
    <div class="clr">&nbsp;</div>
    <input type="hidden" name="task" value="create_new_operator" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
<script type="text/javascript">
    var monitorPermission = new YAHOO.widget.Button({
	id: "monitor-permission-menubutton",
	name: "monitor-permission-menubutton",
	label: "<em class=\"yui-button-label\"><?php echo JText::_('ENABLED'); ?></em>",
	type: "menu",
	menu: "monitor_permission_selector",
	container: "monitor-permission-container"
    });

    var operator2operatorPermission = new YAHOO.widget.Button({
	id: "operator2operator-permission-menubutton",
	name: "operator2operator-permission-menubutton",
	label: "<em class=\"yui-button-label\"><?php echo JText::_('ENABLED'); ?></em>",
	type: "menu",
	menu: "operator2operator_selector",
	container: "operator2operator-container"
    });

    var messagePermissions = new YAHOO.widget.Button({
	id: "message-permission-menubutton",
	name: "message-permission-menubutton",
	label: "<em class=\"yui-button-label\"><?php echo JText::_('READ_AND_DELETE'); ?></em>",
	type: "menu",
	menu: "message_permission_selector",
	container: "message-permission-container"
    });

    var useSSL = new YAHOO.widget.Button({
	id: "useSSL-menubutton",
	name: "useSSL-menubutton",
	label: "<em class=\"yui-button-label\"><?php echo JText::_('DISABLED'); ?></em>",
	type: "menu",
	menu: "use_ssl_selector",
	container: "useSSL-container"
    });

    var ipBlockerDropDown = new YAHOO.widget.Button({
	id: "ipblocker-menubutton",
	name: "ipblocker-menubutton",
	label: "<em class=\"yui-button-label\"><?php echo JText::_('ENABLED'); ?></em>",
	type: "menu",
	menu: "ipblocker_selector",
	container: "ipblocker-container"
    });

    monitorPermission.on("selectedMenuItemChange", onMonitorPermissionMenuItemChange);
    operator2operatorPermission.on("selectedMenuItemChange", onOperator2OperatorPermissionMenuItemChange);
    messagePermissions.on("selectedMenuItemChange", onMessagePermissionsItemChange);
    useSSL.on("selectedMenuItemChange", onUseSSLMenuItemChange);
    ipBlockerDropDown.on("selectedMenuItemChange", onIPBlockerMenuItemChange);

    var allDepartments = {
	departments: [
			<?php
			    if(count($this->departments) > 0) {
				foreach($this->departments as $key => $department) {
			 ?>
			    "<?php echo $department['department']; ?>"<?php if($key != (count($this->departments) - 1)) { ?>, <?php } ?>
			    
			 <?php
				}
			    }
			 ?>
		    ]
    };

    var autoCompleteFunc = function() {
	// Use a LocalDataSource
	var oDS = new YAHOO.util.LocalDataSource(allDepartments.departments);

	// Instantiate the AutoComplete
	var oAC = new YAHOO.widget.AutoComplete("department", "department-autocomplete", oDS);
	oAC.prehighlightClassName = "yui-ac-prehighlight";
	oAC.useShadow = true;

	return {
	    oDS: oDS,
	    oAC: oAC
	};
    }();

</script>
