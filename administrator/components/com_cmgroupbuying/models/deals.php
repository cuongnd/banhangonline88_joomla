<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */


// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class CMGroupBuyingModelDeals extends JModelList
{
	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'alias', 'a.alias',
				'short_description', 'a.short_description',
				'description', 'a.description',
				'mobile_description', 'a.mobile_description',
				'start_date', 'a.start_date',
				'end_date', 'a.end_date',
				'highlights', 'a.highlights',
				'terms', 'a.terms',
				'image_path_1', 'a.image_path_1',
				'image_path_2', 'a.image_path_2',
				'image_path_3', 'a.image_path_3',
				'image_path_4', 'a.image_path_4',
				'image_path_5', 'a.image_path_5',
				'mobile_image_path', 'a.mobile_image_path',
				'background_image', 'a.background_image',
				'min_bought', 'a.min_bought',
				'max_bought', 'a.max_bought',
				'max_coupon', 'a.max_coupon',
				'partner_id', 'a.partner_id',
				'category_id', 'a.category_id',
				'coupon_path', 'a.coupon_path',
				'coupon_elements', 'a.coupon_elements',
				'tipped', 'a.tipped',
				'voided', 'a.voided',
				'featured', 'a.featured',
				'approved', 'a.approved',
				'published', 'a.published',
				'ordering', 'a.ordering'
			);
		}
		parent::__construct($config);
	}

	protected function populateState($ordering = null, $direction = null)
	{
		// Adjust the context to support modal layouts.
		if($layout = JFactory::getApplication()->input->get('layout', '', 'word'))
		{
			$this->context .= '.'.$layout;
		}

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search');
		$this->setState('filter.search', $search);

		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', '');
		$this->setState('filter.published', $published);

		$tipped = $this->getUserStateFromRequest($this->context.'.filter.tipped', 'filter_tipped', '');
		$this->setState('filter.tipped', $tipped);

		$voided = $this->getUserStateFromRequest($this->context.'.filter.voided', 'filter_voided', '');
		$this->setState('filter.voided', $voided);

		$featured = $this->getUserStateFromRequest($this->context.'.filter.featured', 'filter_featured', '');
		$this->setState('filter.featured', $featured);

		$approved = $this->getUserStateFromRequest($this->context.'.filter.approved', 'filter_approved', '');
		$this->setState('filter.approved', $approved);

		// List state information.
		parent::populateState('a.name', 'asc');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':'.$this->getState('filter.search');
		$id .= ':'.$this->getState('filter.published');
		$id .= ':'.$this->getState('filter.tipped');
		$id .= ':'.$this->getState('filter.voided');
		$id .= ':'.$this->getState('filter.featured');
		$id .= ':'.$this->getState('filter.approved');
		return parent::getStoreId($id);
	}

	protected function getListQuery()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			$this->getState(
				'list.select',
				'a.*'
			)
		);
		$query->from('#__cmgroupbuying_deals AS a');

		// Filter by published state
		$published = $this->getState('filter.published');

		if(is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		else
		{
			$query->where('(a.published = 0 OR a.published = 1)');
		}

		// Filter by tipped state
		$tipped = $this->getState('filter.tipped');

		if(is_numeric($tipped))
		{
			$query->where('a.tipped = ' . (int) $tipped);
		}
		else
		{
			$query->where('(a.tipped = 0 OR a.tipped = 1)');
		}

		// Filter by voided state
		$voided = $this->getState('filter.voided');
		if(is_numeric($voided))
		{
			$query->where('a.voided = ' . (int) $voided);
		}
		else
		{
			$query->where('(a.voided = 0 OR a.voided = 1)');
		}

		// Filter by featured
		$featured = $this->getState('filter.featured');
		if(is_numeric($featured))
		{
			$query->where('a.featured = "' . $featured . '"');
		}
		else
		{
			$query->where('(a.featured = 0 OR a.featured = 1)');
		}

		// Filter by approved status
		$approved = $this->getState('filter.approved');

		if(is_numeric($approved))
		{
			$query->where('a.approved = "' . $approved . '"');
		}
		else
		{
			$query->where('(a.approved = 0 OR a.approved = 1)');
		}

		// Filter by search in name.
		$search = $this->getState('filter.search');
		if(!empty($search))
		{
			if(version_compare(JVERSION, '3.0.0', 'lt')):
				$search = $db->Quote('%'.$db->getEscaped($search, true).'%');
			elseif(version_compare(JVERSION, '3.0.0', 'ge')):
				$search = $db->Quote('%'.$db->escape($search, true).'%');
			endif;

			$query->where('(a.name LIKE '.$search.' OR a.alias LIKE '.$search.')');
		}

		// Add the list ordering clause.
		$orderCol = $this->state->get('list.ordering');
		$orderDirn = $this->state->get('list.direction');

		if(version_compare(JVERSION, '3.0.0', 'lt')):
			$query->order($db->getEscaped($orderCol.' '.$orderDirn));
		elseif(version_compare(JVERSION, '3.0.0', 'ge')):
			$query->order($db->escape($orderCol.' '.$orderDirn));
		endif;

		return $query;
	}

	public function getDeals()
	{
		$db = $this->getDbo();
		$query = 'SELECT * FROM #__cmgroupbuying_deals ORDER BY name ASC';
		$db->setQuery($query);
		$allDeals = $db->loadAssocList('id');

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $allDeals;
	}

	public function getDealsByPartnerId($partnerId)
	{
		$db = $this->getDbo();
		$query = 'SELECT * FROM #__cmgroupbuying_deals WHERE partner_id = ' . $partnerId . ' ORDER BY name ASC';
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