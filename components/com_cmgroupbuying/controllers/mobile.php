<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class CMGroupBuyingControllerMobile extends JControllerLegacy
{
	public function request_desktop()
	{
		$returnUrl = JFactory::getApplication()->input->get('return', '', 'BASE64');
		$returnUrl = base64_decode($returnUrl);
		$cookieLifetime = time() + 24 * 60 * 60;
		setcookie("cmmobileRequestDesktop", '1', $cookieLifetime, '/');
		$this->setRedirect($returnUrl);
		$this->redirect();
	}

	public function request_mobile()
	{
		$cookieLifetime = time() + 24 * 60 * 60;
		setcookie("cmmobileRequestDesktop", '0', $cookieLifetime, '/');
		$this->setRedirect('index.php');
		$this->redirect();
	}
}