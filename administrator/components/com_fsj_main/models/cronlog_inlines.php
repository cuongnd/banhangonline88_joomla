<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport('joomla.application.component.modellist');

$option = JRequest::getVar('option');

		
	
	
					
	
	
		
if (file_exists(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjcron.php'))
{
	require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjcron.php');
} else if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjcron.php'))
{
	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjcron.php');
}

				
	
	
		
if (file_exists(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjyesno.php'))
{
	require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjyesno.php');
} else if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjyesno.php'))
{
	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjyesno.php');
}

			
class fsj_mainModelcronlog_inlines extends JModelList
{
	/**
	 * Constructor.
	 *
	 * @param	array	An optional associative array of configuration settings.
	 * @see		JController
	 * @since	1.6
	 */
	public function __construct($config = array())
	{
	
		// list all of the fields that can be used for sorting and filters
		
		if (empty($config['filter_fields'])) {
			$config['filter_fields'] = array(
				'id', 'a.id',
						'source', 'a.source',
	
						'source_id', 'a.source_id',
	
						'event', 'a.event',
	
						'whentime', 'a.whentime',
	
						'whendate', 'a.whendate',
	
						'success', 'a.success',
	
						'result', 'a.result',
	
						'log', 'a.log',
	
			);
		}

		parent::__construct($config);
	}

	/**
	 * Method to auto-populate the model state.
	 *
	 * Note. Calling getState in this method will result in recursion.
	 *
	 * @return	void
	 * @since	1.6
	 */
	protected function populateState($ordering = null, $direction = null)
	{
		// Initialise variables.
		$app = JFactory::getApplication();
		$session = JFactory::getSession();

		// Adjust the context to support modal layouts.
		if ($layout = JRequest::getVar('layout')) {
			//$this->context .= '.'.$layout;
		}
		
				

		// ONE OF THESE FOR EACH ITEM IN THE FILTER

		$search = $this->getUserStateFromRequest($this->context.'.filter.search', 'filter_search', null, 'none', false);
		$this->setState('filter.search', $search);




		//$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id', null, 'none', false);
		//$this->setState('filter.category_id', $categoryId);

		//$level = $this->getUserStateFromRequest($this->context.'.filter.level', 'filter_level', 0, 'int', false);
		//$this->setState('filter.level', $level);



		$filterval = $this->getUserStateFromRequest($this->context.'.filter.success', 'filter_success', null, 'none', false);
		$this->setState('filter.success', $filterval);
		$filterval = $this->getUserStateFromRequest($this->context.'.filter.source', 'filter_source', null, 'none', false);
		$this->setState('filter.source', $filterval);
		$filterval = $this->getUserStateFromRequest($this->context.'.filter.whendate', 'filter_whendate', null, 'none', false);
		$this->setState('filter.whendate', $filterval);

		// List state information.
		
		// need default ordering here
		parent::populateState('a.whentime', 'desc');
	}

	/**
	 * Method to get a store id based on model configuration state.
	 *
	 * This is necessary because the model is used by the component and
	 * different modules that might need different sets of data or different
	 * ordering requirements.
	 *
	 * @param	string		$id	A prefix for the store id.
	 *
	 * @return	string		A store id.
	 * @since	1.6
	 */
	protected function getStoreId($id = '')
	{
		// ONE OF THESE FOR EACH ITEM IN THE FILTER
		
		// Compile the store id.
		$id	.= ':'.$this->getState('filter.search');
		//$id	.= ':'.$this->getState('filter.category_id');
		$id	.= ':'.$this->getState('filter.success');
		$id	.= ':'.$this->getState('filter.source');
		$id	.= ':'.$this->getState('filter.whendate');

		return parent::getStoreId($id);
	}

	/**
	 * Build an SQL query to load the list data.
	 *
	 * @return	JDatabaseQuery
	 * @since	1.6
	 */
	protected function getListQuery()
	{
					// HORRID COMPLEX QUERY BUILDING FOR THE DATA
			// INCLUDES ALL SUB TABLES AND LANGUGAES AND OTHER SHIT
		
			// Create a new query object.
			$db		= $this->getDbo();
			$query	= $db->getQuery(true);
			$user	= JFactory::getUser();

						
			// Select the required fields from the table.
			$query->select(
				$this->getState(
					'list.select',
					'a.id, a.source as source, a.source_id as source_id, a.event as event, a.whentime as whentime, a.whendate as whendate, a.success as success, a.result as result, a.log as log'
				)
			);

			$query->from('#__fsj_main_cronlog AS a');

			
			
			
																																																											
			
			
			
			
			

											$filterval = $this->getState('filter.success');
				if ($filterval)
				{
											
          
              if ($filterval == -1) $filterval = 0;
              $query->where("a.success = '" . $db->escape($filterval) . "'");
          
        	
									}
											$filterval = $this->getState('filter.source');
				if ($filterval)
				{
											
          
              list ($source, $source_id) = explode("-", $filterval);
              $query->where("a.source = '" . $db->escape($source) . "'");
              $query->where("a.source_id = '" . $db->escape($source_id) . "'");
          
        	
									}
											$filterval = $this->getState('filter.whendate');
				if ($filterval)
				{
											$query->where("a.whendate = '" . $db->escape($filterval) . "'");
									}
			
			
			
			
			
			
				// Add the list ordering clause.
							$orderCol	= $this->state->get('list.ordering', 'a.id');
							$orderDirn	= $this->state->get('list.direction', 'asc');
		
			
						
			
			$query->order($db->escape($orderCol.' '.$orderDirn));
		
			//echo "Qry: " . $query->__toString() . "<br>";
		
			//echo nl2br(str_replace('#__','jos_',$query));
			return $query;
			}


	/**
	 * Method to get a list of articles.
	 * Overridden to add a check for access levels.
	 *
	 * @return	mixed	An array of data items on success, false on failure.
	 * @since	1.6.1
	 */
	public function getItems()
	{
				
			$items	= parent::getItems();
		
			$app	= JFactory::getApplication();
			if ($app->isSite()) {
				$user	= JFactory::getUser();
				$groups	= $user->getAuthorisedViewLevels();

				for ($x = 0, $count = count($items); $x < $count; $x++) {
					//Check the access level. Remove articles the user shouldn't see
					if (!in_array($items[$x]->access, $groups)) {
						unset($items[$x]);
					}
				}
			}
			return $items;
			}
	


}
