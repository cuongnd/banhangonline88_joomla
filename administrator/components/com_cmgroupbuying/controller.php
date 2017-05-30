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
	// The default view
	protected $default_view = 'dashboard';

	public function display($cachable = false, $urlparams = false)
	{
		require_once JPATH_COMPONENT.'/helpers/cmgroupbuying.php';
		$jinput = JFactory::getApplication()->input;
		CMGroupBuyingHelper::addSubmenu($jinput->get('view', '', 'word'));
		parent::display();
		return $this;
	}
}
