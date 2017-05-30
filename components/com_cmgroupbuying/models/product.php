<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class CMGroupBuyingModelProduct extends JModelLegacy
{
	public function __construct($config = array())
	{
		parent::__construct($config);

		$configuration  = JModelLegacy::getInstance('Configuration', 'CMGroupBuyingModel')->getConfiguration('pagination_limit');

		$app  = JFactory::getApplication();
		$limit = $configuration['pagination_limit'];
		$limitstart = $app->input->get('limitstart', 0, 'int');
		$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0);
		$this->setState('limit', $limit);
		$this->setState('limitstart', $limitstart);
	}

	function getPagination($categoryId = null)
	{
		require_once JPATH_COMPONENT.'/helpers/cmpagination.php';

		$total = $this->count($categoryId);
		$pagination = new CMPagination($total, $this->getState('limitstart'), $this->getState('limit'));

		return $pagination;
	}

	function getLimit($categoryId = 0)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_products'))
			->where($db->quoteName('published') . ' = ' . $db->quote('1'));

		if ($categoryId > 0)
		{
			$query->where($db->quoteName('category_id') . ' = ' . $db->quote($categoryId));
		}

		$query->order('ordering ASC');
		$db->setQuery($query, $this->getState('limitstart'), $this->getState('limit'));
		$products = $db->loadAssocList('id');

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $products;
	}

	function count($categoryId = 0)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);
		$query->select('COUNT(*)')
			->from($db->quoteName('#__cmgroupbuying_products'))
			->where($db->quoteName('published') . ' = ' . $db->quote('1'));

		if ($categoryId > 0)
		{
			$query->where($db->quoteName('category_id') . ' = ' . $db->quote($categoryId));
		}

		$db->setQuery($query);
		$count = $db->loadResult();
		return $count;
	}

	public function getProductById($productId)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_products'))
			->where($db->quoteName('id') . ' = ' . $db->quote($productId));

		$db->setQuery($query);
		$product = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $product;
	}

	public function getProductByIdAndAlias($productId, $productAlias)
	{
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		$query->select('*')
			->from($db->quoteName('#__cmgroupbuying_products'))
			->where($db->quoteName('id') . ' = ' . $db->quote($productId))
			->where($db->quoteName('alias') . ' = ' . $db->quote($productAlias));

		$db->setQuery($query);
		$product = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $product;
	}
}