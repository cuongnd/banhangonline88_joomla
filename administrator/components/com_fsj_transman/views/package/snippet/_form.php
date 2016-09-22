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
</div><div class="control-group" id="field_group_langcode">
	<div class="control-label">
<?php echo $this->form->getLabel('langcode'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('langcode'); ?>
	</div>
</div>	</div>

	
<div class="form-horizonal">	
	<ul class="nav nav-tabs">		<li  class="active"  id="form_li_files">
			<a href="#form_tab_files" data-toggle="tab"><?php echo JText::_("FSJ_TM_FILES"); ?></a>
		</li>
		<li  id="form_li_main">
			<a href="#form_tab_main" data-toggle="tab"><?php echo JText::_("FSJ_TM_OVERVIEW"); ?></a>
		</li>
		<li  id="form_li_description">
			<a href="#form_tab_description" data-toggle="tab"><?php echo JText::_("FSJ_TM_DESCRIPTION"); ?></a>
		</li>
		<li  id="form_li_pubtab">
			<a href="#form_tab_pubtab" data-toggle="tab"><?php echo JText::_("FSJ_TM_PUBLISHING"); ?></a>
		</li>
</ul>	<div class="tab-content">		<div class="tab-pane active" id="form_tab_files">
										<div class="">
					<div class="control-group" id="field_group_files">
	<div class="controls">
<?php echo $this->form->getInput('files'); ?>
	</div>
</div>				</div>
					</div>
		<div class="tab-pane " id="form_tab_main">
										<div class="form-horizontal">
					<div class='row-fluid'>
<div class='span6 form-horizontal'><div class="control-group" id="field_group_email">
	<div class="control-label">
<?php echo $this->form->getLabel('email'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('email'); ?>
	</div>
</div><div class="control-group" id="field_group_license">
	<div class="control-label">
<?php echo $this->form->getLabel('license'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('license'); ?>
	</div>
</div><div class="control-group" id="field_group_ver">
	<div class="control-label">
<?php echo $this->form->getLabel('ver'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('ver'); ?>
	</div>
</div><div class="control-group" id="field_group_creationDate">
	<div class="control-label">
<?php echo $this->form->getLabel('creationDate'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('creationDate'); ?>
	</div>
</div></div><div class='span6 form-horizontal'><div class="control-group" id="field_group_author">
	<div class="control-label">
<?php echo $this->form->getLabel('author'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('author'); ?>
	</div>
</div><div class="control-group" id="field_group_url">
	<div class="control-label">
<?php echo $this->form->getLabel('url'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('url'); ?>
	</div>
</div><div class="control-group" id="field_group_copyright">
	<div class="control-label">
<?php echo $this->form->getLabel('copyright'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('copyright'); ?>
	</div>
</div></div></div>
				</div>
					</div>
		<div class="tab-pane " id="form_tab_description">
										<div class="">
					<div class="control-group" id="field_group_description">
	<div class="controls">
<?php echo $this->form->getInput('description'); ?>
	</div>
</div>				</div>
					</div>
		<div class="tab-pane " id="form_tab_pubtab">
										<div class="form-horizontal">
					<div class="control-group" id="field_group_makepackage">
	<div class="control-label">
<?php echo $this->form->getLabel('makepackage'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('makepackage'); ?>
<span class="help-inline"><?php echo JText::_(''); ?></span>
	</div>
</div><div class="control-group" id="field_group_filename">
	<div class="control-label">
<?php echo $this->form->getLabel('filename'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('filename'); ?>
<span class="help-inline"><?php echo JText::_('Filename template for the package.'); ?></span>
	</div>
</div><div class="control-group" id="field_group_pubfolder">
	<div class="control-label">
<?php echo $this->form->getLabel('pubfolder'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('pubfolder'); ?>
<span class="help-inline"><?php echo JText::_('Folder to publish the package to. This is relative to the root of your Joomla install.'); ?></span>
	</div>
</div><div class="control-group" id="field_group_pubdisp">
	<div class="control-label">
<?php echo $this->form->getLabel('pubdisp'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('pubdisp'); ?>
<span class="help-inline"><?php echo JText::_(''); ?></span>
	</div>
</div><div class="control-group" id="field_group_statichelp">
	<div class="control-label">
<?php echo $this->form->getLabel('statichelp'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('statichelp'); ?>
<span class="help-inline"><?php echo JText::_(''); ?></span>
	</div>
</div><div class="control-group" id="field_group_updateserver">
	<div class="control-label">
<?php echo $this->form->getLabel('updateserver'); ?>
	</div>
	<div class="controls">
<?php echo $this->form->getInput('updateserver'); ?>
<span class="help-inline"><?php echo JText::_('URL for the update server to be used within the package. This needs to be a full url, including http://domain.com etc'); ?></span>
	</div>
</div>				</div>
					</div>
</div></div>
