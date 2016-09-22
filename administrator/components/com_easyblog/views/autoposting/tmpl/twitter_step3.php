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
<script type="text/javascript">
EasyBlog.ready(function($){

	$( '#main_twitter_shorten_url' ).bind( 'change' , function(){
		$( '#bitly-form' ).toggle();
	});

});
</script>
	<form name="facebook" action="index.php" method="post">
	<ul class="list-instruction reset-ul pa-15">
		<li>
			<?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_TWITTER_CONTENT_STEP'); ?>
			<div style="margin-top:10px;">
				<table width="100%">
					<tr>
						<td valign="top" style="width:320px;">
							<textarea style="margin-bottom: 10px;height: 75px; width:300px;" class="inputbox" name="main_twitter_message"><?php echo $this->config->get( 'main_twitter_message');?></textarea>
						</td>
						<td valign="top">
							<?php echo JText::_( 'COM_EASYBLOG_SETTINGS_SOCIALSHARE_TWITTER_MESSAGE_DESC' ); ?>
						</td>
					</tr>
				</table>
			</div>
		</li>
		<li>
			<div class="clearfix">
				<input type="checkbox" value="1" id="main_twitter_shorten_url" name="main_twitter_shorten_url"<?php echo $this->config->get( 'main_twitter_shorten_url' ) ? ' checked="checked"' : '';?> />
				<label for="main_twitter_shorten_url"><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_TWITTER_URL_SHORTENER_STEP' );?></label>
			</div>
			<div id="bitly-form" class="mini-form"<?php echo $this->config->get( 'main_twitter_shorten_url' ) ? ' style="display:block;"' : ' style="display:none;"';?>>
				<div>
					<label for="page-id"><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_TWITTER_BITLY_LOGIN' );?>:</label>
					<input type="text" id="page-id" name="main_twitter_urlshortener_login" value="<?php echo $this->config->get( 'main_twitter_urlshortener_login' );?>" class="input" style="width:300px;margin-right:10px" />
				</div>
				<div style="margin-top: 5px;">
					<label for="page-id"><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_TWITTER_BITLY_API' );?>:</label>
					<input type="text" id="page-id" name="main_twitter_urlshortener_apikey" value="<?php echo $this->config->get( 'main_twitter_urlshortener_apikey' );?>" class="input" style="width:300px;margin-right:10px" />
				</div>
			</div>
		</li>
		<li>
			<div class="clearfix">
				<label><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_TWITTER_ALLOW_BLOGGER_SETUP_STEP' );?></label>
			</div>
			<div>
				<?php echo $this->renderCheckbox( 'integrations_twitter_centralized_and_own' , $this->config->get( 'integrations_twitter_centralized_and_own' ) ); ?>
			</div>
			<div style="clear:both;"></div>
		</li>
	</ul>
	<input type="button" class="button" onclick="previousPage();" value="<?php echo JText::_( 'COM_EASYBLOG_PREVIOUS_STEP_BUTTON' );?>" />
	<input type="submit" class="button social twitter" value="<?php echo JText::_( 'COM_EASYBLOG_COMPLETE_SETUP_BUTTON' );?>" />
	<input type="hidden" name="step" value="3" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="layout" value="twitter" />
	<input type="hidden" name="c" value="autoposting" />
	<input type="hidden" name="option" value="com_easyblog" />
</form>
