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
    confirmDeleteMsg = '<?php echo addslashes(JText::_('CONFIRM_DELETE_HISTORY')); ?>';
    
    prepYUI();
</script>
<form action="" method="post" name="adminForm" id="adminForm">
    <span class="jlc-left">
    <span class="jlc-left"><?php echo JText::_('FROM_LBL'); ?></span><label id="from-month-filter-container" class="jlc-left">
	<select id="from_month_filter" name="from_month_filter">
	    <option value=""><?php echo JText::_('MONTH_SELECT_OPTION'); ?></option>
	    <option value="01">January</option>
	    <option value="02">February</option>
	    <option value="03">March</option>
	    <option value="04">April</option>
	    <option value="05">May</option>
	    <option value="06">June</option>
	    <option value="07">July</option>
	    <option value="08">August</option>
	    <option value="09">September</option>
	    <option value="10">October</option>
	    <option value="11">November</option>
	    <option value="12">December</option>
	</select>
    </label><label id="from-day-filter-container" class="jlc-left">
	<select id="from_day_filter" name="from_day_filter">
	    <option value=""><?php echo JText::_('DAY_SELECT_OPTION'); ?></option>
	    <?php for($a = 1; $a <= 31; $a++) { ?>
	    <option value="<?php echo $a; ?>"><?php echo $a; ?></option>
	    <?php } ?>
	</select>
    </label><label id="from-year-filter-container" class="jlc-left">
	<select id="from_year_filter" name="from_year_filter">
	    <option value=""><?php echo JText::_('YEAR_SELECT_OPTION'); ?></option>
	    <?php for($a = 2010; $a <= (int)date('Y'); $a++) { ?>
	    <option value="<?php echo $a; ?>"><?php echo $a; ?></option>
	    <?php } ?>
	</select>
    </label><span class="jlc-left">&nbsp;&nbsp;<?php echo JText::_('TO_LBL'); ?></span><label id="to-month-filter-container" class="jlc-left">
	<select id="to_month_filter" name="to_month_filter">
	    <option value=""><?php echo JText::_('MONTH_SELECT_OPTION'); ?></option>
	    <option value="01">January</option>
	    <option value="02">February</option>
	    <option value="03">March</option>
	    <option value="04">April</option>
	    <option value="05">May</option>
	    <option value="06">June</option>
	    <option value="07">July</option>
	    <option value="08">August</option>
	    <option value="09">September</option>
	    <option value="10">October</option>
	    <option value="11">November</option>
	    <option value="12">December</option>
	</select>
    </label><label id="to-day-filter-container" class="jlc-left">
	<select id="to_day_filter" name="to_day_filter">
	    <option value=""><?php echo JText::_('DAY_SELECT_OPTION'); ?></option>
	    <?php for($a = 1; $a <= 31; $a++) { ?>
	    <option value="<?php echo $a; ?>"><?php echo $a; ?></option>
	    <?php } ?>
	</select>
    </label><label id="to-year-filter-container" class="jlc-left">
	<select id="to_year_filter" name="to_year_filter">
	    <option value=""><?php echo JText::_('YEAR_SELECT_OPTION'); ?></option>
	    <?php for($a = 2010; $a <= (int)date('Y'); $a++) { ?>
	    <option value="<?php echo $a; ?>"><?php echo $a; ?></option>
	    <?php } ?>
	</select>
    </label>
    <div class="clr" style="margin-bottom: 8px;">&nbsp;</div>
    <label id="operator-filter-container" class="jlc-left">
	<select id="operator_filter" name="operator_filter">
	    <option value=""><?php echo JText::_('FILTER_BY_OPERATOR'); ?></option>
	    <?php
	    if(!empty($this->operators))
	    {
		foreach($this->operators as $operator)
		{
	    ?>
	    <option value="<?php echo $operator['operator_id']; ?>"><?php echo $operator['operator_name']; ?></option>
	    <?php
		}
	    }
	    ?>
	</select>
    </label> <label id="department-filter-container" class="jlc-left">
	<select id="department_filter" name="department_filter">
	    <option value=""><?php echo JText::_('FILTER_BY_DEPARTMENT'); ?></option>
	    <?php
	    if(!empty($this->departments))
	    {
		foreach($this->departments as $department) 
		{
	    ?>
	    <option value="<?php echo $department['department']; ?>"<?php if($department['department'] == 'ring_same_time') { ?> selected="selected"<?php } ?>><?php echo $department['department']; ?></option>
	    <?php
		}
	    }
	    ?>
	</select>
    </label><span class="jlc-left">&nbsp;&nbsp;&nbsp;&nbsp;<button type="button" id="apply_filters_btn" name="apply_filters_btn"><?php echo JText::_('APPLY_FILTERS'); ?></button><button type="button" id="refresh_btn" name="refresh_btn"><?php echo JText::_('REFRESH'); ?></button></span>
    </span>
    <span class="jlc-right" style="text-align: right;">
	<?php echo JText::_('SEARCH_LBL'); ?> <input type="text" id="search_txt" name="search_txt" value="" maxlength="100" />
	<div class="clr" style="margin-bottom: 5px;">&nbsp;</div>
	<button type="button" id="search_txt_btn" name="search_txt_btn"><?php echo JText::_('SEARCH'); ?></button>
    </span>
    <div class="clr">&nbsp;</div>
    <br />
    <div id="history-grid"></div>
    <div class="clr">&nbsp;</div>
    <br />
    <input type="hidden" id="boxchecked" name="boxchecked" value="1" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
