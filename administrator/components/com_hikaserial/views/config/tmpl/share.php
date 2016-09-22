<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><form action="index.php?tmpl=component&amp;option=<?php echo HIKASERIAL_COMPONENT ?>" method="post"  name="adminForm" id="adminForm" >
	<fieldset>
		<div class="header" style="float: left;"><?php echo JText::_('SHARE').' : '.$this->file->name; ?></div>
		<div class="toolbar" id="toolbar" style="float: right;">
			<button class="btn" type="button" onclick="javascript:submitbutton('send')"><?php echo JText::_('SHARE'); ?></button>
		</div>
	</fieldset>
	<fieldset class="adminform">
		<?php hikaserial::display(JText::_('SHARE_HIKASERIAL_CONFIRMATION_1').'<br/>'.JText::_('SHARE_HIKASERIAL_CONFIRMATION_2').'<br/>'.JText::_('SHARE_CONFIRMATION_3'),'info'); ?><br/>
		<textarea cols="100" rows="8" name="mailbody">Hi Hikari Software team,
Here is a new version of the language file for HikaSerial, I translated few more strings...</textarea>
	</fieldset>
	<div class="clr"></div>
	<input type="hidden" name="code" value="<?php echo $this->file->name; ?>" />
	<input type="hidden" name="option" value="<?php echo HIKASERIAL_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="ctrl" value="config" />
	<?php echo JHTML::_('form.token'); ?>
</form>
