<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

$tabindex = 1;

FSJ_Settings::LoadBaseSettings("com_fsj_transman");
?>

<div class="fsj" id="fsj_modal_container">
	<div class="modal fsj_modal hide in" id="fsj_modal" style="width: 400px; margin-left: -200px; display: block;" aria-hidden="false">
	  <div class="modal-header">
		
		<h3>Please Wait</h3>
	  </div>
	  <div class="modal-body">
		<p class="center">
			<img src="<?php echo JURI::root(true); ?>/libraries/fsj_core/assets/images/misc/ajax-loader.gif">	 
		</p>
	  </div>
	  <div class="modal-footer">
		
	  </div>
	</div>

	<div class="modal fsj_modal hide" id="fsj_modal_base">
	  <div class="modal-header">
		<button class="close" data-dismiss="modal">×</button>
		<h3>Please Wait</h3>
	  </div>
	  <div class="modal-body">
		<p class="center">
			<img src="<?php echo JURI::root(true); ?>/libraries/fsj_core/assets/images/misc/ajax-loader.gif">	 
		</p>
	  </div>
	  <div class="modal-footer">
		<a href="#" class="btn" onclick="fsj_modal_hide(); return false;">Cancel</a>
	  </div>
	</div>
</div>

<div class="modal-backdrop in"></div>


<?php if (FSJ_Settings::get('tm_options', 'collapse_headers')): ?>
	<div id="hide_headers" style="display: none;"></div>
<?php endif; ?>

<script type="text/javascript">
jQuery(document).ready(function () {
	fsj_add_shortcut_key(true, false, 'S', '#toolbar-apply');
	fsj_add_shortcut_key(true, true, 'S', '#toolbar-save');
	fsj_add_shortcut_key(true, false, 'D', '#toolbar-cancel');
	fsj_add_shortcut_key(true, false, 'P', '#toolbar-publish');
	fsj_add_shortcut_key(true, true, 'P', '#toolbar-publishclose');
});

Joomla.submitbutton = function(task) {
	
	if (task == 'file.save' || task == 'file.publishclose' || task == 'file.cancel') {
		// check to see if we have pending approvals
		if (jQuery('.input_cont.warning').length > 0)
		{
			if (!confirm("There are phrases pending approval, are you sure? Pressing 'Yes' will result in pending changes being lost.")) return;
		}
	}

	if (task == 'file.apply') {
		return tm_save(false);
	}
	if (task == 'file.save') {
		return tm_save(true);
	}
	if (task == 'file.cancel') {
		var url = jQuery('#cancel_url').text();
		window.location.href = url;
	}
	if (task == 'file.publish') {
		return tm_save(false, true);
	}
	if (task == 'file.publishclose') {
		return tm_save(true, true);
	}
	Joomla.submitform(task, document.getElementById('item-form'));
}
</script>

<div id="cancel_url" style="display:none;"><?php echo JRoute::_('index.php?option=com_fsj_transman&view=files', false); ?></div>
<div id="language_tag" style="display:none;"><?php echo $this->tag; ?></div>
<div id="approve_all_confirm" style="display:none;"><?php echo JText::_('FSJ_TM_APPROVE_ALL_CONFIRM'); ?></div>
<div id="base_lang" style="display:none;"><?php echo FSJ_TM_Helper::GetBaseLanguage(); ?></div>

<div class="fsj fsj_transman">

	<div id="notice_saveing" style="display:none;">
		<div class="alert alert-info">
			<h2>Saving</h2>
			<h4>Please wait</h4>
		</div>
	</div>

<?php if (FSJ_Settings::get('tm_options', 'save_notify')): ?>
	<div id="notice_savewarning" style="display:none;">
		<div class="alert alert-info">
			<h2>You have not saved for 10 minutes</h2>
			<p>Do you want to save now?</p>
			<button class="btn" onclick="tm_save_warning_act(true);">Save</button>
			<button class="btn" onclick="tm_save_warning_act(false);">Dont Save</button>
		</div>
	</div>
