<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */


// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class CMGroupBuyingModelDeallocation extends JModelLegacy
{
	function getLocations($dealId)
	{
		$db = JFactory::getDbo();
		$query = "SELECT location_id FROM #__cmgroupbuying_deal_location WHERE deal_id = $dealId";
		$db->setQuery($query);
		$locationArray = $db->loadColumn();

		if($db->getErrorNum())
		{
			JError::raiseError(500, $db->stderr());
			return false;
		}

		return $locationArray;
	}

	public function delete(&$pks)
	{
		$db = JFactory::getDbo();

		foreach($pks as $dealId)
		{
			$query  = 'DELETE FROM #__cmgroupbuying_deal_location WHERE deal_id = ' . $dealId;
			$db->setQuery($query);
			$db->execute();
		}

		if($db->getErrorNum())
		{
			JError::raiseError(500, $db->stderr());
			return false;
		}

		return true;
	}
}
?>