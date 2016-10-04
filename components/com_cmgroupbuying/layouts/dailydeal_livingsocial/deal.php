<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

if($this->found == false)
{
	echo '<p class="cmgroupbuying_error">' . JText::_('COM_CMGROUPBUYING_DEAL_NOT_FOUND_MESSAGE') . '</p>';
}
else
{
	$today = CMGroupBuyingHelperDateTime::getCurrentDateTime();
	$today = strtotime($today);
	$deal = $this->deal;
	$startDate = strtotime($deal['start_date']);
	$endDate = strtotime($deal['end_date']);
	$configuration = $this->configuration;
	$optionsOfDeal = $this->optionsOfDeal;
	$imageWidth = 280;

	if($deal['voided'] == 1)
		$diff = 0;
	else
		$diff = $endDate - $today;

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
	$jsFilePath = JPATH_SITE. '/components/com_cmgroupbuying/assets/jsjquery.countdown-' . $langCode . '.js';

	if(file_exists($jsFilePath))
	{
		JFactory::getDocument()->addScript('components/com_cmgroupbuying/assets/js/jquery.countdown-' . $langCode . '.js');
	}
	else
	{
		JFactory::getDocument()->addScript('components/com_cmgroupbuying/assets/js/jquery.countdown-en.js');
	}

	$currencyPrefix = $configuration['currency_prefix'];
	$currencyPostfix = $configuration['currency_postfix'];
	$partner = $this->partner;
	$dealPrice = CMGroupBuyingHelperDeal::displayDealPrice($optionsOfDeal[1]['price'], false);
	$discount = 100 - round($optionsOfDeal[1]['price'] / $optionsOfDeal[1]['original_price'] * 100);
	$discount .= "%";
	$dealImages = array();
	$paidCoupons = CMGroupBuyingHelperDeal::countPaidCoupon($deal['id']);
	$imageHeight = 0;

	for($i = 1; $i <= 5; $i++)
	{
		$columnName = 'image_path_' . $i;

		if($deal[$columnName] != '')
		{
			$dealImages[] = JURI::root() . $deal[$columnName];

			if(file_exists(JPATH_BASE . '/' . $deal[$columnName]))
			{
				list($width, $height, $type, $attribute) = getimagesize(JPATH_BASE . '/' . $deal[$columnName]);
				$ratio = $width / $height;
				$resizedHeight = $imageWidth / $ratio;
				if($resizedHeight > $imageHeight)
				{
					$imageHeight = $resizedHeight;
				}
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
	<div class="deal_section clearfix">
		<div class="deal_section_left">
			<?php
			if(count($dealImages) == 1)
			{
				echo '<div class="deal_photo"><img src="' . $dealImages[0] . '" width="' . $imageWidth . 'px" /></div>';
			}
			elseif(count($dealImages) > 1)
			{
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

						var $next =  $active.next().length ? $active.next() : jQuery('#slideshow img:first');

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
			<?php
				echo '<div id="slideshow" class="deal_photo" style="width: ' . $imageWidth . 'px; height: ' . $imageHeight . 'px">';
			?>
			<?php for($i = 1; $i < count($dealImages); $i++): ?>
				<img src="<?php echo $dealImages[$i]; ?>" width="<?php echo $imageWidth;?>px" />
			<?php endfor; ?>
			<img src="<?php echo $dealImages[0]; ?>" width="<?php echo $imageWidth;?>px" />
			</div>
			<?php
			}
			?>
			<div class="partner">
				<h2><?php echo $partner['name']; ?></h2>
				<p><?php echo $partner['about']; ?></p>
				<a target="_blank" href="<?php echo $partner['website']; ?>"><?php echo $partner['website']; ?></a>
				<?php
				$locations = array();

				for($i=1; $i<=5; $i++)
				{
					$locationElementsJSON   = $partner['location' . $i];
					$locationElementsArray  = json_decode($locationElementsJSON);

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
					if($partner['map_zoom_level'] > 0)
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
		<div class="deal_section_right">
			<div class="deal_name">
				<h1><?php echo $deal['name']; ?></h1>
				<p class="deal_short_description"><?php echo $deal['short_description']; ?></p>
			</div>
			<div class="price_block">
				<div class="price">
				<?php if(count($optionsOfDeal) == 1): ?>
					<?php if(JFactory::getLanguage()->isRTL()): ?>
						<sup><?php echo $currencyPostfix; ?></sup><?php echo $dealPrice; ?><sup><?php echo $currencyPrefix; ?></sup>
					<?php else: ?>
						<sup><?php echo $currencyPrefix; ?></sup><?php echo $dealPrice; ?><sup><?php echo $currencyPostfix; ?></sup>
					<?php endif; ?>
				<?php else: ?>
					<div class="from"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_MULTI_OPTION_FROM'); ?></div><sup><?php echo $currencyPrefix; ?></sup><?php echo CMGroupBuyingHelperDeal::displayDealPrice($optionsOfDeal[1]['price'], false); ?><sup><?php echo $currencyPostfix; ?></sup>
				<?php endif; ?>
				</div>
				<div class="buy_button">
					<?php
						if($deal['voided'] == 1)
						{
							echo '<div class="cm_button voided_button"><span>' .  JText::_('COM_CMGROUPBUYING_VOIDED_BUTTON') . '</span></div>';
						}
						elseif($deal['max_coupon'] != -1 && $paidCoupons >= $deal['max_coupon'])
						{
							echo '<div class="cm_button not_avail_button"><span>' .  JText::_('COM_CMGROUPBUYING_NOT_AVAIL_BUTTON') . '</span></div>';
						}
						elseif($startDate > $today)
						{
							echo '<div class="cm_button upcoming_button"><spanp>' .  JText::_('COM_CMGROUPBUYING_UPCOMING_BUTTON') . '</span></div>';
						}
						elseif($endDate < $today)
						{
							echo '<div class="cm_button expired_button"><span>' .  JText::_('COM_CMGROUPBUYING_EXPIRED_BUTTON') . '</span></div>';
						}
						else
						{
							if(count($optionsOfDeal) == 1):
						?>
							<form name="buy_button_form" method="get" action="<?php echo JRoute::_('index.php', false); ?>">
								<input type="hidden" name="option" value="com_cmgroupbuying" />
								<input type="hidden" name="controller" value="cart" />
								<input type="hidden" name="task" value="add_to_cart" />
								<input type="hidden" name="id" value="<?php echo $deal['id']; ?>" />
								<input type="hidden" name="option_id" value="1" />
							</form>
							<div class="cm_button available_button" onClick="document.buy_button_form.submit()"><span><?php echo JText::_('COM_CMGROUPBUYING_BUY_BUTTON'); ?></span></div>
						<?php
							else:
						?>
							<div id="buy_button" class="cm_button available_button"><span><?php echo JText::_('COM_CMGROUPBUYING_BUY_BUTTON'); ?></span></div>
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
																<form name="buy_button_form_<?php echo $option['option_id']; ?>" method="get" action="<?php echo JRoute::_('index.php', false); ?>">
																	<input type="hidden" name="option" value="com_cmgroupbuying" />
																	<input type="hidden" name="controller" value="cart" />
																	<input type="hidden" name="task" value="add_to_cart" />
																	<input type="hidden" name="id" value="<?php echo $deal['id']; ?>" />
																	<input type="hidden" name="option_id" value="<?php echo $option['option_id']; ?>" />
																</form>
																<div onClick="document.buy_button_form_<?php echo $option['option_id']; ?>.submit()"><?php echo $option['name']; ?></div>
															</div>
															<div class="details">
																<div class="value"><em><?php echo CMGroupBuyingHelperDeal::displayDealPrice($option['original_price']); ?></em> <?php echo JText::_('COM_CMGROUPBUYING_DEAL_MULTI_OPTION_VALUE'); ?></div><div class="discount"> - <em><?php echo $optionDiscountPercent; ?></em> <?php echo JText::_('COM_CMGROUPBUYING_DEAL_MULTI_OPTION_OFF'); ?> </div><div class="save"> - <?php echo JText::_('COM_CMGROUPBUYING_DEAL_MULTI_OPTION_SAVE'); ?> <em><?php echo CMGroupBuyingHelperDeal::displayDealPrice(($option['original_price'] - $option['price'])); ?></em></div>
															</div>
														</td>
														<td class="status">
															<div><?php echo JText::sprintf('COM_CMGROUPBUYING_DEAL_BOUGHT', CMGroupBuyingHelperDeal::countPaidOption($deal['id'], $option['option_id'])); ?> </div>
														</td>
														<td class="purchase">
															<div class="option_buy_button">
																<div class="cm_button available_button popup_available_button" onClick="document.buy_button_form_<?php echo $option['option_id']; ?>.submit()"><span class="symbol"><?php echo $currencyPrefix; ?></span><span class="amount"><?php echo CMGroupBuyingHelperDeal::displayDealPrice($option['price'], false); ?></span><span class="symbol"><?php echo $currencyPostfix; ?></span></div>
															</div>
														</td>
													</tr>
													<?php endforeach; ?>
												</tbody>
											</table>
										</div>
									</div>
									<div class="modal_bottom">&nbsp;</div>
								</div>
						<?php
							endif;
						}
						?>
				</div>
				<ul class="clearfix deal_detail_info">
					<li>
						<div id="percentage" class="value"><?php echo $discount; ?></div>
						<div class="label"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_DISCOUNT'); ?></div>
					</li>
					<li class="purchased">
						<div class="value"><?php echo $paidCoupons; ?></div>
						<div class="label"><?php echo JText::_('COM_CMGROUPBUYING_BOUGHT'); ?></div>
					</li>
					<li class="last">
						<div class="value">
							<div class="countdown_block">
								<div id="deal_countdown"></div>
							</div>
						</div>
						<div class="label"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_REMAINING'); ?></div>
					</li>
				</ul>
			</div>
			<?php
			$pageUrl = 'index.php?option=com_cmgroupbuying&view=deal&id=' . $deal['id'] . '&alias=' . $deal['alias'];
			$shareUrl = $pageUrl;
			$user = JFactory::getUser();
			$userId = $user->id;

			if($userId > 0 && $configuration['deal_referral'] == 1 && ($configuration['point_system'] == "aup" || $configuration['point_system'] == "jomsocial"))
			{
				if($configuration['point_system'] == "aup" && CMGroupBuyingHelperAlphauserpoints::checkInstalled() == true)
				{
					require_once JPATH_SITE . '/' . 'components' . '/' . 'com_alphauserpoints' . '/' . 'helper.php';
					$referreId = AlphaUserPointsHelper::getAnyUserReferreId($userId);
					$shareUrl .= '&referrer=' . $referreId;
				}
				elseif($configuration['point_system'] == "jomsocial" && CMGroupBuyingHelperJomsocial::checkInstalled() == true)
				{
					$shareUrl .= '&referrer=' . $userId;
				}
			}

			$pageUrl = str_replace(JURI::root(true), '', substr(JURI::root(), 0, -1)) . CMGroupBuyingHelperCommon::prepareRedirect($pageUrl);
			$shareUrl = str_replace(JURI::root(true), '', substr(JURI::root(), 0, -1)) . CMGroupBuyingHelperCommon::prepareRedirect($shareUrl);
			$shareMessage = str_replace("\"", "'", $deal['name']);
			$shareMessage = str_replace(" ", "+", $shareMessage);
			$shareMessage = str_replace("%", "%25", $shareMessage);
			?>
			<div class="social_share">
				<div class="share_buttons">
					<ul>
						<li>
							<strong><?php echo JText::_('COM_CMGROUPBUYING_SHARE_DEAL'); ?></strong>
						</li>
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
								<a id="custom_tweet_button" href="https://twitter.com/share?url=<?php echo urlencode($shareUrl); ?>&text=<?php echo $shareMessage; ?>" target="_blank"></a>
							</div>
						</li>
						<li>
							<div class="fb-like" data-href="<?php echo $shareUrl; ?>" data-send="true" data-layout="button_count" data-width="100" data-show-faces="false"></div>
						</li>
					</ul>
				</div>
			</div>
			<div class="clearfix"></div>
			<div class="deal_description">
				<?php echo $deal['description']; ?>
			</div>
		</div>
		<div class="deal_detail">
			<?php if($deal['highlights'] != ''): ?>
			<div class="deal_highlight">
				<h2><?php echo JText::_('COM_CMGROUPBUYING_DEAL_HIGHLIGHTS'); ?></h2>
				<?php echo $deal['highlights']; ?>
			</div>
			<?php endif; ?>
			<?php if($deal['terms'] != ''): ?>
			<div class="deal_terms">
				<h2><?php echo JText::_('COM_CMGROUPBUYING_DEAL_TERMS'); ?></h2>
				<?php echo $deal['terms']; ?>
			</div>
			<?php endif; ?>
			<?php
			$document = JFactory::getDocument();
			$siteTitle = htmlspecialchars(JFactory::getConfig()->get('sitename'), ENT_QUOTES);
			$document->addCustomTag('<meta property="og:site_name" content="'. $siteTitle . '" />');
			$document->addCustomTag('<meta property="og:url" content="' . $pageUrl . '" />');
			$document->addCustomTag('<meta property="og:title" content="' . htmlspecialchars($deal['name'], ENT_QUOTES) . '" />');
			$document->addCustomTag('<meta property="og:type" content="product" />');

			if(isset($dealImages[0]))
				$document->addCustomTag('<meta property="og:image" content="' . $dealImages[0] . '" />');

			$document->addCustomTag('<meta property="og:description" content="' . htmlspecialchars($deal['short_description'], ENT_QUOTES) . '" />');
			?>

			<?php
			if($configuration['facebook_comment'] == 1 || $configuration['disqus_comment'] == 1)
				echo '<div class="deal_comment"><h2>' . JText::_('COM_CMGROUPBUYING_DEAL_COMMENT') . '</h2>';
			?>

			<?php
			if($configuration['facebook_comment'] == 1):
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

			<?php
			if($configuration['facebook_comment'] == 1 || $configuration['disqus_comment'] == 1)
				echo '</div>';
			?>

		</div>
	</div>
<?php
	if($configuration['background_override'] == 1 && file_exists($deal['background_image'])):
		echo '<script>jQuery.backstretch("' . JURI::root() . $deal['background_image'] . '");</script>';
	endif;
}
?>