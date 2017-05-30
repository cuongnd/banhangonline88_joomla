<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="acy_content">
<div id="iframedoc"></div>
<form action="index.php?option=com_acymailing&amp;ctrl=fields" method="post" name="adminForm"  id="adminForm" autocomplete="off">
	<table cellspacing="1" width="100%">
		<tr>
		<td width="60%" valign="top">
			<table class="paramlist admintable">
				<tr>
					<td class="key">
					<label for="name">
						<?php echo JText::_( 'FIELD_LABEL' ); ?>
					</label>
					</td>
					<td>
						<input type="text" name="data[fields][fieldname]" id="name" class="inputbox" style="width:200px" value="<?php echo $this->escape(@$this->field->fieldname); ?>" />
					</td>
				</tr>
				<tr>
					<td class="key">
						<label for="published">
							<?php echo JText::_( 'ACY_PUBLISHED' ); ?>
						</label>
					</td>
					<td>
						<?php echo JHTML::_('acyselect.booleanlist', "data[fields][published]" , '',@$this->field->published); ?>
					</td>
				</tr>
				<tr class="columnname" style="display:none">
					<td class="key">
					<label for="namekey">
						<?php echo JText::_( 'FIELD_COLUMN' ); ?>
					</label>
					</td>
					<td>
					<?php if(empty($this->field->fieldid)){?>
						<input type="text" name="data[fields][namekey]" id="namekey" class="inputbox" style="width:200px" value="" />
					<?php }else { echo $this->field->namekey; } ?>
					</td>
				</tr>
				<tr <?php if(!empty($this->field->fieldid) AND substr($this->field->namekey,0,11) == 'customtext_') echo 'style="display:none"'; ?>>
					<td class="key">
					<label for="fieldtype">
						<?php echo JText::_( 'FIELD_TYPE' ); ?>
					</label>
					</td>
					<td>
						<?php echo $this->fieldtype->display('data[fields][type]',$this->field->type); ?>
					</td>
				</tr>
				<?php if(empty($this->field->core)){ ?>
				<tr class="required"  style="display:none">
					<td class="key">
						<label for="required">
							<?php echo JText::_( 'REQUIRED' ); ?>
						</label>
					</td>
					<td>
						<?php echo JHTML::_('acyselect.booleanlist', "data[fields][required]" , '',@$this->field->required); ?>
					</td>
				</tr>

				<tr class="required"  style="display:none">
					<td class="key">
						<label for="errormessage">
							<?php echo JText::_( 'FIELD_ERROR' ); ?>
						</label>
					</td>
					<td>
						<input type="text" id="errormessage" size="80" name="fieldsoptions[errormessage]" value="<?php echo $this->escape(@$this->field->options['errormessage']); ?>"/>
					</td>
				</tr>
				<?php } ?>
				<tr class="checkcontent" style="display:none">
					<td class="key">
					<label for="checkcontent">
						<?php echo JText::_( 'FIELD_AUTHORIZED_CONTENT' ); ?>
					</label>
					</td>
					<td>
						<?php echo $this->fieldCheckContent; ?>
					</td>
				</tr>
				<tr class="checkcontent"  style="display:none">
					<td class="key">
						<label for="errormessagecheckcontent">
							<?php echo JText::_( 'FIELD_ERROR_AUTHORIZED_CONTENT' ); ?>
						</label>
					</td>
					<td>
						<input type="text" id="errormessagecheckcontent" size="80" name="fieldsoptions[errormessagecheckcontent]" value="<?php echo $this->escape(@$this->field->options['errormessagecheckcontent']); ?>"/>
					</td>
				</tr>
				<tr class="default" style="display:none">
					<td class="key">
					<label for="default">
						<?php echo JText::_( 'FIELD_DEFAULT' ); ?>
					</label>
					</td>
					<td>
						<?php echo $this->fieldsClass->display($this->field,@$this->field->default,'data[fields][default]',false,'',true); ?>
					</td>
				</tr>
				<tr class="cols" style="display:none">
					<td class="key">
					<label for="cols">
						<?php echo JText::_( 'FIELD_COLUMNS' ); ?>
					</label>
					</td>
					<td>
						<input type="text"  style="width:50px" name="fieldsoptions[cols]" id="cols" class="inputbox" value="<?php echo $this->escape(@$this->field->options['cols']); ?>"/>
					</td>
				</tr>
				<tr class="rows" style="display:none">
					<td class="key">
					<label for="rows">
						<?php echo JText::_( 'FIELD_ROWS' ); ?>
					</label>
					</td>
					<td>
						<input type="text"  style="width:50px" name="fieldsoptions[rows]" id="rows" class="inputbox" value="<?php echo $this->escape(@$this->field->options['rows']); ?>"/>
					</td>
				</tr>
				<tr class="size" style="display:none">
					<td class="key">
						<label for="size">
							<?php echo JText::_( 'FIELD_SIZE' ); ?>
						</label>
					</td>
					<td>
						<input type="text" id="size" style="width:50px" name="fieldsoptions[size]" value="<?php echo $this->escape(@$this->field->options['size']); ?>"/>
					</td>
				</tr>
				<tr class="format" style="display:none">
					<td class="key">
						<label for="format">
							<?php echo JText::_( 'FORMAT' ); ?>
						</label>
					</td>
					<td>
						<input type="text" id="format" name="fieldsoptions[format]" value="<?php echo $this->escape(@$this->field->options['format']); ?>"/>
					</td>
				</tr>
				<tr class="customtext"  style="display:none">
					<td class="key">
						<label for="size">
							<?php echo JText::_( 'CUSTOM_TEXT' ); ?>
						</label>
					</td>
					<td>
						<textarea cols="50" rows="10" name="fieldcustomtext"><?php echo @$this->field->options['customtext']; ?></textarea>
					</td>
				</tr>
				<tr class="multivalues"  style="display:none">
					<td class="key" valign="top">
					<label for="value">
						<?php echo JText::_( 'FIELD_VALUES' ); ?>
					</label>
					</td>
					<td>
						<table>
						<tbody  id="tablevalues">
						<tr><td><?php echo JText::_('FIELD_VALUE')?></td><td><?php echo JText::_('FIELD_TITLE'); ?></td><td><?php echo JText::_('DISABLED'); ?></td><td></td></tr>
						<?php $optionid = 0;
							if(!empty($this->field->value) AND is_array($this->field->value)){
							foreach($this->field->value as $title => $onevalue){?>
								<tr><td><input style="width:150px;" id="option<?php echo $optionid; ?>title" type="text" name="fieldvalues[title][]" value="<?php echo $this->escape($title); ?>" /></td>
								<td><input style="width:180px;" id="option<?php echo $optionid; ?>value" type="text" name="fieldvalues[value][]" value="<?php echo $this->escape($onevalue->value); ?>" /></td>
								<td><select class="chzn-done" style="width:80px;" id="option<?php echo $optionid; ?>disabled" name="fieldvalues[disabled][]" class="inputbox">
									<option value="0"><?php echo JText::_('JOOMEXT_NO'); ?></option>
									<option <?php if(!empty($onevalue->disabled)) echo 'selected="selected"'; ?> value="1"><?php echo JText::_('JOOMEXT_YES'); ?></option>
								</select></td>
								<td><a onclick="acymove(<?php echo $optionid; ?>,1);return false;" href="#"><img src="<?php echo ACYMAILING_IMAGES; ?>movedown.png" alt=" ˇ "/></a><a onclick="acymove(<?php echo $optionid; ?>,-1);return false;" href="#"><img src="<?php echo ACYMAILING_IMAGES; ?>moveup.png" alt=" ˆ "/></a></td>
								</tr>
						<?php $optionid++; } }?>
						<tr><td><input style="width:150px;" id="option<?php echo $optionid; ?>title" type="text" name="fieldvalues[title][]" value="" /></td>
						<td><input style="width:180px;" id="option<?php echo $optionid; ?>value" type="text" name="fieldvalues[value][]" value="" /></td>
						<td><select class="chzn-done" style="width:80px;" id="option<?php echo $optionid; ?>disabled" name="fieldvalues[disabled][]" class="inputbox">
									<option value="0"><?php echo JText::_('JOOMEXT_NO'); ?></option>
									<option value="1"><?php echo JText::_('JOOMEXT_YES'); ?></option>
								</select></td>
								<td><a onclick="acymove(<?php echo $optionid; ?>,1);return false;" href="#"><img src="<?php echo ACYMAILING_IMAGES; ?>movedown.png" alt=" ˇ "/></a><a onclick="acymove(<?php echo $optionid; ?>,-1);return false;" href="#"><img src="<?php echo ACYMAILING_IMAGES; ?>moveup.png" alt=" ˆ "/></a></td>
								</tr></tbody></table>
						<a class="btn" onclick="addLine();return false;" href='#' title="<?php echo $this->escape(JText::_('FIELD_ADDVALUE')); ?>"><?php echo JText::_('FIELD_ADDVALUE'); ?></a>
					</td>
				</tr>
			</table>
		</td>
		<td valign="top">
			<fieldset class="adminform">
				<legend><?php echo JText::_('FRONTEND'); ?></legend>
				<table class="admintable">
					<tr>
						<td class="key">
							<label for="frontcomp">
								<?php echo JText::_( 'DISPLAY_ACYPROFILE' ); ?>
							</label>
						</td>
						<td>
							<?php echo JHTML::_('acyselect.booleanlist', "data[fields][frontcomp]" , '',@$this->field->frontcomp); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<label for="frontlisting">
								<?php echo JText::_( 'DISPLAY_ACYLISTING' ); ?>
							</label>
						</td>
						<td>
							<?php echo JHTML::_('acyselect.booleanlist', "data[fields][frontlisting]" , '',@$this->field->frontlisting); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<label for="frontjoomlaregistration">
								<?php echo JText::_( 'DISPLAY_FRONTJOOMLEREGISTRATION' ); ?>
							</label>
						</td>
						<td>
							<?php echo JHTML::_('acyselect.booleanlist', "data[fields][frontjoomlaregistration]" , '',@$this->field->frontjoomlaregistration); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<label for="frontjoomlaprofile">
								<?php echo JText::_( 'DISPLAY_JOOMLAPROFILE' ); ?>
							</label>
						</td>
						<td>
							<?php echo JHTML::_('acyselect.booleanlist', "data[fields][frontjoomlaprofile]" , '',@$this->field->frontjoomlaprofile); ?>
						</td>
					</tr>
				</table>
			</fieldset>
			<fieldset class="adminform">
				<legend><?php echo JText::_('BACKEND'); ?></legend>
				<table class="admintable" style="min-width:300px">
					<tr>
						<td class="key">
							<label for="backend">
								<?php echo JText::_( 'DISPLAY_ACYPROFILE' ); ?>
							</label>
						</td>
						<td>
							<?php echo JHTML::_('acyselect.booleanlist', "data[fields][backend]" , '',@$this->field->backend); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<label for="listing">
								<?php echo JText::_( 'DISPLAY_ACYLISTING' ); ?>
							</label>
						</td>
						<td>
							<?php echo JHTML::_('acyselect.booleanlist', "data[fields][listing]" , '',@$this->field->listing); ?>
						</td>
					</tr>
					<tr>
						<td class="key">
							<label for="joomlaprofile">
								<?php echo JText::_( 'DISPLAY_JOOMLAPROFILE' ); ?>
							</label>
						</td>
						<td>
							<?php echo JHTML::_('acyselect.booleanlist', "data[fields][joomlaprofile]" , '',@$this->field->joomlaprofile); ?>
						</td>
					</tr>
				</table>
			</fieldset>
			<?php if(!empty($this->field->fieldid)){ ?>
			<br /><br />
			<fieldset class="adminform">
			<legend><?php echo JText::_('ACY_PREVIEW'); ?></legend>
			<table class="admintable"><tr><td class="key"><?php $this->fieldsClass->suffix='preview'; echo $this->fieldsClass->getFieldName($this->field); ?></td><td><?php echo $this->fieldsClass->display($this->field,$this->field->default,'data[subscriber]['.$this->field->namekey.']'); ?></td></tr></table>
			</fieldset>
			<fieldset class="adminform">
			<legend>HTML</legend>
			<textarea style="width:95%" rows="5"><?php echo htmlentities($this->fieldsClass->display($this->field,$this->field->default,'user['.$this->field->namekey.']')); ?></textarea>
			</fieldset>
			<?php
			} ?>
		</td>
		</tr>
	</table>
	<?php if(!empty($this->field->fieldid) AND in_array($this->field->type,array('radio','singledropdown','checkbox','multipledropdown'))){
				$this->fieldsClass->chart('subscriber',$this->field);
			}
	?>
	<div class="clr"></div>

	<input type="hidden" name="cid[]" value="<?php echo @$this->field->fieldid; ?>" />
	<input type="hidden" name="option" value="com_acymailing" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="ctrl" value="fields" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
</div>
