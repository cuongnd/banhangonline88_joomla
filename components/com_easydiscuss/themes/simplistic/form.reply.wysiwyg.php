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
<script type="text/javascript">
	EasyDiscuss.require()
		.script('composer')
		.done(function($) {
			$("<?php echo $composer->selector ?>").implement(EasyDiscuss.Controller.Composer);
		});
</script>
<div class="discuss-composer <?php echo $composer->classname; ?> discuss-composer-<?php echo $composer->operation; ?>"
	 data-id="<?php echo $composer->id; ?>"
	 data-editortype="<?php echo $composer->editorType ?>"
	 data-operation="<?php echo $composer->operation; ?>"
	 >

	<div class="alert replyNotification" style="display: none;"></div>

	<div class="discuss-story">
		<div class="discuss-content">
			<form name="dc_submit" autocomplete="off" class="form-horizontal" action="<?php echo DiscussRouter::_('index.php?option=com_easydiscuss&controller=posts&task=saveReply'); ?>" method="post">

				<div class="discuss-form">

					<?php if (!$system->my->id) { ?>
						<div class="control-group control-group-guest">
							<input type="text" name="poster_name" class="input-xlarge" placeholder="<?php echo JText::_('COM_EASYDISCUSS_GUEST_NAME'); ?>">
							<input type="text" name="poster_email" class="input-xlarge" placeholder="<?php echo JText::_('COM_EASYDISCUSS_GUEST_EMAIL'); ?>">
						</div>
					<?php } ?>

					<div class="row-fluid">
						<?php echo $composer->getEditor(); ?>
					</div>

					<?php if ($system->config->get('main_location_reply')) { ?>
					<div class="control-group">
						<?php echo $this->loadTemplate('form.location.php'); ?>
					</div>
					<?php } ?>

					<div class="control-group">
						<?php echo $composer->getFields(); ?>
					</div>

					<?php if ($captcha = $this->getRecaptcha()) { ?>
					<div class="control-group">
						<div class="respond-recaptcha mt-10"><?php echo $captcha; ?></div>
					</div>
					<?php }else if( DiscussHelper::getHelper( 'Captcha' )->showCaptcha() ){ ?>
						<?php echo DiscussHelper::getHelper( 'Captcha' )->getHTML();?>
					<?php } ?>

					<div class="row-fluid">
						<div class="pull-left">
							<a href="<?php echo DiscussRouter::_( 'index.php?option=com_easydiscuss&view=post&id=' . $post->parent_id );?>" class="btn btn-medium btn-danger"><?php echo JText::_('COM_EASYDISCUSS_BUTTON_CANCEL'); ?></a>
						</div>
						<div class="pull-right">
							<input type="submit" name="submit-reply" class="btn btn-primary btn-medium submit-reply" value="<?php echo JText::_('COM_EASYDISCUSS_BUTTON_SAVE'); ?>" />
						</div>

						<div class="pull-right reply-loading"></div>
					</div>

					<input type="hidden" name="title" value="Re: <?php echo DiscussStringHelper::escape($parent->title); ?>" />
					<input type="hidden" name="post_id" value="<?php echo $post->id; ?>" />
					<input type="hidden" name="parent_id" value="<?php echo $parent->id; ?>" />
					<input type="hidden" name="parent_catid" value="<?php echo $parent->category_id; ?>" />
					<input type="hidden" name="user_type" value="<?php echo $system->my->id == 0 ? 'guest' : ''; ?>" />
				</div>
			</form>
		</div>
	</div>
</div>
