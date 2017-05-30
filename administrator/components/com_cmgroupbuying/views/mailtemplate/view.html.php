<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class CMGroupBuyingViewMailTemplate extends JViewLegacy
{
	protected $form;
	protected $item;

	public function display($tpl = null)
	{
		// Initialiase variables.
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');

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
		JToolBarHelper::title(JText::_('COM_CMGROUPBUYING_MAIL_TEMPLATE_EDIT_TEMPLATE'), 'mail_template.png');
		JToolBarHelper::apply('mailtemplate.apply');
		JToolBarHelper::save('mailtemplate.save'); 
		JToolBarHelper::cancel('mailtemplate.cancel');
	}
}