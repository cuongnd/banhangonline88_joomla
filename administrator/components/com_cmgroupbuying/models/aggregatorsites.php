<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class CMGroupBuyingModelAggregatorSites extends JModelList
{
	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'url', 'a.url',
				'description', 'a.description',
				'ref', 'a.ref',
				'published', 'a.published',
				'ordering', 'a.ordering',
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

		// List state information.
		parent::populateState('a.name', 'asc');
	}

	protected function getStoreId($id = '')
	{
		// Compile the store id.
		$id .= ':'.$this->getState('filter.search');
		$id .= ':'.$this->getState('filter.published');
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
		$query->from('#__cmgroupbuying_aggregator_sites AS a');

		// Filter by published state
		$published = $this->getState('filter.published');

		if(is_numeric($published))
		{
			$query->where('a.published = ' . (int) $published);
		}
		elseif($published === '')
		{
			$query->where('(a.published = 0 OR a.published = 1)');
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

			$query->where('(a.name LIKE '.$search.' OR a.url LIKE '.$search.' OR a.ref LIKE '.$search.')');
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

	public function getAggregatorSites()
	{
		// Create a new query object.
		$db = $this->getDbo();
		$query = $db->getQuery(true);

		// Select the required fields from the table.
		$query->select(
			'a.id, a.name, a.ref'
		);
		$query->from('#__cmgroupbuying_aggregator_sites AS a');
		$db->setQuery($query);

		if($this->_db->getErrorNum())
		{
			JError::raiseError(500, $this->_db->stderr());
			return false;
		}

		return $db->loadAssocList();
	}
}