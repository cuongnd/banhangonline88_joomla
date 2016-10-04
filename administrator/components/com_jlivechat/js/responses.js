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

var responsesUri = 'index.php?option=com_jlivechat&view=responses&format=raw';
var responsesGrid = null;
var confirmDeleteMsg = 'Are you sure you want to delete the selected global responses?';
var filterByCategory = null;

window.addEvent('domready', function() {
    
});

Joomla.submitbutton = function(action) {
    if(action == 'add') {
	createNewResponse();
    } else if(action == 'cancel') {
	document.location.href='index.php?option=com_jlivechat&view=responses';
    } else if(action == 'remove') {
	deleteOperators();
    }
    else if(action == 'save' || action == 'apply') {
	submitForm();
    }

    return false;
}

function submitForm(action)
{
    var adminForm = $('adminForm');

    if(action)
    {
	var task = $('task');

	task.setProperty('value', action);
    }

    adminForm.submit();
}

function createNewResponse()
{
    document.location.href='index.php?option=com_jlivechat&view=responses&task=new_response';
}

function editResponse(responseId)
{
    document.location.href='index.php?option=com_jlivechat&view=responses&task=display_edit_form&r='+responseId;

    return false;
}

var requestBuilder = function (oState, oSelf) {
    var startIndex = (oState.pagination) ? oState.pagination.recordOffset : 0;
    var results = (oState.pagination) ? oState.pagination.rowsPerPage : null;
    var sort = (oState.sortedBy) ? oState.sortedBy.key : oSelf.getColumnSet().keys[0].getKey();
    var dir = (oState.sortedBy && oState.sortedBy.dir === YAHOO.widget.DataTable.CLASS_DESC) ? "desc" : "asc";

    var returnURI = '&results='+results;

    returnURI += '&startIndex='+startIndex;
    returnURI += '&sort='+sort;
    returnURI += '&dir='+dir;

    if(filterByCategory) {
	if(filterByCategory.length > 0) {
	    returnURI += '&query=response_category&qtype='+filterByCategory;
	}
    }

    return returnURI;
};

var rowClickEvt = function (evt) {
    var selectedRows = $$('#responses-grid .yui-dt-selected');
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

function refreshGrid(resetRecordOffset)
{
    if(responsesGrid)
    {
	fireDT(resetRecordOffset);
    }
}

var fireDT = function (resetRecordOffset) {
    var oState = responsesGrid.dt.getState(), request, oCallback;

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
	success : responsesGrid.dt.onDataReturnSetRows,
	failure : responsesGrid.dt.onDataReturnSetRows,
	argument : oState,
	scope : responsesGrid.dt
    };

    // Generate a query string
    request = responsesGrid.dt.get("generateRequest")(oState, responsesGrid.dt);

    // Fire off a request for new data.
    responsesGrid.ds.sendRequest(request, oCallback);
}

function deleteOperators()
{
    var answer = confirm(confirmDeleteMsg);

    if(answer == true) {
	var adminForm = $('adminForm');
	var task = $('task');

	task.setProperty('value', 'delete_responses');

	var selected = $$('#responses-grid .yui-dt-selected .yui-dt0-col-response_id .yui-dt-liner');
	var selectedList = '';
	var selectedTarget = $('selected_records');

	if(selected) {
	    selected.each(function (item, index) {
		selectedList += String(item.get('text'))+',';
	    });

	    selectedTarget.setProperty('value', selectedList);

	    adminForm.submit();
	}
    }
}

function toggleStatus(responseId)
{
    new Request({url: responsesUri, noCache: true, onSuccess: function(response){
	refreshGrid(false);
    }}).get({'task': 'toggle_status', 'response_id': responseId});
}

function moveUpSortOrder(itemId)
{
    new Request({url: responsesUri, noCache: true, onSuccess: function(response){
	refreshGrid(false);
    }}).get({'task': 'move_up_key_sort_order', 'o': itemId});
}

function moveDownSortOrder(itemId)
{
    new Request({url: responsesUri, noCache: true, onSuccess: function(response){
	refreshGrid(false);
    }}).get({'task': 'move_down_key_sort_order', 'o': itemId});
}

var onFilterCategoryMenuItemChange = function (event) {
    var oMenuItem = event.newValue;

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));

    filterByCategory = oMenuItem.value;

    refreshGrid(false);
};
