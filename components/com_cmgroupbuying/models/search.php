<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class CMGroupBuyingModelSearch extends JModelLegacy
{
	function __construct($config = array())
	{
		parent::__construct($config);
		$configuration  = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration('pagination_limit');
		$limit = $configuration['pagination_limit'];
		$limitstart = JFactory::getApplication()->input->get('limitstart', 0, 'int');
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function getLimit($keyword, $locationId, $categoryId)
	{
		$db = JFactory::getDBO();
		$locationId = (int)$locationId;
		$categoryId = (int)$categoryId;

		if($keyword != '')
		{
			$k = $db->quote('%' . $db->escape($keyword, true) . '%', false);
		}
		else
		{
			$k = '';
		}

		// Search deals.
		$dealQuery = "(SELECT d.id AS id, d.alias AS alias, d.name AS name, d.image_path_1 AS image, d.mobile_image_path AS mobile_image, d.short_description AS description, 'deal' AS type FROM #__cmgroupbuying_deals AS d ";
 
		if ($locationId > 0)
		{
			$dealQuery .= "INNER JOIN  #__cmgroupbuying_deal_location l ON d.id = l.deal_id ";
		}

		$dealQuery .= "WHERE d.published = 1 AND d.approved = 1 ";

		if($keyword != '')
		{
			$dealQuery .= "AND (";
			$dealQuery .= "d.name LIKE " . $k;
			$dealQuery .= " OR d.short_description LIKE " . $k;
			$dealQuery .= " OR d.description LIKE " . $k;
			$dealQuery .= " OR d.highlights LIKE " . $k;
			$dealQuery .= " OR d.terms LIKE " . $k;
			$dealQuery .= ") ";
		}

		if($locationId > 0)
		{
			$dealQuery .= "AND l.location_id = " . $db->quote($locationId) . " ";
		}

		if($categoryId > 0)
		{
			$dealQuery .= "AND d.category_id = " . $db->quote($categoryId) . " ";
		}

		$dealQuery .= ")";

		// Search free coupons.
		$couponQuery = "(SELECT c.id AS id, c.alias AS alias, c.name AS name, c.image_path_1 AS image, c.mobile_image_path AS mobile_image, c.short_description AS description, 'coupon' AS type FROM #__cmgroupbuying_free_coupons AS c ";

		if ($locationId > 0)
		{
			$couponQuery .= "INNER JOIN  #__cmgroupbuying_free_coupon_location m ON c.id = m.coupon_id ";
		}

		$couponQuery .= "WHERE c.published = 1 AND c.approved = 1 ";

		if($keyword != '')
		{
			$couponQuery .= "AND (";
			$couponQuery .= "c.name LIKE " . $k;
			$couponQuery .= " OR c.short_description LIKE " . $k;
			$couponQuery .= " OR c.description LIKE " . $k;
			$couponQuery .= ") ";
		}

		if($locationId > 0)
		{
			$couponQuery .= "AND m.location_id = " . $db->quote($locationId) . " ";
		}

		if($categoryId > 0)
		{
			$couponQuery .= "AND c.category_id = " . $db->quote($categoryId) . " ";
		}

		$couponQuery .= ")";

		$query = $dealQuery . " UNION " . $couponQuery . " ORDER BY name ASC";

		$db->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));
		$items  = $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $items;
	}

	function count($keyword, $locationId, $categoryId)
	{
		$db = JFactory::getDBO();
		$locationId = (int)$locationId;
		$categoryId = (int)$categoryId;

		if($keyword != '')
		{
			$k = $db->quote('%' . $db->escape($keyword, true) . '%', false);
		}
		else
		{
			$k = '';
		}

		// Search deals.
		$dealQuery = "(SELECT d.id FROM #__cmgroupbuying_deals AS d ";
 
		if ($locationId > 0)
		{
			$dealQuery .= "INNER JOIN  #__cmgroupbuying_deal_location l ON d.id = l.deal_id ";
		}

		$dealQuery .= "WHERE d.published = 1 AND d.approved = 1 ";

		if($keyword != '')
		{
			$dealQuery .= "AND (";
			$dealQuery .= "d.name LIKE " . $k;
			$dealQuery .= " OR d.short_description LIKE " . $k;
			$dealQuery .= " OR d.description LIKE " . $k;
			$dealQuery .= " OR d.highlights LIKE " . $k;
			$dealQuery .= " OR d.terms LIKE " . $k;
			$dealQuery .= ") ";
		}

		if($locationId > 0)
		{
			$dealQuery .= "AND l.location_id = " . $db->quote($locationId) . " ";
		}

		if($categoryId > 0)
		{
			$dealQuery .= "AND d.category_id = " . $db->quote($categoryId) . " ";
		}

		$dealQuery .= ")";

		// Search free coupons.
		$couponQuery = "(SELECT c.id FROM #__cmgroupbuying_free_coupons AS c ";
 
		if ($locationId > 0)
		{
			$couponQuery .= "INNER JOIN  #__cmgroupbuying_free_coupon_location m ON c.id = m.coupon_id ";
		}

		$couponQuery .= "WHERE c.published = 1 AND c.approved = 1 ";

		if($keyword != '')
		{
			$couponQuery .= "AND (";
			$couponQuery .= "c.name LIKE " . $k;
			$couponQuery .= " OR c.short_description LIKE " . $k;
			$couponQuery .= " OR c.description LIKE " . $k;
			$couponQuery .= ") ";
		}

		if($locationId > 0)
		{
			$couponQuery .= "AND m.location_id = " . $db->quote($locationId) . " ";
		}

		if($categoryId > 0)
		{
			$couponQuery .= "AND c.category_id = " . $db->quote($categoryId) . " ";
		}

		$couponQuery .= ")";

		$query = "SELECT COUNT(*) FROM (" . $dealQuery . " UNION ALL " . $couponQuery . ") AS count";

		$db->setQuery($query);
		$count  = $db->loadResult();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $count;
	}

	function getPagination($keyword, $locationId, $categoryId)
	{
		require_once JPATH_COMPONENT . '/helpers/cmpagination.php';
		$total = $this->count($keyword, $locationId, $categoryId);
		$pagination = new CMPagination($total, $this->getState('limitstart'), $this->getState('limit'));
		return $pagination;
	}
}