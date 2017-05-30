<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><div class="iframedoc" id="iframedoc"></div>
<form action="<?php echo hikaserial::completeLink('import'); ?>" method="post"  name="adminForm" id="adminForm" enctype="multipart/form-data">
	<fieldset class="adminform">
	<legend><?php echo JText::_('IMPORT'); ?></legend>
		<?php echo JHTML::_('hikaselect.radiolist', $this->importValues, 'importfrom', 'class="inputbox" size="1" onclick="hikaserial.tabSelect(\'import_tabs\',\'tab\',this.value);"', 'value', 'text', $this->defaultValue); ?>
	</fieldset>
	<div id="import_tabs">
<?php
$style = '';
foreach($this->importData as $data) {
?>
	<div class="tab" id="<?php echo $data['key'];?>"<?php echo $style; $style = ' style="display:none;"';?>>
	<fieldset class="adminform">
		<legend><?php echo $data['text'];?></legend>
		<?php
			$file = dirname(__FILE__) . DS . $data['key'] . '.php';
			if(file_exists($file))
				include($file);
		?>
	</fieldset>
	</div>
<?php
}
?>
	</div>
	<input type="hidden" name="option" value="<?php echo HIKASERIAL_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>
