<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */
// Check to ensure this file is within the rest of the framework
defined('JPATH_BASE') or die();
?>
<p><?php echo JText::_('COM_ADSMANAGER_CONTENT_FORM'); ?></p>
<style>
    .fieldsarea {
	  border: 1px solid #CBCBCB;
	  font-size: 12px !important;
	  margin-top: 10px;
	  padding: 15px 5px 5px;
	}
	.fieldsarea .title {
	    background-color: #EDEDED;
	    border: 1px solid #CBCBCB;
	    font-weight: bold;
	    left: 10px;
	    position: relative;
	    top: -25px;
	}
	.connectedSortable {
	  background-color: #EEDDFF;
	  list-style-type: none;
	  margin: 10px 0 0;
	  max-width: 400px;
	  min-height: 50px;
	  padding: 5px;
	}
	.connectedSortable li { 
		margin: 0 5px 5px 5px; padding: 5px; font-size: 10px; 
	}
	.column { float:left;width:30%;}
	.ads_background { float:left;width:70%;}
	.column h2 { text-align:center;}
	.ui-state-highlight { height: 1.5em; line-height: 1.2em; }
</style>
	
<form action="index.php" method="post" name="adminForm" id="adminForm">
<div>
	<div class="column">
		<h2>All Fields</h2>
		<ul id="fields" class="connectedSortable">
		<?php foreach($this->fields as $field) {?>
		<li class='ui-state-default' id="<?php echo $field->fieldid?>"><?php echo JText::_($field->title)." (".$field->name." ".$field->fieldid.")" ?></li>
		<?php } ?>
		</ul>
	</div>
	<div class="ads_background">
		<div class="addetails" align="left">
			<?php foreach($this->positions as $position) {?>
			<div class="fieldsarea">
				<span class="title"><?php echo htmlspecialchars($position->name) ?></span>
				<input type="text" size="50" id="title_position_<?php echo $position->id?>" name="title_position_<?php echo $position->id?>" value="<?php echo htmlspecialchars($position->title) ?>" />
				<ul id="fields_<?php echo $position->id?>" class="connectedSortable"></ul>
				<input type="hidden" id="listfields_<?php echo $position->id?>" name="listfields_<?php echo $position->id?>" />
			</div>
			<?php } ?>
		</div>
	</div>
</div>
<input type="hidden" name="option" value="com_adsmanager" />
<input type="hidden" name="task" value="" />
<input type="hidden" name="c" value="contentform" />
</form> 

<script>
	jQ(function() {
		jQ( "#fields" ).sortable({
			connectWith: ".connectedSortable",
			placeholder: "ui-state-highlight",
		}).disableSelection();
		jQ( ".fieldsarea .connectedSortable" ).sortable({
			connectWith: ".connectedSortable",
			placeholder: "ui-state-highlight",
			update: function() {
				list = jQ(this).sortable('toArray');
				jQ(this).next().val(list.join(','));
			}
		}).disableSelection();

		<?php foreach($this->fDisplay as $positionid => $fields) {
			foreach($fields as $f) {?>
				jQ('#<?php echo $f->fieldid?>').appendTo('#fields_<?php echo $positionid?>');
			<?php } ?>
			list = jQ('#fields_<?php echo $positionid?>').sortable('toArray');
			jQ('#listfields_<?php echo $positionid?>').val(list.join(','));
		<?php 
		} 
		?>
	});
</script>