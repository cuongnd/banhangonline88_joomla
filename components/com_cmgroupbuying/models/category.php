<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.model');

class CMGroupBuyingModelCategory extends JModelLegacy
{
	public function __construct($config = array())
	{
		parent::__construct($config);
	}

	public function getCategories()
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_categories";
		$db->setQuery($query);
		$categories = $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $categories;
	}

	public function getPublishedCategories()
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_categories WHERE published = 1";
		$db->setQuery($query);
		$categories = $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $categories;
	}

	public function getCategoryById($categoryId)
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_categories WHERE id = " . $categoryId;
		$db->setQuery($query);
		$category = $db->loadAssoc();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $category;
	}
}