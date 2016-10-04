<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

require_once(JPATH_SITE. "/components/com_cmgroupbuying/helpers/plugin.php");

class CMGroupBuyingViewOrders extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	public function display($tpl = null)
	{
		$this->items = $this->get('Items');
		$this->pagination = $this->get('Pagination');
		$this->state = $this->get('State');

		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$payments = CMGroupBuyingHelperPlugin::getPaymentPlugins();
		$this->assignRef('payments', $payments);

		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();
		$this->assignRef('configuration', $configuration);

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_CMGROUPBUYING_ORDER_MANAGER'), 'order.png');
		JToolBarHelper::back('COM_CMGROUPBUYING_BACK_TO_DASHBOARD', 'index.php?option=com_cmgroupbuying');
		JToolBarHelper::divider();
		JToolBarHelper::custom('orders.set_unpaid', 'unpublish.png', 'unpublish.png', 'COM_CMGROUPBUYING_ORDER_SET_UNPAID', true);
		JToolBarHelper::custom('orders.set_paid', 'publish.png', 'publish.png', 'COM_CMGROUPBUYING_ORDER_SET_PAID', true);
		JToolBarHelper::custom('orders.set_refunded', 'restore.png', 'restore.png', 'COM_CMGROUPBUYING_ORDER_SET_REFUNDED', true);
		JToolBarHelper::divider();
		JToolBarHelper::custom('order.view_info', 'stats.png', 'stats.png', 'COM_CMGROUPBUYING_ORDER_VIEW_INFO', true);
		JToolBarHelper::custom('order.edit_user_info', 'edit.png', 'edit.png', 'COM_CMGROUPBUYING_ORDER_EDIT_USER_INFO', true);
		JToolBarHelper::divider();
		JToolBarHelper::deleteList(JText::_('COM_CMGROUPBUYING_WARNING_DELETE_ITEMS'), 'orders.delete');
	}
}