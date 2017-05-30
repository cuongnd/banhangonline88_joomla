<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div id="page-security">
<?php if(acymailing_level(1)) {
	?>
	<fieldset class="adminform">
		<legend><?php echo JText::_( 'CAPTCHA' ); ?></legend>
		<table class="admintable" cellspacing="1">
			<tr>
				<td class="key" >
					<?php echo JText::_('ENABLE_CATCHA'); ?>
				</td>
				<td>
					<?php
						$js = "function updateCaptcha(newvalue){";
						$js .= "if(newvalue == 0) {window.document.getElementById('captchafield').style.display = 'none'; }else{window.document.getElementById('captchafield').style.display = ''; }";
						$js .= '}';
						$captchaClass = acymailing_get('class.acycaptcha');
						if($captchaClass->available()){
							$js .='window.addEvent(\'load\', function(){ updateCaptcha('.$this->config->get('captcha_enabled',0).'); });';
							echo JHTML::_('acyselect.booleanlist', "config[captcha_enabled]" , 'onclick="updateCaptcha(this.value)"',$this->config->get('captcha_enabled',0) );
						}else{
							$js .='window.addEvent(\'load\', function(){ updateCaptcha(0); });';
							echo '<input type="hidden" name="config[captcha_enabled]" value="0" />';
							echo $captchaClass->error;
						}

						$doc = JFactory::getDocument();
						$doc->addScriptDeclaration( $js );
					?>
				</td>
			</tr>
		</table>
		<table id="captchafield" width="100%">
			<tr>
				<td colspan="2">
					<table class="admintable" cellspacing="1">
						<tr>
							<td class="key">
								<?php echo acymailing_tooltip(JText::_('CAPTCHA_CHARS_DESC'), JText::_('CAPTCHA_CHARS'), '', JText::_('CAPTCHA_CHARS')); ?>
							</td>
							<td>
								<input class="inputbox" type="text" name="config[captcha_chars]" size="100" value="<?php echo $this->escape($this->config->get('captcha_chars','abcdefghijkmnpqrstwxyz23456798ABCDEFGHJKLMNPRSTUVWXYZ')); ?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php $secKey = $this->config->get('security_key');
								if(empty($secKey)){
									jimport('joomla.user.helper');
									$secKey = JUserHelper::genRandomPassword(30);
								}
								echo acymailing_tooltip(JText::sprintf('SECURITY_KEY_DESC','index.php?option=com_acymailing&ctrl=sub&task=optin&seckey='.$secKey), JText::_('SECURITY_KEY'), '', JText::_('SECURITY_KEY')); ?>
							</td>
							<td>
								<input class="inputbox" type="text" name="config[security_key]" style="width:200px" value="<?php echo $this->escape($secKey); ?>" />
							</td>
						</tr>
					</table>
				</td>
			</tr>
			<tr>
				<td width="50%">
					<fieldset class="adminform">
						<legend><?php echo JText::_('MODULE_VIEW'); ?></legend>
						<table class="admintable" cellspacing="1">
							<tr>
								<td class="key">
									<?php echo JText::_('CAPTCHA_NBCHAR'); ?>
								</td>
								<td>
									<input class="inputbox" type="text" name="config[captcha_nbchar_module]" style="width:50px" value="<?php echo intval($this->config->get('captcha_nbchar_module',3)); ?>" />
								</td>
							</tr>
							<tr>
								<td class="key">
									<?php echo JText::_('CAPTCHA_HEIGHT'); ?>
								</td>
								<td>
									<input class="inputbox" type="text" name="config[captcha_height_module]" style="width:100px" value="<?php echo intval($this->config->get('captcha_height_module',25)); ?>" />
								</td>
							</tr>
							<tr>
								<td class="key">
									<?php echo JText::_('CAPTCHA_WIDTH'); ?>
								</td>
								<td>
									<input class="inputbox" type="text" name="config[captcha_width_module]" style="width:100px" value="<?php echo intval($this->config->get('captcha_width_module',60)); ?>" />
								</td>
							</tr>
							<tr>
								<td class="key">
									<?php echo JText::_('CAPTCHA_BACKGROUND'); ?>
								</td>
								<td>
									<?php echo $this->elements->colortype->displayAll('captcha_background_module','config[captcha_background_module]',$this->config->get('captcha_background_module','#ffffff')); ?>
								</td>
							</tr>
							<tr>
								<td class="key">
									<?php echo JText::_('CAPTCHA_COLOR'); ?>
								</td>
								<td>
									<?php echo $this->elements->colortype->displayAll('captcha_color_module','config[captcha_color_module]',$this->config->get('captcha_color_module','#bbbbbb')); ?>
								</td>
							</tr>
						</table>
					</fieldset>
				</td>
				<td>
					<fieldset class="adminform">
						<legend><?php echo JText::_('COMPONENT_VIEW'); ?></legend>
						<table class="admintable" cellspacing="1">
							<tr>
								<td class="key">
									<?php echo JText::_('CAPTCHA_NBCHAR'); ?>
								</td>
								<td>
									<input class="inputbox" type="text" name="config[captcha_nbchar_component]" style="width:50px" value="<?php echo intval($this->config->get('captcha_nbchar_component',6)); ?>" />
								</td>
							</tr>
							<tr>
								<td class="key">
									<?php echo JText::_('CAPTCHA_HEIGHT'); ?>
								</td>
								<td>
									<input class="inputbox" type="text" name="config[captcha_height_component]" style="width:100px" value="<?php echo intval($this->config->get('captcha_height_component',25)); ?>" />
								</td>
							</tr>
							<tr>
								<td class="key">
									<?php echo JText::_('CAPTCHA_WIDTH'); ?>
								</td>
								<td>
									<input class="inputbox" type="text" name="config[captcha_width_component]" style="width:100px" value="<?php echo intval($this->config->get('captcha_width_component',120)); ?>" />
								</td>
							</tr>
							<tr>
								<td class="key">
									<?php echo JText::_('CAPTCHA_BACKGROUND'); ?>
								</td>
								<td>
									<?php echo $this->elements->colortype->displayAll('captcha_background_component','config[captcha_background_component]',$this->config->get('captcha_background_component','#ffffff')); ?>
								</td>
							</tr>
							<tr>
								<td class="key">
									<?php echo JText::_('CAPTCHA_COLOR'); ?>
								</td>
								<td>
									<?php echo $this->elements->colortype->displayAll('captcha_color_component','config[captcha_color_component]',$this->config->get('captcha_color_component','#bbbbbb')); ?>
								</td>
							</tr>
						</table>
					</fieldset>
				</td>
			</tr>
		</table>
	</fieldset>
	<?php
} ?>

	<fieldset class="adminform">
	<legend><?php echo JText::_('ADVANCED_EMAIL_VERIFICATION'); ?></legend>
		<table class="admintable" cellspacing="1">
			<tr>
				<td class="key" >
					<?php echo JText::_('CHECK_DOMAIN_EXISTS'); ?>
				</td>
				<td>
					<?php
					if(function_exists('getmxrr')){
						echo JHTML::_('acyselect.booleanlist', "config[email_checkdomain]" , '',$this->config->get('email_checkdomain',0) );
					}else{
						echo 'Function getmxrr not enabled';
					}
					 ?>
				</td>
			</tr>
			<tr>
				<td class="key" >
					<?php echo JText::sprintf('X_INTEGRATION','BotScout'); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.booleanlist', "config[email_botscout]" , '',$this->config->get('email_botscout',0) ); ?>
					<br/>API Key: <input class="inputbox" type="text" name="config[email_botscout_key]" style="width:100px" value="<?php echo $this->escape($this->config->get('email_botscout_key')) ?>" />
				</td>
			</tr>
			<tr>
				<td class="key" >
					<?php echo JText::sprintf('X_INTEGRATION','StopForumSpam'); ?>
				</td>
				<td>
					<?php echo JHTML::_('acyselect.booleanlist', "config[email_stopforumspam]" , '',$this->config->get('email_stopforumspam',0) ); ?>
				</td>
			</tr>
		</table>
	</fieldset>

	<fieldset class="adminform">
	<legend><?php echo JText::_( 'ACY_FILES' ); ?></legend>
		<table class="admintable" cellspacing="1">
			<tr>
				<td class="key" >
					<?php echo acymailing_tooltip(JText::_('ALLOWED_FILES_DESC'), JText::_('ALLOWED_FILES'), '', JText::_('ALLOWED_FILES')); ?>
				</td>
				<td>
					<input class="inputbox" type="text" name="config[allowedfiles]" style="width:250px" value="<?php echo $this->escape(strtolower(str_replace(' ','',$this->config->get('allowedfiles')))); ?>" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo acymailing_tooltip(JText::_('UPLOAD_FOLDER_DESC'), JText::_('UPLOAD_FOLDER'), '', JText::_('UPLOAD_FOLDER')); ?>
				</td>
				<td>
					<?php $uploadfolder = $this->config->get('uploadfolder'); if(empty($uploadfolder)) $uploadfolder = 'media/com_acymailing/upload'; ?>
					<input class="inputbox" type="text" name="config[uploadfolder]" style="width:250px" value="<?php echo $this->escape($uploadfolder); ?>" />
				</td>
			</tr>
			<tr>
				<td class="key">
					<?php echo acymailing_tooltip(JText::_('MEDIA_FOLDER_DESC'), JText::_('MEDIA_FOLDER'), '', JText::_('MEDIA_FOLDER')); ?>
				</td>
				<td>
					<?php $mediafolder = $this->config->get('mediafolder'); if(empty($mediafolder)) $mediafolder = 'media/com_acymailing/upload'; ?>
					<input class="inputbox" type="text" name="config[mediafolder]" style="width:250px" value="<?php echo $this->escape($mediafolder); ?>" />
				</td>
			</tr>
		</table>
	</fieldset>
<?php if(acymailing_level(1)) {
?>
	<fieldset class="adminform">
	<legend><?php echo JText::_( 'DATABASE_MAINTENANCE' ); ?></legend>
		<table class="admintable" cellspacing="1">
			<tr>
				<td class="key" >
					<?php echo acymailing_tooltip(JText::_('DATABASE_MAINTENANCE_DESC').'<br />'.JText::_('DATABASE_MAINTENANCE_DESC2'), JText::_('DELETE_DETAILED_STATS'), '', JText::_('DELETE_DETAILED_STATS')); ?>
				</td>
				<td>
					<?php echo $this->elements->delete_stats; ?>
				</td>
			</tr>
			<tr>
				<td class="key">
				<?php echo acymailing_tooltip(JText::_('DATABASE_MAINTENANCE_DESC').'<br />'.JText::_('DATABASE_MAINTENANCE_DESC2'), JText::_('DELETE_HISTORY'), '', JText::_('DELETE_HISTORY')); ?>
				</td>
				<td>
					<?php echo $this->elements->delete_history; ?>
				</td>
			</tr>
		</table>
	</fieldset>
	<?php
} ?>
</div>
