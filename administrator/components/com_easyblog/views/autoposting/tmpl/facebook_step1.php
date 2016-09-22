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
<form name="facebook" action="index.php" method="post">
	<ol class="list-instruction reset-ul pa-15">
		<li>
			<?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_FACEBOOK_CREATE_APPLICATION_STEP'); ?> <a href="http://facebook.com/developers" target="_blank">http://facebook.com/developers</a>.
			<?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_FACEBOOK_CREATE_APP_HOW_TO' ); ?>
			<a href="http://stackideas.com/docs/easyblog/how-tos/245-setup-facebook-application-for-id-and-secret-key" target="_blank"><?php echo JText::_( 'COM_EASYBLOG_HERE' );?></a>
		</li>
		<li>
			<div><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_FACEBOOK_COPY_APP_ID_STEP'); ?></div>

			<div><img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/autoposting/facebook_appid.png" /></div>

			<div class="mini-form">
				<label for="integrations_facebook_api_key"><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_FACEBOOK_APP_ID' );?>:</label>
				<input type="text" name="integrations_facebook_api_key" id="integrations_facebook_api_key" value="<?php echo $this->config->get( 'integrations_facebook_api_key' );?>" class="input" style="width:200px" />
			</div>
		</li>
		<li>

			<div><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_FACEBOOK_COPY_APP_SECRET_STEP'); ?></div>

			<div><img src="<?php echo JURI::root();?>administrator/components/com_easyblog/assets/images/autoposting/facebook_appsecret.png" /></div>

			<div class="mini-form">
				<label for="integrations_facebook_secret_key"><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_FACEBOOK_APP_SECRET' );?>:</label>
				<input type="text" name="integrations_facebook_secret_key" id="integrations_facebook_secret_key" value="<?php echo $this->config->get( 'integrations_facebook_secret_key' );?>" class="input-xlarge" style="width:200px" />
			</div>
		</li>
	</ol>
	<input type="hidden" name="integrations_facebook" value="1" />
	<input type="submit" class="button social facebook btn btn-primary" value="<?php echo JText::_( 'COM_EASYBLOG_NEXT_STEP_BUTTON' );?>" />
	<input type="hidden" name="step" value="1" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="layout" value="facebook" />
	<input type="hidden" name="c" value="autoposting" />
	<input type="hidden" name="option" value="com_easyblog" />
	<?php echo JHTML::_( 'form.token' );?>
</form>
