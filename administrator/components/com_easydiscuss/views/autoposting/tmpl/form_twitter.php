<?php
/**
 * @package		EasyDiscuss
 * @copyright	Copyright (C) 2010 Stack Ideas Private Limited. All rights reserved.
 * @license		GNU/GPL, see LICENSE.php
 *
 * EasyDiscuss is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 * See COPYRIGHT.php for copyright notices and details.
 */

defined('_JEXEC') or die('Restricted access');
?>
<form name="adminForm" id="adminForm" action="index.php" method="post" class="adminForm">
<table width="100%">
	<tr>
		<td valign="top">
			<fieldset>
				<legend><?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_TWITTER' );?></legend>
				<table width="100%" class="adminForm">
					<tbody>
						<tr>
							<td class="key">
								<label for="main_autopost_twitter"><?php echo JText::_( 'COM_EASYDISCUSS_ENABLE_AUTOPOST' );?></label>
							</td>
							<td valign="top">
								<?php echo $this->renderCheckbox( 'main_autopost_twitter' , $this->config->get( 'main_autopost_twitter' ) ); ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="main_autopost_twitter_id"><?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_TWITTER_CONSUMER_KEY' );?></label>
							</td>
							<td valign="top">
								<input type="text" name="main_autopost_twitter_id" id="main_autopost_twitter_id" value="<?php echo $this->config->get( 'main_autopost_twitter_id' );?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="main_autopost_twitter_secret"><?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_TWITTER_CONSUMER_SECRET' );?></label>
							</td>
							<td>
								<input type="text" name="main_autopost_twitter_secret" id="main_autopost_twitter_secret" value="<?php echo $this->config->get( 'main_autopost_twitter_secret' );?>" />
							</td>
						</tr>
						<tr>
							<td class="key">
								<?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_SIGN_IN'); ?>
							</td>
							<td>
								<?php if( $this->associated ){ ?>
								<a href="<?php echo JRoute::_( 'index.php?option=com_easydiscuss&controller=autoposting&task=revoke&type=twitter');?>"><?php echo JText::_( 'COM_EASYDISCUSS_AUTOPOST_REVOKE_ACCCESS' );?></a>
								<?php } else { ?>
								<div>
									<a href="<?php echo JRoute::_( 'index.php?option=com_easydiscuss&controller=autoposting&task=request&type=twitter');?>"><img src="<?php echo JURI::root();?>media/com_easydiscuss/images/twitter_signon.png" /></a>
								</div>
								<?php } ?>
							</td>
						</tr>
						<tr>
							<td class="key">
								<label for="main_autopost_twitter_message"><?php echo JText::_( 'COM_EASYDISCUSS_TWITTER_AUTOPOST_POST_MESSAGE' ); ?></label>
							</td>
							<td>
								<textarea name="main_autopost_twitter_message"><?php echo $this->config->get( 'main_autopost_twitter_message' );?></textarea>
							</td>
						</tr>
					</tbody>
				</table>
			</fieldset>
		</td>
		<td width="50%">&nbsp;</td>
	</tr>
</table>
<input type="hidden" name="step" value="completed" />
<input type="hidden" name="task" value="save" />
<input type="hidden" name="layout" value="twitter" />
<input type="hidden" name="controller" value="autoposting" />
<input type="hidden" name="option" value="com_easydiscuss" />
</form>
