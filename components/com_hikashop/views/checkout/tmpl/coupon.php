<?php
/**
 * @package    HikaShop for Joomla!
 * @version    2.6.3
 * @author    hikashop.com
 * @copyright    (C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');

?>
<div class="row">
    <div class="col-lg-12">
<div class="hikashop_checkout_coupon" id="hikashop_checkout_coupon">
	<?php
    if (empty($this->coupon)) {
        ?>
        <h4><?php echo JText::_('HIKASHOP_ENTER_COUPON'); ?></h4>
        <div class="row">
            <div class="col-lg-12">
                <input id="hikashop_checkout_coupon_input" class="pull-left" type="text" name="coupon" value=""/>
                <?php
                echo $this->cart->displayButton(JText::_('ADD'), 'refresh', $this->params, hikashop_completeLink('checkout'), '', ' onclick="return hikashopCheckCoupon(\'hikashop_checkout_coupon_input\');"');
                ?>
            </div>
        </div>
                <?php
    } else {
        echo JText::sprintf('HIKASHOP_COUPON_LABEL', @$this->coupon->discount_code);
        global $Itemid;
        $url_itemid = '';
        if (!empty($Itemid)) {
            $url_itemid = '&Itemid=' . $Itemid;
        }
        ?>
        <a class="pull-left" href="<?php echo hikashop_completeLink('checkout&task=step&step=' . ($this->step + 1) . '&previous=' . $this->step . '&removecoupon=1' . '&' . hikashop_getFormToken() . '=1' . $url_itemid); ?>"
           title="<?php echo JText::_('REMOVE_COUPON'); ?>">
            <img src="<?php echo HIKASHOP_IMAGES . 'delete2.png'; ?>" alt="<?php echo JText::_('REMOVE_COUPON'); ?>"/>
        </a>
    <?php } ?>
</div>
    </div>
</div>
