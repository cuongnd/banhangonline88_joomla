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
$navigation  = $jinput->get('navigation', 'dashboard', 'word');

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
?>
	<div class="cm-management-management">
		<div class="navbar">
			<div class="navbar-inner">
				<div class="container-fluid">
					<a href="<?php echo $this->pageAddress; ?>" class="brand"><?php echo JText::_('COM_CMGROUPBUYING_MANAGEMENT_STAFF_PAGE_HEADER'); ?></a>
					<form class="form-inline" id="login-form" method="post" action="<?php echo JRoute::_('index.php', true); ?>">
						<p class="logout-button pull-right"><button class="btn btn-danger btn-small"><?php echo JText::_('JLOGOUT'); ?></button></p>
						<input type="hidden" name="option" value="com_users" />
						<input type="hidden" name="task" value="user.logout" />
						<input type="hidden" name="return" value="<?php echo $this->return; ?>" />
						<?php echo JHtml::_('form.token'); ?>
					</form>
					<p class="navbar-text pull-right"><?php echo JText::sprintf('COM_CMGROUPBUYING_MANAGEMENT_LOGGED_IN_MESSAGE', '<span class="user_name">' . $this->user->username . '</span'); ?></p>
				</div>
			</div>
		</div>
		<div class="container-fluid">
			<div class="row-fluid">
				<div class="span3">
					<div class="well sidebar-nav">
						<?php echo $this->menu; ?>
					</div>
				</div>
				<div class="span9">
					<?php if(JFactory::getDocument()->getBuffer('message')): ?>
					<?php echo JFactory::getDocument()->getBuffer('message'); ?>
					<?php else: ?>
					<div id="system-message-container">
						<div id="system-message"></div>
					</div>
					<?php endif; ?>
<?php
		switch($this->navigation)
		{
			case 'order_list':
				$this->configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();
				$orderId = $jinput->get('id', 0, 'int');

				if($orderId <= 0)
				{
					$buyerFilter = $jinput->post->get('filter_buyer', '');
					$statusFilter = $jinput->post->get('filter_status', '-1');
					$gatewayFilter = $jinput->post->get('filter_gateway', '');
					$dateFilter = $jinput->post->get('filter_date', '');
					$fromFilter = $jinput->post->get('filter_from', '');
					$toFilter = $jinput->post->get('filter_to', '');

					JFactory::getApplication()->setUserState("cmgroupbuying.staff_order_buyer_filter", $buyerFilter);
					JFactory::getApplication()->setUserState("cmgroupbuying.staff_order_status_filter", $statusFilter);
					JFactory::getApplication()->setUserState("cmgroupbuying.staff_order_gateway_filter", $gatewayFilter);
					JFactory::getApplication()->setUserState("cmgroupbuying.staff_order_date_filter", $dateFilter);
					JFactory::getApplication()->setUserState("cmgroupbuying.staff_order_from_filter", $fromFilter);
					JFactory::getApplication()->setUserState("cmgroupbuying.staff_order_to_filter", $toFilter);

					$this->orders = JModelLegacy::getInstance('Orders', 'CMGroupBuyingModel')->getLimit();
					$this->pageNav = JModelLegacy::getInstance('Orders', 'CMGroupBuyingModel')->getPagination();
					echo $this->loadTemplate('order_list');
				}
				else
				{
					$this->order = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($orderId);
					echo $this->loadTemplate('order_detail');
				}
				break;

			case 'user_info':
				if($this->permissions['change_user_info'] == false)
				{
					echo $this->loadTemplate('access_denied');
				}
				else
				{
					$orderId = $jinput->get('id', 0, 'int');
					$this->order = JModelLegacy::getInstance('Order', 'CMGroupBuyingModel')->getOrderById($orderId);
					echo $this->loadTemplate('user_info');
				}
				break;

			case 'coupon_list':
				$this->configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();
				$couponCode = $jinput->get('id', '', 'cmd');

				if($couponCode == '')
				{
					$codeFilter = $jinput->post->get('filter_code', '');
					$buyerFilter = $jinput->post->get('filter_buyer', '');
					$dealFilter = $jinput->post->get('filter_deal', '');
					$statusFilter = $jinput->post->get('filter_status', '-1');

					JFactory::getApplication()->setUserState("cmgroupbuying.staff_coupon_code_filter", $codeFilter);
					JFactory::getApplication()->setUserState("cmgroupbuying.staff_coupon_buyer_filter", $buyerFilter);
					JFactory::getApplication()->setUserState("cmgroupbuying.staff_coupon_deal_filter", $dealFilter);
					JFactory::getApplication()->setUserState("cmgroupbuying.staff_coupon_status_filter", $statusFilter);

					$this->coupons = JModelLegacy::getInstance('Coupon', 'CMGroupBuyingModel')->getLimit();
					$this->pageNav = JModelLegacy::getInstance('Coupon', 'CMGroupBuyingModel')->getPagination();
					echo $this->loadTemplate('coupon_list');
				}
				else
				{
					$this->coupon = JModelLegacy::getInstance('Coupon', 'CMGroupBuyingModel')->getCouponByCouponCode($couponCode);
					echo $this->loadTemplate('coupon_detail');
				}
				break;

			default:
				echo $this->loadTemplate('dashboard');
				break;
		}
?>
				</div>
			</div>
		</div>
		<hr>
		<footer>
			<div class="container-fluid">
				<div class="span12"><?php echo $this->footer; ?></div>
			</div>
		</footer>
	</div>
<?php
	endif;
endif;
?>
</div>