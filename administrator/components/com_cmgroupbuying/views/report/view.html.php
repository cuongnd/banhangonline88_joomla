<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

require_once(JPATH_SITE. "/components/com_cmgroupbuying/helpers/common.php");

jimport('joomla.application.component.view');

class CMGroupBuyingViewReport extends JViewLegacy
{
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$jinput = $app->input;
		$report = $jinput->get('report', '', 'word');

		if(version_compare(JVERSION, '3.0.0', 'ge'))
			$this->sidebar = JHtmlSidebar::render();

		if($report == 'deal')
		{
			$dealId = $jinput->get('deal_id', 0, 'int');
			$dealId = (int)$dealId;
			$deal = JModelLegacy::getInstance('Deal','CMGroupBuyingModel')->getDealById($dealId);

			if(empty($deal))
			{
				$message = JText::_('COM_CMGROUPBUYING_REPORT_NO_DEAL_FOUND');
				$redirectUrl = 'index.php?option=com_cmgroupbuying&view=reports';
				$app->enqueueMessage( $message, 'error');
				$app->redirect($redirectUrl);;
			}
			else
			{
				$items = JModelLegacy::getInstance('OrderItem','CMGroupBuyingModel')->getItemForReport($dealId);
				$this->assignRef('report', $report);
				$this->assignRef('deal', $deal);
				$this->assignRef('items', $items);
			}
		}
		elseif($report == 'partner')
		{
			$partnerId = $jinput->get('partner_id', 0, 'int');
			$partnerId = (int)$partnerId;
			$partner = JModelLegacy::getInstance('Partner','CMGroupBuyingModel')->getPartnerById($partnerId);

			if(empty($partner))
			{
				$message = JText::_('COM_CMGROUPBUYING_REPORT_NO_PARTNER_FOUND');
				$app->enqueueMessage( $message, 'error');
				$app->redirect($redirectUrl);
			}
			else
			{
				$deals = JModelLegacy::getInstance('Deals','CMGroupBuyingModel')->getDealsByPartnerId($partnerId);
				$this->assignRef('report', $report);
				$this->assignRef('deals', $deals);
				$this->assignRef('partner', $partner);

				$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();
				$this->assignRef('configuration', $configuration);
			}
		}
		elseif($report == 'aggregator_site')
		{
			$siteId = $jinput->get('site_id', 0, 'int');
			$siteId= (int)$siteId;
			$aggregatorSite   = JModelLegacy::getInstance('AggregatorSite ','CMGroupBuyingModel')->getAggregatorSiteById($siteId);

			if(empty($aggregatorSite))
			{
				$message = JText::_('COM_CMGROUPBUYING_REPORT_NO_SITE_FOUND');
				$redirectUrl = 'index.php?option=com_cmgroupbuying&view=reports';
				$app->enqueueMessage( $message, 'error');
				$app->redirect($redirectUrl);
			}
			else
			{
				$deals = CMGroupBuyingHelperDeal::getDealsByRefId($aggregatorSite['ref']);
				$this->assignRef('report', $report);
				$this->assignRef('deals', $deals);
				$this->assignRef('aggregatorSite', $aggregatorSite);
			}
		}
		else
		{
			$redirectUrl = 'index.php?option=com_cmgroupbuying&view=reports';
			$app->redirect($redirectUrl);
		}

		$this->addDealToolbar();
		parent::display($tpl);
	}

	protected function addDealToolbar()
	{
		JToolBarHelper::title(JText::_('COM_CMGROUPBUYING_REPORT_MANAGER_DEAL'), 'report.png');
		JToolBarHelper::back('COM_CMGROUPBUYING_BACK', 'index.php?option=com_cmgroupbuying&view=reports');
	}
}