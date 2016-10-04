<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

require_once JPATH_COMPONENT.'/helpers/common.php';

class CMGroupBuyingControllerLocation extends JControllerLegacy
{
	public function subscribe()
	{
		$jinput = JFactory::getApplication()->input;
		$app = JFactory::getApplication();
		$locationId = $jinput->get('location_id', 0, 'int');

		$configuration = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')
			->getConfiguration('point_cookie_lifetime');

		if($locationId > 0)
		{
			$cookieLifetime = time() + $configuration['point_cookie_lifetime'] * 60 * 60;
			setcookie("locationSubscription", $locationId, $cookieLifetime, '/');
		}
		elseif($locationId == 0)
		{
			$cookieLifetime = time() - $configuration['point_cookie_lifetime'] * 60 * 60;
			setcookie("locationSubscription", $locationId, $cookieLifetime, '/');
		}

		$returnURL = base64_decode($jinput->post->get('return', '', 'BASE64'));

		// Set the return URL if empty.
		if(empty($returnURL))
		{
			$returnURL = 'index.php?option=com_cmgroupbuying&view=todaydeal';
		}

		$app->redirect(JRoute::_($returnURL, false));
	}
}