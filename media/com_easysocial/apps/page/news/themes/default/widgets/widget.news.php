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
<div class="es-side-widget is-module">
	<div class="es-side-widget__hd">
		<div class="es-side-widget__title">
			<?php echo JText::_('APP_PAGE_NEWS_WIDGET_TITLE');?>

			<span class="es-side-widget__label">(<?php echo $total;?>)</span>
		</div>
	</div>

	<div class="es-side-widget__bd">
		<ul class="o-nav o-nav--stacked">
			<?php if ($items) { ?>
				<?php foreach ($items as $item) { ?>
				<li class="o-nav__item">
					<a href="<?php echo $item->getPermalink();?>"><?php echo $item->_('title'); ?></a>
					<div class="t-text--muted t-fs--sm">
						<?php echo ES::date($item->created)->format(JText::_('DATE_FORMAT_LC3')); ?>
					</div>
				</li>
				<?php } ?>
			<?php } else { ?>
				<?php echo JText::_('APP_PAGE_NEWS_WIDGET_NO_ANNOUNCEMENTS_YET'); ?>
			<?php } ?>
		</ul>

	</div>
</div>
