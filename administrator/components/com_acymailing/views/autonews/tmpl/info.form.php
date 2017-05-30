<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><table class="adminform" width="100%">
	<tr>
		<td>
			<label for="subject">
				<?php echo JText::_( 'JOOMEXT_SUBJECT' ); ?>
			</label>
		</td>
		<td>
			<input type="text" name="data[mail][subject]" id="subject" class="inputbox" style="width:80%" value="<?php echo $this->escape(@$this->mail->subject); ?>" />
		</td>
		<td>
			<label for="published">
				<?php echo JText::_( 'ACY_PUBLISHED' ); ?>
			</label>
		</td>
		<td>
			<?php echo JHTML::_('acyselect.booleanlist', "data[mail][published]" , '',$this->mail->published); ?>
		</td>
	</tr>
	<tr>
		<td>
			<label for="alias">
				<?php echo JText::_( 'JOOMEXT_ALIAS' ); ?>
			</label>
		</td>
		<td>
			<input class="inputbox" type="text" name="data[mail][alias]" id="alias" style="width:60%" value="<?php echo $this->escape(@$this->mail->alias); ?>" />
		</td>
		<td>
			<?php echo JText::_( 'SEND_HTML' ); ?>
		</td>
		<td>
			<?php echo JHTML::_('acyselect.booleanlist', "data[mail][html]" , 'onchange="updateAcyEditor(this.value)"',$this->mail->html); ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo JText::_( 'NEXT_GENERATE' ); ?>
		</td>
		<td>
			<?php if(empty($this->mail->senddate)) $this->mail->senddate = time(); echo JHTML::_('calendar', acymailing_getDate($this->mail->senddate,'%Y-%m-%d %H:%M'), 'data[mail][senddate]','senddate','%Y-%m-%d %H:%M',array('style'=>'width:160px')); ?>
		</td>
		<td>
			<?php echo JText::_( 'GENERATE_FREQUENCY' ); ?>
		</td>
		<td>
			<?php echo $this->frequencyType->displayFrequency('data[mail][frequency]',@$this->mail->frequency, 3); ?>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo JText::_( 'ISSUE_NB' ); ?>
		</td>
		<td>
			<input class="inputbox" type="text" name="data[mail][params][issuenb]" style="width:50px" value="<?php echo empty($this->mail->params['issuenb']) ? 1 : $this->mail->params['issuenb']; ?>" />
		</td>
		<td>
			<?php echo JText::_( 'LAST_RUN' ); ?>
		</td>
		<td>
			<input type="text" class="inputbox"  value="<?php echo $this->escape(acymailing_getDate(@$this->mail->params['lastgenerateddate'],'%Y-%m-%d %H:%M')); ?>" name="data[mail][params][lastgenerateddate]"/>
		</td>
	</tr>
	<tr>
		<td>
			<?php echo JText::_( 'GENERATE_MODE' ); ?>
		</td>
		<td>
			<?php echo $this->generatingMode->display('data[mail][params][generate]',@$this->mail->params['generate']); ?>
		</td>
		<td>
			<?php echo JText::_( 'NOTIFICATION_TO' ); ?>
		</td>
		<td>
			<input class="inputbox" type="text" name="data[mail][params][generateto]" id="generateto" style="width:200px" value="<?php echo $this->escape(@$this->mail->params['generateto']); ?>" /> <?php echo $this->values->editnotification; ?>
		</td>
	</tr>
</table>
