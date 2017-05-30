<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */


// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class CMGroupBuyingModelDealoption extends JModelLegacy
{
	function getOptions($dealId)
	{
		$db = JFactory::getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_deal_option WHERE deal_id = $dealId";
		$db->setQuery($query);
		$optionArray = $db->loadAssocList('option_id');

		if($db->getErrorNum())
		{
			JError::raiseError(500, $db->stderr());
			return false;
		}

		return $optionArray;
	}

	function getOption($dealId, $optionId)
	{
		$db = JFactory::getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_deal_option WHERE deal_id = $dealId AND option_id = $optionId";
		$db->setQuery($query);
		$option = $db->loadAssoc();

		if($db->getErrorNum())
		{
			JError::raiseError(500, $db->stderr());
			return false;
		}

		return $option;
	}

	public function delete(&$pks)
	{
		$db = JFactory::getDbo();

		foreach($pks as $dealId)
		{
			$query  = 'DELETE FROM #__cmgroupbuying_deal_option WHERE deal_id =' . $dealId;
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