<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class CMGroupBuyingViewAggregatorSite extends JViewLegacy
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

		if(version_compare(JVERSION, '3.0.0', 'ge'))
			$this->sidebar = JHtmlSidebar::render();

		parent::display($tpl);
	}

	protected function addToolbar()
	{
		if ($this->item->id == 0)
		{
			JToolBarHelper::title(JText::_('COM_CMGROUPBUYING_AGG_SITE_CREATE_SITE'), 'aggregator_site.png');
		}
		else
		{
			JToolBarHelper::title(JText::_('COM_CMGROUPBUYING_AGG_SITE_EDIT_SITE'), 'aggregator_site.png');
		}

		JToolBarHelper::apply('aggregatorsite.apply');
		JToolBarHelper::save('aggregatorsite.save');
		JToolBarHelper::save2new('aggregatorsite.save2new');
		JToolBarHelper::cancel('aggregatorsite.cancel');
	}
}