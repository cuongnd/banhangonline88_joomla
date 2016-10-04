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
$pageAddress = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement');
$navigation = $jinput->get('navigation', 'dashboard', 'word');
$permissions = array(
	'view_deal_list'		=> $this->settings['partner_view_deal_list'],
	'submit_new_deal'		=> $this->settings['partner_submit_new_deal'],
	'check_coupon_status'	=> $this->settings['partner_check_coupon_status'],
	'change_coupon_status'	=> $this->settings['partner_change_coupon_status'],
	'view_coupon_list'		=> $this->settings['partner_view_coupon_list'],
	'view_buyer_info'		=> $this->settings['partner_view_buyer_info'],
	'view_commission_report'=> $this->settings['partner_view_commission_report'],
);

// Default settings
$this->navigation	= $navigation;
$this->return		= base64_encode($pageAddress);
$this->pageAddress	= $pageAddress;
$this->permissions	= $permissions;
$this->welcome		= $this->settings['partner_welcome'];
$this->footer		= $this->settings['partner_footer'];
?>
<div class=cm-management>
<?php
if($this->user->guest):
	echo $this->loadTemplate('login');
elseif(!$this->user->guest):
	if(empty($this->partner)):
		echo $this->loadTemplate('error');
	else:
		switch($this->navigation)
		{
			case 'commission_report':
				if($this->permissions['view_commission_report'] == 0)
				{
					echo $this->loadTemplate('access_denied');
				}
				else
				{
					$dealIdFilter = $jinput->get('filter_deal', '0', 'int');
					$deals = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealsOfPartner($this->partner['id']);
					$dealsFilte = $deals;
					JFactory::getApplication()->setUserState("cmgroupbuying.commission_deal_filter", $dealIdFilter);

					if($dealIdFilter > 0)
					{
						$deals = array();
						$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($dealIdFilter);

						if(!empty($deal))
							$deals[] = $deal;
					}

					$this->deals = $deals;

					$this->configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();
					echo $this->loadTemplate('commission_report_print');
				}
				break;
			default:
				break;
		}
?>
<?php
	endif;
endif;
?>
</div>