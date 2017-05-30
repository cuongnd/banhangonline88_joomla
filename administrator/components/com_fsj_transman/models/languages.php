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

		
	
	
			
	
	
		
if (file_exists(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmlangname.php'))
{
	require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmlangname.php');
} else if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmlangname.php'))
{
	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmlangname.php');
}

			
	
	
			
	
	
		
if (file_exists(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmfiles.php'))
{
	require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmfiles.php');
} else if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmfiles.php'))
{
	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmfiles.php');
}

			
	
	
		
if (file_exists(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmpath.php'))
{
	require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmpath.php');
} else if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmpath.php'))
{
	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmpath.php');
}

		
class fsj_transmanModellanguages extends JModelList
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
				'extension_id', 'a.extension_id',
						'client_id', 'a.client_id',
	
						'name', 'a.name',
	
						'element', 'a.element',
	
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



		$filterval = $this->getUserStateFromRequest($this->context.'.filter.element', 'filter_element', null, 'none', false);
		$this->setState('filter.element', $filterval);
		$filterval = $this->getUserStateFromRequest($this->context.'.filter.xpath', 'filter_xpath', null, 'none', false);
		$this->setState('filter.xpath', $filterval);

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
		//$id	.= ':'.$this->getState('filter.category_id');
		$id	.= ':'.$this->getState('filter.element');
		$id	.= ':'.$this->getState('filter.xpath');

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
					'a.extension_id, a.client_id as client_id, a.name as name, a.element as element'
				)
			);

			$query->from('#__extensions AS a');

			
			
			
																																													
			
			
			
			
			

											$filterval = $this->getState('filter.element');
				if ($filterval)
				{
											$query->where("a.element = '" . $db->escape($filterval) . "'");
									}
							
			
			
			
			
							$query->where('type = "language"');
			
				// Add the list ordering clause.
							$orderCol	= $this->state->get('list.ordering', 'a.extension_id');
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
        $langs = array();
        foreach ($items as $item)
        {
            $item->component = "general";
            $item->is_core = 1;
            $item->prefix = 'g';
            if ($item->client_id == 0)
            {
                $item->path = 'language';
            } else if ($item->client_id == 1)
            {
                $item->path = 'administrator'.DS.'language';
            }
            $langs[$item->element] = $item->name;
        }
        require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'helpers'.DS.'folder_helper.php');
        $folder_extra = FSJ_TM_Folder_Helper::ScanForComponentLanguages();
        foreach ($folder_extra as $folder)
        {
            foreach ($langs as $code => $name)
            {
                $new = new stdClass();
                $new->path = $folder->path;
                $new->name = $name;
                $new->element = $code;
                $new->component = $folder->component;
                $new->client_id = $folder->admin == 1 ? 1 : 0;
                $new->prefix = $folder->prefix;
                $new->extension_id = abs(crc32(serialize($new)));
                $new->is_core = 0;
                $items[] = $new;
            }
        }
        foreach ($items as $item)
        {
            $item->sortkey = "{$item->prefix}|{$item->client_id}|{$item->component}";
            if ($item->prefix == "g")
              $item->sortkey = "a|{$item->client_id}";
        }
        // need to filter and sort the items here!
        $filterval = $this->getState('filter.xpath');
        if ($filterval != "")
        {
            foreach ($items as $offset => $item)
            {
                $itemkey = $item->client_id . "|" . $item->prefix . "." . $item->component;
                if ($filterval != $itemkey)
                    unset($items[$offset]);
            }
        }
        $filterval = $this->getState('filter.search');
        if ($filterval)
        {
            foreach ($items as $offset => $item)
            {
                if (stripos($item->component, $filterval) === FALSE)
                    unset($items[$offset]);
            }
        }  
        /*$filterval = $this->getState('filter.component');
        if ($filterval)
        {
            foreach ($items as $offset => $item)
            {
                if ($filterval != $item->component)
                    unset($items[$offset]);
            }
        }  
        $filterval = $this->getState('filter.prefix');
        if ($filterval)
        {
            foreach ($items as $offset => $item)
            {
                if ($filterval != substr($item->prefix,0,1))
                    unset($items[$offset]);
            }
        }  */
        $orderCol	= $this->state->get('list.ordering', 'a.sortkey');
		    $orderDirn	= $this->state->get('list.direction', 'asc');
        $orderCol = str_replace("a.", "", $orderCol);
        $sort = array();
        $sort_layer = new stdClass();
        $sort_layer->field = $orderCol;
        $sort_layer->dir = $orderDirn;
        $sort[] = $sort_layer;
        $sort_layer = new stdClass();
        $sort_layer->field = 'sortkey';
        $sort_layer->dir = 'asc';
        $sort[] = $sort_layer;
        $sort_layer = new stdClass();
        $sort_layer->field = 'name';
        $sort_layer->dir = 'asc';
        $sort[] = $sort_layer;
        FSJ_Helper::ArrayObjSortMulti($items, $sort);
		    return $items;
    
    			}
	


}
