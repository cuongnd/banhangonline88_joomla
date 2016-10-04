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

var confirmDeleteMsg = 'Are you sure you want to permanently delete ALL chat history records?';
var historyGrid = null;

var filterFromMonth = null;
var filterFromDay = null;
var filterFromYear = null;
var filterToMonth = null;
var filterToDay = null;
var filterToYear = null;
var filterDepartment = null;
var filterOperator = null;

window.addEvent('domready', function() {
    
});

var onFromMonthFilterMenuItemChange = function (event) {
    var oMenuItem = event.newValue;

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));

    filterFromMonth = oMenuItem.value;
};

var onFromDayFilterMenuItemChange = function (event) {
    var oMenuItem = event.newValue;

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));

    filterFromDay = oMenuItem.value;
};

var onFromYearFilterMenuItemChange = function (event) {
    var oMenuItem = event.newValue;

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));

    filterFromYear = oMenuItem.value;
};

var onToMonthFilterMenuItemChange = function (event) {
    var oMenuItem = event.newValue;

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));

    filterToMonth = oMenuItem.value;
};

var onToDayFilterMenuItemChange = function (event) {
    var oMenuItem = event.newValue;

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));

    filterToDay = oMenuItem.value;
};

var onToYearFilterMenuItemChange = function (event) {
    var oMenuItem = event.newValue;

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));

    filterToYear = oMenuItem.value;
};

var onDepartmentFilterMenuItemChange = function (event) {
    var oMenuItem = event.newValue;

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));

    filterDepartment = oMenuItem.value;
};

var onOperatorFilterMenuItemChange = function (event) {
    var oMenuItem = event.newValue;

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));

    filterOperator = oMenuItem.value;
};

Joomla.submitbutton = function(action) {
    if(action == 'remove') {
	deleteHistoryItems();
    }

    return false;
}

function deleteHistoryItems() {
    var answer = confirm(confirmDeleteMsg);
    
    if(answer == true) {
	document.location.href='index.php?option=com_jlivechat&view=history&task=delete_history';
    }

    return false;
}

function viewSessionContents(sessionId)
{
    var uri = document.location.href+'&task=display_chat_session&tmpl=component&session_id='+sessionId;
    
    var newwindow = window.open(uri,'chat_session_contents','toolbar=0,status=0,location=0,menubar=0,resizable=1,scrollbars=1,height=390,width=400');

    if(window.focus) {
	newwindow.focus();
    }

    return false;
}


function refreshData(resetRecordOffset)
{
    if(historyGrid)
    {
	fireDT(resetRecordOffset);
    }
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

    if(filterDepartment) {
	if(filterDepartment.length > 0) {
	    returnURI += '&filter_by_department='+URLEncode(filterDepartment);
	}
    }

    if(filterOperator) {
	if(parseInt(filterOperator) > 0) {
	    returnURI += '&filter_by_operator='+parseInt(filterOperator);
	}
    }

    if(filterFromMonth && filterFromDay && filterFromYear) {
	if(filterFromMonth.length > 0 && parseInt(filterFromDay) > 0 && parseInt(filterFromYear) > 0) {
	    returnURI += '&filter_fromdate='+parseInt(filterFromYear)+'-'+filterFromMonth+'-'+parseInt(filterFromDay);
	}
    }

    if(filterToMonth && filterToDay && filterToYear) {
	if(filterToMonth.length > 0 && parseInt(filterToDay) > 0 && parseInt(filterToYear) > 0) {
	    returnURI += '&filter_todate='+parseInt(filterToYear)+'-'+filterToMonth+'-'+parseInt(filterToDay);
	}
    }

    var searchTxt = $('search_txt');

    if(searchTxt) {
	var searchTxtValue = String(searchTxt.get('value'));

	if(searchTxtValue.length > 0) {
	    returnURI += '&search_txt='+URLEncode(searchTxtValue);
	}
    }

    return returnURI;
};

var fireDT = function (resetRecordOffset) {
    var oState = historyGrid.dt.getState(), request, oCallback;

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
	success : historyGrid.dt.onDataReturnSetRows,
	failure : historyGrid.dt.onDataReturnSetRows,
	argument : oState,
	scope : historyGrid.dt
    };

    // Generate a query string
    request = historyGrid.dt.get("generateRequest")(oState, historyGrid.dt);

    // Fire off a request for new data.
    historyGrid.ds.sendRequest(request, oCallback);
}