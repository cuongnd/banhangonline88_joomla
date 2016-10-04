<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class CMGroupBuyingModelConfiguration extends JModelLegacy
{
	public function getConfiguration($columns = '*')
	{
		$db = $this->getDBO();
		$query = $db->getQuery(true);
		$query->select($columns);
		$query->from('#__cmgroupbuying_configuration');
		$query->where('id = 1');
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