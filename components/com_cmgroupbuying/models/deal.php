<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class CMGroupBuyingModelDeal extends JModelLegacy
{

	public function __construct($config = array())
	{
		parent::__construct($config);
		global $mainframe;

		$configuration  = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration('pagination_limit');

		$mainframe  = JFactory::getApplication();
		//$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limit = $configuration['pagination_limit'];
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function getPagination($numberOfDeals = null, $active = false, $upcoming = false, $expired = false, $categoryId = null, $partnerId = null, $locationString = null, $productId = null)
	{
		if(empty($this->_pagination))
		{
			require_once JPATH_COMPONENT.'/helpers/cmpagination.php';

			if($numberOfDeals != null)
			{
				$total = $numberOfDeals;
			}
			else
			{
				$total = $this->count($active, $upcoming, $expired, $categoryId, $partnerId, $locationString, $productId);
			}

			$this->_pagination = new CMPagination($total, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	function getLimit($active = true, $upcoming = false, $expired = false, $categoryId = null, $partnerId = null, $locationString = null, $rss = false, $orderBy = 'ordering ASC', $published = true, $productId = null)
	{
		$now = CMGroupBuyingHelperDateTime::getCurrentDateTime();
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_deals'));


		if($published == true)
		{
			$query->where($db->quoteName('published') . ' = ' . $db->quote('1'));
			$query->where($db->quoteName('approved') . ' = ' . $db->quote('1'));
		}
		else
		{
			$query->where('1 = 1');
		}

		if($active)
		{
			$query->where($db->quoteName('start_date') . ' <= ' . $db->quote($now));
			$query->where($db->quoteName('end_date') . ' >= ' . $db->quote($now));
		}
		elseif($upcoming)
		{
			$query->where($db->quoteName('start_date') . ' > ' . $db->quote($now));
		}
		elseif($expired)
		{
			$query->where($db->quoteName('end_date') . ' < ' . $db->quote($now));
		}

		if(is_numeric($categoryId))
		{
			$query->where($db->quoteName('category_id') . ' = ' . $db->quote($categoryId));
		}

		if(is_numeric($partnerId))
		{
			$query->where($db->quoteName('partner_id') . ' = ' . $db->quote($partnerId));
		}

		if(is_numeric($productId))
		{
			$query->where($db->quoteName('product_id') . ' = ' . $db->quote($productId));
		}

		if($locationString != null)
		{
			$query->where($db->quoteName('id') . ' IN ( ' . $locationString . ')');
		}

		$query->order($orderBy);

		if($rss == false)
		{
			$db->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));
		}
		else
		{
			$db->setQuery($query);
		}

		$deals = $db->loadAssocList('id');

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $deals;
	}

	function count($active = false, $upcoming = false, $expired = false, $categoryId = null, $partnerId = null, $locationString = null, $productId = null)
	{
		$now = CMGroupBuyingHelperDateTime::getCurrentDateTime();
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('COUNT(*)')
			->from($db->quoteName('#__cmgroupbuying_deals'))
			->where($db->quoteName('published') . ' = ' . $db->quote('1'))
			->where($db->quoteName('approved') . ' = ' . $db->quote('1'));

		if($active)
		{
			$query->where($db->quoteName('start_date') . ' <= ' . $db->quote($now));
			$query->where($db->quoteName('end_date') . ' >= ' . $db->quote($now));
		}
		elseif($upcoming)
		{
			$query->where($db->quoteName('start_date') . ' > ' . $db->quote($now));
		}
		elseif($expired)
		{
			$query->where($db->quoteName('end_date') . ' < ' . $db->quote($now));
		}

		if(is_numeric($categoryId))
		{
			$query->where($db->quoteName('category_id') . ' = ' . $db->quote($categoryId));
		}

		if(is_numeric($partnerId))
		{
			$query->where($db->quoteName('partner_id') . ' = ' . $db->quote($partnerId));
		}

		if(is_numeric($productId))
		{
			$query->where($db->quoteName('product_id') . ' = ' . $db->quote($productId));
		}

		if($locationString != null)
		{
			$query->where($db->quoteName('id') . ' IN ( ' . $locationString . ')');
		}

		$db->setQuery($query);
		$count = $db->loadResult();
		return $count;
	}

	public function getDealById($dealId)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_deals'))
			->where($db->quoteName('id') . ' = ' . $db->quote($dealId));

		$db->setQuery($query);
		$deal = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $deal;
	}

	public function getTodayDeal()
	{
		$now = CMGroupBuyingHelperDateTime::getCurrentDateTime();
		$locationId = JFactory::getApplication()->input->cookie->get('locationSubscription', '', 'int');
		$dealIdString = '';

		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_deals'))
			->where($db->quoteName('published')		. ' = ' . $db->quote('1'))
			->where($db->quoteName('approved')		. ' = ' . $db->quote('1'))
			->where($db->quoteName('featured')		. ' = ' . $db->quote('1'))
			->where($db->quoteName('voided')		. ' = ' . $db->quote('0'))
			->where($db->quoteName('start_date')	. ' <= ' . $db->quote($now))
			->where($db->quoteName('end_date')		. ' >= ' . $db->quote($now));

		if($locationId > 0)
		{
			$dealIdString = CMGroupBuyingHelperDeal::getDealsInLocation($locationId);
			$query->where($db->quoteName('id') . ' IN (' . $dealIdString . ')');
		}

		$query->order($db->quoteName('ordering') . ' ASC');
		$db->setQuery($query, 0, 1);
		$todayDeal = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		if(empty($todayDeal))
		{
			$query->clear();
			$query->select('*')
				->from($db->quoteName('#__cmgroupbuying_deals'))
				->where($db->quoteName('published')		. ' = ' . $db->quote('1'))
				->where($db->quoteName('approved')		. ' = ' . $db->quote('1'))
				->where($db->quoteName('voided')		. ' = ' . $db->quote('0'))
				->where($db->quoteName('start_date')	. ' <= ' . $db->quote($now))
				->where($db->quoteName('end_date')		. ' >= ' . $db->quote($now));

			if($dealIdString != '')
			{
				$query->where($db->quoteName('id') . ' IN (' . $dealIdString . ')');
			}

			$query->order($db->quoteName('ordering') . ' ASC');
			$db->setQuery($query, 0, 1);
			$todayDeal = $db->loadAssoc();

			if($db->getErrorNum())
			{
				//JError::raiseError(500, implode('<br />', $errors));
				return false;
			}
		}

		return $todayDeal;
	}

	public function getAllDeals()
	{
			$db = $this->getDbo();
			$query = $db->getQuery(true);

			$query->select('*')
				->from($db->quoteName('#__cmgroupbuying_deals'))
				->where($db->quoteName('published') . ' = ' . $db->quote('1'))
				->where($db->quoteName('approved') . ' = ' . $db->quote('1'))
				->order($db->quoteName('ordering') . ' ASC');

			$db->setQuery($query);
			$allDeals = $db->loadAssocList();

			if($db->getErrorNum())
			{
				//JError::raiseError(500, implode('<br />', $errors));
				return false;
			}

			return $allDeals;
	}

	public function getAllActiveDeals()
	{
		$now = CMGroupBuyingHelperDateTime::getCurrentDateTime();
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_deals'))
			->where($db->quoteName('published') . ' = ' . $db->quote('1'))
			->where($db->quoteName('approved') . ' = ' . $db->quote('1'))
			->where($db->quoteName('start_date') . ' <= ' . $db->quote($now))
			->where($db->quoteName('end_date') . ' >= ' . $db->quote($now))
			->order($db->quoteName('ordering') . ' ASC');

		$db->setQuery($query);
		$allActiveDeals = $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $allActiveDeals;
	}

	public function getDealsOfPartner($partnerId)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_deals'))
			->where($db->quoteName('partner_id') . ' = ' . $db->quote($partnerId))
			->order($db->quoteName('id') . ' DESC');

		$db->setQuery($query);
		$deals = $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $deals;
	}
}