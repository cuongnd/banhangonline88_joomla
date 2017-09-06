<?php
/**
* @package		EasySocial
* @copyright	Copyright (C) 2010 - 2014 Stack Ideas Sdn Bhd. All rights reserved.
* @license		GNU/GPL, see LICENSE.php
* EasySocial is free software. This version may have been modified pursuant
* to the GNU General Public License, and as distributed it includes or
* is derivative of works licensed under the GNU General Public License or
* other free or open source software licenses.
* See COPYRIGHT.php for copyright notices and details.
*/
defined('_JEXEC') or die('Unauthorized Access');
?>
<div class="es-widget is-module">
	<div class="es-widget-head">
		<div class="pull-left widget-title">
			<?php echo JText::_('COM_EASYSOCIAL_VIDEOS_FILTERS_RECENT_VIDEOS'); ?>
		</div>

		<span class="widget-label">(<?php echo $totalVideos;?>)</span>

		<?php if ($videos) { ?>
		<a class="t-fs--sm t-lg-pull-right" href="<?php echo FRoute::videos(array('uid' => $group->getAlias(), 'type' => SOCIAL_TYPE_GROUP));?>"><?php echo JText::_('COM_EASYSOCIAL_VIEW_ALL_VIDEOS');?></a>
		<?php } ?>
	</div>
	<div class="es-widget-body">
		<ul class="widget-list o-nav o-nav--stacked">
			<?php if ($videos) { ?>
				<?php foreach ($videos as $video) { ?>
				<li>
					<a href="<?php echo $video->getPermalink();?>" data-es-video="<?php echo $video->id; ?>" class="pl-0 pr-5">
				        <div class="media">
				            <div class="media-object pull-left">
								<img class="widget-video-preview" src="<?php echo $video->getThumbnail();?>" alt="<?php echo $this->html('string.escape' , $video->getTitle());?>" />
				            </div>
				            <div class="media-body">
	    						<div class="widget-video-title"><?php echo $video->getTitle(); ?></div>
	    					</div>
	        				<div class="widget-video-time"><?php echo $video->getDuration();?></div>
				        </div>
			        </a>
				</li>
				<?php } ?>
			<?php } else { ?>
			<li>
				<div class="t-fs--sm">
					<?php echo JText::_('COM_EASYSOCIAL_WIDGETS_NO_VIDEOS_CURRENTLY'); ?>
				</div>
			</li>
			<?php } ?>
		</ul>
	</div>
</div>
