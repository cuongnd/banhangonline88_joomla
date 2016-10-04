<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class CMGroupBuyingViewCoupondesign extends JViewLegacy
{
	public function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$user = JFactory::getUser();
		$getUnpublished = false;
		$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerByUserId($user->id, $getUnpublished);

		if(empty($partner))
		{
			$message = JText::_('COM_CMGROUPBUYING_ACCESS_DENIED');
			$redirectUrl = 'index.php';
			$app->enqueueMessage($message, 'error');
			$app->redirect($redirectUrl);
		}

		//$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();
		//$this->assignRef('configuration', $configuration);
		parent::display($tpl);
	}
}