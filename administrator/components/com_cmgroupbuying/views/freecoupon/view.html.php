<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class CMGroupBuyingViewFreeCoupon extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	public function display($tpl = null)
	{
		// Initialiase variables.
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');

		$app = JFactory::getApplication();
		$context = 'com_cmgroupbuying.edit.freecoupon';
		$data = $app->getUserState($context . '.data');

		// Check for errors.
		if(count($errors = $this->get('Errors')))
		{
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}

		$this->addToolbar();
		parent::display($tpl);
	}

	protected function addToolbar()
	{
		if($this->item->id == 0)
		{
			JToolBarHelper::title(JText::_('COM_CMGROUPBUYING_FREE_COUPON_CREATE_COUPON'), 'free_coupon.png');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_CMGROUPBUYING_FREE_COUPON_EDIT_COUPON'), 'free_coupon.png');
		}

		JToolBarHelper::apply('freecoupon.apply');
		JToolBarHelper::save('freecoupon.save');
		JToolBarHelper::save2new('freecoupon.save2new');
		JToolBarHelper::save2copy('freecoupon.save2copy');
		JToolBarHelper::cancel('freecoupon.cancel');
	}
}