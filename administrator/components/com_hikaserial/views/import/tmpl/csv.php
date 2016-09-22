<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><table class="admintable" cellspacing="1">
	<tr>
		<td class="key"><?php echo JText::_('PACK'); ?></td>
		<td><?php
			$packType = hikaserial::get('type.pack');
			echo $packType->display('csvimport_pack','',false,true);
		?></td>
	</tr>
	<tr>
		<td class="key"><?php echo JText::_('UPLOAD_FILE'); ?></td>
		<td><input type="file" size="50" name="csvimport_file" /><br/><?php
			echo JText::sprintf('MAX_UPLOAD',(hikashop_bytes(ini_get('upload_max_filesize')) > hikashop_bytes(ini_get('post_max_size'))) ? ini_get('post_max_size') : ini_get('upload_max_filesize'));
		?></td>
	</tr>
	<tr>
		<td class="key"><?php echo JText::_('HIKASERIAL_CHECK_DUPLICATES'); ?></td>
		<td><?php
			echo JHTML::_('hikaselect.booleanlist', 'csvimport_checkduplicates', '', 0);
		?></td>
	</tr>
<?php
?>
</table>
