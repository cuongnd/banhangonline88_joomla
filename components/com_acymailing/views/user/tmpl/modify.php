<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="acymodifyform">
<?php if($this->values->show_page_heading){ ?>
<h1 class="contentheading<?php echo $this->values->suffix; ?>"><?php echo $this->values->page_heading; ?></h1>
<?php } ?>
<?php if(!empty($this->introtext)){ echo '<span class="acymailing_introtext">'.$this->introtext.'</span>'; } ?>
<form action="<?php echo JRoute::_( 'index.php' );?>" method="post" name="adminForm" id="adminForm" <?php if(!empty($this->fieldsClass->formoption)) echo $this->fieldsClass->formoption; ?> >
	<fieldset class="adminform acy_user_info">
		<legend><span><?php echo JText::_( 'USER_INFORMATIONS' ); ?></span></legend>
		<table cellspacing="1" align="center" width="100%" id="acyuserinfo">
		<?php if(acymailing_level(3)){
				foreach($this->extraFields as $fieldName => $oneExtraField) {
					echo '<tr id="tr'.$fieldName.'"><td width="150" class="key">'.$this->fieldsClass->getFieldName($oneExtraField).'</td><td>';
					if(in_array($fieldName,array('name','email')) AND !empty($this->subscriber->userid)){echo $this->subscriber->$fieldName; }
					else{echo $this->fieldsClass->display($oneExtraField,@$this->subscriber->$fieldName,'data[subscriber]['.$fieldName.']'); }
					echo '</td></tr>';
				}
			}else{
				if(!empty($this->fieldsToDisplay) && (strpos($this->fieldsToDisplay, 'name') !== false || strpos($this->fieldsToDisplay, 'default') !== false || strpos($this->fieldsToDisplay, 'all') !== false)){ ?>
			<tr id="trname">
				<td width="150" class="key">
					<label for="field_name">
					<?php echo JText::_( 'JOOMEXT_NAME' ); ?>
					</label>
				</td>
				<td>
				<?php
				if(empty($this->subscriber->userid)){
						echo '<input type="text" name="data[subscriber][name]" id="field_name" class="inputbox" style="width:200px;" value="'.$this->escape(@$this->subscriber->name).'" />';
				}else{
					echo $this->subscriber->name;
				}
				?>
				</td>
			</tr>
				<?php }
				if(!empty($this->fieldsToDisplay) && (strpos($this->fieldsToDisplay, 'email') !== false || strpos($this->fieldsToDisplay, 'default') !== false || strpos($this->fieldsToDisplay, 'all') !== false)){ ?>
			<tr id="tremail">
				<td class="key">
					<label for="field_email">
					<?php echo JText::_( 'JOOMEXT_EMAIL' ); ?>
					</label>
				</td>
				<td>
					<?php
					if(empty($this->subscriber->userid)){
						echo '<input class="inputbox" type="text" name="data[subscriber][email]" id="field_email" style="width:200px;" value="'.$this->escape(@$this->subscriber->email).'" />';
					}else{
						echo $this->subscriber->email;
					}
					?>
				</td>
			</tr>
				<?php }
				if(!empty($this->fieldsToDisplay) && (strpos($this->fieldsToDisplay, 'html') !== false || strpos($this->fieldsToDisplay, 'default') !== false || strpos($this->fieldsToDisplay, 'all') !== false)){ ?>
			<tr id="trhtml">
				<td class="key">
					<?php echo JText::_( 'RECEIVE' ); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.booleanlist', "data[subscriber][html]" , '',$this->subscriber->html,JText::_('HTML'),JText::_('JOOMEXT_TEXT'),'user_html'); ?>
				</td>
			</tr>
				<?php }
			}
		if(empty($this->subscriber->subid) AND $this->config->get('captcha_enabled')){ ?>
			<tr id="trcaptcha">
				<td class="captchakeycomponent">
					<img title="<?php echo JText::_('ERROR_CAPTCHA'); ?>" id="captcha_picture" width="<?php echo $this->config->get('captcha_width_component') ?>" height="<?php echo $this->config->get('captcha_height_component') ?>" class="captchaimagecomponent" src="<?php if(ACYMAILING_J16){ echo JRoute::_('index.php?option=com_acymailing&ctrl=captcha&val='.rand(0,10000).'&no_html=1'); }else{ echo rtrim(JURI::root(),'/').'/index.php?option=com_acymailing&amp;ctrl=captcha&amp;val='.rand(0,10000).'&amp;no_html=1';} ?>" alt="captcha" />
					<span class="refreshCaptcha" onClick="refreshCaptcha()">&nbsp;</span>
				</td>
				<td class="captchafieldcomponent">
					<input title="<?php echo JText::_('ERROR_CAPTCHA'); ?>" id="user_captcha" class="inputbox captchafield" type="text" name="acycaptcha" style="width:50px" />
				</td>
			</tr>
			<?php }
			?>
		</table>
	</fieldset>
	<?php if($this->displayLists){?>
	<fieldset class="adminform acy_subscription_list">
		<legend><span><?php echo JText::_( 'SUBSCRIPTION' ); ?></span></legend>
		<table cellspacing="1" align="center" width="100%" id="acyusersubscription">
			<thead>
				<tr>
					<th  nowrap="nowrap" align="center" width="150">
					<?php echo JText::_( 'SUBSCRIBE' );?>
					</th>
					<th  nowrap="nowrap" align="center">
					</th>
				</tr>
			</thead>
			<tbody>
				<?php
				$k = 0;
				foreach($this->subscription as $row){
					if(empty($row->published) OR !$row->visible) continue;
					$listClass = 'acy_list_status_' . str_replace('-','m',(int) @$row->status);
					?>
				<tr class="<?php echo "row$k $listClass"; ?>">
					<td align="center" nowrap="nowrap" valign="top" class="acystatus">
						<span><?php echo $this->status->display("data[listsub][".$row->listid."][status]",@$row->status); ?></span>
					</td>
					<td valign="top">
						<div class="list_name"><?php echo $row->name ?></div>
						<div class="list_description"><?php echo $row->description ?></div>
					</td>
				</tr>
				<?php
					$k = 1 - $k;
				} ?>
			</tbody>
		</table>
	</fieldset>
	<?php } ?>
	<br/>
	<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>" />
	<input type="hidden" name="task" value="savechanges" />
	<input type="hidden" name="ctrl" value="user" />
	<?php echo JHTML::_( 'form.token' ); ?>
	<input type="hidden" name="subid" value="<?php echo $this->subscriber->subid; ?>" />
	<?php if(JRequest::getCmd('tmpl') == 'component'){ ?><input type="hidden" name="tmpl" value="component" /><?php } ?>
	<input type="hidden" name="key" value="<?php echo $this->subscriber->key; ?>" />
	<p class="acymodifybutton">
		<input class="button btn btn-primary" type="submit" onclick="return checkChangeForm();" value="<?php echo empty($this->subscriber->subid) ? $this->escape(JText::_('SUBSCRIBE')) :  $this->escape(JText::_('SAVE_CHANGES'))?>"/>
	</p>
</form>
<?php if(!empty($this->finaltext)){ echo '<span class="acymailing_finaltext">'.$this->finaltext.'</span>'; } ?>
</div>
