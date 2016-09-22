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

defined( '_JEXEC' ) or die( 'Unauthorized Access' );
?>
<tr>
	<td style="text-align: center;padding: 40px 10px 0;">
		<div style="margin-bottom:15px;">
			<div style="font-family:Arial;font-size:32px;font-weight:normal;color:#333;display:block; margin: 4px 0">
				<?php echo JText::_( 'COM_EASYBLOG_NOTIFICATION_SUBSCRIPTION_CONFIRMATION' ); ?>
			</div>

		</div>
	</td>
</tr>


<tr>
	<td style="text-align: center;font-size:12px;color:#888">

		<div style="margin:10px auto;text-align:center;display:block">
			<img src="<?php echo rtrim( JURI::root() , '/' );?>/components/com_easyblog/themes/default/images/emails/divider.png" alt="<?php echo JText::_( 'divider' );?>" />
		</div>

		<table width="540" cellspacing="0" cellpadding="0" border="0" align="center">
			<tr>
				<td>
					<p style="text-align:left;">
						<?php echo JText::_( 'COM_EASYBLOG_NOTIFICATION_HELLO' ) . ' ' . $fullname; ?>,
					</p>

					<p style="text-align:left;">
						<?php echo JText::_( 'COM_EASYBLOG_NOTIFICATION_SUBSCRIBED_TO' ); ?> <?php echo JText::sprintf( 'COM_EASYBLOG_NOTIFICATION_SUBSCRIBE_' . strtoupper( $type ), $target ); ?> <?php echo $targetlink; ?>
					</p>
				</td>
			</tr>
		</table>

		<span style="margin:10px auto;text-align:center;display:block">
			<img src="<?php echo rtrim( JURI::root() , '/' );?>/components/com_easyblog/themes/default/images/emails/divider.png" alt="<?php echo JText::_( 'divider' );?>" />
		</span>

		<table width="540" align="center" style="margin: 20px auto 0;background-color:#f8f9fb;padding:15px 20px;" border="0" cellspacing="0" cellpadding="0">
			<tr>

				<td>
					<table style="font-size: 14px;margin: 0 auto 10px 20px; text-align:left;color:#798796" align="">


						<tr>
							<td>
							<?php echo JText::_( 'COM_EASYBLOG_NOTIFICATION_SUBSCRIBE_CONFIRMATION_NOTICE' ); ?>

							</td>
						</tr>

					</table>
				</td>
			</tr>
		</table>


	</td>
</tr>
