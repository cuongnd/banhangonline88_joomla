<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;
require_once(JPATH_SITE . "/components/com_cmgroupbuying/helpers/common.php");

JFactory::getDocument()->addStyleSheet('components/com_cmgroupbuying/assets/css/style.css');

$jinput = JFactory::getApplication()->input;
$pageAddress = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=staffmanagement');
$navigation = $jinput->get('navigation', 'dashboard', 'word');

$permissions = array(
	'change_order_to_paid'		=> $this->settings['staff_change_order_paid'],
	'change_order_to_unpaid'	=> $this->settings['staff_change_order_unpaid'],
	'change_user_info'			=> $this->settings['staff_change_user_info'],
	'view_coupon'				=> $this->settings['staff_view_coupon'],
	'send_coupon'				=> $this->settings['staff_send_coupon']
);

// Default settings
$this->navigation	= $navigation;
$this->return		= base64_encode($pageAddress);
$this->pageAddress	= $pageAddress;
$this->permissions	= $permissions;
$this->menu			= CMGroupBuyingHelperStaffManagement::buildMenu($this->permissions, $this->navigation);
$this->welcome		= $this->settings['staff_welcome'];
$this->footer		= $this->settings['staff_footer'];
?>
<div class=cm-management>
<?php
if($this->user->guest):
	echo $this->loadTemplate('login');
elseif(!$this->user->guest):
	if(!$this->access):
		echo $this->loadTemplate('error');
	else:
		switch($this->navigation)
		{
			case 'coupon_view':
				if($this->permissions['view_coupon'] == false)
				{
					echo $this->loadTemplate('access_denied');
				}
				else
				{
					$couponCode = $jinput->get('id', '', 'string');
					$this->coupon = JModelLegacy::getInstance('Coupon', 'CMGroupBuyingModel')->getCouponByCouponCode($couponCode);
					echo $this->loadTemplate('coupon_view');
				}
			default:
				break;
		}
	endif;
endif;
?>
</div>