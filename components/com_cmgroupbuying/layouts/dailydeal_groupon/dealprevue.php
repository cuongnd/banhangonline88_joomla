<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

$deal = $this->deal;
$today = CMGroupBuyingHelperDateTime::getCurrentDateTime();
$today = strtotime($today);
$endDate = strtotime($deal['end_date']);
$configuration = $this->configuration;
$optionsOfDeal = $deal['options'];
$imageWidth = 450;
$diff = $endDate - $today;

if($deal['min_bought'] == '')
{
	$deal['min_bought'] = 1;
}

if($configuration['jquery_loading'] != "" && version_compare(JVERSION, '3.0.0', 'lt'))
{
	JFactory::getDocument()->addScript($configuration['jquery_loading']);
}

JFactory::getDocument()->addScript('components/com_cmgroupbuying/assets/js/jquery.bpopup.min.js');
JFactory::getDocument()->addScript('components/com_cmgroupbuying/assets/js/jquery.countdown.js');

// In case the site is Multi-lingual
$lang = JFactory::getLanguage();
$langTag = $lang->getTag();
$langArr = explode('-', $langTag);
$langCode = $langArr[0];
$fbLang = str_replace('-', '_', $langTag);
$jsFilePath = JPATH_SITE. '/' . "components" . '/' . "com_cmgroupbuying" . '/' . "assets" . '/' . "js" . '/' . "jquery.countdown-" . $langCode . ".js";

if(file_exists($jsFilePath))
{
	JFactory::getDocument()->addScript('components/com_cmgroupbuying/assets/js/jquery.countdown-' . $langCode . '.js');
}
else
{
	JFactory::getDocument()->addScript('components/com_cmgroupbuying/assets/js/jquery.countdown-en.js');
}

if($configuration['background_override'] == 1 && file_exists($deal['background_image'])):
?>
<script>
	jQuery("#cm_deals_stretch_image").css("background-image","url(<?php echo JURI::root() . $deal['background_image']; ?>)");
</script>
<?php
endif;

$partner = $this->partner;

if(isset($optionsOfDeal[1]))
{
	$dealPrice = CMGroupBuyingHelperDeal::displayDealPrice($optionsOfDeal[1]['price']);
	$originalPrice = CMGroupBuyingHelperDeal::displayDealPrice($optionsOfDeal[1]['original_price']);
	$savedPrice = CMGroupBuyingHelperDeal::displayDealPrice($optionsOfDeal[1]['original_price'] - $optionsOfDeal[1]['price']);

	if($optionsOfDeal[1]['original_price'] != 0)
	{
		$discount = 100 - round($optionsOfDeal[1]['price'] / $optionsOfDeal[1]['original_price'] * 100);
	}
	else
	{
		$discount = 0;
	}
}
else
{
	$dealPrice = 0;
	$originalPrice = 0;
	$savedPrice = 0;
	$discount = 0;
}

$discount .= "%";
$dealImages  = array();
$paidCoupons = 0;
$imageHeight = 0;

for($i = 1; $i <= 5; $i++)
{
	$columnName = 'image_path_' . $i;

	if($deal[$columnName] != '' && file_exists(JPATH_BASE . '/' . $deal[$columnName]))
	{
		$dealImages[] = JURI::root() . $deal[$columnName];
		list($width, $height, $type, $attribute) = getimagesize(JPATH_BASE . '/' . $deal[$columnName]);
		$ratio = $width / $height;
		$resizedHeight = $imageWidth / $ratio;

		if($resizedHeight > $imageHeight)
		{
			$imageHeight = $resizedHeight;
		}
	}
}
?>
<script type="text/javascript">
function mysqlTimeStampToDate(timestamp)
{
	// Function parses MySQL datetime string and returns Javascript Date object
	// Input has to be in this format: 2007-06-05 15:26:02
	var regex=/^([0-9]{2,4})-([0-1][0-9])-([0-3][0-9]) (?:([0-2][0-9]):([0-5][0-9]):([0-5][0-9]))?$/;
	var parts=timestamp.replace(regex,"$1 $2 $3 $4 $5 $6").split(' ');
	return new Date(parts[0],parts[1]-1,parts[2],parts[3],parts[4],parts[5]);
}

