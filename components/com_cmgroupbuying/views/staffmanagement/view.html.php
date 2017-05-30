<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

require_once JPATH_COMPONENT.'/helpers/common.php';

class CMGroupBuyingViewStaffManagement extends JViewLegacy
{
	protected $form;
	protected $item;
	protected $state;

	function display($tpl = null)
	{
		$jinput = JFactory::getApplication()->input;
		$tmpl = $jinput->get('tmpl', '', 'word');
		$navigation = $jinput->get('navigation', 'dashboard', 'word');
		$user = JFactory::getUser();
		$settings = JModelLegacy::getInstance('Management', 'CMGroupBuyingModel')->getManagementSettings();
		$access = false;
		$userAccessLevels = $user->getAuthorisedViewLevels($user->id);

		if(in_array($settings['staff_access_level'], $userAccessLevels))
		{
			$access = true;
		}

		$this->assignRef('user', $user);
		$this->assignRef('settings', $settings);
		$this->assignRef('access', $access);

		if($navigation == 'coupon_view')
			$this->_layout = "print";

		parent::display($tpl);
	}
}