<?php
/**
* @package 		EasyBlog
* @copyright	Copyright (C) 2010 - 2013 Stack Ideas Sdn Bhd. All rights reserved.
* @license 		Proprietary Use License http://stackideas.com/licensing.html
* @author 		Stack Ideas Sdn Bhd
*/
defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<form name="frmSubscribe" id="frmSubscribe">
<p>
	<?php echo $message; ?>
	<?php if( $registration && $system->my->id == 0){ ?>
		<?php echo JText::_('COM_EASYBLOG_SUBSCRIPTIONS_SITE_DIALOG_FILL_USERNAME_TO_REGISTER'); ?>
	<?php } ?>
</p>
<table width="100%" cellpadding="0" cellspacing="5" border="0" class="form-layout" style="margin-top: 10px;">
	<?php if ( !empty( $system->my->email ) ) { ?>
	<tr>
		<td class="key" width="30%"><?php echo JText::_('COM_EASYBLOG_EMAIL'); ?></td>
		<td>
			<?php echo $system->my->email; ?>
			<input type="text" style="display: none;" id="email" name="email" size="45" value="<?php echo $system->my->email; ?>" />
			<input type="hidden" id="esfullname" name="esfullname" size="45" value="<?php echo $this->escape( $system->my->name ); ?>" />
		</td>
	</tr>
	<?php } else { ?>
	<tr>
		<td class="key" width="30%">
			<label class="key" for="esfullname"><?php echo JText::_('COM_EASYBLOG_FULLNAME'); ?> <?php if($registration && $system->my->id == 0) : ?><small>(<?php echo JText::_('COM_EASYBLOG_REQUIRED'); ?>)</small><?php endif; ?></label>
		</td>
		<td>
			<input class="inputbox" type="text" id="esfullname" name="esfullname" size="45" />
		</td>
	</tr>
	<tr>
		<td class="key" width="30%">
			<label class="key" for="email"><?php echo JText::_('COM_EASYBLOG_EMAIL'); ?> <small>(<?php echo JText::_('COM_EASYBLOG_REQUIRED'); ?>)</small></label>
		</td>
		<td>
			<input class="inputbox" type="text" id="email" name="email" size="45" />
		</td>
	</tr>
	<?php } ?>
	<?php if( $registration && $system->my->id == 0) : ?>
	<tr>
		<td class="key">
			<label class="key" for="esusername"><?php echo JText::_('COM_EASYBLOG_USERNAME'); ?> <small>(<?php echo JText::_('COM_EASYBLOG_REQUIRED'); ?>)</small></label>
		</td>
		<td>
			<input class="inputbox" type="text" id="esusername" name="esusername" size="45" />
		</td>
	</tr>
	<tr>
		<td class="key">&nbsp;</td>
		<td>
			<input class="inputbox" type="checkbox" id="esregister" name="esregister" value="y" />
			<label for="esregister" style="display: inline;"><?php echo JText::_('COM_EASYBLOG_REGISTER_AS_SITE_MEMBER'); ?></label>
		</td>
	</tr>
	<?php endif; ?>
</table>
<div class="dialog-actions">
	<input type="button" value="<?php echo JText::_('COM_EASYBLOG_CANCEL');?>" class="button" id="edialog-cancel" name="edialog-cancel" onclick="ejax.closedlg();" />
	<input type="button" value="<?php echo JText::_('COM_EASYBLOG_SUBSCRIBE');?>" class="button" id="edialog-submit" name="edialog-submit" onclick="eblog.subscription.submit('<?php echo $type;?>');" />
	<?php if( isset( $id ) ){ ?>
	<input class="inputbox" type="hidden" name="id" value="<?php echo $id; ?>" />
	<?php } ?>
	<input class="inputbox" type="hidden" name="userid" value="<?php echo $system->my->id; ?>" />
	<span id="eblog_loader"></span>
</div>
</form>
