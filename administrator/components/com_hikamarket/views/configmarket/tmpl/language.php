<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><form action="<?php echo hikamarket::completeLink('config',true);?>" method="post" name="adminForm" id="adminForm">
	<fieldset>
		<div class="header" style="float: left;"><?php echo JText::_('HIKA_FILE').' : '.$this->file->name; ?></div>
		<div class="toolbar" id="toolbar" style="float: right;">
			<button class="btn" type="button" onclick="window.hikashop.submitform('savelanguage', 'adminForm'); return false;"><?php echo JText::_('HIKA_SAVE'); ?></button>
			<button class="btn" type="button" onclick="window.hikashop.submitform('share', 'adminForm'); return false;"><?php echo JText::_('SHARE'); ?></button>
		</div>
	</fieldset>
	<fieldset class="adminform">
		<legend><?php echo JText::_('HIKA_FILE').' : '.$this->file->name; ?></legend>
		<textarea style="width:100%;" rows="18" name="content" id="translation" ><?php echo @$this->file->content;?></textarea>
	</fieldset>
	<fieldset class="adminform">
		<legend><?php echo JText::_('HIKAMARKET_OVERRIDE').' : '; ?></legend>
		<?php echo JText::_('OVERRIDE_WITH_EXPLANATION'); ?>
		<textarea style="width:100%;" rows="18" name="content_override" id="translation_override" ><?php echo $this->override_content;?></textarea>
	</fieldset>
	<div class="clr"></div>
	<input type="hidden" name="code" value="<?php echo $this->file->name; ?>" />
	<input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="ctrl" value="config" />
	<?php echo JHTML::_('form.token'); ?>
</form>
