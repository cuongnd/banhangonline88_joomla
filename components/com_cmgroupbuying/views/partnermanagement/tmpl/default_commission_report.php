<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;
$link = CMGroupBuyingHelperCommon::prepareRedirect('index.php?option=com_cmgroupbuying&view=partnermanagement&navigation=commission_report');
$filterDealOption = array();
$option = JHTML::_('select.option', "0", JText::_('COM_CMGROUPBUYING_PARTNER_DEAL_FILTER_ALL_DEALS'));
array_push($filterDealOption, $option);

foreach($this->dealsFilter as $deal)
{
	$option = JHTML::_('select.option', $deal['id'], $deal['name']);
	array_push($filterDealOption, $option);
}

$filterDealState = JFactory::getApplication()->getUserStateFromRequest("cmgroupbuying.commission_deal_filter", 'commission_deal_filter', 0);
?>
<h3><?php echo JText::_('COM_CMGROUPBUYING_MANAGEMENT_PARTNER_COMMISSION_REPORT_HEADER'); ?></h3>
<div class="row-fluid">
	<div class="span6">
		<form class="form-inline pull-left" id="commission_filter_form" name="partner_filter_form" method="post" action="<?php echo $link; ?>">
			<?php echo JHTML::_('select.genericList', $filterDealOption, 'filter_deal', null , 'value', 'text', $filterDealState); ?>
			<input type="submit" class="btn btn-primary" value="<?php echo JText::_('COM_CMGROUPBUYING_PARTNER_DEAL_FILTER_SUBMIT_BUTTON'); ?>" />
		</form>
	</div>
	<div class="span6">
		<a href="index.php?option=com_cmgroupbuying&view=partnermanagement&navigation=commission_report&filter_deal=<?php echo $this->dealIdFilter; ?>&tmpl=component" target="_blank" class="btn btn-primary btn-small pull-right"><?php echo JText::_('COM_CMGROUPBUYING_PRINTABLE_VERSION'); ?></a>
	</div>
</div>
<?php foreach($this->deals as $deal): ?>
<fieldset>
	<legend><?php echo $deal['name']; ?></legend>
	<p><?php echo JText::_('COM_CMGROUPBUYING_COMMISSION_RATE'); ?>: <?php echo $deal['commission_rate']; ?>%</p>
	<table class="table table-striped">
		<thead>
			<tr>
				<th><?php echo JText::_('COM_CMGROUPBUYING_ORDER_OPTION_NAME'); ?></th>
				<th><?php echo JText::_('COM_CMGROUPBUYING_ORDER_VALUE'); ?></th>
				<th><?php echo JText::_('COM_CMGROUPBUYING_NUMBER_OF_SOLD_COUPONS'); ?></th>
				<th><?php echo JText::_('COM_CMGROUPBUYING_MANAGEMENT_PARTNER_COMMISSION_SITE_EARNS'); ?></th>
				<th><?php echo JText::_('COM_CMGROUPBUYING_MANAGEMENT_PARTNER_COMMISSION_PARTNER_EARNS'); ?></th>
			</tr>
		</thead>
		<tbody>
			<?php
			$optionsOfDeal = JModelLegacy::getInstance('dealoption','cmgroupbuyingModel')->getOptions($deal['id']);
			$couponTotal = $siteTotal = $partnerTotal = 0;
			foreach($optionsOfDeal as $option):
			?>
			<tr>
				<td><?php echo $option['name']; ?></td>
				<td>
					<?php
					if($deal['advance_payment'])
						$value = $option['advance_price'];
					else
						$value = $option['price'];
					echo CMGroupBuyingHelperDeal::displayDealPrice($value, $this->configuration);
					?>
				</td>
				<td>
					<?php
					$numOfCoupons = CMGroupBuyingHelperDeal::countPaidOption($deal['id'], $option['option_id']);
					$couponTotal += $numOfCoupons;
					echo $numOfCoupons;
					?>
				</td>
				<td>
					<?php
					$siteCommission = $value * $numOfCoupons * $deal['commission_rate'] / 100;
					$siteTotal += $siteCommission;
					echo CMGroupBuyingHelperDeal::displayDealPrice($siteCommission, $this->configuration);
					?>
				</td>
				<td>
					<?php
					$partnerCommission = $value * $numOfCoupons * (100 - $deal['commission_rate']) / 100;
					$partnerTotal += $partnerCommission;
					echo CMGroupBuyingHelperDeal::displayDealPrice($partnerCommission, $this->configuration);
					?>
				</td>
			</tr>
			<?php
			endforeach;
			?>
			<tr class="success">
				<td><?php echo JText::_('COM_CMGROUPBUYING_TOTAL'); ?>:</td>
				<td></td>
				<td><?php echo $couponTotal; ?></td>
				<td><?php echo CMGroupBuyingHelperDeal::displayDealPrice($siteTotal, $this->configuration); ?></td>
				<td><?php echo CMGroupBuyingHelperDeal::displayDealPrice($partnerTotal, $this->configuration); ?></td>
			</tr>
		</tbody>
	</table>
</fieldset>
<?php endforeach; ?>
