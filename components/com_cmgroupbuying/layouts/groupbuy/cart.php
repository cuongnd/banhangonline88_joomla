<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

JHtml::_('behavior.keepalive');
$cart = $this->cart;
$formCache = $this->formCache;
$configuration = $this->configuration;
$profileSetting = $this->profileSetting;
$maxDisplayedQuantity = $configuration['max_displayed_quantity'];

if($configuration['jquery_loading'] != "" && version_compare(JVERSION, '3.0.0', 'lt'))
{
	JFactory::getDocument()->addScript($configuration['jquery_loading']);
}

if(!is_numeric($maxDisplayedQuantity) || $maxDisplayedQuantity <= 0)
{
	$maxDisplayedQuantity = 10;
}

$tosLink = JRoute::_('index.php?Itemid=' . $configuration['tos']);
$tos = '<a href="' . $tosLink . '" target="_blank">' . JText::_('COM_CMGROUPBUYING_TERMS_OF_SERVICE') . '</a>';
$tos = JText::sprintf('COM_CMGROUPBUYING_AGREE_TO_TERMS_OF_SERVICE', $tos);

if(empty($formCache))
{
	if(JFactory::getUser()->id > 0)
	{
		$profile = CMGroupBuyingHelperUser::getUserProfile(JFactory::getUser()->id, $profileSetting);
	}
	else
	{
		$profile["phone"] = $profile["zip"] = $profile["state"] = $profile["city"] = $profile["address"] = $profile["lastname"] = $profile["firstname"]= $profile["name"] = $profile["email"] = '';
	}

	$friend = array('full_name' => '', 'email' => '');
}
else
{
	$profile["name"] = $formCache['buyer']['name'];
	$profile["firstname"] = $formCache['buyer']['first_name'];
	$profile["lastname"] = $formCache['buyer']['last_name'];
	$profile["address"] = $formCache['buyer']['address'];
	$profile["city"] = $formCache['buyer']['city'];
	$profile["state"] = $formCache['buyer']['state'];
	$profile["zip"] = $formCache['buyer']['zip_code'];
	$profile["phone"] = $formCache['buyer']['phone'];
	$profile["email"] = $formCache['buyer']['email'];
	$friend = array(
		'full_name' => $formCache['friend']['full_name'],
		'email' => $formCache['friend']['email']
	);
}

$fields = array();

foreach($profile as $key=>$value)
{
	if($key != 'email')
		$fields[] = array(str_replace('_', '', $key), strtoupper($key));
}
?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style.css" type="text/css" />
<?php if(JFactory::getLanguage()->isRTL()): ?>
<link rel="stylesheet" href="components/com_cmgroupbuying/layouts/<?php echo CMGroupBuyingHelperCommon::getLayout(); ?>/css/style-rtl.css" type="text/css" />
<?php endif; ?>
<div class="clearfix">
	<div class="page_title">
		<p><?php echo $this->pageTitle; ?></p>
	</div>
