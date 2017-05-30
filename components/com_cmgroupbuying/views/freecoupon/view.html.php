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

class CMGroupBuyingViewFreeCoupon extends JViewLegacy
{
	function display($tpl = null)
	{
		$document = JFactory::getDocument();
		$couponId = JFactory::getApplication()->input->get('id', 0, 'int');
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();
		$coupon = JModelLegacy::getInstance('FreeCoupon', 'CMGroupBuyingModel')->getCouponById($couponId);
		$found = true;

		if(empty($coupon))
		{
			$found = false;
		}
		else
		{
			if($coupon['published'] == 0 || $coupon['approved'] == 0)
			{
				$found = false;
			}
			else
			{
				$found = true;
				$partnerId = $coupon['partner_id'];
				$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerById($partnerId);
				$this->assignRef('partner', $partner);

				// SEO
				$document->setTitle($coupon['name']);
				$document->setMetaData('description', $coupon['metadesc']);
				$document->setMetadata('keywords', $coupon['metakey']);

				$this->assignRef('configuration', $configuration);
				$this->assignRef('coupon', $coupon);
			}
		}

		$this->assignRef('found', $found);
		$layout = CMGroupBuyingHelperCommon::getLayout();
		$this->_setPath('template', JPATH_SITE . '/components/com_cmgroupbuying/layouts/' . $layout . '/');
		$this->_layout = "freecoupon";

		// Support content plugins
		// parent::display($tpl);
		$content = $this->loadTemplate();
		$content = JHTML::_('content.prepare', $content);
		echo $content;
	}
}