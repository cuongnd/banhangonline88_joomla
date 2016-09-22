<?php
/**
 * @package		EasyBlog
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *  
 * EasyBlog is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>

<div id="subscription-form">
	<form name="frmSubscribe" id="frmSubscribe">
	
		<table width="100%" cellpadding="0" cellspacing="5" border="0" class="form-layout" style="margin-top: 10px;">
			<?php if ( !empty( $my->email ) ) { ?>
			<tr>
				<td class="label" valign="top" style="text-align: right;"><?php echo JText::_('COM_EASYBLOG_EMAIL'); ?></td>
				<td class="input" valign="top" style="font-weight: 700; padding-left: 20px;">
					<?php echo $my->email; ?>
					<input type="text" style="display: none;" id="email" name="email" size="45" value="<?php echo $my->email; ?>" />
					<input type="hidden" id="esfullname" name="esfullname" size="45" value="<?php echo $my->name; ?>" />
				</td>
			</tr>
			<?php } else { ?>
			
				<?php if($canRegister && $my->id == 0) : ?>
				<tr>
				    <td colspan="2">
						<?php echO JText::_('COM_EASYBLOG_FILL_IN_USERNAME_AND_FULLNAME_TO_REGISTER'); ?>
					</td>
				</tr>
				<?php endif; ?>
			<tr>
				<td class="label" valign="top">
					<label class="label" for="email"><?php echo JText::_('COM_EASYBLOG_EMAIL'); ?></label><br />
					<small>(<?php echo JText::_('COM_EASYBLOG_REQUIRED'); ?>)</small>
				</td>
				<td class="input" valign="top">
					<input class="inputbox" type="text" id="email" name="email" size="45" />
				</td>
			</tr>
			<tr>
				<td class="label" valign="top">
					<label class="label" for="esfullname"><?php echo JText::_('COM_EASYBLOG_FULLNAME'); ?></label>
					<?php if($canRegister && $my->id == 0) : ?>
					<br />
					<small>(<?php echo JText::_('COM_EASYBLOG_REQUIRED'); ?>)</small>
					<?php endif; ?>
				</td>
				<td class="input" valign="top">
					<input class="inputbox" type="text" id="esfullname" name="esfullname" size="45" />
				</td>
			</tr>
			<?php } ?>
			
			
			<?php if($canRegister && $my->id == 0) : ?>
			<tr>
				<td class="label" valign="top">
					<label class="label" for="esusername"><?php echo JText::_('COM_EASYBLOG_USERNAME'); ?></label><br />
					<small>(<?php echo JText::_('COM_EASYBLOG_REQUIRED'); ?>)</small>
				</td>
				<td class="input" valign="top">
					<input class="inputbox" type="text" id="esusername" name="esusername" size="45" />
				</td>
			</tr>
			<tr>
				<td valign="top">&nbsp;</td>
				<td valign="top">
					<input class="inputbox" style="width:10px;" type="checkbox" id="esregister" name="esregister" value="y" />
					<?php echo JText::_('COM_EASYBLOG_REGISTER_AS_SITE_MEMBER'); ?>
				</td>
			</tr>
			<?php endif; ?>
			
			
			<tr>
				<td></td>
				<td valign="top">

				</td>
			</tr>
		</table>
	</form>
</div>

<div class="dialog-actions">
	<input type="button" value="<?php echo JText::_('COM_EASYBLOG_CANCEL');?>" class="button" id="edialog-cancel" name="edialog-cancel" onclick="ejax.closedlg();" />
	<input type="button" value="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE');?>" class="button" id="edialog-submit" name="edialog-submit" onclick="" />
</div>