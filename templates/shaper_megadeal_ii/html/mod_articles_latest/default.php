<?php
/**
 * @package     Joomla.Site
 * @subpackage  mod_articles_latest
 *
 * @copyright   Copyright (C) 2005 - 2015 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('_JEXEC') or die;
?>
<div class="latestnews<?php echo $moduleclass_sfx; ?>">
<?php foreach ($list as $item) : 
	$item->images = json_decode($item->images);
?>
	<div itemscope itemtype="http://schema.org/Article">
		<?php if (!empty($item->images->image_intro)) {?>
			<div class="img-responsive">
				<img src="<?php echo $item->images->image_intro; ?>">
			</div>
		<?php } ?>
		<a href="<?php echo $item->link; ?>" class="soccer-news-title" itemprop="url">
			<span itemprop="name">
				<?php echo $item->title; ?>
			</span>
		</a>
		
	</div>
<?php endforeach; ?>
</div>
