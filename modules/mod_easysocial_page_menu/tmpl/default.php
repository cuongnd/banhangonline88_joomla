<?php
/**
* @package      EasySocial
* @copyright    Copyright (C) 2010 - 2016 Stack Ideas Sdn Bhd. All rights reserved.
* @license      GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div id="es" class="mod-es mod-es-menu mod-es-group-menu <?php echo $lib->getSuffix();?>">

	<?php if ($params->get('show_edit', true) && $page->isAdmin()) { ?>
	<div class="mod-es-menu-bar">
		<nav class="o-nav">
			<div class="o-nav__item pull-right">
				<a href="<?php echo $page->getPermalink(true, false, 'edit');?>" class="o-nav__link mod-es-menu-bar__icon-link has-new">
					<i class="fa fa-pencil-square-o"></i>
				</a>
			</div>
		</nav>
	</div>
	<?php } ?>
	<div class="mod-es-pf-hd">
		<div class="mod-es-pf-hd__cover-wrap">
			<div style="background-image : url(<?php echo $cover->getSource();?>);background-position: <?php echo $cover->getPosition();?>;" class="mod-es-pf-hd__cover">
			</div>
		</div>
		<div class="mod-es-pf-hd__content">
			<?php if ($params->get('show_avatar', true)) { ?>
			<div class="mod-es-pf-hd__avatar">
				<a href="<?php echo $page->getPermalink();?>" class="o-avatar o-avatar--lg">
					<img src="<?php echo $page->getAvatar(SOCIAL_AVATAR_MEDIUM);?>" alt="<?php echo $lib->html('string.escape', $page->getName());?>" />
				</a>
			</div>
			<?php } ?>

			<?php if ($params->get('show_name', true)) { ?>
			<a href="<?php echo $page->getPermalink();?>" class="mod-es-title"><?php echo $page->getName();?></a>
			<?php } ?>

			<?php if ($params->get('show_likes', true)) { ?>            
			<div class="mod-es-meta">
				<i class="fa fa-thumbs-o-up"></i>&nbsp; <?php echo $page->getTotalMembers(); ?>
				<?php echo JText::_(ES::string()->computeNoun('COM_EASYSOCIAL_PAGES_FOLLOWERS', $page->getTotalMembers())); ?>
			</div>
			<?php } ?>
		</div>
	</div>
	<div class="mod-es-menu-list">
		<?php foreach ($apps as $item) { ?>
		<a href="<?php echo $page->getAppPermalink($item->element);?>" class="mod-es-menu-list__item">
			<?php echo $item->_('title'); ?>
		</a>
		<?php } ?>
	</div>
</div>