jQuery(document).ready(function()
{
	jQuery('#deal_countdown').countdown({until: +<?php echo $diff; ?>,
	description: '', layout: '{dnn} {dl} {hnn}:{mnn}:{snn}'});
	jQuery('#deal_countdown').countdown(jQuery.countdown.regional['<?php echo $langCode; ?>']);
});
</script>
<div id="fb-root"></div>
<script>
;(function($) {
	$(function() {
		$('#buy_button').bind('click', function(e) {
			e.preventDefault();
			$('#modal_options_window').bPopup({followSpeed: 0});
		});
	});
})(jQuery);
</script>
<script>(function(d, s, id) {
	var js, fjs = d.getElementsByTagName(s)[0];
	if (d.getElementById(id)) {return;}
	js = d.createElement(s); js.id = id;
	js.src = "//connect.facebook.net/<?php echo $fbLang; ?>/all.js#xfbml=1";
	fjs.parentNode.insertBefore(js, fjs);
	}(document, 'script', 'facebook-jssdk'));</script>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style.css" type="text/css" />
<?php if(JFactory::getLanguage()->isRTL()): ?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style-rtl.css" type="text/css" />
<?php endif; ?>
<div class="deal_section_1 clearfix">
	<div class="deal_section_1_top">
		<p class="deal_name"><?php echo $deal['name']; ?></p>
		<p class="deal_short_description"><?php echo $deal['short_description']; ?></p>
	</div>
	<div class="deal_section_1_left">
		<div class="price_block deal_detail_block clearfix">
			<div class="tag">
				<div>
						<?php if(count($optionsOfDeal) == 1): ?>
							<p class="price"><?php echo $dealPrice; ?></p>
						<?php elseif(count($optionsOfDeal) > 1): ?>
							<p class="price price_multi_option"><span class="price_label"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_MULTI_OPTION_FROM'); ?></span><?php echo $dealPrice; ?></p>
							<div class="multi_option_modal" id="modal_options_window" style="display:none;">
								<div class="modal_top">
									<div class="deal_options">
										<div class="page_header">
											<h1><?php echo JText::_('COM_CMGROUPBUYING_DEAL_MULTI_OPTION_CHOOSE_YOUR_DEAL'); ?></h1>
										</div>
										<table>
											<tbody>
												<?php
												foreach($optionsOfDeal as $option):
													$optionDiscountPercent = 100 - round($option['price'] / $option['original_price'] * 100);
													$optionDiscountPercent = $optionDiscountPercent . "%";
												?>
												<tr>
													<td >
														<div class="option_name">
															<div><?php echo $option['name']; ?></div>
														</div>
														<div class="details">
															<div class="value"><em><?php echo CMGroupBuyingHelperDeal::displayDealPrice($option['original_price']); ?></em> <?php echo JText::_('COM_CMGROUPBUYING_DEAL_MULTI_OPTION_VALUE'); ?></div><div class="discount"> - <em><?php echo $optionDiscountPercent; ?></em> <?php echo JText::_('COM_CMGROUPBUYING_DEAL_MULTI_OPTION_OFF'); ?> </div><div class="save"> - <?php echo JText::_('COM_CMGROUPBUYING_DEAL_MULTI_OPTION_SAVE'); ?> <em><?php echo CMGroupBuyingHelperDeal::displayDealPrice(($option['original_price'] - $option['price'])); ?></em></div>
														</div>
													</td>
													<td class="status">
														<div><?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_BOUGHT', 0); ?> </div>
													</td>
													<td class="purchase">
														<div class="buy_button"><span class="amount"><?php echo CMGroupBuyingHelperDeal::displayDealPrice($option['price']); ?></span></div>
													</td>
												</tr>
												<?php endforeach; ?>
											</tbody>
										</table>
									</div>
								</div>
								<div class="modal_bottom">&nbsp;</div>
							</div>
						<?php endif;?>
						<div class="buy_button"><p><?php echo JText::_('COM_CMGROUPBUYING_PREVIEW_BUTTON'); ?></p></div>
				</div>
			</div>
			<div class="clearfix">
				<div class="discount_info">
					<dl class="deal_value">
						<dt><?php echo JText::_('COM_CMGROUPBUYING_DEAL_ORIGINAL_PRICE'); ?></dt>
						<dd><?php echo $originalPrice; ?></dd>
					</dl>
					<dl class="deal_discount">
						<dt><?php echo JText::_('COM_CMGROUPBUYING_DEAL_DISCOUNT'); ?></dt>
						<dd><?php echo $discount; ?></dd>
					</dl>
					<dl class="deal_save">
						<dt><?php echo JText::_('COM_CMGROUPBUYING_DEAL_SAVED_VALUE'); ?></dt>
						<dd><?php echo $savedPrice; ?></dd>
					</dl>
				</div>
			</div>
		</div>
		<div class="remaining_time_block deal_detail_block clearfix">
			<div class="countdown_block">
				<h3><?php echo JText::_('COM_CMGROUPBUYING_TIME_LEFT_TO_BUY'); ?></h3>
				<div id="deal_countdown"></div>
			</div>
		</div>
		<div class="bought_block deal_detail_block clearfix">
			<h3>
				<?php echo JText::sprintf('COM_CMGROUPBUYING_BOUGHT_MESSAGE', $paidCoupons); ?>
			</h3>
			<div class="tip_meter">
				<div class="tipping_point" style="left: <?php echo 190 * ($paidCoupons/$deal['min_bought']); ?>px"></div>
				<div class="progress_bar">
					<div class="pre_tipped left_end_cap"></div>
					<div class="pre_tipped left_bar " style="width: <?php echo 190 * ($paidCoupons/$deal['min_bought']); ?>px"></div>
					<div class="pre_tipped right_end_cap"></div>
				</div>
				<span class="min">0</span>
				<span class="max"><?php echo $deal['min_bought']; ?></span>
				<div class="need_more">
					<?php echo JText::sprintf('COM_CMGROUPBUYING_NEED_MORE_MESSAGE', $deal['min_bought'] - $paidCoupons); ?>
				</div>
			</div>
		</div>
		<?php
		$pageUrl = JURI::root();
		$shareMessage = '';
		?>
		<div class="share_buttons">
			<ul>
				<li>
					<g:plusone size="medium" annotation="none"></g:plusone>
					<script type="text/javascript">
					  (function() {
						var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
						po.src = 'https://apis.google.com/js/plusone.js';
						var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
					  })();
					</script>
				</li>
				<li>
					<script src="//platform.twitter.com/widgets.js" type="text/javascript"></script>
					<div>
						<a id="custom_tweet_button" href="https://twitter.com/share?url=<?php echo urlencode($pageUrl); ?>&text=<?php echo $shareMessage; ?>" target="_blank"></a>
					</div>
				</li>
				<li>
					<div class="fb-like" data-send="true" data-layout="button_count" data-show-faces="false"></div>
				</li>
			</ul>
		</div>
	</div>

	<div class="deal_section_1_right">
		<?php if(count($dealImages) == 1): ?>
		<div class="deal_photo">
		<?php foreach($dealImages as $imagePath): ?>
			<img src="<?php echo $imagePath; ?>" width="<?php echo $imageWidth;?>px" />
		<?php endforeach; ?>
		</div>
		<?php elseif(count($dealImages) > 1):
			if(is_numeric($configuration['slideshow_switch_time']) && $configuration['slideshow_switch_time'] > 0)
			{
				$switchTime = $configuration['slideshow_switch_time'] * 1000;
			}
			else
			{
				$switchTime = 4000;
			}

			if(is_numeric($configuration['slideshow_fade_time']) && $configuration['slideshow_fade_time'] > 0)
			{
				$fadeTime = $configuration['slideshow_fade_time'] * 1000;
			}
			else
			{
				$fadeTime = 1000;
			}
		?>
		<script type="text/javascript">
			function slideSwitch()
			{
				var $active = jQuery('#slideshow img.active');
				if ($active.length == 0)
				{
					$active = jQuery('#slideshow img:last');
				}

				var $next =  $active.next().length ? $active.next() : jQuery('#slideshow IMG:first');

				$active.addClass('last_active');

				$next.css({opacity: 0.0})
					.addClass('active')
					.animate({opacity: 1.0}, <?php echo $fadeTime; ?>, function() {
						$active.removeClass('active last_active');
				});
			}

			jQuery(function()
			{
				setInterval( "slideSwitch()", <?php echo $switchTime; ?> );
			});
		</script>
		<div id="slideshow" class="deal_photo" style="width: <?php echo $imageWidth; ?>px; height: <?php echo $imageHeight; ?>px">
		<?php for($i = 1; $i < count($dealImages); $i++): ?>
			<img src="<?php echo $dealImages[$i]; ?>" width="<?php echo $imageWidth;?>px" />
		<?php endfor; ?>
			<img src="<?php echo $dealImages[0]; ?>" width="<?php echo $imageWidth;?>px" />
		</div>
		<div class="highlight_terms">
			<div class="deal_highlight">
				<h2><?php echo JText::_('COM_CMGROUPBUYING_DEAL_HIGHLIGHTS'); ?></h2>
				<?php echo $deal['highlights']; ?>
			</div>
			<div class="deal_terms">
				<h2><?php echo JText::_('COM_CMGROUPBUYING_DEAL_TERMS'); ?></h2>
				<?php echo $deal['terms']; ?>
			</div>
		</div>
		<?php endif; ?>
	</div>
