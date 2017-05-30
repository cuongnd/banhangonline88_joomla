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

	$( '#metadescription' ).bind( 'keyup' , function(){
		var length	= $(this).val().length;
		$( '#text-counter' ).val( length );
	});
});
</script>
<div id="error-msg-box"></div>
<div class="clearfix"></div>

<form id="dashboard" name="dashboard" enctype="multipart/form-data" method="post" action="">
	<div class="dashboard-head clearfix">
		<?php echo $this->fetch( 'dashboard.user.heading.php' ); ?>
		<button onclick="eblog.dashboard.settings.submit();" class="buttons"><?php echo JText::_('COM_EASYBLOG_DASHBOARD_SAVE_SETTINGS_BUTTON'); ?></button>
	</div>

    <?php if( $system->config->get( 'main_dashboard_editaccount' ) ){ ?>
		<?php echo $this->fetch( 'dashboard.profile.account.php' ); ?>
	<?php } ?>
	<?php echo $this->fetch( 'dashboard.profile.blog.php' ); ?>
	<?php echo $this->fetch( 'dashboard.profile.seo.php' ); ?>
	<?php echo $this->fetch( 'dashboard.profile.facebook.php' ); ?>
	<?php echo $this->fetch( 'dashboard.profile.twitter.php' ); ?>
	<?php echo $this->fetch( 'dashboard.profile.linkedin.php' );?>

	<?php if( $system->config->get( 'main_google_profiles') ){ ?><?php echo $this->fetch( 'dashboard.profile.google.php' ); ?><?php } ?>

	<?php if( $system->config->get( 'integrations_google_adsense_blogger' ) ){ ?>
		<?php echo $this->fetch( 'dashboard.profile.googleads.php' ); ?>
	<?php } ?>

	<?php echo $this->fetch( 'dashboard.profile.feedburner.php' ); ?>
	<input type="hidden" name="metaid" value="<?php echo $meta->id; ?>" />
  	<input type="hidden" name="controller" value="dashboard" />
  	<input type="hidden" name="task" value="saveProfile" />
	<?php echo JHTML::_( 'form.token' ); ?>
</form>

