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

	$(document).ready( function(){
		$( '#integrations_facebook_impersonate_page' ).bind( 'change' , function(){
			$( '#page-form' ).toggle();
		});
	});
});
</script>
<form name="facebook" action="index.php" method="post">
	<ul class="list-instruction reset-ul pa-15">
		<li>
			<?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_FACEBOOK_CONTENT_SOURCE_STEP'); ?>

			<div>
				<select class="inputbox" name="integrations_facebook_source">
					<option selected="selected" value="intro"><?php echo JText::_( 'COM_EASYBLOG_INTROTEXT' );?></option>
					<option value="content"><?php echo JText::_( 'COM_EASYBLOG_CONTENT' );?></option>
				</select>
			</div>
		</li>
		<li>
			<?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_FACEBOOK_CONTENT_LENGTH_STEP' );?>
			<div>
				<input type="text" size="5" value="<?php echo $this->config->get( 'integrations_facebook_blogs_length' );?>" class="inputbox" name="integrations_facebook_blogs_length">
			</div>
		</li>
		<li>
			<div class="clearfix">
				<label><?php echo JText::_( 'COM_EASYBLOG_AUTOPOST_FACEBOOK_ALLOW_BLOGGER_SETUP_STEP' );?></label>
			</div>
			<div>
				<?php echo $this->renderCheckbox( 'integrations_facebook_centralized_and_own' , $this->config->get( 'integrations_facebook_centralized_and_own' ) ); ?>
			</div>
			<div style="clear:both;"></div>
		</li>
	</ul>
	<input type="button" class="button" onclick="previousPage();" value="<?php echo JText::_( 'COM_EASYBLOG_PREVIOUS_STEP_BUTTON' );?>" />
	<input type="submit" class="button social facebook btn btn-primary" value="<?php echo JText::_( 'COM_EASYBLOG_COMPLETE_SETUP_BUTTON' );?>" />
	<input type="hidden" name="step" value="3" />
	<input type="hidden" name="task" value="save" />
	<input type="hidden" name="layout" value="facebook" />
	<input type="hidden" name="c" value="autoposting" />
	<input type="hidden" name="option" value="com_easyblog" />
</form>
