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
    confirmDeleteMsg = '<?php echo addslashes(JText::_('CONFIRM_DELETE_RESPONSES')); ?>';
    
    prepYUI();
</script>
<form action="" method="post" name="adminForm" id="adminForm">
    
    <label id="category-filter-container">
	<select id="category_filter" name="category_filter">
	    <option value=""><?php echo JText::_('FILTER_BY_CATEGORY'); ?></option>
	    <?php
	    if(!empty($this->response_categories))
	    {
		foreach($this->response_categories as $data)
		{
	    ?>
	    <option value="<?php echo $data['response_category']; ?>"<?php if($data['response_category'] == 'ring_same_time') { ?> selected="selected"<?php } ?>><?php echo $data['response_category']; ?></option>
	    <?php 
		}
	    }
	    ?>
	</select>
    </label>
    <div class="clr">&nbsp;</div>
    <br />
    <div id="responses-grid"></div>
    <div class="clr">&nbsp;</div>
    <br />
    <span class="note smaller"><?php echo JText::_('HOLD_CTRL_SELECT_MULTIPLE_NOTE'); ?></span>
    <br />

    <input type="hidden" id="selected_records" name="selected_records" value="" />
    <input type="hidden" id="task" name="task" value="save_responses" />
    <input type="hidden" id="boxchecked" name="boxchecked" value="0" />
    <?php echo JHTML::_( 'form.token' ); ?>
</form>
<script type="text/javascript">
    var filterMenu = new YAHOO.widget.Button({
	id: "category-filter-menubutton",
	name: "category-filter-menubutton",
	label: "<em class=\"yui-button-label\"><?php echo JText::_('FILTER_BY_CATEGORY'); ?></em>",
	type: "menu",
	menu: "category_filter",
	container: "category-filter-container"
    });

    filterMenu.on("selectedMenuItemChange", onFilterCategoryMenuItemChange);
    
    YAHOO.util.Event.addListener(window, "load", function() {
	responsesGrid = function() {
	    var myColumnDefs = [
		{key:"response_id", label:"<?php echo JText::_('ID'); ?>", sortable:true},
		{key:"response_name", label:"<?php echo JText::_('RESPONSE_NAME'); ?>", sortable:true},
		{key:"response_category", label:"<?php echo JText::_('RESPONSE_CATEGORY'); ?>", sortable:true},
		{key:"response_txt", label:"<?php echo JText::_('RESPONSE_PREVIEW'); ?>", sortable:true},
		{key:"response_sort_order", label:"<?php echo JText::_('SORT_ORDER'); ?>", sortable:true},
		{key:"response_enabled", label:"<?php echo JText::_('STATUS'); ?>", sortable:true},
		{key:"options", label:"<?php echo JText::_('OPTIONS'); ?>", sortable:false}
	    ];

	    this.myDataSource = new YAHOO.util.DataSource('index.php?option=com_jlivechat&view=responses&task=get_responses&');
	    this.myDataSource.responseType = YAHOO.util.DataSource.TYPE_JSON;
	    this.myDataSource.connXhrMode = "queueRequests";
	    this.myDataSource.responseSchema = {
		resultsList: 'Records',
		fields: ["response_id","response_name","response_category","response_txt","response_sort_order","response_enabled","options"],
		metaFields: {
		    totalRecords: "totalResultsAvailable" // Access to value in the server response
		}
	    };

	    var myConfigs = {
		initialRequest: 'startIndex=0&results=15',
		paginator: new YAHOO.widget.Paginator({ rowsPerPage:15 }),
		sortedBy: {key:"response_sort_order", dir:YAHOO.widget.DataTable.CLASS_ASC},
		dynamicData: true, // Enables dynamic server-driven data
		generateRequest: requestBuilder
	    };

	    this.myDataTable = new YAHOO.widget.DataTable("responses-grid", myColumnDefs, this.myDataSource, myConfigs);
	    
	    this.myDataTable.handleDataReturnPayload = function(oRequest, oResponse, oPayload) {
		oPayload.totalRecords = oResponse.meta.totalRecords;
		return oPayload;
	    }
	    
	    // Subscribe to events for row selection
	    this.myDataTable.subscribe("rowMouseoverEvent", this.myDataTable.onEventHighlightRow);
	    this.myDataTable.subscribe("rowMouseoutEvent", this.myDataTable.onEventUnhighlightRow);
	    this.myDataTable.subscribe("rowClickEvent", this.myDataTable.onEventSelectRow);

	    this.myDataTable.subscribe("rowClickEvent", rowClickEvt);

	    // Programmatically select the first row
	    //this.myDataTable.selectRow(this.myDataTable.getTrEl(0));

	    // Programmatically bring focus to the instance so arrow selection works immediately
	    this.myDataTable.focus();

	    return {ds: this.myDataSource, dt: this.myDataTable};
	}();
    });
</script>
