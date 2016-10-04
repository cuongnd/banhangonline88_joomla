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

class CMGroupBuyingViewDealPrevue extends JViewLegacy
{
	function display($tpl = null)
	{
		$app = JFactory::getApplication();
		$document = JFactory::getDocument();
		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();
		$deal = $app->input->post->get('deal', array(), 'array');

		if(empty($deal))
		{
			$app->redirect(JURI::root());
		}
		else
		{
			$partnerId = $deal['partner_id'];
			$partner = JModelLegacy::getInstance('Partner', 'CMGroupBuyingModel')->getPartnerById($partnerId);
			$this->assignRef('partner', $partner);
			$document->setTitle($deal['name']);
			$deal['options'] = array();

			for($i = 1; $i <= 10; $i++)
			{
				if($deal['option_name_' . $i] != '' && $deal['option_original_price_' . $i] != '' && $deal['option_price_' . $i] != '')
				{
					$option = array();
					$option['name'] = $deal['option_name_' . $i];
					$option['original_price'] = $deal['option_original_price_' . $i];
					$option['price'] = $deal['option_price_' . $i];
					$deal['options'][$i] = $option;
				}
			}
		}

		$this->assignRef('configuration', $configuration);
		$this->assignRef('deal', $deal);

		$layout = CMGroupBuyingHelperCommon::getLayout();
		$this->_setPath('template', JPATH_SITE . '/components/com_cmgroupbuying/layouts/' . $layout . '/');
		$this->_layout = "dealprevue";
		parent::display($tpl);
	}
}