<script type="text/javascript">
    var searchTxtButton = new YAHOO.widget.Button("search_txt_btn", { onclick: { fn: refreshData } });
    var applyFiltersButton = new YAHOO.widget.Button("apply_filters_btn", { onclick: { fn: refreshData } });
    var refreshButton = new YAHOO.widget.Button("refresh_btn", { onclick: { fn: refreshData } });

    var departmentFilterMenu = new YAHOO.widget.Button({
	id: "department-filter-menubutton",
	name: "department-filter-menubutton",
	label: "<em class=\"yui-button-label\"><?php echo JText::_('FILTER_BY_DEPARTMENT'); ?></em>",
	type: "menu",
	menu: "department_filter",
	container: "department-filter-container"
    });

    var operatorFilterMenu = new YAHOO.widget.Button({
	id: "operator-filter-menubutton",
	name: "operator-filter-menubutton",
	label: "<em class=\"yui-button-label\"><?php echo JText::_('FILTER_BY_OPERATOR'); ?></em>",
	type: "menu",
	menu: "operator_filter",
	container: "operator-filter-container"
    });

    var fromMonthFilterMenu = new YAHOO.widget.Button({
	id: "from-month-filter-menubutton",
	name: "from-month-filter-menubutton",
	label: "<em class=\"yui-button-label\"><?php echo JText::_('MONTH_SELECT_OPTION'); ?></em>",
	type: "menu",
	menu: "from_month_filter",
	container: "from-month-filter-container"
    });

    var fromDayFilterMenu = new YAHOO.widget.Button({
	id: "from-day-filter-menubutton",
	name: "from-day-filter-menubutton",
	label: "<em class=\"yui-button-label\"><?php echo JText::_('DAY_SELECT_OPTION'); ?></em>",
	type: "menu",
	menu: "from_day_filter",
	container: "from-day-filter-container"
    });

    var fromYearFilterMenu = new YAHOO.widget.Button({
	id: "from-year-filter-menubutton",
	name: "from-year-filter-menubutton",
	label: "<em class=\"yui-button-label\"><?php echo JText::_('YEAR_SELECT_OPTION'); ?></em>",
	type: "menu",
	menu: "from_year_filter",
	container: "from-year-filter-container"
    });


    var toMonthFilterMenu = new YAHOO.widget.Button({
	id: "to-month-filter-menubutton",
	name: "to-month-filter-menubutton",
	label: "<em class=\"yui-button-label\"><?php echo JText::_('MONTH_SELECT_OPTION'); ?></em>",
	type: "menu",
	menu: "to_month_filter",
	container: "to-month-filter-container"
    });

    var toDayFilterMenu = new YAHOO.widget.Button({
	id: "to-day-filter-menubutton",
	name: "to-day-filter-menubutton",
	label: "<em class=\"yui-button-label\"><?php echo JText::_('DAY_SELECT_OPTION'); ?></em>",
	type: "menu",
	menu: "to_day_filter",
	container: "to-day-filter-container"
    });

    var toYearFilterMenu = new YAHOO.widget.Button({
	id: "to-year-filter-menubutton",
	name: "to-year-filter-menubutton",
	label: "<em class=\"yui-button-label\"><?php echo JText::_('YEAR_SELECT_OPTION'); ?></em>",
	type: "menu",
	menu: "to_year_filter",
	container: "to-year-filter-container"
    });

    fromMonthFilterMenu.on("selectedMenuItemChange", onFromMonthFilterMenuItemChange);
    fromDayFilterMenu.on("selectedMenuItemChange", onFromDayFilterMenuItemChange);
    fromYearFilterMenu.on("selectedMenuItemChange", onFromYearFilterMenuItemChange);
    toMonthFilterMenu.on("selectedMenuItemChange", onToMonthFilterMenuItemChange);
    toDayFilterMenu.on("selectedMenuItemChange", onToDayFilterMenuItemChange);
    toYearFilterMenu.on("selectedMenuItemChange", onToYearFilterMenuItemChange);
    departmentFilterMenu.on("selectedMenuItemChange", onDepartmentFilterMenuItemChange);
    operatorFilterMenu.on("selectedMenuItemChange", onOperatorFilterMenuItemChange);
    
    YAHOO.util.Event.addListener(window, "load", function() {
	historyGrid = function() {
	    var myColumnDefs = [
		{key:"chat_alt_id", label:"<?php echo JText::_('CHAT_SESSION_ID'); ?>", sortable:true},
		{key:"client_name", label:"<?php echo JText::_('CLIENT_NAME'); ?>", sortable:false},
		{key:"operators", label:"<?php echo JText::_('OPERATORS_OPTIONAL'); ?>", sortable:false},
		{key:"cdate", label:"<?php echo JText::_('DATE_TIME'); ?>", sortable:true},
		{key:"options", label:"<?php echo JText::_('OPTIONS'); ?>", sortable:false}
	    ];

	    this.myDataSource = new YAHOO.util.DataSource('index.php?option=com_jlivechat&view=history&task=get_history&');
	    this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    this.myDataSource.connXhrMode = "queueRequests";
	    this.myDataSource.responseSchema = {
		resultsList: 'Records',
		fields: ["chat_alt_id","client_name","operators","cdate","options"],
		metaFields: {
		    totalRecords: "totalResultsAvailable" // Access to value in the server response
		}
	    };

	    var myConfigs = {
		initialRequest: 'startIndex=0&results=50',
		paginator: new YAHOO.widget.Paginator({ rowsPerPage:50 }),
		sortedBy: {key:"cdate", dir:YAHOO.widget.DataTable.CLASS_DESC},
		dynamicData: true, // Enables dynamic server-driven data
		generateRequest: requestBuilder
	    };

	    this.myDataTable = new YAHOO.widget.DataTable("history-grid", myColumnDefs, this.myDataSource, myConfigs);
	    
	    this.myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
		oPayload.totalRecords = oResponse.meta.totalRecords;
		return oPayload;
	    }
	    
	    // Subscribe to events for row selection
	    this.myDataTable.subscribe("rowMouseoverEvent", this.myDataTable.onEventHighlightRow);
	    this.myDataTable.subscribe("rowMouseoutEvent", this.myDataTable.onEventUnhighlightRow);
	    this.myDataTable.subscribe("rowClickEvent", this.myDataTable.onEventSelectRow);

	    // Programmatically select the first row
	    //this.myDataTable.selectRow(this.myDataTable.getTrEl(0));

	    // Programmatically bring focus to the instance so arrow selection works immediately
	    this.myDataTable.focus();

	    return {ds: this.myDataSource, dt: this.myDataTable};
	}();
    });
</script>