<?php
if(empty($cart) || empty($cart['items']))
{
	echo '<p class="cmgroupbuying_error">' . JText::_('COM_CMGROUPBUYING_CART_IS_EMPTY') . '</p>';
}
else
{
?>
	<?php
	$orderTotal = 0;
	$orderDiscount = $cart['points'] * $configuration['exchange_rate'];;
	$userPoints = $this->userPoints;
	?>
	<?php if(($configuration['point_system'] == "aup" || $configuration['point_system'] == "jomsocial")
				&& $configuration['pay_with_point'] == 1
				&& $userPoints > 0): ?>
	<script type="text/javascript">
			jQuery(document).ready(function() {
				jQuery("#points").keydown(function(event)
				{
					// Allow only backspace, delete
					if(event.keyCode == 46 || event.keyCode == 8)
					{
						// let it happen, don't do anything
					}
					else
					{
						// Ensure that it is a number and stop the keypress
						if((event.keyCode < 48 || event.keyCode > 57)
							&& (event.keyCode < 96 || event.keyCode > 105))
						{
							event.preventDefault();
						}
					}
				});

				jQuery("#points").keyup(function(event) {
					points = jQuery(this).val();
					rate = <?php echo $configuration['exchange_rate']; ?>;

					if(isNaN(points))
					{
						points = 0;
					}

					pointToCurrency = points * rate;

					order_total = calculate(false);

					if(pointToCurrency > order_total)
					{
						points = order_total / rate;
						pointToCurrency = points * rate;
						jQuery(this).attr('value', points);
					}

					order_discount = formatNumber(pointToCurrency,
						'<?php echo $configuration['currency_decimals']; ?>',
						'<?php echo $configuration['currency_thousands_sep']; ?>',
						'<?php echo $configuration['currency_dec_point']; ?>',
						'<?php echo $configuration['currency_prefix']; ?>',
						'<?php echo $configuration['currency_postfix']; ?>',
						'',
						''
					);

					jQuery('#html_order_discount').html('-' + order_discount);
					calculate(true);
				});
			});
		</script>
	<?php endif; ?>
	<script type="text/javascript">
		function submit_cart(controller, task)
		{
			jQuery('#cart_form_controller').attr('value', controller);
			jQuery('#cart_form_task').attr('value', task);
			document.cart_form.submit();
		}

		function formatNumber(num, dec, thou, pnt, curr1, curr2, n1, n2)
		{
			var x = Math.round(num * Math.pow(10,dec));

			if(x >= 0)
			{
				n1=n2='';
			}

			var y = (''+Math.abs(x)).split('');
			var z = y.length - dec;

			if(z<0)
			{
				z--;
			}

			for(var i = z; i < 0; i++)
			{
				y.unshift('0');
			}

			if(z<0)
			{
				z = 1;
			}

			if(dec > 0)
			{
				y.splice(z, 0, pnt);
			}

			if(y[0] == pnt)
			{
				y.unshift('0');
			}

			while(z > 3)
			{
				z-=3;
				y.splice(z,0,thou);
			}

			var r = curr1+n1+y.join('')+n2+curr2;
			return r;
		}

		jQuery(document).ready(function() {
			jQuery('[id^="quantity_"]').change(function(){
				element_id = jQuery(this).attr('id');
				temp = element_id.split("_");
				deal_id = temp[1];
				option_id = temp[2];
				item_single_price = jQuery('#item_single_' + deal_id + '_' + option_id).val();
				quantity = jQuery(this).val();
				new_item_total_price = item_single_price * quantity;
				jQuery('#text').attr('value', new_item_total_price);

				if(jQuery('#shipping_single_' + deal_id + '_' + option_id).length)
				{
					shipping_single_price = jQuery('#shipping_single_' + deal_id + '_' + option_id).val();
					new_shipping_total_price = shipping_single_price * quantity;
					new_item_total_price = new_item_total_price + new_shipping_total_price
				}

				jQuery('#item_total_' + deal_id + '_' + option_id).attr('value', new_item_total_price);

				new_item_total_price = formatNumber(new_item_total_price,
					'<?php echo $configuration['currency_decimals']; ?>',
					'<?php echo $configuration['currency_thousands_sep']; ?>',
					'<?php echo $configuration['currency_dec_point']; ?>',
					'<?php echo $configuration['currency_prefix']; ?>',
					'<?php echo $configuration['currency_postfix']; ?>',
					'',
					''
				);

				if(jQuery('#advance_price_' + deal_id + '_' + option_id).length)
				{
					item_regular_price = jQuery('#item_regular_' + deal_id + '_' + option_id).val();
					new_remain_price = (item_regular_price * quantity) - (item_single_price * quantity);

					new_remain_price = formatNumber(new_remain_price,
						'<?php echo $configuration['currency_decimals']; ?>',
						'<?php echo $configuration['currency_thousands_sep']; ?>',
						'<?php echo $configuration['currency_dec_point']; ?>',
						'<?php echo $configuration['currency_prefix']; ?>',
						'<?php echo $configuration['currency_postfix']; ?>',
						'',
						''
					);

					jQuery('#advance_price_' + deal_id + '_' + option_id).html(new_item_total_price);
					jQuery('#remain_price_' + deal_id + '_' + option_id).html(new_remain_price);
				}

				jQuery('#html_item_total_' + deal_id + '_' + option_id).html(new_item_total_price);
				calculate(true);
			});
		});

		function calculate(calculate_point)
		{
			order_total_price = 0;

			jQuery('[id^="quantity_"]').each(function() {
				element_id = jQuery(this).attr('id');
				temp = element_id.split("_");
				deal_id = temp[1];
				option_id = temp[2];
				item_single_price = jQuery('#item_single_' + deal_id + '_' + option_id).val();
				quantity = jQuery(this).val();
				new_item_total_price = item_single_price * quantity;
				order_total_price = order_total_price + new_item_total_price;
				jQuery('#item_total_' + deal_id + '_' + option_id).attr('value', new_item_total_price);

				if(jQuery('#shipping_single_' + deal_id + '_' + option_id).length)
				{
					shipping_single_price = jQuery('#shipping_single_' + deal_id + '_' + option_id).val();
					new_shipping_total_price = shipping_single_price * quantity;
					order_total_price = order_total_price + new_shipping_total_price;
				}
			});

			if(jQuery('#points').length && calculate_point == true)
			{
				points = jQuery("#points").val();
				rate = <?php echo $configuration['exchange_rate']; ?>;
				pointToCurrency = points * rate;

				if(pointToCurrency > parseInt(order_total_price))
				{
					points = parseInt(order_total_price) / rate;
					pointToCurrency = points * rate;
					jQuery(this).attr('value', points);

					order_discount = formatNumber(pointToCurrency,
						'<?php echo $configuration['currency_decimals']; ?>',
						'<?php echo $configuration['currency_thousands_sep']; ?>',
						'<?php echo $configuration['currency_dec_point']; ?>',
						'<?php echo $configuration['currency_prefix']; ?>',
						'<?php echo $configuration['currency_postfix']; ?>',
						'',
						''
					);

					jQuery("#points").attr('value', points);
					jQuery('#html_order_discount').html('-' + order_discount);
				}

				order_total_price = order_total_price - pointToCurrency;
			}

			jQuery('#order_total').attr('value', order_total_price);
			result = order_total_price;

			order_total_price = formatNumber(order_total_price,
				'<?php echo $configuration['currency_decimals']; ?>',
				'<?php echo $configuration['currency_thousands_sep']; ?>',
				'<?php echo $configuration['currency_dec_point']; ?>',
				'<?php echo $configuration['currency_prefix']; ?>',
				'<?php echo $configuration['currency_postfix']; ?>',
				'',
				''
			);

			jQuery('#html_order_total').html(order_total_price);

			if(result == 0)
			{
				jQuery('#payments').hide();
			}
			else if(result > 0)
			{
				jQuery('#payments').show();
			}

			return result;
		}
	</script>
	<script>
		function validateEmail(email)
		{
			var re = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
			return re.test(email);
		}

		function check_out()
		{
			var valid = true;

			<?php foreach($fields as $field): ?>
			<?php if($profileSetting['profile_' . $field[0] . '_attribute'] == 'required'): ?>
			if(jQuery("#<?php echo $field[0]; ?>").val() == "")
			{
				jQuery("#<?php echo $field[0]; ?>_error").html("<?php echo JText::_('COM_CMGROUPBUYING_INVALID_' . $field[1]); ?>");
				valid = false;
			}
			else
			{
				jQuery("#<?php echo $field[0]; ?>_error").html("");
			}
			<?php endif; ?>
			<?php endforeach; ?>

			if(validateEmail(jQuery("#email").val()) == false)
			{
				jQuery("#email_error").html("<?php echo JText::_('COM_CMGROUPBUYING_INVALID_EMAIL'); ?>");
				valid = false;
			}
			else
			{
				jQuery("#email_error").html("");
			}

			if(jQuery("#friend_full_name").val() == "" && jQuery("#friend_email").val() != "")
			{
				jQuery("#friend_full_name_error").html("<?php echo JText::_('COM_CMGROUPBUYING_INVALID_FRIEND_FULL_NAME'); ?>");
				valid = false;
			}
			else
			{
				jQuery("#friend_full_name_error").html("");
			}

			if(jQuery("#friend_full_name").val() != '' && validateEmail(jQuery("#friend_email").val()) == false)
			{
				jQuery("#friend_email_error").html("<?php echo JText::_('COM_CMGROUPBUYING_INVALID_FRIEND_EMAIL'); ?>");
				valid = false;
			}
			else
			{
				jQuery("#friend_email_error").html("");
			}

			if(valid)
			{
				jQuery('#cart_form_controller').attr('value', 'checkout');
				jQuery('#cart_form_task').attr('value', 'checkout');
				document.cart_form.submit();
			}
		}
	</script>
	<form class="form-horizontal"name="cart_form" id="cart_form" method="post" action="<?php echo JRoute::_('index.php', false); ?>">
		<input type="hidden" name="option" value="com_cmgroupbuying" />
		<input type="hidden" id="cart_form_controller" name="controller" value="cart" />
		<input type="hidden" id="cart_form_task" name="task" value="" />
		<div class="row-fluid">
			<div class="span12">
				<table id="cart_table" class="table">
					<tr>
						<th class="center name"><?php echo JText::_('COM_CMGROUPBUYING_DEAL'); ?></th>
						<th class="center quantity"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_QUANTITY'); ?></th>
						<th class="center price"><?php echo JText::_('COM_CMGROUPBUYING_DEAL_UNIT_PRICE'); ?></th>
						<?php if($cart['total_shipping_cost'] > 0): ?>
						<th class="center price"><?php echo JText::_('COM_CMGROUPBUYING_SHIPPING_COST'); ?></th>
						<?php endif; ?>
						<th class="center price"><?php echo JText::_('COM_CMGROUPBUYING_ORDER_PRICE'); ?></th>
						<th class="center remove"><?php echo JText::_('COM_CMGROUPBUYING_CART_REMOVE'); ?></th>
					</tr>
					<?php $i = 1; ?>
					<?php foreach($cart['items'] as $item): ?>
						<?php
						$itemPrice  = $item['quantity'] * $item['unit_price'] + $item['quantity'] * $item['shipping_cost'];
						$orderTotal = $orderTotal + $itemPrice;
						?>
						<tr>
							<td>
								<?php echo $item['option_name']; ?>
								<?php if($item['remain_price'] > 0): ?>
								<?php $tooltip = JText::sprintf('COM_CMGROUPBUYING_DEAL_DETAIL_ADVANCE_PAYMENT_INFO',
										CMGroupBuyingHelperDeal::displayDealPrice($item['unit_price'], true, $configuration),
										CMGroupBuyingHelperDeal::displayDealPrice($item['remain_price'], true, $configuration)); ?>
								<span class="hasTooltip"><span class="cmquestion" data-toggle="tooltip" data-placement="right" data-original-title="<?php echo $tooltip; ?>">?</span></span>
								<script>
								jQuery('.hasTooltip').tooltip({
								  selector: "span[data-toggle=tooltip]"
								})
								</script>
								<?php endif; ?>
							</td>
							<td class="center">
								<select id="<?php echo 'quantity_' . $item['deal_id'] . '_' . $item['option_id']; ?>" name="<?php echo 'quantity_' . $item['deal_id'] . '_' . $item['option_id']; ?>">
								<?php for($i = 1; $i <= $maxDisplayedQuantity; $i++): ?>
									<?php $selected = ($i == $item['quantity']) ? ' selected="selected"' : ''; ?>
									<option value="<?php echo $i; ?>"<?php echo $selected; ?>><?php echo $i; ?></option>
								<?php endfor; ?>
								</select>
							</td>
							<td class="center">
								<?php echo CMGroupBuyingHelperDeal::displayDealPrice($item['unit_price'], true, $configuration); ?>
							</td>
							<?php if($cart['total_shipping_cost'] > 0): ?>
							<td class="center">
								<?php echo CMGroupBuyingHelperDeal::displayDealPrice($item['shipping_cost'], true, $configuration); ?>
							</td>
							<?php endif; ?>
							<td class="center" id="<?php echo 'html_item_total_' . $item['deal_id'] . '_' . $item['option_id']; ?>">
								<?php echo CMGroupBuyingHelperDeal::displayDealPrice($itemPrice); ?>
							</td>
							<td class="center"><input type="checkbox" name="<?php echo 'remove_' . $item['deal_id'] . '_' . $item['option_id']; ?>" id="<?php echo 'remove_' . $item['deal_id'] . '_' . $item['option_id']; ?>" /></td>
						</tr>
						<?php if($item['remain_price'] > 0): ?>
						<input type="hidden" id="<?php echo 'item_regular_' . $item['deal_id'] . '_' . $item['option_id']; ?>" value="<?php echo $item['unit_price']; ?>" />
						<?php endif; ?>
						<input type="hidden" id="<?php echo 'item_single_' . $item['deal_id'] . '_' . $item['option_id']; ?>" value="<?php echo $item['unit_price']; ?>" />
						<input type="hidden" id="<?php echo 'item_total_' . $item['deal_id'] . '_' . $item['option_id']; ?>" value="<?php echo $item['unit_price']; ?>" />
						<input type="hidden" id="<?php echo 'shipping_single_' . $item['deal_id'] . '_' . $item['option_id']; ?>" value="<?php echo $item['shipping_cost']; ?>" />
					<?php $i++; endforeach; ?>
					<?php if(($configuration['point_system'] == "aup" || $configuration['point_system'] == "jomsocial")
								&& $configuration['pay_with_point'] == 1
								&& $userPoints > 0): ?>
						<?php $orderTotal = $orderTotal - $orderDiscount; ?>
						<tr class='point'>
							<td colspan="<?php if($cart['total_shipping_cost'] > 0) echo "6"; else echo "5"; ?>" class="discount deal_name">
								<h4><?php echo JText::_('COM_CMGROUPBUYING_CHECK_OUT_PAY_WITH_POINT'); ?></h4>
							</td>
						</tr>
						<tr class='point'>
							<td colspan="<?php if($cart['total_shipping_cost'] > 0) echo "6"; else echo "5"; ?>" class="point_instruction discount">
								<p><?php echo JText::sprintf('COM_CMGROUPBUYING_POINT_PAYMENT_INSTRUCTION', $userPoints); ?></p>
								<p><?php echo JText::sprintf('COM_CMGROUPBUYING_POINT_EXCHANGE_RATE_INFO', CMGroupBuyingHelperDeal::displayDealPrice($configuration['exchange_rate'], true, $configuration)); ?></p>
							</td>
						</tr>
						<tr class='point'>
							<td class="discount">
								<?php echo JText::_('COM_CMGROUPBUYING_CHECK_OUT_POINT_INPUT'); ?>
							</td>
							<td class="discount"><input id="points" name="points" value="<?php echo $orderDiscount; ?>" size="3" /></td>
							<td class="discount"></td>
							<td class="center discount" id="html_order_discount">-<?php echo CMGroupBuyingHelperDeal::displayDealPrice($orderDiscount, true, $configuration); ?></td>
							<td class="discount"></td>
						</tr>
					<?php endif; ?>
						<tr>
							<td colspan="<?php if($cart['total_shipping_cost'] > 0) echo "4"; else echo "3"; ?>" class="total"></td>
							<td class="center total" id="html_order_total"><?php echo CMGroupBuyingHelperDeal::displayDealPrice($orderTotal, true, $configuration); ?></td>
							<td class="total"></td>
						</tr>
						<input type="hidden" id="order_total" value="<?php echo $orderTotal; ?>" />
				</table>
				<div class="pull-right">
					<button class="btn btn-primary" onClick="submit_cart('cart', 'update_cart')">
						<?php echo JText::_('COM_CMGROUPBUYING_UPDATE_CART_BUTTON'); ?>
					</button>
					<button class="btn btn-primary" onClick="submit_cart('cart', 'empty_cart')">
						<?php echo JText::_('COM_CMGROUPBUYING_EMPTY_CART_BUTTON'); ?>
					</button>
				</div>
			</div>
		</div>
		<?php if(JFactory::getUser()->id != 0 || ($configuration['buy_as_guest'] == 1 && JFactory::getUser()->id == 0)): ?>
		<div class="row-fluid">
			<div class="span12">
				<div class="row-fluid">
					<div class="span12">
						<fieldset>
							<legend>
								<?php echo JText::_('COM_CMGROUPBUYING_CHECK_OUT_PROVIDE_INFORMATION'); ?>
							</legend>
							<?php
							// All fields except email field
							foreach($fields as $field)
							{
								if($profileSetting['profile_' . $field[0] . '_attribute'] != 'hidden'):
									echo '<div class="control-group">';
									echo '<label class="control-label" for="' . $field[0] . '">';
									echo JText::_('COM_CMGROUPBUYING_USER_' . $field[1]);
									if($profileSetting['profile_' . $field[0] . '_attribute'] == 'optional')
										echo $profileSetting['optional_text'];
									if($profileSetting['profile_' . $field[0] . '_attribute'] == 'required')
										echo $profileSetting['required_text'];
									echo '</label>';
									echo '<div class="controls">';
									echo '<input type="text" name="' . $field[0] . '" id="' . $field[0] . '" value="' . htmlspecialchars($profile[$field[0]]) . '" />';
									if($profileSetting['profile_' . $field[0] . '_attribute'] == 'required')
										echo '<div class="text-error" id="' . $field[0] . '_error"></div>';
									echo '</div>';
									echo '</div>';
								endif;
							}

							// Email field
							echo '<div class="control-group">';
							echo '<label class="control-label" for="email">';
							echo JText::_('COM_CMGROUPBUYING_USER_EMAIL');
							echo $profileSetting['required_text'];
							echo '</label>';
							echo '<div class="controls">';
							echo '<input type="text" name="email" id="email" value="' . htmlspecialchars($profile['email']) . '" />';
							echo '<div class="text-error" id="email_error"></div>';
							echo '</div>';
							echo '</div>';
						?>
						</fieldset>
						<fieldset class="fluid">
							<legend>
								<?php echo JText::_('COM_CMGROUPBUYING_BUY_AS_GIFT_LEGEND'); ?>
							</legend>
							<div id="buy_for_friend_message">
								<?php echo JText::_('COM_CMGROUPBUYING_CHECK_OUT_BUY_FOR_FRIEND_MESSAGE'); ?>
							</div>
							<div class="control-group">
								<label class="control-label" for="friend_full_name"><?php echo JText::_('COM_CMGROUPBUYING_FRIEND_FULL_NAME'); ?></label>
								<div class="controls">
									<input type="text" id="friend_full_name" name="friend_full_name" value="<?php echo $friend['full_name']; ?>" />
									<div class="text-error" id="friend_full_name_error"></div>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="friend_email"><?php echo JText::_('COM_CMGROUPBUYING_FRIEND_EMAIL'); ?></label>
								<div class="controls">
									<input type="text" id="friend_email" name="friend_email" value="<?php echo $friend['email']; ?>">
									<div class="text-error" id="friend_email_error"></div>
								</div>
							</div>
						</fieldset>
						<fieldset class="fluid">
							<?php
							if($configuration['payment_method_type'] == 'hosted')
								$legend = JText::_('COM_CMGROUPBUYING_CHECK_OUT_SELECT_PAYMENT');
							elseif($configuration['payment_method_type'] == 'direct')
								$legend = JText::_('COM_CMGROUPBUYING_CHECK_OUT_CREDIT_CARD_DETAILS');
							?>
							<legend>
								<?php echo $legend; ?>
							</legend>
						<?php
						if($orderTotal > 0)
						{
							echo $configuration['payment_method_pretext'];

							if($configuration['payment_method_type'] == 'hosted')
							{
								$count = 1;

								foreach($this->payments as $payment)
								{
									if($count == 1)
									{
										$checked = ' CHECKED';
									}
									else
									{
										$checked = '';
									}

									$count++;
									echo '<label class="radio">';
									echo '<input type="radio" name="payment_id" value="' . $payment['id'] . '" ' . $checked . '>';
									echo $payment['name'];
									echo '</label>';
								}
							}
							elseif($configuration['payment_method_type'] == 'direct')
							{
							?>
							<div class="control-group">
								<label class="control-label" for="account_number">
									<?php echo JText::_('COM_CMGROUPBUYING_CHECK_OUT_CARD_NUMBER'); ?>
								</label>
								<div class="controls">
									<input type="text" name="card_number" value="">
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="card_number">
									<?php echo JText::_('COM_CMGROUPBUYING_CHECK_OUT_EXPIRATION_DATE'); ?>
								</label>
								<div class="controls">
									<select name="expiration_month" class="input-small">
										<?php for($i = 1; $i <= 12; $i++): ?>
										<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
										<?php endfor; ?>
									</select>
									/
									<?php $year = gmdate('Y'); ?>
									<select name="expiration_year" class="input-small">
										<?php for($i = $year; $i <= $year+10; $i++): ?>
										<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
										<?php endfor; ?>
									</select>
								</div>
							</div>
							<div class="control-group">
								<label class="control-label" for="csc">
									<?php echo JText::_('COM_CMGROUPBUYING_CHECK_OUT_CARD_SECURITY_CODE'); ?>
								</label>
								<div class="controls">
									<input type="text" name="csc" value="" class="input-mini">
								</div>
							</div>
							<?php
							}

							echo $configuration['payment_method_posttext'];
						}
						else
						{
							echo JText::_('COM_CMGROUPBUYING_CHECK_OUT_FREE_DEAL_MESSAGE');
						}
						?>
						</fieldset>
						<fieldset class="fluid">
							<legend>
								<?php echo JText::_('COM_CMGROUPBUYING_TERMS_OF_SERVICE_AGREEMENT'); ?>
							</legend>
							<input type="checkbox" name="tos" value="1" class="tos_checkbox"><?php echo $tos; ?>
						</fieldset>
					</div>
					<div class="fluid">
						<div class="span12">
							<div class="pull-right"><button type="button" class="btn btn-primary" onClick="check_out()"><?php echo JText::_('COM_CMGROUPBUYING_CHECK_OUT_BUTTON'); ?></button></div>
						</div>
					</div>
				</div>
			</div>
		</div>
		<?php
		else:
			$redirectUrl = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=cart');
			$redirectUrl = base64_encode($redirectUrl);
			$loginUrl = JRoute::_("index.php?option=com_easysocial&view=login&return=".$redirectUrl, false);
			$loginTag = '<a href="' . $loginUrl . '">' . JText::_('COM_CMGROUPBUYING_LOGIN') . '</a>';
			$registerTag = '<a href="' . JRoute::_('index.php?com_easysocial&view=registration', false) . '">' . JText::_('COM_CMGROUPBUYING_REGISTER') . '</a>';
			echo '<div class="guest_check_out_message">'
				. JText::sprintf('COM_CMGROUPBUYING_GUEST_CHECK_OUT_MESSAGE', $loginTag, $registerTag)
				. '</div>';
		endif;
		?>
	</form>
<?php
}
?>
</div>
