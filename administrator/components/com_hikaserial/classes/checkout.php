<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class hikaserialCheckoutClass extends hikaserialClass {

	public function afterCheckoutStep($controllerName, &$go_back, $original_go_back, &$controller) {
		if($controllerName == 'plg.serial.coupon')
			return $this->afterCouponCheckoutStep($go_back, $original_go_back, $controller);
		return true;
	}

	private function afterCouponCheckoutStep(&$go_back, $original_go_back, &$controller) {
		$app = JFactory::getApplication();

		$coupon = JRequest::getString('hikaserial_coupon', '');
		$qty = 1;

		if(empty($coupon)) {
			$coupon = JRequest::getInt('removecoupon', 0);
			$qty = 0;
		}

		if(!empty($coupon)) {
			$go_back = true;

			$config = hikaserial::config();
			$user_id = hikaserial::loadUser();
			$serialClass = hikaserial::get('class.serial');
			$serial = null;
			$serials = null;
			if(is_string($coupon))
				$serials = $serialClass->find($coupon, null, array('serial_user_id DESC', 'serial_id ASC'));

			if(count($serials) == 1) {
				$serial = reset($serials);
			} else if(!empty($serials)) {
				$assigned_status = $config->get('assigned_serial_status', 'assigned');
				foreach($serials as $s) {
					if(($s->serial_user_id == $user_id || $s->serial_user_id ==  0 || $user_id == 0) && ($s->serial_status == $assigned_status)) {
						$serial = $s;
					}
					if($serial != null)
						break;
				}
			}

			$consume_ret = false;
			if(!empty($serial) && !empty($serial->serial_id)) {
				if(empty($user_id) && $config->get('forbidden_consume_guest', 1)) {
					$app->enqueueMessage(JText::_('CONSUME_NOT_LOGGED'), 'error');
				} else {
					$consume_ret = $serialClass->consume($serial->serial_id, null, false);
					if($consume_ret) {
						$full_serial = $serialClass->get($serial->serial_id);
						$msg = JText::sprintf('SERIAL_CHECKOUT_X_CONSUMED', $coupon);
						if(!empty($msg))
							$app->enqueueMessage($msg, 'success');
					}
				}
			}

			$cart = $controller->initCart();
			$coupon_ret = false;
			if(empty($cart->coupon) || ($coupon === 1 && $qty == 0)) {
				$class = hikaserial::get('shop.class.cart');
				if($class->update($coupon, $qty, 0, 'coupon')) {
					if(strpos($controller->checkout_workflow, 'shipping') !==false) {
						$controller->before_shipping(true);
					}
					if(strpos($controller->checkout_workflow, 'payment') !== false) {
						$controller->before_payment(true);
					}
					$controller->initCart(true);
					$controller->cart_update = true;
					$coupon_ret = true;
				}
			}

			if(!$coupon_ret && !$consume_ret) {
				$msg = JText::sprintf('SERIAL_CHECKOUT_UNKNOWN_CODE', $coupon);
				if(!empty($msg))
					$app->enqueueMessage($msg, 'error');
			}

			return false;
		}
		return true;
	}
}
