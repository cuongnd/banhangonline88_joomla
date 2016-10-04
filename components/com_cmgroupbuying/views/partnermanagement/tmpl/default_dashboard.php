<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;
?>
<h1><?php echo JText::_('COM_CMGROUPBUYING_MANAGEMENT_PARTNER_DASHBOARD_HEADER'); ?></h1>
<div class="row-fluid">
	<div class="span12 statistics"> 
		<div class="pre-text"><?php echo JText::_('COM_CMGROUPBUYING_MANAGEMENT_PARTNER_DASHBOARD_PRE_TEXT'); ?></div>
		<ul>
			<li>
				<div><i class="icon-tags"></i></div>
				<div class="statistic"><?php echo JText::sprintf('COM_CMGROUPBUYING_MANAGEMENT_PARTNER_DASHBOARD_STATS_DEALS', $this->numOfDeals); ?></div>
			</li>
			<li>
				<div><i class="icon-shopping-cart"></i></div>
				<div class="statistic"><?php echo JText::sprintf('COM_CMGROUPBUYING_MANAGEMENT_PARTNER_DASHBOARD_STATS_COUPONS', $this->numOfCoupons); ?></div>
			</li>
			<li>
				<div><i class="icon-money"></i></div>
				<div class="statistic"><?php echo JText::sprintf('COM_CMGROUPBUYING_MANAGEMENT_PARTNER_DASHBOARD_STATS_EARNING', CMGroupBuyingHelperDeal::displayDealPrice($this->earning)); ?></div>
			</li>
		</ul>
	</div>
</div>
<div class="row-fluid">
	<div class="span12"><?php echo $this->welcome; ?></div>
</div>