<?php endif; ?>

	<div id="notice_saved" style="display:none;">
		<button type="button" class="close" onclick="tm_close_saved();">&#x00D7;</button>
		<div class="alert alert-success">
			<h4>Saved</h4>
			<div style="clear: both;"></div>
		</div>
	</div>
	
	<div class="alert alert-error" id="save_error" style="display:none;">
		<button type="button" class="close" onclick="jQuery('#save_error').hide();">&#x00D7;</button>
		<h4>Error Saving</h4>
		<p>Some Text Here!</p>
	</div>
		
	<div id="result"></div>

	<div class="tm_key">
		<div class="form-inline">
			<div class="input_cont control-group success">
				<div class="input-prepend input_div">
					<span class="add-on"><i class="icon-arrow-right"></i></span>
					<input type="text" class="" isdone="1" value="<?php echo JText::_('FSJ_TM_COMPLETED'); ?>">
				</div>
			</div>
			<div class="form_header_info"><div class="alert alert-success"><?php echo JText::_('FSJ_TM_COUNT'); ?></div></div>
		</div>
		<div class="form-inline">
			<div class="input_cont control-group error">
				<div class="input-prepend input_div">
					<span class="add-on"><i class="icon-arrow-right"></i></span>
					<input type="text" class="" isdone="1" value="<?php echo JText::_('FSJ_TM_INCOMPLETE'); ?>">
				</div>
			</div>
			<div class="form_header_info"><div class="alert alert-error"><?php echo JText::_('FSJ_TM_COUNT'); ?></div></div>
		</div>
	</div>

	<div class="fsj form-inline pull-right" id="trans_filter" style="display:none;margin-top:8px;margin-bottom: 8px;">
		<label class="checkbox">
			<?php echo JText::_('FSJ_TM_PHRASES'); ?>
		</label>
		<div class="btn-group">
			<button class="btn" onclick="tm_show_lines('all')"><?php echo JText::_('FSJ_TM_ALL'); ?></button><button class="btn btn-success" onclick="tm_show_lines('complete')"><?php echo JText::_('FSJ_TM_COMPLETED'); ?></button><button class="btn btn-danger" onclick="tm_show_lines('incomplete')"><?php echo JText::_('FSJ_TM_INCOMPLETE'); ?></button>
		</div>
		<label class="checkbox">
			<?php echo JText::_('FSJ_TM_FILTER'); ?>
		</label>
		<div class="input-prepend" id="filter_div">
			<span class="add-on"><i class="icon-search"></i></span>
			<input type="text" class="input-small" id='filter_input'>
			<button class="btn" type="button" style="display:none;" id="filter_clear"><i class="icon-remove"></i></button>
		</div>
		<?php if ($this->strings->has_comments): ?>
			<label class="checkbox">
				<?php echo JText::_('FSJ_TM_SECTIONS'); ?>
			</label>
			<div class="btn-group">
				<button class="btn" onclick="tm_show_sections('all')"><?php echo JText::_('FSJ_TM_ALL'); ?></button><button class="btn" onclick="tm_show_sections('none')"><?php echo JText::_('FSJ_TM_NONE'); ?></button><button class="btn btn-success" onclick="tm_show_sections('complete')"><?php echo JText::_('FSJ_TM_COMPLETED'); ?></button><button class="btn btn-danger" onclick="tm_show_sections('incomplete')"><?php echo JText::_('FSJ_TM_INCOMPLETE'); ?></button>
			</div>
		<?php endif; ?>
	</div>

	<h4>
		<?php echo $this->file; ?>
		<?php if ($this->strings->is_new): ?> - New Translation<?php endif; ?>
		<?php if (!$this->strings->published): ?> - <?php echo JText::_('JUNPUBLISHED');?><?php endif; ?>
	</h4>
	<div>
		<?php if ($this->basetag == $this->tag): ?>
			<div class="control-group warning">
				<p class="help-block">
					<?php echo JText::_('FSJ_TM_EDITING_BASE');?>
				</p>
			</div>
		<?php elseif ($this->strings->has_base): ?>
			<div class="control-group success">
				<p class="help-block">
					<?php echo JText::sprintf('FSJ_TM_BASE_LANGUAGE_TAG', $this->basetag); ?>
				</p>
			</div>
		<?php else: ?>
			<div class="control-group error">
				<p class="help-block">
					<?php echo JText::sprintf('FSJ_TM_NO_BASE', $this->tag.".".$this->file); ?>
				</p>
			</div>
		<?php endif; ?>
	</div>
	<p>Currently Editing: <strong><?php echo $this->current_file; ?></strong></p>
	
	<div class="pull-right">
		<a href="#" onclick="tm_approve_all();return false;" class="btn" style="margin-top: 12px;">
			<i class="icon-ok"></i>
			<?php echo JText::_('FSJ_TM_APPROVE_ALL'); ?>
		</a>
		<a href="#" onclick="tm_auto_all();return false;" class="btn" style="margin-top: 12px;">
			<i class="icon-flag"></i>
			<?php echo JText::_('FSJ_TM_AUTO_TRANSLATE_ALL'); ?>
		</a>
	</div>
	
	<form action="<?php echo JRoute::_('index.php'); ?>" method="post" name="adminForm" id="item-form" class="form-validate">
		<input type="hidden" name="task" value="" />
		<input type="hidden" name="option" value="com_fsj_transman" />
		<input type="hidden" name="file" id="form_file" value="<?php echo JRequest::getVar('file'); ?>" />

		<div class="trans_form">
			<table>
				<tr>
					<td valign="top">
						<h3><?php echo JText::_('FSJ_TM_BASE_LANGUAGE'); ?></h3>
					</td>
					<td valign="top">
						<h3><?php echo JText::_('FSJ_TM_TRANSLATION'); ?></h3>
					</td>
				</tr>
				<tr>
					<td>
						<h4><?php echo JText::_('FSJ_TM_HEADER'); ?></h4>
					</td>
					<td>
						<h4><?php echo JText::_('FSJ_TM_HEADER_TRANS'); ?></h4>
					</td>
				</tr>
				<tr>
					<td valign="top">
						<div><?php echo $this->strings->getBaseHeader(); ?></div>
					</td>
					<td valign="top">
						<div>
							<textarea style="width: 100%" cols="60" id="input_header" name="header" tabindex="<?php echo $tabindex++; ?>" rows="1<?php //echo $this->strings->getTransHeaderCnt() + 2; ?>"><?php echo $this->strings->getTransHeader() . "\n"; ?></textarea>
						</div>
					</td>
				</tr>
			</table>

			<?php if (count($this->strings->lines) > 0 && !$this->strings->lines[0]->is_comment): ?>
				<div style="height:18px;"></div>
			<?php endif; ?>

			<?php
			$intable = false; 
			$setno = 1;
			$lineno = 1;
			$form_set = 1;
			$form_set_count = 1;
			$new_header = false;
			$extrano = 0;
			
			?>
			
			<?php foreach ($this->strings->lines as $line_obj): ?>
				<?php if ($line_obj->is_comment): ?>
		
					<!-- Comment lines, show as header -->
					<?php if ($intable): ?>
						</table>
					<?php 
							$intable = false; 
							$setno++;
						?>
					<?php endif; ?>

					<div class="pull-right form_section_<?php echo $setno; ?>">
						&nbsp;
						<a class="btn" style="position: relative;top: 4px;padding: 4px 7px;" href="#" onclick="tm_approve_set('<?php echo $setno; ?>');return false;">
							<i class="icon-ok"></i>
						</a>
						&nbsp;
						<a class="btn" style="position: relative;top: 4px;padding: 4px 7px;" href="#" onclick="tm_auto_set('<?php echo $setno; ?>');return false;">
							<i class="icon-flag"></i>
						</a>
					</div>
					
					<div class="form_header_info form_section_<?php echo $setno; ?>" id="form_header_info_<?php echo $setno; ?>"></div>
					<h4 class='form_header form_section_<?php echo $setno; ?>' set='<?php echo $setno; ?>' onclick='tm_toggle_set(<?php echo $setno; ?>);'><?php echo implode(" ", $line_obj->comment); ?></h4>
					<div class="header_bottom_border form_section_<?php echo $setno; ?>"></div>


				<?php elseif ($line_obj->is_new): ?>
					
					<?php if (!$new_header): ?>
						<?php $setno++; ?>
						<?php if ($intable): ?>
							</table>
							<?php $setno++; ?>
						<?php endif; ?>

						<button class="btn btn-small add_new_entry_button">
							<i class="icon-new"></i>
							<?php echo JText::_('FSJ_TM_ADD_NEW_STRING'); ?>
						</button>

						<h4 class='form_header form_section_<?php echo $setno; ?>' set='<?php echo $setno; ?>' onclick='tm_toggle_set(<?php echo $setno; ?>);'><?php echo JText::_('FSJ_TM_ADDITIONAL_STRINGS'); ?></h4>
						<div class="header_bottom_border form_section_<?php echo $setno; ?>"></div>

						<table width='100%' class="form_table new_table form_section_<?php echo $setno; ?>" id="form_table_<?php echo $setno; ?>">
						<?php $new_header = true; ?>
					<?php endif; ?>


				<tr id="extra_<?php echo $extrano; ?>">
					<td valign="top">
						<div class="additional input_div">
							<div class="remove_extra">
								<button class="btn btn-mini hasTooltip" title="<?php echo JText::_('FSJ_TM_REMOVE_EXTRA_STRING'); ?>" 
									onclick="return tm_remove_extra(<?php echo $extrano; ?>)">
									<i class="icon-cancel"></i>
								</button>
							</div>
							<input 
								type="text" 
								id="extra_key_<?php echo $extrano; ?>" 
								value="<?php echo $line_obj->key; ?>" 
								class='' 
								onchange='tm_check_key(this)'
								tabindex="<?php echo $tabindex++; ?>"
								/>
						</div>
					</td>
					<td valign="top">
						<div class='input_cont control-group input_div'>
							<input 
								type="text" 
								id="extra_input_<?php echo $extrano; ?>" 
								class=' extra_input' 
								value="<?php echo $line_obj->getInputValue(); ?>" 	
								tabindex="<?php echo $tabindex++; ?>"
							/>
						</div>
						&nbsp;
					</td>
				</tr>
				
					<?php $extrano++; ?>		
				<?php else: ?>

					<?php if ($line_obj->key == "") continue; ?>

					<?php if (!$intable): ?>
						<table class="form_table existing_table form_section_<?php echo $setno; ?>" id="form_table_<?php echo $setno; ?>">
						<?php $intable = true; ?>
					<?php endif; ?>

