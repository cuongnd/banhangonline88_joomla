<?php
/**
 * @package	AcyMailing for Joomla!
 * @version	4.5.0
 * @author	acyba.com
 * @copyright	(C) 2009-2013 ACYBA S.A.R.L. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><textarea style="width:80%" rows="20" name="textareaentries">
<?php $text = JRequest::getString("textareaentries");
if(empty($text)){ ?>
name,email
Adrien,adrien@example.com
John,john@example.com
<?php }else echo $text?>
</textarea>
<table class="admintable" cellspacing="1">
<?php if($this->config->get('require_confirmation')){ ?>
		<tr id="trtextareaconfirm">
			<td class="key" >
				<?php echo JText::_('IMPORT_CONFIRMED'); ?>
			</td>
			<td>
				<?php echo JHTML::_('acyselect.booleanlist', "import_confirmed_textarea" , '',in_array('import_confirmed_textarea',$this->selectedParams['textarea']) ? 1 : 0,JText::_('JOOMEXT_YES'),JTEXT::_('JOOMEXT_NO') ); ?>
			</td>
		</tr>
<?php } ?>
	<tr id="trtextareagenerate" >
		<td class="key" >
			<?php echo JText::_('GENERATE_NAME'); ?>
		</td>
		<td>
			<?php echo JHTML::_('acyselect.booleanlist', "generatename_textarea" , '',in_array('generatename_textarea',$this->selectedParams['textarea']) ? 1 : 0,JText::_('JOOMEXT_YES'),JTEXT::_('JOOMEXT_NO')); ?>
		</td>
	</tr>
	<tr id="trtextareablock" >
		<td class="key" >
			<?php echo JText::_('IMPORT_BLOCKED'); ?>
		</td>
		<td>
			<?php echo JHTML::_('acyselect.booleanlist', "importblocked_textarea" , '',in_array('importblocked_textarea',$this->selectedParams['textarea']) ? 1 : 0,JText::_('JOOMEXT_YES'),JTEXT::_('JOOMEXT_NO')); ?>
		</td>
	</tr>
	<tr id="trtextareaoverwrite">
		<td class="key" >
			<?php echo JText::_('OVERWRITE_EXISTING'); ?>
		</td>
		<td>
			<?php echo JHTML::_('acyselect.booleanlist', "overwriteexisting_textarea" , '',in_array('overwriteexisting_textarea',$this->selectedParams['textarea']) ? 1 : 0,JText::_('JOOMEXT_YES'),JTEXT::_('JOOMEXT_NO')); ?>
		</td>
	</tr>
</table>