</div>
<div class="deal_section_2 clearfix">
	<div class="deal_section_2_left">
		<?php echo $deal['description']; ?>
		<?php
		if($configuration['facebook_comment'] == 1 || $configuration['disqus_comment'] == 1)
			echo '<h2>' . JText::_('COM_CMGROUPBUYING_DEAL_COMMENT') . '</h2>';
		?>

		<?php
		if($configuration['facebook_comment'] == 1):
			$document = JFactory::getDocument();
			$document->addCustomTag('<meta property="fb:app_id" content="' . $configuration['facebook_app_id'] . '"/>');
			$document->addCustomTag('<meta property="fb:admins" content="' . $configuration['facebook_admin_user_id'] . '"/>');
			$numPosts = $configuration['facebook_comment_num_posts'] > 0 ? $configuration['facebook_comment_num_posts'] : 10;
			$commentWidth = $configuration['facebook_comment_width'] > 0 ? $configuration['facebook_comment_width'] : 400;
		?>
		<div class="facebook_comment">
			<div class="fb-comments" data-href="<?php echo $pageUrl; ?>" data-num-posts="<?php echo $numPosts; ?>" data-width="<?php echo $commentWidth; ?>"></div>
		</div>
		<?php endif; ?>

		<?php
		if($configuration['disqus_comment'] == 1 && $configuration['disqus_shortname'] != ''):
			$disqusShortname = $configuration['disqus_shortname'];
			$disqusIdentifier = $deal['id'] . '-' . $langCode;
		?>
		<div class="disqus_comment">
			<div id="disqus_thread"></div>
			<script type="text/javascript">
				var disqus_shortname = '<?php echo $disqusShortname; ?>';
				var disqus_identifier = '<?php echo $disqusIdentifier; ?>';
				<?php if($configuration['disqus_multilanguage'] == 1): ?>
				var disqus_config = function () {
					this.language = '<?php echo $langCode ?>';
				};
				<?php endif; ?>
				(function() {
					var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;
					dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';
					(document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);
				})();
			</script>
		</div>
		<?php endif; ?>
	</div>
	<div class="deal_section_2_right">
		<h2><?php echo $partner['name']; ?></h2>
		<?php if($partner['logo'] != ''): ?>
		<div class="partner_logo"><img src="<?php echo $partner['logo']; ?>" /></div>
		<?php endif; ?>
		<p><?php echo $partner['about']; ?></p>
		<a target="_blank" href="<?php echo $partner['website']; ?>"><?php echo $partner['website']; ?></a>
		<?php
		$locations = array();

		for($i=1; $i<=5; $i++)
		{
			$locationElementsJSON = $partner['location' . $i];
			$locationElementsArray = json_decode($locationElementsJSON);

			if(!empty($locationElementsArray))
			{
				$name = $locationElementsArray->name;
				$latitude = $locationElementsArray->latitude;
				$longitude = $locationElementsArray->longitude;

				if($name != "" && is_numeric($latitude) && is_numeric($longitude))
				{
					$locations[] =  $locationElementsArray;
				}
			}
		}

		if(empty($locations))
		{
			$defaultLatitude = '';
			$defaultLongitude = '';
		}
		else
		{
			$defaultLatitude = $locations[0]->latitude;
			$defaultLongitude = $locations[0]->longitude;
		}

		if ($defaultLatitude != '' && $defaultLongitude != '')
		{
			if ($partner['map_zoom_level'] > 0)
			{
				$zoomLevel = $partner['map_zoom_level'];
			}
			else
			{
				$zoomLevel = 14;
			}
		?>
			<script type="text/javascript" src="//maps.googleapis.com/maps/api/js?sensor=true"></script>
			<script type="text/javascript">
				var map;

				function initialize()
				{
					var latlng = new google.maps.LatLng(<?php echo $defaultLatitude; ?>, <?php echo $defaultLongitude; ?>);
					var myOptions = {
						zoom: <?php echo $zoomLevel; ?>,
						center: latlng,
						mapTypeId: google.maps.MapTypeId.ROADMAP
					};

					map = new google.maps.Map(document.getElementById("map_canvas"), myOptions);
					<?php
					$n = 1;
					foreach($locations as $location):
						$js = 'var latlng' . $n . ' = new google.maps.LatLng(' . $location->latitude . ', ' . $location->longitude . ');
							var marker' . $n . ' = new google.maps.Marker({
							position: latlng' . $n . ',
							map: map,
							title: "' .$location->name . '"});';
						$n++;
						echo $js;
					endforeach;
					?>
				}

				function markerTrigger(coordinateString)
				{
					coordinateArray = coordinateString.split(",");
					var point = new google.maps.LatLng(
						parseFloat(coordinateArray[0]),
						parseFloat(coordinateArray[1]));
					window.map.setCenter(point);
				}

				jQuery(document).ready(function()
				{
					initialize();
				});
			</script>
			<div id="map_canvas"></div>
			<div class="partner_address">
			<?php
			foreach($locations as $location):
				echo '<div class="address_name"><span onclick="markerTrigger(\''.$location->latitude.','.$location->longitude.'\')">' . $location->name . '</span></div>';
				echo "<p>" . nl2br($location->address) . "<br />" . $location->phone . "</p>";
				echo '<a href="//maps.google.com/maps?f=d&daddr=' . $location->address. '" target="_blank">' . JText::_('COM_CMGROUPBUYING_DEAL_GET_DIRECTION') . '</a>';
			endforeach;
			?>
			</div>
		<?php
		}
		?>
	</div>
</div>