<tr id="tr_<?php echo $lineno; ?>" lineno="<?php echo $lineno; ?>" key='<?php echo $line_obj->key; ?>'>
	<td>
		<div class='orig_phrase'><?php echo htmlspecialchars($line_obj->base); ?></div>
		<div class="small">(<?php echo str_replace("_"," ",$line_obj->key); ?>)</div>
	</td>
	<td>
		<div class='input_cont <?php if ($line_obj->isLong()): ?>large<?php endif; ?> control-group <?php echo $line_obj->isDone() ? "success" : "error"?>'>
			<div class="input-prepend <?php if (!$line_obj->isDone()): ?>input-append<?php endif; ?>">
				<?php if (trim($line_obj->base) == ""): ?>
					<span class="add-on icon-flag" style="background-color: rgb(238, 238, 238);color: rgb(51, 51, 51);border-color: rgb(204, 204, 204);cursor:inherit;"></span>
				<?php else: ?>
					<span class="add-on auto_tran icon-flag"></span>
				<?php endif; ?>
				<span class="add-on copy_orig icon-arrow-right"></span>
				<?php if ($line_obj->isLong()): ?>
					<textarea tabindex="<?php echo $tabindex++; ?>" isdone="<?php echo $line_obj->isDone() ? "1" : "0"?>" name="lines_<?php echo $form_set; ?>[<?php echo $line_obj->key; ?>]" ><?php echo $line_obj->getInputValue(); ?></textarea>
				<?php else: ?>
					<input tabindex="<?php echo $tabindex++; ?>" type="text" isdone="<?php echo $line_obj->isDone() ? "1" : "0"?>" name="lines_<?php echo $form_set; ?>[<?php echo $line_obj->key; ?>]" value="<?php echo $line_obj->getInputValue(); ?>" />
				<?php endif; ?>
				<button class="btn <?php if ($line_obj->isDone()): ?>hide<?php endif; ?>"><i class="icon-ok"></i></button>
			</div>
		</div>
	</td>
