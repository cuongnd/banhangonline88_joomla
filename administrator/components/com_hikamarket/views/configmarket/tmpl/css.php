<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><form action="<?php echo hikamarket::completeLink('config', true); ?>" method="post"  name="adminForm" id="adminForm">
	<fieldset>
		<div class="header" style="float: left;"><?php
			if($this->new) {
				echo JText::_('HIKA_FILE').' : '.$this->type.'_<input type="text" name="filename" value="'.$this->filename.'" />';
			} else {
				echo JText::_('HIKA_FILE').' : '.$this->type.'_'.$this->filename.'.css';
			}
		?></div>
		<div class="toolbar" id="toolbar" style="float: right;">
			<button class="btn" type="button" onclick="window.hikashop.submitform('savecss', 'adminForm'); return false;"><?php echo JText::_('HIKA_SAVE'); ?></button>
		</div>
	</fieldset>
<?php
	echo $this->editor->displayCode('csscontent', $this->content);
?>
	<input type="hidden" name="option" value="<?php echo HIKAMARKET_COMPONENT; ?>" />
	<input type="hidden" name="task" value="savecss" />
	<input type="hidden" name="tmpl" value="component" />
	<input type="hidden" name="ctrl" value="config" />
<?php if($this->new) { ?>
	<input type="hidden" name="new" value="1" />
<?php } else { ?>
	<input type="hidden" name="file" value="<?php echo $this->type.'_'.$this->filename; ?>" />
<?php } ?>
	<input type="hidden" name="var" value="<?php echo JRequest::getCmd('var'); ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>
