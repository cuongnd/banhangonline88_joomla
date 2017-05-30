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

class CMGroupBuyingViewTodayDeal extends JViewLegacy
{
	function display($tpl = null)
	{
		$document = JFactory::getDocument();
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('jquery_loading, datetime_format, deal_referral, point_system,
				slideshow_switch_time, slideshow_fade_time, background_override,
				facebook_comment, facebook_app_id, facebook_admin_user_id, facebook_comment_num_posts, facebook_comment_width,
				disqus_comment, disqus_shortname, disqus_multilanguage,
				currency_thousands_sep, currency_dec_point, currency_decimals, currency_postfix, currency_prefix');
		$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getTodayDeal();
		$found = true;

		if(empty($deal))
		{
			$found = false;
		}
		else
		{
			if($deal['published'] == 0 || $deal['approved'] == 0)
			{
				$found = false;
			}
			else
			{
				$optionsOfDeal = array();
				$partnerId = $deal['partner_id'];
				$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerById($partnerId);
				$this->assignRef('partner', $partner);

				// SEO
				$document->setTitle($deal['name']);
				$params = JFactory::getApplication()->getParams();

				if ($params->get('menu-meta_description'))
				{
					$document->setMetaData('description', $params->get('menu-meta_description'));
				}
				else
				{
					$document->setMetaData('description', $deal['metadesc']);
				}

				if ($params->get('menu-meta_keywords'))
				{
					$document->setMetaData('keywords', $params->get('menu-meta_keywords'));
				}
				else
				{
					$document->setMetadata('keywords', $deal['metakey']);
				}

				$optionsOfDeal = JModelLegacy::getInstance('dealoption','cmgroupbuyingModel')->getOptions($deal['id']);
				$this->assignRef('optionsOfDeal', $optionsOfDeal);
				$this->assignRef('configuration', $configuration);
				$this->assignRef('deal', $deal);
			}
		}

		$this->assignRef('found', $found);

		$layout = CMGroupBuyingHelperCommon::getLayout();
		$this->_setPath('template', JPATH_SITE . '/components/com_cmgroupbuying/layouts/' . $layout . '/');
		$this->_layout = "todaydeal";

		// Support content plugins
		// parent::display($tpl);
		$content = $this->loadTemplate();
		$content = JHTML::_('content.prepare', $content);
		echo $content;
	}
}