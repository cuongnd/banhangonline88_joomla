<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class CMGroupBuyingModelPartner extends JModelLegacy
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function getPartnerById($partnerId)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_partners'))
			->where($db->quoteName('id') . ' = ' . $db->quote($partnerId));

		$db->setQuery($query);
		$partner = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $partner;
	}

	public function getPartnerByUserId($userId, $getUnpublished = true)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// One partner is assigned to only one Joomla user, but to prevent unexpected error whern a partner is assigned to more than one Joomla user we use ORDER BY and LIMIT

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_partners'))
			->where($db->quoteName('user_id') . ' = ' . $db->quote($userId));

		if(!$getUnpublished)
			$query->where($db->quoteName('published') . ' = ' . $db->quote('1'));

		$query->order($db->quoteName('id'));
		$db->setQuery($query, 0, 1);
		$partner = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $partner;
	}

	public function getPartnerIdByUserId($userId)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// One partner is assigned to only one Joomla user, but to prevent unexpected error whern a partner is assigned to more than one Joomla user we use ORDER BY and LIMIT

		$query->select($db->quoteName('id'))
			->from($db->quoteName('#__cmgroupbuying_partners'))
			->where($db->quoteName('published') . ' = ' . $db->quote('1'))
			->where($db->quoteName('user_id') . ' = ' . $db->quote($userId));

		$query->order($db->quoteName('id'));
		$db->setQuery($query, 0, 1);

		$partnerId = $db->loadResult();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $partnerId;
	}
}