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
    confirmDeleteMsg = '<?php echo addslashes(JText::_('CONFIRM_DELETE_OPEATORS')); ?>';
    
    prepYUI();
</script>
<form action="" method="post" name="adminForm" id="adminForm">
    
    <label id="department-filter-container">
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
    </label>
    <div class="clr">&nbsp;</div>
    <br />
    <div id="operators-grid"></div>
    <div class="clr">&nbsp;</div>
    <br />
    <span class="note smaller"><?php echo JText::_('HOLD_CTRL_SELECT_MULTIPLE_NOTE'); ?></span>
    <br />

    <input type="hidden" id="selected_operators" name="selected_operators" value="" />
    <input type="hidden" id="task" name="task" value="save_operators" />
    <input type="hidden" id="boxchecked" name="boxchecked" value="0" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
<script type="text/javascript">
    var departmentFilterMenu = new YAHOO.widget.Button({
	id: "department-filter-menubutton",
	name: "department-filter-menubutton",
	label: "<em class=\"yui-button-label\"><?php echo JText::_('FILTER_BY_DEPARTMENT'); ?></em>",
	type: "menu",
	menu: "department_filter",
	container: "department-filter-container"
    });

    departmentFilterMenu.on("selectedMenuItemChange", onFilterDepartmentMenuItemChange);
    
    YAHOO.util.Event.addListener(window, "load", function() {
	operatorsGrid = function() {
	    var myColumnDefs = [
		{key:"operator_id", label:"<?php echo JText::_('OPERATOR_ID'); ?>", sortable:true},
		{key:"alt_name", label:"<?php echo JText::_('OPERATOR_ALT_NAME'); ?>", sortable:true},
		{key:"operator_name", label:"<?php echo JText::_('OPERATOR_NAME'); ?>", sortable:true},
		{key:"department", label:"<?php echo JText::_('DEPARTMENT'); ?>", sortable:true},
		{key:"sort_order", label:"<?php echo JText::_('CALL_ORDER'); ?>", sortable:true},
		{key:"last_auth_date", label:"<?php echo JText::_('LAST_ACCESS'); ?>", sortable:true},
		{key:"mobile_key_code", label:"<?php echo JText::_('MOBILE_KEY_CODE'); ?>", sortable:false},
		{key:"is_enabled", label:"<?php echo JText::_('STATUS'); ?>", sortable:true},
		{key:"options", label:"<?php echo JText::_('OPTIONS'); ?>", sortable:false}
	    ];

	    this.myDataSource = new YAHOO.util.DataSource('index.php?option=com_jlivechat&view=operators&task=get_operators&');
	    this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    this.myDataSource.connXhrMode = "queueRequests";
	    this.myDataSource.responseSchema = {
		resultsList: 'Records',
		fields: ["operator_id","alt_name","operator_name","department","sort_order","last_auth_date","mobile_key_code","is_enabled","options"],
		metaFields: {
		    totalRecords: "totalResultsAvailable" // Access to value in the server response
		}
	    };

	    var myConfigs = {
		initialRequest: 'startIndex=0&results=10',
		paginator: new YAHOO.widget.Paginator({ rowsPerPage:10 }),
		sortedBy: {key:"sort_order", dir:YAHOO.widget.DataTable.CLASS_ASC},
		dynamicData: true, // Enables dynamic server-driven data
		generateRequest: requestBuilder
	    };

	    this.myDataTable = new YAHOO.widget.DataTable("operators-grid", myColumnDefs, this.myDataSource, myConfigs);
	    
	    this.myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
		oPayload.totalRecords = oResponse.meta.totalRecords;
		return oPayload;
	    }
	    
	    // Subscribe to events for row selection
	    this.myDataTable.subscribe("rowMouseoverEvent", this.myDataTable.onEventHighlightRow);
	    this.myDataTable.subscribe("rowMouseoutEvent", this.myDataTable.onEventUnhighlightRow);
	    this.myDataTable.subscribe("rowClickEvent", this.myDataTable.onEventSelectRow);
	    this.myDataTable.subscribe("rowClickEvent", operatorRowClickEvt);

	    // Programmatically select the first row
	    //this.myDataTable.selectRow(this.myDataTable.getTrEl(0));

	    // Programmatically bring focus to the instance so arrow selection works immediately
	    this.myDataTable.focus();
	    
	    return {ds: this.myDataSource, dt: this.myDataTable};
	}();
    });
</script>
