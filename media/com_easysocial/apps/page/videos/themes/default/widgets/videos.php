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
			<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_FILTERS_RECENT_VIDEOS'); ?>

			<span class="es-side-widget__label">(<?php echo $totalVideos;?>)</span>
		</div>
	</div>
	<div class="es-side-widget__bd">
		<?php echo $this->html('widget.videos', $videos, 'COM_EASYSOCIAL_WIDGETS_NO_VIDEOS_CURRENTLY'); ?>

		<?php if ($videos) { ?>
		<div>
			<?php echo $this->html('widget.viewAll', 'COM_EASYSOCIAL_VIEW_ALL_VIDEOS', ESR::videos(array('uid' => $page->getAlias(), 'type' => SOCIAL_TYPE_PAGE))); ?>
		</div>
		<?php } ?>
	</div>
</div>
