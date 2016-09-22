<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><textarea style="width:100%" rows="20" name="textareaimport_content"><?php
	$text = JRequest::getString("textareaentries");
	if(empty($text)){
?>pack_name,serial_data
"License Pack 1","00000001"
"License Pack 1","00000002"
"License Pack 1","00000003"
<?php
} else {
	echo $text;
}
?>
</textarea>
<table class="admintable" cellspacing="1">
	<tr>
		<td class="key"><?php echo JText::_('PACK'); ?></td>
		<td><?php
			$packType = hikaserial::get('type.pack');
			echo $packType->display('textareaimport_pack','',false,true);
		?></td>
	</tr>
	<tr>
		<td class="key"><?php echo JText::_('IMPORT_AS_CSV'); ?></td>
		<td><?php
			echo JHTML::_('hikaselect.booleanlist', 'textareaimport_as_csv', '', 1);
		?></td>
	</tr>
	<tr>
		<td class="key"><?php echo JText::_('HIKASERIAL_CHECK_DUPLICATES'); ?></td>
		<td><?php
			echo JHTML::_('hikaselect.booleanlist', 'textareaimport_checkduplicates', '', 0);
		?></td>
	</tr>
</table>
