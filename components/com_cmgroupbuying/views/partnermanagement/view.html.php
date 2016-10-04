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

class CMGroupBuyingViewPartnerManagement extends JViewLegacy
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
		$getUnpublished = false;
		$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerByUserId($user->id, $getUnpublished);
		$settings = JModelLegacy::getInstance('Management', 'CMGroupBuyingModel')->getManagementSettings();

		$this->assignRef('user', $user);
		$this->assignRef('partner', $partner);
		$this->assignRef('settings', $settings);
		$this->assignRef('navigation', $navigation);

		if($tmpl == 'component' && $navigation == 'commission_report')
			$this->_layout = "print";

		parent::display($tpl);
	}
}