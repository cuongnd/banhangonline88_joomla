<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.controller');

class CMGroupBuyingController extends JControllerLegacy
{
	public function display($cachable = false, $urlparams = false)
	{
		$jinput = JFactory::getApplication()->input;

		// Set the default view name and format from the Request.
		$viewName = $jinput->get('view', 'todaydeal', 'word');
		$jinput->set('view', $viewName);

		parent::display($cachable, $urlparams);
		return $this;
	}
}
