<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class CMGroupBuyingModelManagement extends JModelLegacy
{
	public function getManagementSettings()
	{
		$db = JFactory::getDBO();
		$query = 'SELECT * FROM #__cmgroupbuying_management WHERE id = 1';
		$db->setQuery($query);
		$configuration = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $configuration;
	}
}