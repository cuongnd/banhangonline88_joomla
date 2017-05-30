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

var previousFilterType = null;
var filterType = 'none';
var filterTypeName = null;
var byCityTxt = null;
var selectAllTrafficLbl = null;
var byCountryTxt = null;
var byIPTxt = null;
var byTimeTxt = null;
var byDayTxt = null;
var byGroupTxt = '';
var byStatusTxt = '';
var countryFilter = '';

window.addEvent('domready', function() {
    changeAction();
});

function addFilter()
{
    var filtersTable = $('filters-table');
    var tableBody = filtersTable.getElement('tbody');
    var selectAll = tableBody.getElement('tr.place-holder');
    var doAdd = false;
    var filterValueTxt = null;
    
    if(filterType == 'by_city')
    {
	var filterValue = $$('div.by_city input[type=text]');
	filterValueTxt = String(filterValue.get('value'));

	if(filterValueTxt.length > 0)
	{
	    doAdd = true;

	    var labelTxt = byCityTxt;
	}
    }
    else if(filterType == 'by_country')
    {
	filterValueTxt = countryFilter;
	
	if(filterValueTxt.length > 0)
	{
	    doAdd = true;

	    var labelTxt = byCountryTxt;
	}
    }
    else if(filterType == 'by_ip_address')
    {
	var filterValue = $$('div.by_ip_address input[type=text]');
	filterValueTxt = String(filterValue.get('value'));

	if(filterValueTxt.length > 0)
	{
	    doAdd = true;

	    var labelTxt = byIPTxt;
	}
    }
    else if(filterType == 'by_time')
    {
	var fromHour = $$('div.by_time select[name=from_hour]');
	var fromMinute = $$('div.by_time select[name=from_minute]');
	var toHour = $$('div.by_time select[name=to_hour]');
	var toMinute = $$('div.by_time select[name=to_minute]');
	
	filterValueTxt = String(fromHour.get('value')+':'+fromMinute.get('value')+'-'+toHour.get('value')+':'+toMinute.get('value'));
	
	if(fromHour.get('value') > 0 || fromMinute.get('value') > 0 || toHour.get('value') > 0 || toMinute.get('value') > 0)
	{
	    doAdd = true;

	    var labelTxt = byTimeTxt;
	}
    }
    else if(filterType == 'by_day')
    {
	var days = $$('div.by_day input[type=checkbox]');
	filterValueTxt = '';
	var howMany = 0;
	
	days.each(function (item, index) {
	    if(item.get('value'))
	    {
		if(howMany > 0)
		{
		    filterValueTxt += ',';
		}
		
		filterValueTxt += String(item.get('value'));
		
		doAdd = true;
		howMany += 1;
	    }
	});

	var labelTxt = byDayTxt;
    }
    else if(filterType == 'by_group')
    {
	var groupSelector = $$('div.by_group select');
	filterValueTxt = String(groupSelector.get('value'));
	var labelTxt = byGroupTxt;

	if(filterValueTxt.length > 0)
	{
	    doAdd = true;
	}
    }
    else if(filterType == 'by_user_status')
    {
	var groupSelector = $$('div.by_user_status select');
	filterValueTxt = String(groupSelector.get('value'));
	var labelTxt = byStatusTxt;

	doAdd = true;
    }

    if(doAdd == true)
    {
	if(selectAll)
	{
	    selectAll.destroy();
	}
	
	var tableRow = new Element('tr');
	var tableCell1 = new Element('td');
	var tableCell2 = new Element('td');
	var tableCell3 = new Element('td');

	tableCell1.set('text', labelTxt);
	tableCell2.set('text', filterValueTxt);

	var optionsHTML = '<a href="javascript: void(0);" onClick="deleteThisFilter(this);">Delete</a>';
	optionsHTML += '<input type="hidden" name="source[]" value="'+filterType+':'+filterValueTxt+'" />';

	tableCell3.set('html', optionsHTML);

	tableCell1.inject(tableRow);
	tableCell2.inject(tableRow);
	tableCell3.inject(tableRow);

	tableRow.inject(tableBody);
    }
    
    return false;
}

