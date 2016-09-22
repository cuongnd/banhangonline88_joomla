<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><table class="admintable" cellspacing="1">
	<tr id="trfileupload">
		<td class="key" >
			<?php echo JText::_('UPLOAD_FILE'); ?>
		</td>
		<td>
			<input type="file" style="width:auto;" name="importfile" />
			<?php echo '<br/>'.(JText::sprintf('MAX_UPLOAD',(acymailing_bytes(ini_get('upload_max_filesize')) > acymailing_bytes(ini_get('post_max_size'))) ? ini_get('post_max_size') : ini_get('upload_max_filesize'))); ?>
		</td>
	</tr>
	<?php if($this->config->get('require_confirmation')){ ?>
	<tr id="trfileconfirm">
		<td class="key" >
			<?php echo JText::_('IMPORT_CONFIRMED'); ?>
		</td>
		<td>
			<?php echo JHTML::_('acyselect.booleanlist', "import_confirmed" , '',in_array('import_confirmed',$this->selectedParams['file']) ? 1 : 0,JText::_('JOOMEXT_YES'),JTEXT::_('JOOMEXT_NO') ); ?>
		</td>
	</tr>
	<?php } ?>
	<tr id="trfilegenerate">
		<td class="key" >
			<?php echo JText::_('GENERATE_NAME'); ?>
		</td>
		<td>
			<?php echo JHTML::_('acyselect.booleanlist', "generatename" , '',in_array('generatename',$this->selectedParams['file']) ? 1 : 0,JText::_('JOOMEXT_YES'),JTEXT::_('JOOMEXT_NO') ); ?>
		</td>
	</tr>
	<tr id="trfileblock">
		<td class="key" >
			<?php echo JText::_('IMPORT_BLOCKED'); ?>
		</td>
		<td>
			<?php echo JHTML::_('acyselect.booleanlist', "importblocked" , '',in_array('importblocked',$this->selectedParams['file']) ? 1 : 0,JText::_('JOOMEXT_YES'),JTEXT::_('JOOMEXT_NO') ); ?>
		</td>
	</tr>
	<tr id="trfileoverwrite">
		<td class="key" >
			<?php echo JText::_('OVERWRITE_EXISTING'); ?>
		</td>
		<td>
			<?php echo JHTML::_('acyselect.booleanlist', "overwriteexisting" , '',in_array('overwriteexisting',$this->selectedParams['file']) ? 1 : 0,JText::_('JOOMEXT_YES'),JTEXT::_('JOOMEXT_NO') ); ?>
		</td>
	</tr>
	<tr id="trfilecharset">
		<td class="key" >
			<?php echo JText::_('CHARSET_FILE'); ?>
		</td>
		<td>
			<?php $charsetType = acymailing_get('type.charset'); array_unshift($charsetType->values,JHTML::_('select.option', '',JText::_('UNKNOWN'))); echo $charsetType->display('charsetconvert',JRequest::getString('charsetconvert','')); ?>
		</td>
	</tr>
</table>
