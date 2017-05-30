<?php
/**
 * @package SJ Slideshow for Hikashop
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 */

defined('_JEXEC') or die;
?>
<div class="sl-container"
	 data-cycle-target="<?php echo htmlentities(HKSlideshowHelper::	parseTarget($params->get('item_link_target')),ENT_QUOTES); ?>"
	>
	<?php
	foreach ($list as $item) {

		$condition = (int)$params->get('display_votes',1) || (int)$params->get('display_add_to_cart',1) || (int)$params->get('display_add_to_wishlist',1);
		if($condition) include JModuleHelper::getLayoutPath($module->module, $layout.'_others');

		$img = HKSlideshowHelper::getPHKImage($item, $params);
		$rand_keys = array_rand($effect);
		$desc = HKSlideshowHelper::_cleanText($item->description);
		$desc = HKSlideshowHelper::truncate($desc, $params->get('item_desc_max_chars', 100));
		$show_desc = $params->get('show_introtext');
		$show_readmore = $params->get('item_detail_display');
		$show_title = $params->get('item_title_display');
		$condition = ($show_desc == 0   &&  $desc =='' && $minivotes == '' && $btn_add == '');
		?>
		<div class="sl-item "
			 data-cycle-emptycustom ="<?php echo ($condition)?'empty':'';  ?>"
			 data-cycle-readmoretext="<?php echo $params->get('item_detail_text','Detail'); ?>"
			 data-cycle-price="<?php $price = ($params->get('item_price_display',1))?htmlentities($item->_price, ENT_QUOTES | ENT_IGNORE, "UTF-8"):''; echo $price?>"
			 data-cycle-href="<?php echo $item->link ?>"
			 data-tile-vertical="<?php echo ($effect[$rand_keys] == 'tileSlide'|| $effect[$rand_keys] == 'tileBlind')?'false':''; ?>"
			 data-cycle-desc="<?php echo  htmlentities($desc, ENT_QUOTES | ENT_IGNORE, "UTF-8"); ?>"
			 data-cycle-fx="<?php echo ($fx == 'random')?$effect[$rand_keys]:$fx; ?>"
			 data-cycle-subtitle="<?php echo htmlentities(HKSlideshowHelper::truncate($item->title, $params->get('item_title_max_characs')),ENT_QUOTES | ENT_IGNORE, "UTF-8"); ?>"
			 data-cycle-titlehover="<?php echo htmlentities($item->title ,ENT_QUOTES | ENT_IGNORE, "UTF-8"); ?>"
			 data-cycle-btnadd = "<?php echo htmlentities($btn_add , ENT_QUOTES | ENT_IGNORE, "UTF-8"); ?>"
			 data-cycle-votes = "<?php echo htmlentities($minivotes , ENT_QUOTES | ENT_IGNORE, "UTF-8"); ?>"
			>
			<a href="<?php $item->link;?>" title="<?php echo $item->title ?>" <?php echo HKSlideshowHelper::parseTarget($params->get('item_link_target')); ?> >
				<?php if(!is_null($img)) echo HKSlideshowHelper::imageTag($img);?>
			</a>

		</div>
	<?php } ?>
	<div class="item-info"></div>
	<div class="sl-caption"></div>
	<div class='sl-control'>
		<span class='sl-prev'></span>
		<span class='sl-play-pause  <?php echo $play ? 'sl-pause' : 'sl-play'; ?>'></span>
		<span class='sl-next'></span>
	</div>
</div>
<?php if($progress) {?>
	<div class="sl-progress" ></div>
<?php } ?>
