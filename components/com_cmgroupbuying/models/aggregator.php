<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class CMGroupBuyingModelAggregator extends JModelLegacy
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function getAggregatorSiteByRef($ref)
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_aggregator_sites WHERE ref = '$ref'";
		$db->setQuery($query);
		$site = $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}
		
		return $site;
	}

	public function updateView($ref, $dealId)
	{
		$db = JFactory::getDbo();
		$query = $db->getQuery(true);

		$query->select('COUNT(*)')
			->from($db->quoteName('#__cmgroupbuying_aggregator_counter'))
			->where($db->quoteName('ref_id') . ' = ' . $db->quote($ref))
			->where($db->quoteName('deal_id') . ' = ' . $db->quote($dealId));

		$db->setQuery($query);
		$count = $db->loadResult();

		$query->clear();

		if($count == 0)
		{
			$query->insert($db->quoteName('#__cmgroupbuying_aggregator_counter'))
				->columns(
					array(
						$db->quoteName('ref_id'),
						$db->quoteName('deal_id'),
						$db->quoteName('view')
					)
				)->values($db->quote($ref) . ', ' . $db->quote($dealId) . ', ' . $db->quote('1'));
		}
		elseif($count == 1)
		{
			$query->update($db->quoteName('#__cmgroupbuying_aggregator_counter'))
				->set('view = view + 1 ')
				->where($db->quoteName('ref_id') . ' = ' . $db->quote($ref))
				->where($db->quoteName('deal_id') . ' = ' . $db->quote($dealId));
		}

		$db->setQuery($query);
		$db->execute();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return true;
	}
}