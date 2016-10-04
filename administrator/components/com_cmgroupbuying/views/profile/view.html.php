<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class CMGroupBuyingViewProfile extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	public function display($tpl = null)
	{
		$this->form = $this->get('Form');
		$this->item = $this->get('Item');
		$this->state = $this->get('State');

		$this->addToolbar();

		if(version_compare(JVERSION, '3.0.0', 'ge'))
		{
			$this->sidebar = JHtmlSidebar::render();
			JHtml::_('formbehavior.chosen', 'select');
		}

		parent::display($tpl);
	}

	protected function addToolbar()
	{
		JToolBarHelper::title(JText::_('COM_CMGROUPBUYING_USER_PROFILE_MANAGER'), 'profile');
		JToolBarHelper::back('COM_CMGROUPBUYING_BACK_TO_DASHBOARD', 'index.php?option=com_cmgroupbuying');
		JToolBarHelper::apply('profile.apply');
		JToolBarHelper::save('profile.savenclose');
	}
}
