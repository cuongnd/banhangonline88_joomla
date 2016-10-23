<?php
/**
 * @company		:	BriTech Solutions
 * @created by	:	JoomBri Team
 * @contact		:	www.joombri.in, support@joombri.in
 * @created on	:	12 March 2012
 * @file name	:	views/admproject/tmpl/editproject.php
 * @copyright   :	Copyright (C) 2012 - 2015 BriTech Solutions. All rights reserved.
 * @license     :	GNU General Public License version 2 or later
 * @author      :	Faisel
 * @description	: 	Shows list of Projects (jblance)
 */
 defined('_JEXEC') or die('Restricted access');
 JHtml::_('behavior.framework', true);
 JHtml::_('behavior.formvalidation');
 JHtml::_('formbehavior.chosen', 'select.advancedSelect');
 JHtml::_('bootstrap.tooltip');

 $doc = JFactory::getDocument();
 $doc->addStyleSheet(JURI::root().'components/com_jblance/css/style.css');
 $doc->addScript(JURI::root()."components/com_jblance/js/utility.js");
 
 $fields = JblanceHelper::get('helper.fields');		// create an instance of the class fieldsHelper

 $editor = JFactory::getEditor();
 $model = $this->getModel();
 $select = JblanceHelper::get('helper.select');		// create an instance of the class SelectHelper
 
 $isNew = ($this->row->id == 0) ? 1 : 0;

 $config = JblanceHelper::getConfig();
 $currencycode = $config->currencyCode;
 $currencysym = $config->currencySymbol;
 $dformat = $config->dateFormat;
 $fileLimitConf = $config->projectMaxfileCount;
 
 JText::script('COM_JBLANCE_CLOSE');
 
 JblanceHelper::setJoomBriToken();
?>
<script type="text/javascript">
	Joomla.submitbutton = function(task){
		if(!$$('input[name="id_category[]"]:checked')[0])
			isCategoryValid = false;
		else
			isCategoryValid = true;

		isFormValid = document.formvalidator.isValid(document.id('editproject-form'));
		
		if(task == 'admproject.cancelproject' || (isFormValid && isCategoryValid)){
			Joomla.submitform(task, document.getElementById('editproject-form'));
		}
		else {
			 var msg = '<?php echo JText::_('COM_JBLANCE_FIEDS_HIGHLIGHTED_RED_COMPULSORY', true); ?>';
			 if(!isCategoryValid)
			    	msg = msg+'\n\n'+'<?php echo JText::_('COM_JBLANCE_PLEASE_SELECT_CATEGORY_FROM_THE_LIST', true); ?>';
			 alert(msg);
		}
	}
	window.addEvent('domready', function(){
		$$('input[name="project_type"]').addEvent('click', updateProjectTypeFields);
		$$('input[name="commitment[undefined]"]').addEvent('click', updateCommitmentTypeFields);
		updateProjectTypeFields();
		updateCommitmentTypeFields();
	});

	function updateProjectTypeFields(){
		var checkedval = $$('input[name="project_type"]:checked').get('value');
		if(checkedval == 'COM_JBLANCE_FIXED'){
			$$('div[data-project-type="hourly"]').hide();
			$$('div[data-project-type="fixed"]').show();
			$('budgetrange_fixed').addClass('required').setProperty('required', 'required');
			$('budgetrange_hourly').removeClass('required').removeProperty('required');
			$('project_duration').removeClass('required').removeProperty('required');
			//$('commitment_period').removeClass('required').removeProperty('required');
		}
		else if(checkedval == 'COM_JBLANCE_HOURLY'){
			$$('div[data-project-type="hourly"]').show();
			$$('div[data-project-type="fixed"]').hide();
			$('budgetrange_fixed').removeClass('required').removeProperty('required');;
			$('budgetrange_hourly').addClass('required').setProperty('required', 'required');
			$('project_duration').addClass('required').setProperty('required', 'required');
			//$('commitment_period').addClass('required').setProperty('required', 'required');
		}
	}
	function updateCommitmentTypeFields(){
		var projectTypeVal = $$('input[name="project_type"]:checked').get('value');
		var checkedval = $$('input[name="commitment[undefined]"]:checked').get('value');
		if(projectTypeVal == 'COM_JBLANCE_HOURLY'){
			if(checkedval == 'sure'){
				$('commitment_period').addClass('required').setProperty('required', 'required');
			}
			else if(checkedval == 'notsure'){
				$('commitment_period').removeClass('required').removeProperty('required');
			}
		}
	}
	function editLocation(){
		$('level1').setStyle('display', 'inline-block').addClass('required');
	}
