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

var operatorsUri = 'index.php?option=com_jlivechat&view=operators';
var operatorsGrid = null;
var filterByDepartment = null;
var confirmDeleteMsg = 'Are you sure you want to delete the selected operators?';

window.addEvent('domready', function() {
    
});

Joomla.submitbutton = function(action) {
    if(action == 'add') {
	return createNewOperatorPage();
    } else if(action == 'remove') {
	return deleteOperators();
    } else if(action == 'cancel') {
	document.location.href='index.php?option=com_jlivechat&view=operators';

	return false;
    } else if(action == 'save' || action == 'apply') {
	return saveOperator();
    }
}

function saveOperator()
{
    var adminForm = $('adminForm');

    adminForm.submit();
    
    return true;
}

function createNewOperatorPage()
{
    document.location.href='index.php?option=com_jlivechat&view=operators&task=new_operator';

    return false;
}

var onFilterDepartmentMenuItemChange = function (event) {
    var oMenuItem = event.newValue;

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));

    filterByDepartment = oMenuItem.value;

    refreshOperators(false);
};

var onMonitorPermissionMenuItemChange = function (event) {
    var oMenuItem = event.newValue;
    var altValue = $('monitor_permission_value');

    altValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var onOperator2OperatorPermissionMenuItemChange = function (event) {
    var oMenuItem = event.newValue;
    var altValue = $('operator2operator_permission_value');

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

var onMessagePermissionsItemChange = function (event) {
    var oMenuItem = event.newValue;
    var altValue = $('message_permission_value');

    altValue.setProperty('value', oMenuItem.value);

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));
};

var requestBuilder = function (oState, oSelf) {
    var startIndex = (oState.pagination) ? oState.pagination.recordOffset : 0;
    var results = (oState.pagination) ? oState.pagination.rowsPerPage : null;
    var sort = (oState.sortedBy) ? oState.sortedBy.key : oSelf.getColumnSet().keys[0].getKey();
    var dir = (oState.sortedBy && oState.sortedBy.dir === YAHOO.widget.DataTable.CLASS_DESC) ? "desc" : "asc";
    
    var returnURI = '&results='+results;

    returnURI += '&startIndex='+startIndex;
    returnURI += '&sort='+sort;
    returnURI += '&dir='+dir;

    if(filterByDepartment) {
	if(filterByDepartment.length > 0) {
	    returnURI += '&query=department&qtype='+URLEncode(filterByDepartment);
	}
    }
    
    return returnURI;
};


var operatorRowClickEvt = function (evt) {
    var selectedRows = $$('#operators-grid .yui-dt-selected');
    var boxChecked = $('boxchecked');
    
    if(selectedRows) {
	if(selectedRows.length > 0) {
	    // There is at least one row selected
	    boxChecked.setProperty('value', 1);

	    return true;
	}
    }

    boxChecked.setProperty('value', 0);
};

function refreshOperators(resetRecordOffset)
{
    if(operatorsGrid)
    {
	fireDT(resetRecordOffset);
    }
}

var fireDT = function (resetRecordOffset) {
    var oState = operatorsGrid.dt.getState(), request, oCallback;

    /* We don't always want to reset the recordOffset.
	If we want the Paginator to be set to the first page,
	pass in a value of true to this method. Otherwise, pass in
	false or anything falsy and the paginator will remain at the
	page it was set at before.*/
    if (resetRecordOffset) {
	oState.pagination.recordOffset = 0;
    }

    /* If the column sort direction needs to be updated, that may be done here.
	It is beyond the scope of this example, but the DataTable::sortColumn() method
	has code that can be used with some modification. */

    /*
	This example uses onDataReturnSetRows because that method
	will clear out the old data in the DataTable, making way for
	the new data.*/
    oCallback = {
	success : operatorsGrid.dt.onDataReturnSetRows,
	failure : operatorsGrid.dt.onDataReturnSetRows,
	argument : oState,
	scope : operatorsGrid.dt
    };

    // Generate a query string
    request = operatorsGrid.dt.get("generateRequest")(oState, operatorsGrid.dt);

    // Fire off a request for new data.
    operatorsGrid.ds.sendRequest(request, oCallback);
}

function deleteOperators()
{
    var answer = confirm(confirmDeleteMsg);

    if(answer == true) {
	var adminForm = $('adminForm');
	var task = $('task');

	task.setProperty('value', 'delete_operators');

	var selectedOperators = $$('#operators-grid .yui-dt-selected .yui-dt0-col-operator_id .yui-dt-liner');
	var selectedOperatorsList = '';
	var selectedOperatorsTarget = $('selected_operators');
	
	if(selectedOperators) {
	    selectedOperators.each(function (item, index) {
		selectedOperatorsList += String(item.get('text'))+',';
	    });

	    selectedOperatorsTarget.setProperty('value', selectedOperatorsList);

	    adminForm.submit();
	}
    }
}

function toggleOperatorStatus(operatorId)
{
    new Request({url: operatorsUri, noCache: true, onSuccess: function(response){
	refreshOperators(false);
    }}).get({'task': 'toggle_operator_status', 'operator_id': operatorId});
}

function moveUpSortOrder(itemId)
{
    new Request({url: operatorsUri, noCache: true, onSuccess: function(response){
	refreshOperators(false);
    }}).get({'task': 'move_up_key_sort_order', 'o': itemId});
}

function moveDownSortOrder(itemId)
{
    new Request({url: operatorsUri, noCache: true, onSuccess: function(response){
	refreshOperators(false);
    }}).get({'task': 'move_down_key_sort_order', 'o': itemId});
}


