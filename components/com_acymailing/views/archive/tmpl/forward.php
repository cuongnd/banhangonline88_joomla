<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><h1 class="componentheading"><?php echo JText::_('FORWARD_FRIEND'); ?></h1>
<form action="<?php echo JRoute::_( 'index.php' );?>" method="post" name="adminForm" id="adminForm" >
	<div class="acymailing_forward">
		<table id="forward_sender_information">
			<tbody id="friend_table">
				<tr>
					<td>
						<label for="sendername"><?php echo JText::_('YOUR_NAME'); ?></label>
					</td>
					<td>
						<input id="sendername" type="text" class="inputbox required" name="sendername" value="<?php echo $this->escape($this->senderName); ?>" style="width:100px"/>
					</td>
				</tr>
				<tr>
					<td>
						<label for="senderemail"><?php echo JText::_('YOUR_EMAIL'); ?></label>
					</td>
					<td>
						<input id="senderemail" type="text" class="inputbox required" name="senderemail" value="<?php echo $this->escape($this->senderMail); ?>" style="width:100px"/>
					</td>
				</tr>
				<tr>
					<td>
						<label for="forwardname"><?php echo JText::_('FRIEND_NAME'); ?></label>
					</td>
					<td>
						<input id="forwardname" type="text" class="inputbox required" name="forwardusers[0][name]" value="" style="width:100px"/>
					</td>
				</tr>
				<tr>
					<td>
						<label for="forwardemail"><?php echo JText::_('FRIEND_EMAIL'); ?></label>
					</td>
					<td>
						<input id="forwardemail" type="text" class="inputbox required" name="forwardusers[0][email]" value="" style="width:100px"/>
					</td>
				</tr>
			</tbody>
		</table>
		<div id="forward_addfriend">
			<a onClick="addLine();return false;" ><?php echo JText::_('ADD_FRIEND');?></a>
		</div>
		<div id="forward_sender_message">
			<label for="forwardmsg"><?php echo JText::_('ADD_FORWARD_MESSAGE'); ?></label><br/>
			<textarea cols="60" rows="5" name="forwardmsg" id="forwardmsg" ></textarea>
		</div>
		<?php if($this->config->get('forward') == 2 AND acymailing_level(1)){ ?>
			<br/><div id="captcha_forward">
				<?php
				if(ACYMAILING_J16){
					$image = '<img id="captcha_picture" title="'.JText::_('ERROR_CAPTCHA').'" width="'.$this->config->get('captcha_width_component').'" height="'.$this->config->get('captcha_height_component').'" class="captchaimagecomponent" src="'.JRoute::_('index.php?option=com_acymailing&ctrl=captcha&val='.rand(0,10000).'&no_html=1').'" alt="captcha" />';
				}else{
					$image = '<img id="captcha_picture" title="'.JText::_('ERROR_CAPTCHA').'" width="'.$this->config->get('captcha_width_component').'" height="'.$this->config->get('captcha_height_component').'" class="captchaimagecomponent" src="'.rtrim(JURI::root(),'/').'/index.php?option=com_acymailing&amp;ctrl=captcha&amp;val='.rand(0,10000).'&amp;no_html=1" alt="captcha" />';
				}
				echo $image.'<span class="refreshCaptchaForward" onClick="refreshCaptcha()">&nbsp;</span>';
				?>
				<input id="user_captcha" title="<?php echo JText::_('ERROR_CAPTCHA'); ?>" class="inputbox captchafield required" type="text" name="acycaptcha" style="width:50px" /></td>
			</div>
		<?php } ?>
		<input type="submit" class="btn btn-primary" value="<?php echo JText::_('SEND',true); ?>"/>
	</div>
	<input type="hidden" name="key" value="<?php echo $this->mail->key;?>" />
	<input type="hidden" name="option" value="<?php echo ACYMAILING_COMPONENT; ?>" />
	<input type="hidden" name="task" value="doforward" />
	<input type="hidden" name="ctrl" value="archive" />
	<input type="hidden" name="mailid" value="<?php echo $this->mail->mailid; ?>" />
	<?php if(!empty($this->receiver->subid)){ ?>
		<input type="hidden" name="subid" value="<?php echo $this->receiver->subid.'-'.$this->receiver->key ?>" />
	<?php }
	echo JHTML::_( 'form.token' );
	if(JRequest::getCmd('tmpl') == 'component'){ ?><input type="hidden" name="tmpl" value="component" /><?php } ?>
</form>

<?php include(dirname(__FILE__).DS.'view.php'); ?>
