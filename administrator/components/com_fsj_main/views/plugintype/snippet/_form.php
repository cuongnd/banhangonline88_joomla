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
</div>	</div>

	
<div class="form-horizonal">	
	<ul class="nav nav-tabs">		<li  class="active"  id="form_li_main">
			<a href="#form_tab_main" data-toggle="tab"><?php echo JText::_("Description"); ?></a>
		</li>
		<li  id="form_li_settings">
			<a href="#form_tab_settings" data-toggle="tab"><?php echo JText::_("Plugin Settings"); ?></a>
		</li>
</ul>	<div class="tab-content">		<div class="tab-pane active" id="form_tab_main">
										<div class="">
					<div class="control-group" id="field_group_description">
	<div class="control-label">
<?php echo $this->form->getLabel('description'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('description'); ?>
	</div>
</div>				</div>
					</div>
		<div class="tab-pane " id="form_tab_settings">
										<div class="">
					<div class="control-group" id="field_group_params">
	<div class="control-label">
<?php echo $this->form->getLabel('params'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('params'); ?>
	</div>
</div>				</div>
					</div>
</div></div>
