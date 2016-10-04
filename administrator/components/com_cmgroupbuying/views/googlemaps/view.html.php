<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.view');

class CMGroupBuyingViewGoogleMaps extends JViewLegacy
{
	public function display($tpl = null)
	{
		$jinput = JFactory::getApplication()->input;
		$latitude = $jinput->get('latitude', 0);
		$longitude = $jinput->get('longitude', 0);
		$zoom = 10;

		$this->assignRef('latitude', $latitude);
		$this->assignRef('longitude', $longitude);
		$this->assignRef('zoom', $zoom);
		parent::display($tpl);
	}
}