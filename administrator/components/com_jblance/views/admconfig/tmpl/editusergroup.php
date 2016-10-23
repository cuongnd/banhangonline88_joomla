<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	14 March 2012
 * @file name	:	views/admconfig/tmpl/editusergroup.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Edit User Group (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 
 JHtml::_('behavior.framework', true);
 JHtml::_('behavior.formvalidation');
 JHtml::_('bootstrap.tooltip');
 
 $model = $this->getModel();
 $editor = JFactory::getEditor();
 $select = JblanceHelper::get('helper.select');		// create an instance of the class SelectHelper
 
 $user = JFactory::getUser();
 $isSuperAdmin = false;
 if(isset($user->groups[8]))
 	$isSuperAdmin = true;
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task){
		if (task == 'admconfig.cancelusergroup' || document.formvalidator.isValid(document.id('editusergroup-form'))) {
			Joomla.submitform(task, document.getElementById('editusergroup-form'));
		}
		else {
			alert('<?php echo $this->escape(JText::_('COM_JBLANCE_FIEDS_HIGHLIGHTED_RED_COMPULSORY'));?>');
		}
	}
</script>
<form name="adminForm" id="editusergroup-form" action="index.php" method="post" enctype="multipart/form-data" class="form-validate form-horizontal">
	<div class="row-fluid">
		<div class="span6">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_JBLANCE_GROUP_DETAILS');?></legend>
				<div class="control-group">
		    		<label class="control-label" for="name"><?php echo JText::_('COM_JBLANCE_GROUP_TITLE'); ?><span class="redfont">*</span>:</label>
					<div class="controls">
						<input type="text" class="input-xlarge input-large-text required" id="name" name="name" value="<?php echo $this->row->name; ?>" />
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_REQUIRE_APPROVAL_TIPS')); ?>
		    		<label class="control-label hasTooltip" for="approval" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_REQUIRE_APPROVAL'); ?>:</label>
					<div class="controls">
						<?php $approval = $select->YesNoBool('approval', $this->row->approval);
						echo  $approval; ?>
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_SKIP_PLAN_TIPS')); ?>
		    		<label class="control-label hasTooltip" for="skipPlan" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_SKIP_PLAN'); ?>:</label>
					<div class="controls">
						<?php $skipPlan = $select->YesNoBool('skipPlan', $this->row->skipPlan);
						echo  $skipPlan; ?>
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_JOOMLA_USER_GROUP_TIPS')); ?>
		    		<label class="control-label hasTooltip" for="joomla_ug_id" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_JOOMLA_USER_GROUP'); ?>:</label>
					<div class="controls">
						<?php 
						if($isSuperAdmin) : 
							echo JHtml::_('access.usergroups', 'joomla_ug_id', explode(',', $this->row->joomla_ug_id), true);
						else :
							echo $model->getJoomlaUserGroupTitles($this->row->joomla_ug_id);
						endif; ?>
					</div>
		  		</div>
				<div class="control-group" style="display: none;">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_FREE_MODE')); ?>
		    		<label class="control-label hasTooltip" for="freeMode" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_FREE_MODE'); ?>:</label>
					<div class="controls">
						<?php $freeMode = $select->YesNoBool('freeMode', $this->row->freeMode);
						echo  $freeMode; ?>
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_DESCRIPTION')); ?>
		    		<label class="control-label hasTooltip" for="description" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_DESCRIPTION'); ?>:</label>
					<div class="controls">
						<?php echo $editor->display('description', $this->row->description, '100%', '300', '60', '20');?>
					</div>
		  		</div>
			</fieldset>		
		</div>
		<div class="span6">
			<fieldset class="adminform">
				<legend><?php echo JText::_('COM_JBLANCE_PERMISSION_DASHBOARD_SETTINGS'); ?></legend>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_BID_PROJECTS_TIPS')); ?>
		    		<label class="control-label hasTooltip" for="paramsallowBidProjects" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_BID_PROJECTS'); ?>:</label>
					<div class="controls">
						<?php 
						$val = isset($this->params['allowBidProjects']) ? $this->params['allowBidProjects'] : 0;
						$allowBidProjects = $select->YesNoBool('params[allowBidProjects]', $val);
						echo  $allowBidProjects; ?>
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_POST_PROJECTS_TIPS')); ?>
		    		<label class="control-label hasTooltip" for="paramsallowPostProjects" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_POST_PROJECTS'); ?>:</label>
					<div class="controls">
						<?php 
						$val = isset($this->params['allowPostProjects']) ? $this->params['allowPostProjects'] : 0;
						$allowPostProjects = $select->YesNoBool('params[allowPostProjects]', $val);
						echo  $allowPostProjects; ?>
					</div>
		  		</div>
				<div class="control-group">
					<?php $tip = JHtml::tooltipText(JText::_('COM_JBLANCE_ADD_PORTFOLIO_TIPS')); ?>
		    		<label class="control-label hasTooltip" for="paramsallowAddPortfolio" title="<?php echo $tip; ?>"><?php echo JText::_('COM_JBLANCE_ADD_PORTFOLIOS'); ?>:</label>
					<div class="controls">
						<?php 
						$val = isset($this->params['allowAddPortfolio']) ? $this->params['allowAddPortfolio'] : 0;
						$allowAddPortfolio = $select->YesNoBool('params[allowAddPortfolio]', $val);
						echo  $allowAddPortfolio; ?>
					</div>
		  		</div>
			</fieldset>
			<fieldset>
				<legend><?php echo JText::_('COM_JBLANCE_ASSIGN_FIELDS');?></legend>
				<div class="alert alert-info"><?php echo JText::_('COM_JBLANCE_ASSIGN_FIELDS_FOR_USERGROUP_INFO'); ?></div>
				<table class="table table-striped">
					<thead>
						<tr class="title">
							<th width="1%">#</th>
							<th style="text-align: left;">
								<?php echo JText::_('COM_JBLANCE_FIELD_TITLE');?>
							</th>
							<th width="15%" style="text-align: center;">
								<?php echo JText::_('COM_JBLANCE_FIELD_TYPE');?>
							</th>
							<th width="1%" style="text-align: center;">
								<?php echo JText::_('COM_JBLANCE_INCLUDE');?>
							</th>
						</tr>
					</thead>
					<?php
					$count	= 0;
					$i		= 0;
		
					foreach($this->fields as $field){
						if($field->field_type == 'group'){ ?>
					<tr>
						<td>&nbsp;</td>
						<td colspan="4" >
							<strong><?php echo JText::_('COM_JBLANCE_GROUPS');?>:
								<span><?php echo $field->field_title;?></span>
							</strong>
							<div style="clear: both;"></div>
							<input type="hidden" name="parents[]" value="<?php echo $field->id;?>" />
						</td>
					</tr>
						<?php
							$i	= 0;	// Reset count
						}
						else if($field->field_type != 'group'){
							++$i;
						?>
					<tr id="rowid<?php echo $field->id;?>">
						<td><?php echo $i;?></td>
						<td><span><?php echo $field->field_title;?></span></td>
						<td align="center"><?php echo $field->field_type;?></td>
						<td style="text-align: center;" id="publish<?php echo $field->id;?>">
							<input type="checkbox" name="fields[]" value="<?php echo $field->id;?>"<?php echo $this->row->isChild($field->id) ? ' checked="checked"' : '';?> />
						</td>
					</tr>
				<?php
						}
					$count++;
				}
				?>
				</table>
			</fieldset>
		</div>
	</div>

	<input type="hidden" name="option" value="com_jblance" />
	<input type="hidden" name="task" value="saveusergroup" />
	<input type="hidden" name="id" value="<?php echo $this->row->id;?>" />
	<input type="hidden" name="boxchecked" value="0" />
	<?php echo JHtml::_('form.token'); ?>	
</form>