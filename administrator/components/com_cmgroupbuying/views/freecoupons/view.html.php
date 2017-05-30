<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class CMGroupBuyingViewFreeCoupons extends JViewLegacy
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
		if (count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_CMGROUPBUYING_FREE_COUPON_MANAGER'), 'free_coupon.png');
		JToolBarHelper::back('COM_CMGROUPBUYING_BACK_TO_DASHBOARD', 'index.php?option=com_cmgroupbuying');
		JToolBarHelper::divider();
		JToolBarHelper::addNew('freecoupon.add');
		JToolBarHelper::editList('freecoupon.edit');
		JToolBarHelper::divider();
		JToolBarHelper::publish('freecoupons.publish', 'JTOOLBAR_PUBLISH', true);
		JToolBarHelper::unpublish('freecoupons.unpublish', 'JTOOLBAR_UNPUBLISH', true);
		JToolBarHelper::divider();
		JToolBarHelper::custom('freecoupons.approve', 'checkin.png', 'checkin.png', 'COM_CMGROUPBUYING_FREE_COUPON_APPROVE', true);
		JToolBarHelper::divider();
		JToolBarHelper::deleteList(JText::_('COM_CMGROUPBUYING_WARNING_DELETE_ITEMS'), 'freecoupons.delete');
	}
}