</tr>
					<?php 
						$lineno++;
						$form_set_count++; 
						if ($form_set_count > 10)
						{
							$form_set_count = 0;
							$form_set++;		
						}
					?>
				<?php endif; ?>
			<?php endforeach; ?>

			<?php if ($intable): ?>
			</table>
			<?php endif; ?>
			
			<?php if (count($this->strings->lines) == 0): ?>
				<h4><?php echo JText::_('FSJ_TM_NO_CONTENT'); ?></h4>
			<?php endif; ?>
						
			
			<div id="max_extra" style="display:none;"><?php echo $extrano; ?></div>
			<?php if(!$new_header): ?>
				<?php $setno++; ?>
				<button class="btn btn-small add_new_entry_button">
					<i class="icon-new"></i>
					<?php echo JText::_('FSJ_TM_ADD_NEW_STRING'); ?>
				</button>
				<h4 class='form_header form_section_<?php echo $setno; ?>' set='<?php echo $setno; ?>' onclick='tm_toggle_set(<?php echo $setno; ?>);'><?php echo JText::_('FSJ_TM_ADDITIONAL_STRINGS'); ?></h4>
				<div class="header_bottom_border form_section_<?php echo $setno; ?>"></div>
				<table width='100%' class="form_table new_table form_section_<?php echo $setno; ?>" id="form_table_<?php echo $setno; ?>">
				</table>
			<?php endif; ?>
		</div>
	</form>
</div>

<style>
.navbar-fixed-bottom {
	display: none;
}
</style>