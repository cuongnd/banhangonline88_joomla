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

class CMGroupBuyingViewFreeCouponPrevue extends JViewLegacy
{
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();
		$coupon = $app->input->post->get('coupon', array(), 'array');

		if(empty($coupon))
		{
			$app->redirect(JURI::root());
		}
		else
		{
			$partnerId = $coupon['partner_id'];
			$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerById($partnerId);
			$this->assignRef('partner', $partner);
			$document->setTitle($coupon['name']);
		}

		$this->assignRef('configuration', $configuration);
		$this->assignRef('coupon', $coupon);

		$layout = CMGroupBuyingHelperCommon::getLayout();
		$this->_setPath('template', JPATH_SITE . '/components/com_cmgroupbuying/layouts/' . $layout . '/');
		$this->_layout = "freecouponprevue";
		parent::display($tpl);
	}
}