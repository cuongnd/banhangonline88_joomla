<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
if(!empty($this->element->mail->dst_email) && is_array($this->element->mail->dst_email)){
	$this->element->mail->dst_email = implode(',',$this->element->mail->dst_email);
}
?><div class="hikam_toolbar">
	<div class="hikam_toolbar_btn hikam_btn_32">
		<div class="hikam_toolbar_right">
			<div class="btn">
				<a href="#send" onclick="return window.hikamarket.submitform('sendmail','hikamarket_mail_form');"><span class="btnIcon iconM-32-email"></span><span class="btnName"><?php echo JText::_('SEND_EMAIL'); ?></span></a>
			</div>
		</div>
		<div style="clear:right"></div>
	</div>
</div>
<form action="<?php echo hikamarket::completeLink('order',true); ?>" method="post" name="hikamarket_mail_form" id="hikamarket_mail_form">
	<table class="hikam_options">
		<tr>
			<td class="key">
				<label for="data[order][mail][from_email]"><?php echo JText::_('FROM_ADDRESS'); ?></label>
			</td>
			<td>
<?php if($this->vendor->vendor_id <= 1) { ?>
				<input type="text" name="data[order][mail][from_email]" size="80" value="<?php echo $this->escape($this->element->mail->from_email);?>" />
<?php } else {
	echo $this->escape($this->element->mail->from_email);
} ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="data[order][mail][from_name]"><?php echo JText::_('FROM_NAME'); ?></label>
			</td>
			<td>
<?php if($this->vendor->vendor_id <= 1) { ?>
				<input type="text" name="data[order][mail][from_name]" size="80" value="<?php echo $this->escape($this->element->mail->from_name);?>" />
<?php } else {
	echo $this->escape($this->element->mail->from_name);
} ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="data[order][mail][dst_email]"><?php echo JText::_('TO_ADDRESS'); ?></label>
			</td>
			<td>
				<input type="text" name="data[order][mail][dst_email]" size="80" value="<?php echo $this->escape($this->element->mail->dst_email);?>" />
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="data[order][mail][dst_name]"><?php echo JText::_('TO_NAME'); ?></label>
			</td>
			<td>
				<input type="text" name="data[order][mail][dst_name]" size="80" value="<?php echo $this->escape($this->element->mail->dst_name);?>" />
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="data[order][mail][subject]"><?php echo JText::_('EMAIL_SUBJECT'); ?></label>
			</td>
			<td>
				<input type="text" name="data[order][mail][subject]" size="80" value="<?php echo $this->escape($this->element->mail->subject);?>" />
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="hikashop_mail_body"><?php echo JText::_('HTML_VERSION'); ?></label>
			</td>
			<td></td>
		</tr>
		<tr>
			<td colspan="2">
				<?php echo $this->editor->display(); ?>
			</td>
		</tr>
		<tr>
			<td class="key">
				<label for="data[order][mail][altbody]"><?php echo JText::_('TEXT_VERSION'); ?></label>
			</td>
			<td>
				<textarea cols="60" rows="10" name="data[order][mail][altbody]"><?php echo $this->escape($this->element->mail->altbody); ?></textarea>
			</td>
		</tr>
	</table>
	<input type="hidden" name="data[order][mail][html]" value="<?php echo $this->element->mail->html;?>" />
	<input type="hidden" name="data[order][history][history_type]" value="email sent" />
	<input type="hidden" name="data[order][order_id]" value="<?php echo @$this->element->order_id;?>" />
	<input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>" />
	<input type="hidden" name="task" value="mail" />
	<input type="hidden" name="ctrl" value="order" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
