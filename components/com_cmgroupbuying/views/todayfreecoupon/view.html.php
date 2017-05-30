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

class CMGroupBuyingViewTodayFreeCoupon extends JViewLegacy
{
	function display($tpl = null)
	{
		$jinput = JFactory::getApplication()->input;
		$document = JFactory::getDocument();
		$couponId = $jinput->get('id', 0, 'int');
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('jquery_loading, datetime_format, deal_referral, point_system,
				slideshow_switch_time, slideshow_fade_time, background_override,
				facebook_comment, facebook_app_id, facebook_admin_user_id, facebook_comment_num_posts, facebook_comment_width,
				disqus_comment, disqus_shortname, disqus_multilanguage,
				currency_thousands_sep, currency_dec_point, currency_decimals, currency_postfix, currency_prefix');
		$coupon = JModelLegacy::getInstance('FreeCoupon', 'CMGroupBuyingModel')->getTodayFreeCoupon();
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

				$partnerId  = $coupon['partner_id'];
				$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerById($partnerId);
				$this->assignRef('partner', $partner);

				// SEO
				$document->setTitle($coupon['name']);
				$params = JFactory::getApplication()->getParams();

				if ($params->get('menu-meta_description'))
				{
					$document->setMetaData('description', $params->get('menu-meta_description'));
				}
				else
				{
					$document->setMetaData('description', $coupon['metadesc']);
				}

				if ($params->get('menu-meta_keywords'))
				{
					$document->setMetaData('keywords', $params->get('menu-meta_keywords'));
				}
				else
				{
					$document->setMetadata('keywords', $coupon['metakey']);
				}

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