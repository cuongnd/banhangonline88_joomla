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
<form action="<?php echo hikaserial::completeLink('pack'); ?>" method="post" name="adminForm" id="adminForm" enctype="multipart/form-data">
<?php if(!HIKASHOP_BACK_RESPONSIVE) { ?>
	<table class="admintable" style="width:100%">
		<tr>
			<td valign="top" width="50%">
<?php } else { ?>
	<div class="row-fluid">
		<div class="span6">
<?php } ?>
				<fieldset class="adminform">
					<legend><?php echo JText::_('MAIN_INFORMATION'); ?></legend>
					<table class="admintable table" style="width:100%">
						<tr>
							<td class="key">
								<label><?php echo JText::_('HIKA_NAME'); ?></label>
							</td>
							<td><?php
								echo $this->escape(@$this->pack->pack_name);
							?></td>
						</tr>
						<tr>
							<td class="key">
								<label><?php echo JText::_('PACK_GENERATOR'); ?></label>
							</td>
							<td><?php
								echo $this->packGeneratorType->get(@$this->pack->pack_generator);
							?></td>
						</tr>
					</table>
				</fieldset>
<?php if(!HIKASHOP_BACK_RESPONSIVE) { ?>
			</td>
			<td valign="top" width="50%">
<?php } else { ?>
		</div>
		<div class="span6">
<?php } ?>
				<fieldset class="adminform">
					<legend><?php echo JText::_('GENERATION_INFORMATION'); ?></legend>
					<table class="admintable table" style="width:100%">
						<tr>
							<td class="key">
								<label><?php echo JText::_('NUMBER_OF_SERIALS'); ?></label>
							</td>
							<td>
								<input type="text" name="data[number_serials]" value=""/>
							</td>
						</tr>
						<tr>
							<td class="key">
								<label><?php echo JText::_('SERIAL_STATUS'); ?></label>
							</td>
							<td><?php
								echo $this->serialStatusType->display('data[serial_status]', '');
							?></td>
						</tr>
					</table>
				</fieldset>
<?php
if(!empty($this->populateFormData)) {
?>				<fieldset class="adminform">
					<legend><?php echo JText::_('GENERATOR_PARAMETERS'); ?></legend>
					<table class="admintable" style="width:100%"><?php
						echo $this->populateFormData;
					?></table>
				</fieldset>
<?php
}
?>
<?php if(!HIKASHOP_BACK_RESPONSIVE) { ?>
			</td>
		</tr>
	</table>
<?php } else { ?>
		</div>
	</div>
<?php } ?>
	<div class="clr"></div>
	<input type="hidden" name="cid[]" value="<?php echo @$this->pack->pack_id; ?>" />
	<input type="hidden" name="option" value="<?php echo HIKASERIAL_COMPONENT; ?>" />
	<input type="hidden" name="task" value="generate" />
	<input type="hidden" name="ctrl" value="<?php echo JRequest::getCmd('ctrl'); ?>" />
	<?php echo JHTML::_('form.token'); ?>
</form>
