<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class CMGroupBuyingViewPartner extends JViewLegacy
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
			JToolBarHelper::title(JText::_('COM_CMGROUPBUYING_PARTNER_CREATE_PARTNER'), 'partner.png');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_CMGROUPBUYING_PARTNER_EDIT_PARTNER'), 'partner.png');
		}

		JToolBarHelper::apply('partner.apply');
		JToolBarHelper::save('partner.save');
		JToolBarHelper::save2new('partner.save2new');
		JToolBarHelper::cancel('partner.cancel');
	}
}