<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><span class="hikashop_checkout_coupon" id="hikashop_checkout_coupon">
<?php
if(!empty($this->coupon)) {
	echo JText::sprintf('HIKASHOP_COUPON_LABEL', @$this->coupon->discount_code);
?>
	<a href="<?php echo hikaserial::completeLink('shop.checkout&task=step&step='.($this->step+1).'&previous='.$this->step.'&removecoupon=1'.'&'.hikaserial::getFormToken().'=1'.$this->url_itemid); ?>"  title="<?php echo JText::_('REMOVE_COUPON'); ?>" ><img src="<?php echo HIKASHOP_IMAGES . 'delete2.png';?>" alt="<?php echo JText::_('REMOVE_COUPON'); ?>" /></a>
	<br/>
<?php
}
?>
	<?php echo JText::_('HIKASHOP_ENTER_COUPON'); ?>
	<input id="hikashop_checkout_serial_coupon_input" type="text" name="hikaserial_coupon" value="" />
<?php
	$params = null;
	echo $this->cartHelper->displayButton(JText::_('ADD'), 'refresh', $params, hikaserial::completeLink('shop.checkout'),'',' onclick="return hikashopCheckCoupon(\'hikashop_checkout_serial_coupon_input\');"');
?>
</span>
<script type="text/javascript">
function hikashopCheckCoupon(id) {
	var el = document.getElementById(id);
	if(el) {
		if(el.value == '') {
			el.className = 'hikashop_red_border';
			return false;
		}
		el.form.submit();
	}
	return false;
}
</script>
