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
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-side-widget">
	<?php echo $this->html('widget.title', 'APP_FOLLOWERS_WIDGET_TITLE_SUGGESTIONS'); ?>

	<div class="es-side-widget__bd">
		<?php if ($users) { ?>
		<div class="o-flag-list">
			<?php foreach ($users as $user) { ?>
			<div class="o-flag">
				<div class="o-flag__image o-flag--top">
					<?php echo $this->html('avatar.user', $user, 'md'); ?>
				</div>

				<div class="o-flag__body">
					<a href="<?php echo $user->getPermalink();?>"><?php echo $user->getName();?></a>
					<div>
						<?php echo $this->html('user.subscribe', $user, 'xs'); ?>	
					</div>
				</div>
			</div>
			<?php } ?>
		</div>
		<?php } else { ?>
			<?php echo $this->html('widget.emptyBlock', 'APP_FOLLOWERS_WIDGET_NO_SUGGESTIONS_CURRENTLY'); ?>
		<?php } ?>

		<?php if ($users) { ?>
		<div>
			<a href="<?php echo ESR::followers(array('filter' => 'suggest')); ?>" class="btn btn-sm es-side-widget-btn-showmore">
				<i class="i-chevron i-chevron--down t-lg-mr--sm"></i> <?php echo JText::_('APP_FOLLOWERS_WIDGET_VIEW_ALL'); ?>
			</a>
		</div>
		<?php } ?>
	</div>
</div>
