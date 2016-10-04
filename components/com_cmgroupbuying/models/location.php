<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class CMGroupBuyingModelLocation extends JModelLegacy
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function getLocationById($locationId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__cmgroupbuying_locations')
			->where('id = ' . $db->quote($locationId));
		$db->setQuery($query);
		$location = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $location;
	}

	public static function getPublishedLocations()
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__cmgroupbuying_locations')
			->where('published = 1')
			->order('name ASC');
		$db->setQuery($query);
		$locations = $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $locations;
	}

	public function getLocationByGeotargetingName($geoName)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from('#__cmgroupbuying_locations')
			->where('geotargeting_name = ' . $db->quote($geoName));
		$db->setQuery($query);
		$location = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $location;
	}
}
