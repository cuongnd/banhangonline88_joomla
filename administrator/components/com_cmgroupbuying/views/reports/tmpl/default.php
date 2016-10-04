<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

JHtml::_('behavior.multiselect');

$dealNameOption = array();

foreach($this->deals as $deal)
{
	$option = JHTML::_('select.option', $deal['id'], $deal['name']);
	array_push($dealNameOption, $option);
}

$partnerNameOption = array();

foreach($this->partners as $partner)
{
	$option = JHTML::_('select.option', $partner['id'], $partner['name']);
	array_push($partnerNameOption, $option);
}

$aggregatorSiteOption = array();

foreach($this->aggregatorSites as $aggregatorSite)
{
	$option = JHTML::_('select.option', $aggregatorSite['id'], $aggregatorSite['name']);
	array_push($aggregatorSiteOption, $option);
}
?>
<div class="cmgroupbuying">
	<h4><?php echo JText::_('COM_CMGROUPBUYING_REPORT_DEAL_REPORT'); ?></h4>

	<form action="index.php" method="get" name="dealForm" id="dealForm" class="report-form form-horizontal">
		<input type="hidden" name="option" value="com_cmgroupbuying" />
		<input type="hidden" name="view" value="report" />
		<input type="hidden" name="report" value="deal" />
		<?php echo JHTML::_('select.genericList', $dealNameOption, 'deal_id', null , 'value', 'text');?>
		<button class="btn" type="submit"><?php echo JText::_('COM_CMGROUPBUYING_REPORT_VIEW_REPORT_BUTTON'); ?></button>
	</form>

	<h4><?php echo JText::_('COM_CMGROUPBUYING_REPORT_PARTNER_REPORT'); ?></h4>
	<form action="index.php" method="get" name="partnerForm" id="partnerForm" class="report-form form-horizontal">
		<input type="hidden" name="option" value="com_cmgroupbuying" />
		<input type="hidden" name="view" value="report" />
		<input type="hidden" name="report" value="partner" />
		<?php echo JHTML::_('select.genericList', $partnerNameOption, 'partner_id', null , 'value', 'text');?>
		<button class="btn" type="submit"><?php echo JText::_('COM_CMGROUPBUYING_REPORT_VIEW_REPORT_BUTTON'); ?></button>
	</form>

	<h4><?php echo JText::_('COM_CMGROUPBUYING_REPORT_AGG_SITE_REPORT'); ?></h4>
	<form action="index.php" method="get" name="aggregatorForm" id="aggregatorForm" class="report-form form-horizontal">
		<input type="hidden" name="option" value="com_cmgroupbuying" />
		<input type="hidden" name="view" value="report" />
		<input type="hidden" name="report" value="aggregator_site" />
		<?php echo JHTML::_('select.genericList', $aggregatorSiteOption, 'site_id', null , 'value', 'text');?>
		<button class="btn" type="submit"><?php echo JText::_('COM_CMGROUPBUYING_REPORT_VIEW_REPORT_BUTTON'); ?></button>
	</form>
</div>