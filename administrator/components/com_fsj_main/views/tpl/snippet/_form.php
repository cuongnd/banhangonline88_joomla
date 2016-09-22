<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
?>
	
	<div class="form-inline form-inline-header">
		<div class="control-group" id="field_group_title">
	<div class="control-label">
<?php echo $this->form->getLabel('title'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('title'); ?>
	</div>
</div><div class="control-group" id="field_group_name">
	<div class="control-label">
<?php echo $this->form->getLabel('name'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('name'); ?>
	</div>
</div>	</div>

	
<div class="form-horizonal">	
	<ul class="nav nav-tabs">		<li  class="active"  id="form_li_overview">
			<a href="#form_tab_overview" data-toggle="tab"><?php echo JText::_("FSJ_TEMPLATE_OVERVIEW"); ?></a>
		</li>
<?php $field_list["params"] = $this->form->getField("params"); ?>
<?php $field_list["params"]->getTabLabels(); ?>
</ul>	<div class="tab-content">		<div class="tab-pane active" id="form_tab_overview">
										<div class="form-horizontal">
					<div class="control-group" id="field_group_description">
	<div class="control-label">
<?php echo $this->form->getLabel('description'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('description'); ?>
	</div>
</div><div class="control-group" id="field_group_type">
	<div class="control-label">
<?php echo $this->form->getLabel('type'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('type'); ?>
	</div>
</div><div class="control-group" id="field_group_component">
	<div class="control-label">
<?php echo $this->form->getLabel('component'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('component'); ?>
	</div>
</div>				</div>
					</div>
<?php $field_list["params"]->getTabContent(); ?>
</div></div>
