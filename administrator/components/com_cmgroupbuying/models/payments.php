<?php
/**
 * @package     CMGroupBuying component
 * @copyright   Copyright (C) 2012-2014 CMExtension Team http://www.cmext.vn/
 * @license     http://www.gnu.org/licenses/gpl-3.0.html GNU/GPL
 */

// No direct access
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

class CMGroupBuyingModelPayments extends JModelList
{
	public function __construct($config = array())
	{
		if(empty($config['filter_fields']))
		{
			$config['filter_fields'] = array(
				'id', 'a.id',
				'name', 'a.name',
				'folder', 'a.folder',
				'lock_time', 'a.lock_time',
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
		$query->from('#__cmgroupbuying_payments AS a');

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

	public function getPayments()
	{
		$db = $this->getDbo();
		$query = "SELECT * FROM #__cmgroupbuying_payments WHERE published = 1 ORDER BY ordering ASC";
		$db->setQuery($query);
		$payments = $db->loadAssocList();

		if($db->getErrorNum())
		{
			//JError::raiseError(500, implode('<br />', $errors));
			return false;
		}

		return $payments;
	}
}
