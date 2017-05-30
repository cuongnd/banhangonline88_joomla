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

var usersUri = 'index.php?option=com_jadmin&view=users&format=raw';
var usersGrid = null;
var filterByDepartment = null;
var confirmDeleteMsg = 'Are you sure you want to delete the selected users?';

window.addEvent('domready', function() {
    
});

Joomla.submitbutton = function(action) {
    if(action == 'add') {
	return createNewPage();
    } else if(action == 'remove') {
	return deleteUsers();
    } else if(action == 'cancel') {
	document.location.href='index.php?option=com_jadmin&view=users';

	return false;
    } else if(action == 'save' || action == 'apply') {
	return saveSortOrder();
    }
}

function saveSortOrder()
{
    var adminForm = $('adminForm');

    adminForm.submit();
    
    return true;
}

function createNewPage()
{
    document.location.href='index.php?option=com_jadmin&view=users&task=new_user';

    return false;
}

var onFilterDepartmentMenuItemChange = function (event) {
    var oMenuItem = event.newValue;

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));

    filterByDepartment = oMenuItem.value;

    refreshUsers(false);
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


var userRowClickEvt = function (evt) {
    var selectedRows = $$('#users-grid .yui-dt-selected');
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

function refreshUsers(resetRecordOffset)
{
    if(usersGrid)
    {
	fireDT(resetRecordOffset);
    }
}

var fireDT = function (resetRecordOffset) {
    var oState = usersGrid.dt.getState(), request, oCallback;

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
	success : usersGrid.dt.onDataReturnSetRows,
	failure : usersGrid.dt.onDataReturnSetRows,
	argument : oState,
	scope : usersGrid.dt
    };

    // Generate a query string
    request = usersGrid.dt.get("generateRequest")(oState, usersGrid.dt);

    // Fire off a request for new data.
    usersGrid.ds.sendRequest(request, oCallback);
}

function deleteUsers()
{
    var answer = confirm(confirmDeleteMsg);

    if(answer == true) {
	var adminForm = $('adminForm');
	var task = $('task');

	task.setProperty('value', 'delete_users');

	var selectedItems = $$('#users-grid .yui-dt-selected .yui-dt0-col-user_id .yui-dt-liner');
	var selectedItemsList = '';
	var selectedItemsTarget = $('selected_rows');
	
	if(selectedItems) {
	    selectedItems.each(function (item, index) {
		selectedItemsList += String(item.get('text'))+',';
	    });
	    
	    selectedItemsTarget.setProperty('value', selectedItemsList);

	    adminForm.submit();
	}
    }
}

function toggleStatus(itemId)
{
    new Request({url: usersUri, noCache: true, onSuccess: function(response){
	refreshUsers(false);
    }}).get({'task': 'toggle_status', 'uid': itemId});
}

function moveUpSortOrder(itemId)
{
    new Request({url: usersUri, noCache: true, onSuccess: function(response){
	refreshUsers(false);
    }}).get({'task': 'move_up_key_sort_order', 'o': itemId});
}

function moveDownSortOrder(itemId)
{
    new Request({url: usersUri, noCache: true, onSuccess: function(response){
	refreshUsers(false);
    }}).get({'task': 'move_down_key_sort_order', 'o': itemId});
}


