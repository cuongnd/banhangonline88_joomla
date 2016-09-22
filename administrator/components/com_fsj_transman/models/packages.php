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
	
			
	
	
										
	
	
					
	
	
		
if (file_exists(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmaddfiles.php'))
{
	require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmaddfiles.php');
} else if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmaddfiles.php'))
{
	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmaddfiles.php');
}

			
	
	
		
if (file_exists(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmmakepackage.php'))
{
	require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmmakepackage.php');
} else if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmmakepackage.php'))
{
	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmmakepackage.php');
}

				
	
	
		
if (file_exists(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmpubdisp.php'))
{
	require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmpubdisp.php');
} else if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmpubdisp.php'))
{
	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmpubdisp.php');
}

				
	
	
		
if (file_exists(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjstatic.php'))
{
	require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjstatic.php');
} else if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjstatic.php'))
{
	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjstatic.php');
}

	
class fsj_transmanModelpackages extends JModelList
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
				'title', 'a.title',
						'lf5',
	
						'ver', 'a.ver',
	
						'author', 'a.author',
	
						'creationDate', 'a.creationDate',
	
						'email', 'a.email',
	
						'url', 'a.url',
	
						'copyright', 'a.copyright',
	
						'license', 'a.license',
	
						'filename', 'a.filename',
	
						'description', 'a.description',
	
						'adminfiles', 'a.adminfiles',
	
						'sitefiles', 'a.sitefiles',
	
						'files', 'a.files',
	
							'pubfolder', 'a.pubfolder',
	
							'updateserver', 'a.updateserver',
	
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



		$filterval = $this->getUserStateFromRequest($this->context.'.filter.langcode', 'filter_langcode', null, 'none', false);
		$this->setState('filter.langcode', $filterval);

		// List state information.
		
		// need default ordering here
		parent::populateState('a.title', 'asc');
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
		$id	.= ':'.$this->getState('filter.langcode');

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
					'a.id, a.title, a.langcode as langcode, a.ver as ver, a.author as author, a.creationDate as creationDate, a.email as email, a.url as url, a.copyright as copyright, a.license as license, a.filename as filename, a.description as description, a.adminfiles as adminfiles, a.sitefiles as sitefiles, a.files as files, a.pubfolder as pubfolder, a.updateserver as updateserver'
				)
			);

			$query->from('#__fsj_transman_package AS a');

			
			
			
												// Lookup field
					$query->select('l5.text AS lf5');
					$query->join('LEFT', '(SELECT element, element as text, element as value FROM #__extensions WHERE type = "language" GROUP BY element ORDER BY element) AS l5 ON l5.element = a.langcode');
																																																																																																																														
			
			
			
			
			

											$filterval = $this->getState('filter.langcode');
				if ($filterval)
				{
											$query->where("a.langcode = '" . $db->escape($filterval) . "'");
									}
			
			
			
							// Filter by search in title.
				$search = $this->getState('filter.search');
				if (!empty($search)) {
					if (stripos($search, 'id:') === 0) {
						$query->where('a.id = '.(int) substr($search, 3));
					}
					else {
						$search = $db->Quote('%'.$db->escape($search, true).'%');
						$query->where('(a.title LIKE '.$search.' OR a.alias LIKE '.$search.')');
					}
				}
			
			
			
				// Add the list ordering clause.
							$orderCol	= $this->state->get('list.ordering', 'a.title');
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
