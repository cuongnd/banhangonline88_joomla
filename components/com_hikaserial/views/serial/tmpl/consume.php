<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
global $Itemid;
$url_itemid='';
if(!empty($Itemid))
	$url_itemid='&Itemid='.$Itemid;

$from_module = (!empty($this->params) && ($this->params->get('from_module', 0) != 0));

if($this->consumed === false) {
	$app = JFactory::getApplication();
	$app->enqueueMessage(JText::_('CONSUME_INVALID_SERIAL'));
}
?>
<form action="<?php echo hikaserial::completeLink('serial&task=consume'.$url_itemid); ?>" method="post" name="hikaserial_consume_form" enctype="multipart/form-data">
	<div class="hikaserial_serial_consume_page">
<?php
	if(empty($this->confirmation)) {
		if(!$from_module) {
?>
			<h2><?php echo JText::_('HIKASERIAL_ENTER_SERIAL'); ?></h2>
<?php
		}
?>
			<input type="text" style="max-width:90%" value="" name="hikaserial[serial_data]"/>
<?php } else {
		if(!$from_module) {
?>
			<h2><?php echo JText::_('HIKASERIAL_CONFIRM_SERIAL'); ?></h2>
<?php
		}
?>
			<p class="hikaserial_confirmation"><?php
				echo JText::sprintf('SERIAL_CONFIRMATION', $this->serial_data);
			?></p>
			<input type="hidden" value="<?php echo $this->serial_data; ?>" name="hikaserial[serial_data]"/>
			<input type="hidden" value="<?php echo $this->format; ?>" name="hikaserial[format]"/>
<?php
		$tmpl = JRequest::getVar('tmpl', '');
		if(!empty($tmpl)) {
?>
			<input type="hidden" value="<?php echo $tmpl; ?>" name="tmpl"/>
<?php
		}

		$return_url = '';
		if(!empty($this->params))
			$return_url = $this->params->get('return_url', '');
		if(!empty($return_url)) {
?>
			<input type="hidden" value="<?php echo $return_url; ?>" name="return_url"/>
<?php
		}
	}
?>
			<input type="submit" class="btn btn-primary"/>
<?php echo JHTML::_('form.token'); ?>
	</div>
</form>
