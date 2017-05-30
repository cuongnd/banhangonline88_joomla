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
    confirmDeleteMsg = '<?php echo addslashes(JText::_('CONFIRM_DELETE_ROUTES')); ?>';
    
    prepYUI();
</script>
<form action="" method="post" name="adminForm" id="adminForm">
    <div id="routes-grid"></div>
    <div class="clr">&nbsp;</div>
    <br />
    <span class="note smaller"><?php echo JText::_('HOLD_CTRL_SELECT_MULTIPLE_NOTE'); ?></span>
    <br />

    <input type="hidden" id="selected_routes" name="selected_routes" value="" />
    <input type="hidden" id="task" name="task" value="save_routes" />
    <input type="hidden" id="boxchecked" name="boxchecked" value="0" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
<script type="text/javascript">
    YAHOO.util.Event.addListener(window, "load", function() {
	routeGrid = function() {
	    var myColumnDefs = [
		{key:"route_id", label:"<?php echo JText::_('ID'); ?>", sortable:true},
		{key:"route_name", label:"<?php echo JText::_('ROUTE_NAME'); ?>", sortable:true},
		{key:"source_criteria", label:"<?php echo JText::_('SOURCE_CRITERIA'); ?>", sortable:false},
		{key:"route_action", label:"<?php echo JText::_('ROUTE_ACTION'); ?>", sortable:false},
		{key:"route_sort_order", label:"<?php echo JText::_('SORT_ORDER'); ?>", sortable:true},
		{key:"route_enabled", label:"<?php echo JText::_('STATUS'); ?>", sortable:true},
		{key:"options", label:"<?php echo JText::_('OPTIONS'); ?>", sortable:false}
	    ];

	    this.myDataSource = new YAHOO.util.DataSource('index.php?option=com_jlivechat&view=routing&task=get_routes&');
	    this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    this.myDataSource.connXhrMode = "queueRequests";
	    this.myDataSource.responseSchema = {
		resultsList: 'Records',
		fields: ["route_id","route_name","source_criteria","route_action","route_sort_order","route_enabled","options"],
		metaFields: {
		    totalRecords: "totalResultsAvailable" // Access to value in the server response
		}
	    };

	    var myConfigs = {
		initialRequest: 'startIndex=0&results=15',
		paginator: new YAHOO.widget.Paginator({ rowsPerPage:10 }),
		sortedBy: {key:"route_sort_order", dir:YAHOO.widget.DataTable.CLASS_ASC},
		dynamicData: true
	    };

	    this.myDataTable = new YAHOO.widget.DataTable("routes-grid", myColumnDefs, this.myDataSource, myConfigs);
	    
	    this.myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
		oPayload.totalRecords = oResponse.meta.totalRecords;
		return oPayload;
	    }
	    
	    // Subscribe to events for row selection
	    this.myDataTable.subscribe("rowMouseoverEvent", this.myDataTable.onEventHighlightRow);
	    this.myDataTable.subscribe("rowMouseoutEvent", this.myDataTable.onEventUnhighlightRow);
	    this.myDataTable.subscribe("rowClickEvent", this.myDataTable.onEventSelectRow);

	    this.myDataTable.subscribe("rowClickEvent", routingRowClickEvt);

	    // Programmatically select the first row
	    //this.myDataTable.selectRow(this.myDataTable.getTrEl(0));

	    // Programmatically bring focus to the instance so arrow selection works immediately
	    this.myDataTable.focus();

	    return {ds: this.myDataSource, dt: this.myDataTable};
	}();
    });
</script>
