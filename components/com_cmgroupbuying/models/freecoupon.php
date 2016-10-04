<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class CMGroupBuyingModelFreeCoupon extends JModelLegacy
{

	public function __construct($config = array())
	{
		parent::__construct($config);
		global $mainframe;

		$configuration  = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration();

		$mainframe = JFactory::getApplication();
		//$limit = $mainframe->getUserStateFromRequest('global.list.limit', 'limit', $mainframe->getCfg('list_limit'), 'int');
		$limit = $configuration['pagination_limit'];
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function getPagination($numberOfCoupons = null, $locationString = null, $state = '')
	{
		if(empty($this->_pagination))
		{
			require_once JPATH_COMPONENT . '/helpers/cmpagination.php';

			if($numberOfCoupons != null)
			{
				$total = $numberOfCoupons;
			}
			else
			{
				$total = $this->count($locationString, $state);
			}

			$this->_pagination = new CMPagination($total, $this->getState('limitstart'), $this->getState('limit'));
		}

		return $this->_pagination;
	}

	function getLimit($locationString = null, $state = '', $orderBy = 'ordering ASC', $published = true)
	{
		$now = CMGroupBuyingHelperDateTime::getCurrentDateTime();
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_free_coupons'));

		if($published == true)
		{
			$query->where($db->quoteName('published') . ' = ' . $db->quote('1'));
			$query->where($db->quoteName('approved') . ' = ' . $db->quote('1'));
		}
		else
		{
			$query->where('1 = 1');
		}

		if($state == 'active')
		{
			$query->where($db->quoteName('start_date') . ' <= ' . $db->quote($now));
			$query->where($db->quoteName('end_date') . ' >= ' . $db->quote($now));
		}
		elseif($state == 'upcoming')
		{
			$query->where($db->quoteName('start_date') . ' > ' . $db->quote($now));
		}
		elseif($state == 'expired')
		{
			$query->where($db->quoteName('end_date') . ' < ' . $db->quote($now));
		}

		if($locationString != null)
		{
			$query->where($db->quoteName('id') . ' IN ( ' . $locationString . ')');
		}

		$query->order($orderBy);

		$db->setQuery($query);

		$coupons = $db->loadAssocList('id');

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $coupons;
	}

	function count($locationString = null, $state = '')
	{
		$now = CMGroupBuyingHelperDateTime::getCurrentDateTime();
		$db = JFactory::getDBO();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_free_coupons'))
			->where($db->quoteName('published') . ' = ' . $db->quote('1'))
			->where($db->quoteName('approved') . ' = ' . $db->quote('1'));

		if($locationString != null)
		{
			$query->where($db->quoteName('id') . ' IN ( ' . $locationString . ')');
		}

		if($state == 'active')
		{
			$query->where($db->quoteName('start_date') . ' <= ' . $db->quote($now));
			$query->where($db->quoteName('end_date') . ' >= ' . $db->quote($now));
		}
		elseif($state == 'upcoming')
		{
			$query->where($db->quoteName('start_date') . ' > ' . $db->quote($now));
		}
		elseif($state == 'expired')
		{
			$query->where($db->quoteName('end_date') . ' < ' . $db->quote($now));
		}

		if($locationString != null)
		{
			$query->where($db->quoteName('id') . ' IN ( ' . $locationString . ')');
		}

		$db->setQuery($query);
		$count = $db->loadResult();
		return $count;
	}

	public function getCouponById($couponId, $columns = '*')
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select($columns)
			->from($db->quoteName('#__cmgroupbuying_free_coupons'))
			->where($db->quoteName('id') . ' = ' . $db->quote($couponId));

		$db->setQuery($query);
		$coupon = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $coupon;
	}

	public function updateView($couponId)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->update($db->quoteName('#__cmgroupbuying_free_coupons'))
			->set('view = view + 1')
			->where($db->quoteName('id') . ' = ' . $db->quote($couponId));

		$db->setQuery($query);
		$db->execute();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return true;
	}

	public function getFreeCouponsOfPartner($partnerId)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_free_coupons'))
			->where($db->quoteName('partner_id') . ' = ' . $db->quote($partnerId))
			->order($db->quoteName('id') . ' DESC');

		$db->setQuery($query);
		$coupons = $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $coupons;
	}

	public function getTodayFreeCoupon()
	{
		$now = CMGroupBuyingHelperDateTime::getCurrentDateTime();
		$locationId = JFactory::getApplication()->input->cookie->get('locationSubscription', '', 'int');
		$couponIdString = '';

		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_free_coupons'))
			->where($db->quoteName('published')		. ' = ' . $db->quote('1'))
			->where($db->quoteName('approved')		. ' = ' . $db->quote('1'))
			->where($db->quoteName('featured')		. ' = ' . $db->quote('1'))
			->where($db->quoteName('start_date')	. ' <= ' . $db->quote($now))
			->where($db->quoteName('end_date')		. ' >= ' . $db->quote($now));

		if($locationId > 0)
		{
			$couponIdString = CMGroupBuyingHelperFreeCoupon::getFreeCouponsInLocation($locationId);
			$query->where($db->quoteName('id') . ' IN (' . $couponIdString . ')');
		}

		$query->order($db->quoteName('ordering') . ' ASC');
		$db->setQuery($query, 0, 1);
		$todayCoupon = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		if(empty($todayCoupon))
		{
			$query->clear();
			$query->select('*')
				->from($db->quoteName('#__cmgroupbuying_free_coupons'))
				->where($db->quoteName('published')		. ' = ' . $db->quote('1'))
				->where($db->quoteName('approved')		. ' = ' . $db->quote('1'))
				->where($db->quoteName('start_date')	. ' <= ' . $db->quote($now))
				->where($db->quoteName('end_date')		. ' >= ' . $db->quote($now));

			if($couponIdString != '')
			{
				$query->where($db->quoteName('id') . ' IN (' . $couponIdString . ')');
			}

			$query->order($db->quoteName('ordering') . ' ASC');
			$db->setQuery($query, 0, 1);
			$todayCoupon = $db->loadAssoc();

			if($db->getErrorNum())
			{
				//JError::raiseError(500, implode('<br />', $errors));
				return false;
			}
		}

		return $todayCoupon;
	}
}
