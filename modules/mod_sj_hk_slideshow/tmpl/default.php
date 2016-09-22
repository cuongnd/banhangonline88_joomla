<?php
/**
 * @package SJ Slideshow for Hikashop
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 */

defined('_JEXEC') or die;

$tag_id = 'slideshow' . rand() . time();
ob_start();
?>
	#<?php echo $tag_id ?> .sl-progress{
	background-color:<?php echo $params->get('progress_color', '#2D6987'); ?>
	}
<?php
$css = ob_get_contents();
ob_end_clean();
$document = JFactory::getDocument();
$document->addStyleDeclaration($css);
JHtml::stylesheet('modules/' . $module->module . '/assets/css/slideshow.css');

if (!defined('SMART_JQUERY') && $params->get('include_jquery', 0) == "1") {
	JHtml::script('modules/' . $module->module . '/assets/js/jquery-1.8.2.min.js');
	JHtml::script('modules/' . $module->module . '/assets/js/jquery-noconflict.js');
	define('SMART_JQUERY', 1);
}

if (!defined('JQUERY_CYLE2')) {
	JHtml::script('modules/' . $module->module . '/assets/js/jquery.easing.min.js');
	JHtml::script('modules/' . $module->module . '/assets/js/jquery.cycle2.js');
	JHtml::script('modules/' . $module->module . '/assets/js/jquery.cycle2.transition.js');
	define('JQUERY_CYLE2', 1);
}
ImageHelper::setDefault($params);
$theme = $params->get('theme', 'theme1');
if ($params->get('pretext') != null) {
	?>
	<div class="pre-text">
		<?php echo $params->get('pretext'); ?>
	</div>
<?php } ?>
<?php if (!empty($list)) {
	$start = $params->get('start', 1);
	if ($start <= 0 || ($start > count($list))) {
		$start = 0;
	} else {
		$start = $start - 1;
	}
	$progress = $params->get('progress', 1);
	$play = $params->get('play', 1);
	$fx = ($params->get('effect') != 'random') ? $params->get('effect') : 'random';
	$effect = array(0 => 'tileBlind', 1 => 'tileSlide');
	?>
	<div class="slideshow pre-load <?php echo $theme; ?> " id="<?php echo $tag_id; ?>">
		<div class="sl-loading"></div>
		<?php
		include JModuleHelper::getLayoutPath($module->module, $layout . '_' . $theme);
		?>
	</div>
	<script ="text/javascript">
	        //<![CDATA[
	        jQuery(document).ready(function ($) {
		        ;
		        (function (element) {
			        var $element = $(element);
			        var $loading = $('.sl-loading', $element);
			        $loading.remove();
			        $element.removeClass('pre-load');
			        var $slideshow = $('.sl-container', $element);
			        var $sl_item = $('.sl-item', $element);
			        var $infor = $('.item-info', $element);
			        var $caption = $('.sl-caption', $element);
			        var $prev = $('.sl-prev', $element);
			        var $next = $('.sl-next', $element);
			        var $progress = $('.sl-progress', $element);
			        var $control = $('.sl-control', $element);
			        $control.delay(800).fadeIn(400);
			        <?php if($theme == 'theme2'){?>

			        var $prev = $('.ctr-prev', $element);
			        var $next = $('.ctr-next', $element);
			        var $pager = $('.ctr-page', $element);

			        <?php  }
			else if($theme == 'theme1'){?>
			        var $pager = $('.sl-pager', $element);
			        <?php }?>
			        <?php if($progress && $play && (count($list) > 1)) { ?>
			        $slideshow.on('cycle-initialized cycle-before', function (e, opts) {
				        $progress.stop(true).css({width: 0 });
				        $('.sl-caption', $slideshow).css({opacity: 1});
			        });

			        $slideshow.on('cycle-initialized cycle-after', function (e, opts) {
				        $('.sl-caption', $slideshow).css({opacity: 1});
				        if (!$slideshow.is('.cycle-paused'))
					        $progress.animate({ width: '100%' }, opts.timeout, 'linear');
			        });

			        $slideshow.on('cycle-paused', function (e, opts) {
				        $progress.stop();
			        });

			        $slideshow.on('cycle-resumed', function (e, opts, timeoutRemaining) {
				        $progress.animate({ width: '100%' }, timeoutRemaining, 'linear');
			        });
			        <?php } ?>
			        $slideshow.cycle({
				        easing: 'easeInOutQuint',
				        fx: '<?php echo ($fx == 'random')?$effect[0]:$fx; ?>',
				        slides: $sl_item,
				        autoHeight: 'container',
				        timeout:                <?php echo $params->get('timeout', 4000); ?>,
				        overlay: $infor,
				        caption: $caption,
				        overlayTemplate: "<?php echo  HKSlideshowHelper::getOverlayTemplate($params);  ?>",
				        captionTemplate: "<?php echo HKSlideshowHelper::getCaptionTemplate($params); ?>",
				        swipe:                    <?php echo ($params->get('swipe') == 1)?'true':'false'; ?>,
				        progress: $progress,
				        loader: true,
				        log: false,
				        allowWrap: true,
				        random: false,
				        speed:                <?php echo $params->get('speed',500); ?>,
				        startingSlide:          <?php echo $start ;?>,
				        pauseOnHover:            <?php echo ($params->get('pauseOnHover') == 1)?'true':'false'; ?>,
				        prev: $prev,
				        next: $next,
				        captionPlugin: '<?php echo ($params->get('overlay_effect') == 'none')?'caption':'caption2'; ?>',
				        overlayFxOut: '<?php echo  ($params->get('overlay_effect') == 'fade')?'fadeOut':'slideUp' ?>',
				        overlayFxIn: '<?php echo  ($params->get('overlay_effect') == 'fade')?'fadeIn':'slideDown' ?>',
				        captionFxOut: '<?php echo  ($params->get('overlay_effect') == 'fade')?'fadeOut':'slideUp' ?>',
				        captionFxIn: '<?php echo  ($params->get('overlay_effect') == 'fade')?'fadeIn':'slideDown' ?>',
				        <?php if($theme == 'theme2' || $theme == 'theme1'){?>
				        pager: $pager,
				        <?php } ?>
				        <?php if($theme == 'theme2'){?>
				        pagerTemplate: '<span> {{slideNum}} </span>',
				        <?php } ?>
				        pagerActiveClass: 'sl-pager-active'
			        });
			        <?php if (!$play){ ?>
			        $slideshow.cycle('pause');
			        <?php } ?>
			        <?php if($theme == 'theme3'){?>
			        $('.sl-play-pause', $element).click(function () {
				        if ($(this).hasClass('sl-play')) {
					        $(this).toggleClass('sl-play sl-pause');
					        $slideshow.cycle('resume');
				        } else {
					        $(this).toggleClass('sl-play sl-pause');
					        $slideshow.cycle('pause');
				        }
			        });
			        <?php } ?>


		        })('#<?php echo $tag_id; ?>');
	        });
	        //]]>
	        </script>

		<?php
		$condition = (int)$params->get('display_votes', 1) || (int)$params->get('display_add_to_cart', 1) || (int)$params->get('display_add_to_wishlist', 1);
		if ($condition) include JModuleHelper::getLayoutPath($module->module, $layout . '_others');
	?>

<?php if  ( $theme == 'theme1' ) { ?>
		<script ="text/javascript">
		        //<![CDATA[
		        jQuery(document).ready(function ($) {
			        ;
			        (function (element) {
				        var $element = $(element);
				        var $sl_item = $('.sl-item', $element);
				        $('.item-votes', $element).html('<p>FGFGFGFGFG</p>');
				        $sl_item.each(function () {
					        if(  $('.cycle-slide-active', $element) ) {
							        $('.item-votes', $element).html('<p>FGFGFGFGFG</p>');

					        };
				        });
			        })('#<?php echo $tag_id; ?>');
		        });
		        //]]>
		        </script>
		<?php } ?>


<?php } else {
	echo JText::_('Has no content to show!');
}?>
<?php if ($params->get('posttext') != null) { ?>
	<div class="post-text">
		<?php echo $params->get('posttext'); ?>
	</div>
<?php } ?>