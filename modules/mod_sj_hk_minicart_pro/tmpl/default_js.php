<?php
/**
 * @package SJ Minicart Pro for Hikashop
 * @version 1.0.0
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @copyright (c) 2014 YouTech Company. All Rights Reserved.
 * @author YouTech Company http://www.smartaddons.com
 *
 */

defined('_JEXEC') or die; ?>
<?php
$doc=JFactory::getDocument();
$doc->addScript(JUri::root().'/media/system/js/jquery-migrate-1.1.1.js');

	$scriptId = "script_" . $block->id;
	ob_start();
	?>
	<script type="text/javascript">
		jQuery(document).ready(function ($) {

			(function (minicart) {
				var $minicart = $(minicart);
				/*
				 * Set display jscrollpanel
				 */
				//var  jscrollDisplay = function (){
				$('.mc-list-inner', $minicart).mCustomScrollbar({
					scrollInertia: 550,
					horizontalScroll: false,
					mouseWheelPixels: 116,
					autoDraggerLength: true,
					scrollButtons: {
						enable: true,
						scrollAmount: 116
					},
					advanced: {
						updateOnContentResize: true,
						autoScrollOnFocus: false
					}, theme: "dark"
				});
				//return true;
				//}

				var $_mc_wrap = $('.mc-wrap', $minicart);
				var $_mc_content = $('.mc-content', $_mc_wrap);
				var _posLR = function () {
					var $_width_minicart = $minicart.width(), $_posleft = $minicart.offset().left,
						$_posright = $(window).innerWidth() - $_width_minicart - $_posleft,
						$_width_content = $_mc_content.width();
					if (($_posleft + $_width_content) > $(window).innerWidth()) {
						if (!$_mc_wrap.hasClass('mc-right')) {
							$_mc_wrap.removeClass('mc-left').addClass('mc-right');
						}
					} else {
						if (!$_mc_wrap.hasClass('mc-left')) {
							$_mc_wrap.removeClass('mc-right').addClass('mc-left');
						}
					}
				}

				_posLR();

				$(window).resize(function () {
					_posLR();
				});
				//jscrollDisplay();
				/*
				 * MouseOver - MouseOut
				 */
				/**
				  * jQuery.browser.mobile (http://detectmobilebrowser.com/)
				  * jQuery.browser.mobile will be true if the browser is a mobile device
				  **/
				(function(a){jQuery.browser.mobile=/android.+mobile|avantgo|bada\/|blackberry|blazer|compal|elaine|fennec|hiptop|iemobile|ip(hone|od)|iris|kindle|lge |maemo|midp|mmp|netfront|opera m(ob|in)i|palm( os)?|phone|p(ixi|re)\/|plucker|pocket|psp|symbian|treo|up\.(browser|link)|vodafone|wap|windows (ce|phone)|xda|xiino/i.test(a)||/1207|6310|6590|3gso|4thp|50[1-6]i|770s|802s|a wa|abac|ac(er|oo|s\-)|ai(ko|rn)|al(av|ca|co)|amoi|an(ex|ny|yw)|aptu|ar(ch|go)|as(te|us)|attw|au(di|\-m|r |s )|avan|be(ck|ll|nq)|bi(lb|rd)|bl(ac|az)|br(e|v)w|bumb|bw\-(n|u)|c55\/|capi|ccwa|cdm\-|cell|chtm|cldc|cmd\-|co(mp|nd)|craw|da(it|ll|ng)|dbte|dc\-s|devi|dica|dmob|do(c|p)o|ds(12|\-d)|el(49|ai)|em(l2|ul)|er(ic|k0)|esl8|ez([4-7]0|os|wa|ze)|fetc|fly(\-|_)|g1 u|g560|gene|gf\-5|g\-mo|go(\.w|od)|gr(ad|un)|haie|hcit|hd\-(m|p|t)|hei\-|hi(pt|ta)|hp( i|ip)|hs\-c|ht(c(\-| |_|a|g|p|s|t)|tp)|hu(aw|tc)|i\-(20|go|ma)|i230|iac( |\-|\/)|ibro|idea|ig01|ikom|im1k|inno|ipaq|iris|ja(t|v)a|jbro|jemu|jigs|kddi|keji|kgt( |\/)|klon|kpt |kwc\-|kyo(c|k)|le(no|xi)|lg( g|\/(k|l|u)|50|54|e\-|e\/|\-[a-w])|libw|lynx|m1\-w|m3ga|m50\/|ma(te|ui|xo)|mc(01|21|ca)|m\-cr|me(di|rc|ri)|mi(o8|oa|ts)|mmef|mo(01|02|bi|de|do|t(\-| |o|v)|zz)|mt(50|p1|v )|mwbp|mywa|n10[0-2]|n20[2-3]|n30(0|2)|n50(0|2|5)|n7(0(0|1)|10)|ne((c|m)\-|on|tf|wf|wg|wt)|nok(6|i)|nzph|o2im|op(ti|wv)|oran|owg1|p800|pan(a|d|t)|pdxg|pg(13|\-([1-8]|c))|phil|pire|pl(ay|uc)|pn\-2|po(ck|rt|se)|prox|psio|pt\-g|qa\-a|qc(07|12|21|32|60|\-[2-7]|i\-)|qtek|r380|r600|raks|rim9|ro(ve|zo)|s55\/|sa(ge|ma|mm|ms|ny|va)|sc(01|h\-|oo|p\-)|sdk\/|se(c(\-|0|1)|47|mc|nd|ri)|sgh\-|shar|sie(\-|m)|sk\-0|sl(45|id)|sm(al|ar|b3|it|t5)|so(ft|ny)|sp(01|h\-|v\-|v )|sy(01|mb)|t2(18|50)|t6(00|10|18)|ta(gt|lk)|tcl\-|tdg\-|tel(i|m)|tim\-|t\-mo|to(pl|sh)|ts(70|m\-|m3|m5)|tx\-9|up(\.b|g1|si)|utst|v400|v750|veri|vi(rg|te)|vk(40|5[0-3]|\-v)|vm40|voda|vulc|vx(52|53|60|61|70|80|81|83|85|98)|w3c(\-| )|webc|whit|wi(g |nc|nw)|wmlb|wonu|x700|xda(\-|2|g)|yas\-|your|zeto|zte\-/i.test(a.substr(0,4))})(navigator.userAgent||navigator.vendor||window.opera);
				//var isiDevice = /ipad|iphone|ipod/i.test(navigator.userAgent.toLowerCase());

				if (!jQuery.browser.mobile) {
					$_mc_wrap.hover(function () {
						var $this = $(this);
						if ($this.hasClass('over')) {
							return;
						}
						if ($minicart.data('timeout')) {
							clearTimeout($minicart.data('timeout'));
						}
						var timeout = setTimeout(function () {
							$this.addClass('over');
							$('.mc-content', $this).stop(false, true).slideDown('slow');
							//jscrollDisplay();
						}, 300);
						$minicart.data('timeout', timeout);

					}, function () {
						var $this = $(this);
						if ($minicart.data('timeout')) {
							clearTimeout($minicart.data('timeout'));
						}
						var timeout = setTimeout(function () {
							$('.mc-content', $this).stop(false, true).slideUp('slow');
							$this.removeClass('over');
						}, 300);
						$minicart.data('timeout', timeout);
					});
				}
				else {
					$('.mc-arrow').attr('data-value', 1);
					$('.mc-arrow').click(function () {
						var $this = $_mc_wrap;
						if ($('.mc-arrow').attr('data-value') == 1) {
							if ($this.hasClass('over')) {
								return;
							}
							if ($minicart.data('timeout')) {
								clearTimeout($minicart.data('timeout'));
							}
							var timeout = setTimeout(function () {
								$this.addClass('over');
								$('.mc-content', $this).stop(false, true).slideDown('slow');
								//jscrollDisplay();
							}, 300);
							$minicart.data('timeout', timeout);
							$('.mc-arrow').attr('data-value',0);
						} else {
							if ($minicart.data('timeout')) {
								clearTimeout($minicart.data('timeout'));
							}
							var timeout = setTimeout(function () {
								$('.mc-content', $this).stop(false, true).slideUp('slow');
								$this.removeClass('over');
							}, 300);
							$('.mc-arrow').attr('data-value',1);
							$minicart.data('timeout', timeout);
						}
					})
				}
				/*
				 * Event Addtocart Button - no load page
				 */

				$('.hikashop_cart_input_button').bind('click.mini', function () {
					if ($minicart.data('timeout')) {
						clearTimeout($minicart.data('timeout'));
					}
					var timeout = setTimeout(function () {
						productsRefresh();
					}, 200);
					$minicart.data('timeout', timeout);
				});

				var $_mark_process = $('.mc-process', $minicart);
				var _processGeneral = function () {
					var $_product = $('.mc-product', $minicart);
					$_product.each(function () {
						var $_prod = $(this);
						var $_pid = $_prod.attr('data-product-id');
						var $_quantity = $($_prod.find('.mc-quantity'));
						$_quantity.click(function () {
							return false;
						});
						/*-- process when click quantity control and change value input quantity --*/
						$('.quantity-control', $_prod).each(function () {
							$(this).children().click(function () {
								var Qtt = parseInt($_quantity.val());
								if ($(this).is('.quantity-plus')) {
									var max_quantity = $(this).parent().siblings().find('.mc-quantity').attr('data-max-quantity');
									if ((Qtt + 1) <= max_quantity || max_quantity < 0)
										$_quantity.val(Qtt + 1);
								} else {
									if (!isNaN(Qtt) && Qtt > 1) {
										$_quantity.val(Qtt - 1);
									} else {
										$_quantity.val(1);
									}
								}
								return false;
							});
						})
						var $timer = 0;
						$_quantity.on('keyup', function () {
							var $that = $(this);
							var max_quantity = $that.attr('data-max-quantity');
							var _Qtt = parseInt($that.val());
							if ($timer) {
								clearTimeout($timer);
								$timer = 0;
							}
							$timer = setTimeout(function () {
								if (!isNaN(_Qtt) && _Qtt >= 1) {
									if (_Qtt <= max_quantity || max_quantity < 0)
										$that.val(_Qtt);
									else
										$that.val(max_quantity);
								} else {
									$that.val(0);
									if (!$_prod.hasClass('mc-product-zero')) {
										$_prod.addClass('mc-product-zero');
									}
								}
							}, 500);
						});

						/*-- Process delete product --*/
						$('.mc-remove', $_prod).click(function () {
							$_mark_process.show();
							if (!$_prod.hasClass('mc-product-zero')) {
								$_prod.addClass('mc-product-zero');
							}
							$.ajax({
								type: 'POST',
								url: ajax_url,
								data: {
									minicart_ajax: 1,
									option: 'com_hikashop',
									view: 'cart',
									minicart_task: 'delete',
									cart_hikashop_product_id: $_pid // important
								},
								success: function ($json) {
									if ($json.status && $json.status == 1) {
										productsRefresh();
									}
								},
								dataType: 'json'
							});
						});
					});
				}

				_processGeneral();

				/*
				 * Update Products
				 */
				$('.mc-update-btn', $minicart).click(function () {
					var array_id = [], array_qty = [];
					var $_flag = false;
					$('.mc-product', $minicart).each(function () {
						var $this = $(this);
						var $_pid = $this.attr('data-product-id');
						var $_quantity = $($this.find('.mc-quantity'));
						var $_old_quantity = $this.attr('data-old-quantity');
						if ($_quantity.val() != $_old_quantity) {
							$_flag = true;
							array_id.push($_pid);
							array_qty.push($_quantity.val())
						}
					});
					if ($_flag) {
						$_mark_process.show();
						$.ajax({
							type: 'POST',
							url: ajax_url,
							data: {
								minicart_ajax: 1,
								option: 'com_hikashop',
								view: 'cart',
								minicart_task: 'update',
								cart_hikashop_product_id: array_id,
								quantity: array_qty
							},
							success: function ($json) {
								if ($json.status && $json.status == 1) {
									productsRefresh();
								}
							},
							dataType: 'json'
						});
					}
				});


				/*
				 *  Ajax url
				 */
				var ajax_url = '<?php echo $cart->ajaxurl; ?>';

				/*
				 * Refresh
				 */
				var productsRefresh = function (cart) {
					var $cart = cart ? $(cart) : $minicart;
					$.ajax({
						type: 'POST',
						url: ajax_url,
						data: {
							minicart_ajax: 1,
							option: 'com_hikashop',
							minicart_task: 'refresh',
							minicart_modid: '<?php echo $module->id; ?>',
							tmpl: 'default_list',
							view: 'cart'
						},
						success: function (list) {
							var $mpEmpty = $cart.find('.mc-product-zero');
							$('.mc-product-wrap', $cart).html($.trim(list.list_html));
							$('.mc-totalprice ,.mc-totalprice-footer', $cart).html(list.billTotal);
							$('.mc-totalproduct', $cart).html(list.length);
							_processGeneral();
							if (list.length > 0) {
								$mpEmpty.fadeOut('slow').remove();
								$cart.removeClass('mc-cart-empty');
							} else {
								$cart.addClass('mc-cart-empty');
							}
							if (list.length > 1) {
								$cart.find('.mc-status').html('<?php echo JText::_('ITEMS') ?>');
							} else {
								$cart.find('.mc-status').html('<?php echo JText::_('ITEM') ?>');
							}
							$_mark_process.hide();
							_posLR();
						},
						dataType: 'json'
					});
					return;
				}

			})('#<?php echo $tag_id;?>');

		});
	</script>
<?php
$script = ob_get_clean();
$script = JUtility::remove_string_javascript($script);
$doc->addScriptDeclaration($script);


?>
