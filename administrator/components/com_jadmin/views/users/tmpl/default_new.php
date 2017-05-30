<?php 
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

defined('_JEXEC') or die('Restricted access'); 
?>
<script language="javascript" type="text/javascript">
    prepYUI();
</script>
<form action="index.php?option=com_jadmin&view=users" method="post" name="adminForm" id="adminForm">
    <table cellpadding="3" cellspacing="0" border="0">
	<tr>
	    <td class="label"><span class="hasTip" title="<?php echo JText::_('DESCRIPTIVE_NAME_HELPTIP'); ?>"><?php echo JText::_('DESCRIPTIVE_NAME'); ?></span></td>
	    <td><input type="text" id="desc_name" name="desc_name" value="<?php echo JRequest::getVar('desc_name', '', 'method'); ?>" size="30" /></td>
	</tr>
	<tr>
	    <td class="label"><span class="hasTip" title="<?php echo JText::_('DEPARTMENT_HELPTIP'); ?>"><?php echo JText::_('DEPARTMENT_LBL'); ?></span></td>
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
    <h3><?php echo JText::_('SECURITY'); ?></h3>
    <table cellpadding="3" cellspacing="0" border="0">
	<tr>
	    <td class="label"><span class="hasTip" title="<?php echo JText::_('MAPPED_TO_ADMIN_LBL_HELPTIP'); ?>"><?php echo JText::_('MAPPED_TO_ADMIN_LBL'); ?></span></td>
	    <td>
		<label id="mapped-to-admin-container">
		    <select id="mapped_to_admin_selector" name="mapped_to_admin_selector">
			<?php
			if(!empty($this->admin_users)) {
			    foreach($this->admin_users as $row) {
			?>
			<option value="<?php echo $row['id']; ?>"><?php echo $row['username']; ?> - <?php echo $row['name'].' '.$row['id']; ?></option>
			<?php
			    }
			}
			?>
		    </select>
		</label>
		<input type="hidden" id="mapped_to_admin_value" name="mapped_to_admin_value" />
	    </td>
	</tr>
	<tr>
	    <td class="label"><span class="hasTip" title="<?php echo JText::_('USER_IP_RESTRICT_HELPTIP'); ?>"><?php echo JText::_('USER_IP_RESTRICT_LBL'); ?></span></td>
	    <td>
		<textarea name="ip_restrict" cols="20" rows="4"><?php echo JRequest::getVar('ip_restrict', '', 'method'); ?></textarea>
	    </td>
	</tr>
    </table>
    <br />
    <h3><?php echo JText::_('PERMISSIONS'); ?></h3>
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
    </table>
    <br />
    <div class="clr">&nbsp;</div>
    <?php
    if(!empty($this->available_permissions)) {
	$numOfColumns = 4;
	$columnCounter = 0;

	foreach($this->available_permissions as $permissionId => $permission) {
    ?>
	<span style="width: 200px; float: left; margin: 10px 10px 5px 0;">
	    <input type="checkbox" name="permissions[]" value="<?php echo $permissionId; ?>" checked="checked" /><?php echo $permission['label']; ?>
	</span>
	<?php
	    ++$columnCounter;

	    if($columnCounter >= $numOfColumns)
	    {
		$columnCounter = 0;
	?>
	<div class="jlc-clr"></div>
    <?php
	    }
	}
    }
    ?>
    <br />
    <div class="clr">&nbsp;</div>
    <input type="hidden" name="task" value="create_new" />
    <input type="hidden" name="boxchecked" value="0" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
<script type="text/javascript">
    var mappedToAdminList = new YAHOO.widget.Button({
	id: "mapped-to-admin-menubutton",
	name: "mapped-to-admin-menubutton",
	label: "<em class=\"yui-button-label\">admin - Administrator 42</em>",
	type: "menu",
	menu: "mapped_to_admin_selector",
	container: "mapped-to-admin-container"
    });
    
    var monitorPermission = new YAHOO.widget.Button({
	id: "monitor-permission-menubutton",
	name: "monitor-permission-menubutton",
	label: "<em class=\"yui-button-label\"><?php echo JText::_('ENABLED'); ?></em>",
	type: "menu",
	menu: "monitor_permission_selector",
	container: "monitor-permission-container"
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

    mappedToAdminList.on("selectedMenuItemChange", onAdminUserMenuItemChange);
    monitorPermission.on("selectedMenuItemChange", onMonitorPermissionMenuItemChange);
    useSSL.on("selectedMenuItemChange", onUseSSLMenuItemChange);
    ipBlockerDropDown.on("selectedMenuItemChange", onIPBlockerMenuItemChange);

    var allDepartments = {
	departments: [
			<?php
			    if(!empty($this->departments)) {
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
