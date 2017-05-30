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

class CMGroupBuyingViewDeal extends JViewLegacy
{
	function display($tpl = null)
	{
		$jinput = JFactory::getApplication()->input;
		$document = JFactory::getDocument();
		$dealId = $jinput->get('id', 0, 'int');
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('jquery_loading, datetime_format, deal_referral, point_system,
				slideshow_switch_time, slideshow_fade_time, background_override,
				facebook_comment, facebook_app_id, facebook_admin_user_id, facebook_comment_num_posts, facebook_comment_width,
				disqus_comment, disqus_shortname, disqus_multilanguage,
				currency_thousands_sep, currency_dec_point, currency_decimals, currency_postfix, currency_prefix');
		$deal = JModelLegacy::getInstance('Deal', 'CMGroupBuyingModel')->getDealById($dealId);
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
				$found = true;

				// Check for referral
				$referrer = $jinput->get('referrer', null, 'string');

				if($referrer != null)
				{
					$cookieLifetime = time() + $configuration['point_cookie_lifetime'] * 60 * 60;
					setcookie($deal['id'], $referrer, $cookieLifetime, '/');
				}

				$partnerId  = $deal['partner_id'];
				$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerById($partnerId);
				$this->assignRef('partner', $partner);

				// SEO
				$document->setTitle($deal['name']);
				$document->setMetaData('description', $deal['metadesc']);
				$document->setMetadata('keywords', $deal['metakey']);

				// Update deal aggregators' status
				$ref = $jinput->get('ref', null, 'string');

				if($ref != null)
				{
					JModelLegacy::getInstance('Aggregator', 'CMGroupBuyingModel')->updateView($ref, $deal['id']);
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
		$this->_layout = "deal";

		// Support content plugins
		// parent::display($tpl);
		$content = $this->loadTemplate();
		$content = JHTML::_('content.prepare', $content);
		echo $content;
	}
}