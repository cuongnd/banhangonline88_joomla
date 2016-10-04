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

var routeUri = 'index.php?option=com_jlivechat&view=routing&format=raw';
var routeGrid = null;
var confirmDeleteMsg = 'Are you sure you want to delete the selected operators?';

window.addEvent('domready', function() {

});

Joomla.submitbutton = function(action) {
    if(action == 'add') {
	return createNewRoutePage();
    } else if(action == 'remove') {
	return deleteSelectedRoutes();
    } else if(action == 'cancel') {
	document.location.href='index.php?option=com_jlivechat&view=routing';

	return false;
    } else if(action == 'save' || action == 'apply') {
	selectAllOperators();
	return saveRoute();
    }
}

function saveRoute()
{
    var adminForm = $('adminForm');

    adminForm.submit();

    return true;
}

function createNewRoutePage()
{
    document.location.href='index.php?option=com_jlivechat&view=routing&task=new_route';

    return false;
}

var routingRowClickEvt = function (evt) {
    var selectedRows = $$('#routes-grid .yui-dt-selected');
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

function refreshRouting(resetRecordOffset)
{
    if(routeGrid)
    {
	fireDT(resetRecordOffset);
    }
}

var fireDT = function (resetRecordOffset) {
    var oState = routeGrid.dt.getState(), request, oCallback;

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
	success : routeGrid.dt.onDataReturnSetRows,
	failure : routeGrid.dt.onDataReturnSetRows,
	argument : oState,
	scope : routeGrid.dt
    };

    // Generate a query string
    request = routeGrid.dt.get("generateRequest")(oState, routeGrid.dt);

    // Fire off a request for new data.
    routeGrid.ds.sendRequest(request, oCallback);
}

function deleteSelectedRoutes()
{
    var answer = confirm(confirmDeleteMsg);

    if(answer == true) {
	var adminForm = $('adminForm');
	var task = $('task');

	task.setProperty('value', 'delete_operators');

	var selectedItems = $$('#routes-grid .yui-dt-selected .yui-dt0-col-route_id .yui-dt-liner');
	var selectedItemsList = '';
	var selectedItemsTarget = $('selected_routes');

	if(selectedItems) {
	    selectedItems.each(function (item, index) {
		selectedItemsList += String(item.get('text'))+',';
	    });
	    
	    selectedItemsTarget.setProperty('value', selectedItemsList);

	    adminForm.submit();
	}
    }
}

function toggleRouteStatus(routeId)
{
    new Request({url: routeUri, noCache: true, onSuccess: function(response){
	refreshRouting(false);
    }}).get({'task': 'toggle_route_status', 'i': routeId});
}

function moveUpSortOrder(itemId)
{
    new Request({url: routeUri, noCache: true, onSuccess: function(response){
	refreshRouting(false);
    }}).get({'task': 'move_up_key_sort_order', 'o': itemId});
}

function moveDownSortOrder(itemId)
{
    new Request({url: routeUri, noCache: true, onSuccess: function(response){
	refreshRouting(false);
    }}).get({'task': 'move_down_key_sort_order', 'o': itemId});
}


