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

		
	
		
require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjlookup.php');
	
			
	
	
			
	
	
		
if (file_exists(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjdisplay.php'))
{
	require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjdisplay.php');
} else if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjdisplay.php'))
{
	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjdisplay.php');
}

				
	
	
		
if (file_exists(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjpluginparams.php'))
{
	require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjpluginparams.php');
} else if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjpluginparams.php'))
{
	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjpluginparams.php');
}

			
	
	
	
class fsj_mainModelplugins extends JModelList
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
				'state', 'a.state',
						'lf2',
	
						'name', 'a.name',
	
						'title', 'a.title',
	
						'description', 'a.description',
	
							'settings', 'a.settings',
	
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



		$published = $this->getUserStateFromRequest($this->context.'.filter.published', 'filter_published', null, 'none', false);
		$this->setState('filter.published', $published);

		//$categoryId = $this->getUserStateFromRequest($this->context.'.filter.category_id', 'filter_category_id', null, 'none', false);
		//$this->setState('filter.category_id', $categoryId);

		//$level = $this->getUserStateFromRequest($this->context.'.filter.level', 'filter_level', 0, 'int', false);
		//$this->setState('filter.level', $level);



		$filterval = $this->getUserStateFromRequest($this->context.'.filter.type', 'filter_type', null, 'none', false);
		$this->setState('filter.type', $filterval);

		// List state information.
		
		// need default ordering here
		parent::populateState($ordering, $direction);
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
		$id	.= ':'.$this->getState('filter.published');
		//$id	.= ':'.$this->getState('filter.category_id');
		$id	.= ':'.$this->getState('filter.type');

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
					'a.id, a.state, a.type as type, a.name as name, a.title as title, a.description as description, a.settings as settings'
				)
			);

			$query->from('#__fsj_plg_plugin AS a');

			
			
			
												// Lookup field
					$query->select('l2.title AS lf2');
					$query->join('LEFT', '#__fsj_plg_type AS l2 ON l2.name = a.type');
																																										
			
			
							// Filter by published state
				$published = $this->getState('filter.published');
				if (is_numeric($published)) {
					$query->where('a.state = ' . (int) $published);
				}
				elseif ($published === '' || $published === null) {
					$query->where('(a.state = 0 OR a.state = 1)');
				}

			
			
			

											$filterval = $this->getState('filter.type');
				if ($filterval)
				{
											$query->where("a.type = '" . $db->escape($filterval) . "'");
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