</script>
<form action="index.php" method="post" name="adminForm" id="editproject-form" enctype="multipart/form-data" class="form-validate">
	<div class="form-inline form-inline-header">
		<div class="control-group">
    		<label class="control-label" for="project_title"><?php echo JText::_('COM_JBLANCE_PROJECT_TITLE'); ?><span class="redfont">*</span>:</label>
			<div class="controls">						
				<input type="text" class="input-xxlarge input-large-text required" name="project_title" id="project_title" size="60" value="<?php echo $this->row->project_title;?>">
			</div>
  		</div>
	</div>
	<div class="form-horizontal">
	<?php echo JHtml::_('bootstrap.startTabSet', 'myTab', array('active' => 'projectinfo')); ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'projectinfo', JText::_('COM_JBLANCE_PROJECT_INFORMATION', true)); ?>
		<div class="row-fluid">
			<div class="span8">
				<div class="control-group">
		    		<label class="control-label" for="id_category"><?php echo JText::_('COM_JBLANCE_PROJECT_CATEGORIES'); ?><span class="redfont">*</span>:</label>
					<div class="controls">						
						<?php 
						$attribs = '';
						$select->getCheckCategoryTree('id_category[]', explode(',', $this->row->id_category), $attribs); ?>
					</div>
		  		</div>
		  		<div class="control-group">
		    		<label class="control-label" for="status"><?php echo JText::_('COM_JBLANCE_PROJECT_STATUS'); ?><span class="redfont">*</span>:</label>
					<div class="controls">						
						<?php 
						$attribs = "class='input-medium required advancedSelect' size='1'";
						$list_status = $select->getSelectProjectStatus('status', $this->row->status, 'COM_JBLANCE_PLEASE_SELECT', $attribs, '');
						echo $list_status; ?>	
					</div>
		  		</div>
		  		<div class="control-group">
		    		<label class="control-label" for="publisher_userid"><?php echo JText::_('COM_JBLANCE_PUBLISHER'); ?><span class="redfont">*</span>:</label>
					<div class="controls">						
						<?php echo $this->lists['userlist']; ?>
					</div>
		  		</div>
		  		<div class="control-group">
		    		<label class="control-label" for="approved"><?php echo JText::_('COM_JBLANCE_APPROVED'); ?><span class="redfont">*</span>:</label>
					<div class="controls">						
						<?php $approved = $select->YesNoBool('approved', $this->row->approved);
						echo  $approved; ?>
					</div>
		  		</div>
		  		<div class="control-group">
		    		<label class="control-label" for="start_date"><?php echo JText::_('COM_JBLANCE_PUBLISH_DATE'); ?><span class="redfont">*</span>:</label>
					<div class="controls">						
						<?php 
						$now = JFactory::getDate()->toSql();
						$startdate = (empty($this->row->start_date)) ? $now : $this->row->start_date;
						echo JHtml::_('calendar', $startdate, 'start_date', 'start_date', '%Y-%m-%d', array('class'=>'input-small required', 'size'=>'20',  'maxlength'=>'32'));
						?>
					</div>
		  		</div>
		  		<div class="control-group">
		    		<label class="control-label" for="expires"><?php echo JText::_('COM_JBLANCE_EXPIRES'); ?><span class="redfont">*</span>:</label>
					<div class="controls">	
						<div class="input-append">
							<input type="text" name="expires" id="expires" class="input-small required validate-numeric" value="<?php echo $this->row->expires; ?>">
							<span class="add-on"><?php echo JText::_('COM_JBLANCE_DAYS'); ?></span>
						</div>					
					</div>
		  		</div>
		  		<div class="control-group">
		    		<label class="control-label" for="project_type"><?php echo JText::_('COM_JBLANCE_PROJECT_TYPE'); ?><span class="redfont">*</span>:</label>
					<div class="controls">						
						<span class="" <?php echo $isNew ? 'style="display:block;"' : 'style="display:none;"';?>>				
						<?php 
						$default = empty($this->row->project_type) ?  'COM_JBLANCE_FIXED' : $this->row->project_type;
						$project_type = $select->getRadioProjectType('project_type', $default);
						echo  $project_type; 
						?>
						</span>
						<span class="label label-info"><?php echo JText::_($this->row->project_type); ?></span>
					</div>
		  		</div>
				<!-- fields for fixed projects -->
				<div data-project-type="fixed">
					<div class="control-group">
						<label class="control-label" for="budgetrange_fixed"><?php echo JText::_('COM_JBLANCE_BUDGET'); ?><span class="redfont">*</span>:</label>
						<div class="controls">
							<?php 
							$attribs = 'class="input-xlarge required advancedSelect"';
							$default = $this->row->budgetmin.'-'.$this->row->budgetmax;
							echo $select->getSelectBudgetRange('budgetrange_fixed', $default, 'COM_JBLANCE_PLEASE_SELECT', $attribs, '', 'COM_JBLANCE_FIXED');
							?>
						</div>
					</div>
				</div>
				<!-- fields for hourly projects -->
				<div data-project-type="hourly">
					<div class="control-group">
						<label class="control-label" for="budgetrange_hourly"><?php echo JText::_('COM_JBLANCE_BUDGET'); ?><span class="redfont">*</span>:</label>
						<div class="controls">
							<?php 
							$attribs = 'class="input-large required advancedSelect"';
							$default = $this->row->budgetmin.'-'.$this->row->budgetmax;
							echo $select->getSelectBudgetRange('budgetrange_hourly', $default, 'COM_JBLANCE_PLEASE_SELECT', $attribs, '', 'COM_JBLANCE_HOURLY');
							?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="project_duration"><?php echo JText::_('COM_JBLANCE_PROJECT_DURATION'); ?> <span class="redfont">*</span>:</label>
						<div class="controls">
							<?php 
							$attribs = 'class="input-large required advancedSelect"';
							echo $select->getSelectProjectDuration('project_duration', $this->row->project_duration, 'COM_JBLANCE_PLEASE_SELECT', $attribs, '');
							?>
						</div>
					</div>
					<div class="control-group">
						<label class="control-label" for="budgetrange"><?php echo JText::_('COM_JBLANCE_HOURS_OF_WORK_REQUIRED'); ?> <span class="redfont">*</span>:</label>
						<div class="controls">
							<label class="radio">
								<?php 
								// Convert the commitment params field to an array.
								$commitment = new JRegistry;
								$commitment->loadString($this->row->commitment);
								?>
								<input class="" type="radio" id="commitment_defined_option" name="commitment[undefined]" value="sure" <?php echo ($commitment->get('undefined') == 'sure') ? 'checked' : ''; ?> />
								<input type="text" class="input-mini validate-numeric" name="commitment[period]" id="commitment_period" maxlength="3" size="3" value="<?php echo $commitment->get('period'); ?>" />&nbsp;<?php echo JText::_('COM_JBLANCE_HOURS_PER'); ?>&nbsp;
								<select name="commitment[interval]" id="commitment_interval" class="input-small advancedSelect">
				                	<option value="COM_JBLANCE_DAY" <?php echo ($commitment->get('interval') == 'COM_JBLANCE_DAY') ? 'selected' : ''; ?>><?php echo JText::_('COM_JBLANCE_DAY'); ?></option>
				                	<option value="COM_JBLANCE_WEEK" <?php echo ($commitment->get('interval') == 'COM_JBLANCE_WEEK') ? 'selected' : ''; ?>><?php echo JText::_('COM_JBLANCE_WEEK'); ?></option>
				                	<option value="COM_JBLANCE_MONTH" <?php echo ($commitment->get('interval') == 'COM_JBLANCE_MONTH') ? 'selected' : ''; ?>><?php echo JText::_('COM_JBLANCE_MONTH'); ?></option>
				            	</select>
							</label>
			            	<label class="radio">
								<input type="radio" id="commitment_undefined_option" name="commitment[undefined]" value="notsure" <?php echo ($commitment->get('undefined', 'notsure') == 'notsure') ? 'checked' : ''; ?>  />
								<?php echo JText::_('COM_JBLANCE_NOT_SURE'); ?>
							</label>
						</div>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="level1"><?php echo JText::_('COM_JBLANCE_LOCATION'); ?> <span class="redfont">*</span>:</label>
					<?php 
					if($this->row->id_location > 0){ ?>
						<div class="controls">
							<?php echo JblanceHelper::getLocationNames($this->row->id_location); ?>
							<button type="button" class="btn btn-mini" onclick="editLocation();"><?php echo JText::_('JACTION_EDIT'); ?></button>
						</div>
					<?php 	
					}
					?>
					<div class="controls controls-row" id="location_info">
						<?php 
						$attribs = array('class' => 'input-medium', 'data-level-id' => '1', 'onchange' => 'getLocation(this, \'admproject.getlocationajax\');');
						
						if($this->row->id_location == 0){
							$attribs['class'] = 'input-medium required';
							$attribs['style'] = 'display: inline-block;';
						}
						else {
							$attribs['style'] = 'display: none;';
						}
						echo $select->getSelectLocationCascade('location_level[]', '', 'COM_JBLANCE_PLEASE_SELECT', $attribs, 'level1');
						?>
						<input type="hidden" name="id_location" id="id_location" value="<?php echo $this->row->id_location; ?>" />
						<div id="ajax-container" class="dis-inl-blk"></div>	
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="description"><?php echo JText::_('COM_JBLANCE_DESCRIPTION'); ?><span class="redfont">*</span>:</label>
					<div class="controls">
						<?php echo $editor->display('description', $this->row->description, '100%', '400', '50', '10'); ?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label"><?php echo JText::_('COM_JBLANCE_ATTACHMENT'); ?> :</label>
					<div class="controls">
						<?php
						for($i=0; $i < $fileLimitConf; $i++){
						?>
						<input name="uploadFile<?php echo $i;?>" type="file" id="uploadFile<?php echo $i;?>" /><br>
						<?php 
						} ?>
						<input name="uploadLimit" type="hidden" value="<?php echo $fileLimitConf;?>" />
						<?php echo  JHtml::tooltip(JText::_('COM_JBLANCE_ALLOWED_FILE_TYPES').' : '.$config->projectFileText.'<br>'.JText::_('COM_JBLANCE_MAXIMUM_FILE_SIZE').' : '.$config->projectMaxsize.' kB', JText::_('COM_JBLANCE_ATTACH_FILE'), JURI::root().'components/com_jblance/images/tooltip.png'); ?>
						<div class="lineseparator"></div>
						<?php 
						foreach($this->projfiles as $projfile){ ?>
						<label class="checkbox">
							<input type="checkbox" name=file-id[] value="<?php echo $projfile->id; ?>" />
		  					<?php echo LinkHelper::getDownloadLink('project', $projfile->id, 'admproject.download'); ?>
						</label>
						<?php	
						}
						?>
					</div>
				</div>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>	<!-- end of project info tab -->
	
	<?php 
		$parents = array();
		$children = array();
		//isolate parent and childr
		foreach($this->fields as $ct){
			if($ct->parent == 0)
				$parents[] = $ct;
			else
				$children[] = $ct;
		}
			
		if(count($parents)){ ?>
		<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'addinfo', JText::_('COM_JBLANCE_ADDITIONAL_INFO', true)); ?>
			<?php 
			foreach($parents as $pt){ ?>
			<fieldset>
				<legend><?php echo JText::_($pt->field_title); ?></legend>
					<?php
					foreach($children as $ct){
						if($ct->parent == $pt->id){ ?>
					<div class="control-group">
							<?php
							$labelsuffix = '';
							if($ct->field_type == 'Checkbox') $labelsuffix = '[]'; //added to validate checkbox
							?>
						<label class="control-label" for="custom_field_<?php echo $ct->id.$labelsuffix; ?>"><?php echo JText::_($ct->field_title); ?><span class="redfont"><?php echo ($ct->required)? '*' : ''; ?></span>:</label>
						<div class="controls">
							<?php $fields->getFieldHTML($ct, $this->row->id, 'project'); ?>
						</div>
					</div>
					<?php
						}
					} ?>
			</fieldset>
			<?php
			}
			?>
	<?php echo JHtml::_('bootstrap.endTab'); ?>	<!-- end of additional info tab -->
	<?php } ?>
	
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'seo', JText::_('COM_JBLANCE_SEO_OPTIMIZATION', true)); ?>
		<div class="control-group">
			<?php 
			$tipmsg = JHtml::tooltipText(JText::_('COM_JBLANCE_META_DESCRIPTION'), JText::_('COM_JBLANCE_META_DESCRIPTION_TIPS'));
			?>
			<label class="control-label hasTooltip" for="metadesc" title="<?php echo $tipmsg; ?>"><?php echo JText::_('COM_JBLANCE_META_DESCRIPTION'); ?>:</label>
			<div class="controls">
				<textarea name="metadesc" id="metadesc" rows="3" cols="60" class="input-large"><?php echo $this->row->metadesc; ?></textarea>
			</div>
		</div>
		<div class="control-group">
			<?php 
			$tipmsg = JHtml::tooltipText(JText::_('COM_JBLANCE_META_KEYWORDS'), JText::_('COM_JBLANCE_META_KEYWORDS_TIPS'));
			?>
			<label class="control-label hasTooltip" for="metakey" class="" title="<?php echo $tipmsg; ?>"><?php echo JText::_('COM_JBLANCE_META_KEYWORDS'); ?>:</label>
			<div class="controls">
				<textarea name="metakey" id="metakey" rows="3" cols="60" class="input-large"><?php echo $this->row->metakey; ?></textarea>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>		<!-- end of seo optimize tab -->
	
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'upgrade', JText::_('COM_JBLANCE_PROJECT_UPGRADES', true)); ?>
		<div class="row-fluid">
			<div class="span8">
				<div class="control-group">
					<label class="control-label" for="is_featured"><?php echo JText::_('COM_JBLANCE_FEATURED_PROJECT'); ?>:</label>
					<div class="controls">
						<?php $is_featured = $select->YesNoBool('is_featured', $this->row->is_featured);
						echo  $is_featured;
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="is_urgent"><?php echo JText::_('COM_JBLANCE_URGENT_PROJECT'); ?>:</label>
					<div class="controls">
						<?php $is_urgent = $select->YesNoBool('is_urgent', $this->row->is_urgent);
						echo  $is_urgent;
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="is_private"><?php echo JText::_('COM_JBLANCE_PRIVATE_PROJECT'); ?>:</label>
					<div class="controls">
						<?php $is_private = $select->YesNoBool('is_private', $this->row->is_private);
						echo  $is_private;
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="is_sealed"><?php echo JText::_('COM_JBLANCE_SEALED_PROJECT'); ?>:</label>
					<div class="controls">
						<?php $is_sealed = $select->YesNoBool('is_sealed', $this->row->is_sealed);
						echo  $is_sealed;
						?>
					</div>
				</div>
				<div class="control-group">
					<label class="control-label" for="is_nda"><?php echo JText::_('COM_JBLANCE_NDA_PROJECT'); ?>:</label>
					<div class="controls">
						<?php $is_nda = $select->YesNoBool('is_nda', $this->row->is_nda);
						echo  $is_nda;
						?>
					</div>
				</div>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>		<!-- end of project upgrate tab -->
	
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'bids', JText::_('COM_JBLANCE_ALL_BIDS', true)); ?>
		<div class="row-fluid">
			<div class="span8">
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th><?php echo JText::_('COM_JBLANCE_FREELANCERS'); ?></th>
							<th><?php echo JText::_('COM_JBLANCE_BIDS').' ('.$currencycode.')'; ?></th>
							<th><?php echo ($this->row->project_type == 'COM_JBLANCE_FIXED') ? JText::_('COM_JBLANCE_DELIVERY_DAYS') : JText::_('COM_JBLANCE_WORK_FOR'); ?></th>
							<th><?php echo JText::_('COM_JBLANCE_TIME_OF_BID'); ?></th>
							<th><?php echo JText::_('COM_JBLANCE_RATING'); ?></th>	
							<th><?php echo JText::_('COM_JBLANCE_STATUS'); ?></th>
						</tr>
					</thead>
					<tbody>
						<?php
							$k = 0;
							for($i=0, $n=count($this->bids); $i < $n; $i++){
								$bid 		 = $this->bids[$i];
								$link_lancer = JRoute::_( 'index.php?option=com_jblance&view=admproject&layout=edituser&cid[]='.$bid->user_id);
						?>
						<tr id="tr_r1_bid_<?php echo $bid->id; ?>" class="<?php echo "row$k"; ?>">
							<td><a href="<?php echo $link_lancer; ?>"> <?php echo $bid->username; ?></a></td>
							<td><?php echo JblanceHelper::formatCurrency($bid->amount, true, false, 0); ?><?php echo ($this->row->project_type == 'COM_JBLANCE_HOURLY') ? ' / '.JText::_('COM_JBLANCE_HR') : ''; ?></td>
							<td class="center">
							<?php if($this->row->project_type == 'COM_JBLANCE_FIXED') : ?>
		                    <?php echo $bid->delivery; ?> <?php echo JText::_('COM_JBLANCE_BID_DAYS'); ?>
		                    <?php elseif($this->row->project_type == 'COM_JBLANCE_HOURLY') : 
				             	$commitment = new JRegistry;
				            	$commitment->loadString($this->row->commitment);
		                    ?>
		                    <?php echo $bid->delivery; ?> <?php echo JText::_('COM_JBLANCE_HOURS_PER').' '.JText::_($commitment->get('interval')); ?>
		                    <?php endif; ?>
							</td> 
							<td nowrap="nowrap"><?php echo JHtml::_('date', $bid->bid_date, $dformat); ?></td>
							<td>
								<?php
								$rate = JblanceHelper::getAvarageRate($bid->user_id, true);
								?>
							</td>
							<td><?php echo JText::_($bid->status); ?></td>
						</tr>
						<tr id="tr_r2_bid_<?php echo $bid->id; ?>" class="<?php echo "row$k"; ?>">
							<td colspan="5">
							<?php echo $bid->details; ?>
							</td>
							<td  class="center">
								<a class="remFeed" onclick="processBid('<?php echo $bid->id; ?>', 'admproject.processBid');" href="javascript:void(0);">
								<img alt="" src="<?php echo JURI::root();?>components/com_jblance/images/remove.gif" title="<?php echo JText::_('COM_JBLANCE_REMOVE'); ?>">
								</a>
							</td>
						</tr>
						<?php 
							$k = 1 - $k;
							} ?>
					</tbody>
				</table>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>		<!-- end of bids tab -->
	
	<?php if(!empty($this->row->invite_user_id)) { ?>
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'invite', JText::_('COM_JBLANCE_INVITED_USERS', true)); ?>
		<div class="row-fluid">
			<div class="span6">
				<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th><?php echo JText::_('COM_JBLANCE_FREELANCERS'); ?></th>
							<th><?php echo JText::_('COM_JBLANCE_RATING'); ?></th>	
						</tr>
					</thead>
					<tbody>
						<?php
							$k = 0;
							$invite_user_ids = explode(',', $this->row->invite_user_id);
							foreach($invite_user_ids as $key=>$val){
								$inviteUserInfo = JFactory::getUser($val);
						?>
						<tr class="<?php echo "row$k"; ?>">
							<td><?php echo $inviteUserInfo->username; ?></td>
							<td>
								<?php $rate = JblanceHelper::getAvarageRate($inviteUserInfo->id, true); ?>
							</td>
						</tr>
						<?php 
							$k = 1 - $k;
							} ?>
					</tbody>
				</table>
			</div>
		</div>
	
	<?php echo JHtml::_('bootstrap.endTab'); ?>		<!-- end of invite tab -->
	<?php } ?>
	
	<?php echo JHtml::_('bootstrap.addTab', 'myTab', 'forum', JText::_('COM_JBLANCE_PUBLIC_CLARIFICATION_BOARD')); ?>
		<div class="row-fluid">
			<div class="span8">
				<div style="max-height: 600px; overflow: auto;">
					<table class="table table-striped table-bordered">
					<thead>
						<tr>
							<th><?php echo JText::_('COM_JBLANCE_USERNAME'); ?></th>
							<th><?php echo JText::_('COM_JBLANCE_POSTED_ON'); ?></th>
							<th><?php echo JText::_('COM_JBLANCE_MESSAGE'); ?></th>	
							<th></th>
						</tr>
					</thead>
					<tbody>
						<?php
						for($i=0, $n=count($this->forums); $i < $n; $i++){
							$forum = $this->forums[$i];
							$link_user = JRoute::_( 'index.php?option=com_jblance&view=admproject&layout=edituser&cid[]='.$forum->user_id);
							$poster = JFactory::getUser($forum->user_id)->username;
						?>
						<tr id="tr_forum_<?php echo $forum->id; ?>">
							<td nowrap="nowrap"><a href="<?php echo $link_user; ?>"> <?php echo $poster; ?></a></td> 
							<td nowrap="nowrap"><?php echo JHtml::_('date', $forum->date_post, $dformat); ?></td>
							<td><?php echo $forum->message; ?></td>
							<td>
								<a class="remFeed" onclick="processForum('<?php echo $forum->id; ?>', 'admproject.removeForum');" href="javascript:void(0);">
								<img alt="" src="<?php echo JURI::root();?>components/com_jblance/images/remove.gif" title="<?php echo JText::_('COM_JBLANCE_REMOVE'); ?>">
								</a>
							</td>
						</tr>
						<?php } ?>
					</tbody>
					</table>
				</div>
			</div>
		</div>
	<?php echo JHtml::_('bootstrap.endTab'); ?>		<!-- end of forum tab -->
	<?php echo JHtml::_('bootstrap.endTabSet'); ?>
	</div>
	
	<input type="hidden" name="option" value="com_jblance" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="id" value="<?php echo $this->row->id; ?>" />
	<?php echo JHtml::_('form.token'); ?>
</form>