function deleteThisFilter(deleteLink)
{
    var parent = $(deleteLink).getParent();
    var grandParent = parent.getParent();

    grandParent.destroy();

    var filtersTable = $('filters-table');
    var tableBody = filtersTable.getElement('tbody');
    var tableRows = tableBody.getElements('tr');

    if(parseInt(tableRows.length) < 1)
    {
	var tableRow = new Element('tr', {'class': 'place-holder'});
	var tableCell1 = new Element('td');
	var tableCell2 = new Element('td', {'colspan': 2});
	
	var optionsHTML = '*:*<input type="hidden" name="source[]" value="*:*" />';

	tableCell1.set('html', selectAllTrafficLbl);
	tableCell2.set('html', optionsHTML);

	tableCell1.inject(tableRow);
	tableCell2.inject(tableRow);

	tableRow.inject(tableBody);
    }
}

function changeAction()
{
    var routeAction = $('route_action');
    var fromBox = $('available_operators');
    var toBox = $('selected_operators');

    if(routeAction && fromBox && toBox)
    {
	if(String(routeAction.get('value')) == 'send_to_selected_operators')
	{
	    fromBox.setProperty('disabled', false);
	    toBox.setProperty('disabled', false);
	}
	else
	{
	    fromBox.setProperty('disabled', true);
	    toBox.setProperty('disabled', true);
	}
    }
}

function clearSelectedOperators()
{
    var availableOperators = $('available_operators');
    var selectedOperators = $('selected_operators');

    var allOptions = selectedOperators.getElements('option');

    if(allOptions)
    {
	allOptions.each(function (item, index) {
	    item.inject(availableOperators);
	});
    }
}

function addSelectedOperators()
{
    var availableOperators = $('available_operators');
    var selectedOperators = $('selected_operators');

    var availableOptions = availableOperators.getElements('option');

    if(availableOptions)
    {
	availableOptions.each(function (item, index) {
	    if(item.getProperty('selected') == true)
	    {
		item.inject(selectedOperators);
	    }
	});
    }
}

function removeSelectedOperators()
{
    var availableOperators = $('selected_operators');
    var selectedOperators = $('available_operators');

    var availableOptions = availableOperators.getElements('option');

    if(availableOptions)
    {
	availableOptions.each(function (item, index) {
	    if(item.getProperty('selected') == true)
	    {
		item.inject(selectedOperators);
	    }
	});
    }
}

function changeFilterType()
{
    var oldLayer = $$('div.'+previousFilterType);

    if(oldLayer)
    {
	oldLayer.addClass('hide');
    }

    if(String(filterType) == '')
    {
	filterType = 'none';
	filterTypeName = '';
    }
    else
    {
	var layer = $$('div.'+filterType);

	layer.removeClass('hide');
    }
}

function selectAllOperators()
{
    var selectedOperators = $('selected_operators');

    if(selectedOperators) {
	var options = selectedOperators.getElements('option');

	if(options) {
	    options.setProperty('selected', true);
	}
    }
}

function submitForm()
{
    var adminForm = $('adminForm');

    adminForm.submit();
}

function changeCountryFilter(newCountryFilter)
{
    countryFilter = newCountryFilter;
}

var onFilterByMenuItemChange = function (event) {
    var oMenuItem = event.newValue;

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));

    previousFilterType = filterType;

    filterType = oMenuItem.value;
    filterTypeName = String(oMenuItem.cfg.getProperty("text"));
    
    changeFilterType();
};

var onCountryFilterMenuItemChange = function (event) {
    var oMenuItem = event.newValue;

    this.set("label", ("<em class=\"yui-button-label\">"+oMenuItem.cfg.getProperty("text")+"</em>"));

    countryFilter = oMenuItem.value;
};


