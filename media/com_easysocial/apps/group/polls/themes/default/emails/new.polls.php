<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
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
				<?php echo JText::sprintf('COM_EASYSOCIAL_EMAILS_GROUP_NEW_POLLS', $group); ?>
			</div>
		</div>
	</td>
</tr>

<tr>
	<td style="text-align: center;font-size:12px;color:#888">

		<div style="margin:30px auto;text-align:center;display:block">
			<img src="<?php echo rtrim( JURI::root() , '/' );?>/components/com_easysocial/themes/wireframe/images/emails/divider.png" alt="<?php echo JText::_( 'divider' );?>" />
		</div>

		<p style="text-align:center;padding: 0 30px;">
			<?php echo JText::sprintf( 'COM_EASYSOCIAL_EMAILS_GROUP_NEW_POLLS_CONTENT' , '<a href="' . $posterLink . '">' . $actor . '</a>' , '<a href="' . $groupLink . '">' . $group . '</a>' );?>
		</p>

        <table align="center" border="0" cellpadding="0" cellspacing="0" style="table-layout:fixed;width:100%;margin-top: 40px;">
        	<tr>
        		<td align="center">
            		<table align="center" width="380" style="margin-top:-10px" border="0" cellspacing="0" cellpadding="0">
		                <tr>
		                    <td style="color:#888;border-top: 1px solid #ebebeb;padding: 15px 20px; background-color:#f8f9fb;font-size:13px;text-align:center">
		                        <?php echo JText::_('COM_EASYSOCIAL_EMAILS_POLL_TITLE'); ?>: <b><?php echo $message;?></b>
		                    </td>
		                </tr>
		                <?php if( $this->config->get( 'registrations.email.password' ) ){ ?>
		                <tr>
		                    <td style="color:#888;border-top: 1px solid #ebebeb;padding: 15px 20px; background-color:#f8f9fb;font-size:13px;text-align:center">
		                        <?php echo JText::_('COM_EASYSOCIAL_EMAILS_POSTED_BY');?>: <a href="<?php echo $posterLink;?>" style="text-decoration: none;color:#798796;"><?php echo $actor;?></a>
		                    </td>
		                </tr>
		                <?php } ?>
            		</table>

		            <a href="<?php echo $permalink;?>" style="
		            display:inline-block;
		            text-decoration:none;
		            font-weight:bold;
		            margin-top: 20px;
		            padding:10px 15px;
		            line-height:20px;
		            color:#fff;font-size: 12px;
		            background-color: #83B3DD;
		            background-image: linear-gradient(to bottom, #91C2EA, #6D9CCA);
		            background-repeat: repeat-x;
		            border-color: rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.1) rgba(0, 0, 0, 0.25);
		            border-style: solid;
		            border-width: 1px;
		            box-shadow: 0 1px 0 rgba(255, 255, 255, 0.2) inset, 0 1px 2px rgba(0, 0, 0, 0.05);
		            border-radius:2px; -moz-border-radius:2px; -webkit-border-radius:2px;
		            "><?php echo JText::_('COM_EASYSOCIAL_EMAILS_VIEW_POLLS_BUTTON'); ?></a>
		       	</td>
		    </tr>
		</table>
	</td>
</tr>
