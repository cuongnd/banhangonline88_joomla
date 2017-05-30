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
				<?php echo JText::_( 'COM_EASYBLOG_NOTIFICATION_TEAMBLOG_REJECTED' ); ?>
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
						<?php echo JText::_( 'COM_EASYBLOG_NOTIFICATION_HELLO' ); ?>,
					</p>

					<p style="text-align:left;">
						<?php echo JText::sprintf( 'COM_EASYBLOG_NOTIFICATION_TEAMBLOG_REQUEST_REJECTED' , '<a href="' . $teamLink . '" style="font-weight:bold;color:#477fda;text-decoration:none">' . $teamName . '</a>' ); ?>
					</p>
				</td>
			</tr>
		</table>
		</td>
</tr>
