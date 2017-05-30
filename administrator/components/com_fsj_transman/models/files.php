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
	
			
	
	
		
if (file_exists(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmlangname.php'))
{
	require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmlangname.php');
} else if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmlangname.php'))
{
	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmlangname.php');
}

			
	
	
		
if (file_exists(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmfilename.php'))
{
	require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmfilename.php');
} else if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmfilename.php'))
{
	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmfilename.php');
}

			
	
	
		
if (file_exists(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmstate.php'))
{
	require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmstate.php');
} else if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmstate.php'))
{
	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmstate.php');
}

			
	
	
		
if (file_exists(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmstatus.php'))
{
	require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmstatus.php');
} else if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmstatus.php'))
{
	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmstatus.php');
}

			
	
	
				
	
	
		
if (file_exists(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmcat.php'))
{
	require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmcat.php');
} else if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmcat.php'))
{
	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmcat.php');
}

			
	
	
		
if (file_exists(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmpath.php'))
{
	require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmpath.php');
} else if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmpath.php'))
{
	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmpath.php');
}

			
	
	
		
if (file_exists(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmdesc.php'))
{
	require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmdesc.php');
} else if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmdesc.php'))
{
	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmdesc.php');
}

			
	
	
		
if (file_exists(JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmdownloadfile.php'))
{
	require_once (JPATH_LIBRARIES.DS.'fsj_core'.DS.'html'.DS.'field'.DS.'fsjtmdownloadfile.php');
} else if (file_exists(JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmdownloadfile.php'))
{
	require_once (JPATH_ADMINISTRATOR.DS.'components'.DS.$option.DS.'models'.DS.'fields'.DS.'fsjtmdownloadfile.php');
}

	
class fsj_transmanModelfiles extends JModelList
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
						'client_id', 'a.client_id',
	
						'f_state', 'a.f_state',
	
						'f_status', 'a.f_status',
	
						'lf4',
	
						'name', 'a.name',
	
						'filename', 'a.filename',
	
						'tstate', 'a.tstate',
	
						'status', 'a.status',
	
						'phrases', 'a.phrases',
	
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



		$filterval = $this->getUserStateFromRequest($this->context.'.filter.xpath', 'filter_xpath', null, 'none', false);
		$this->setState('filter.xpath', $filterval);
		$filterval = $this->getUserStateFromRequest($this->context.'.filter.element', 'filter_element', null, 'none', false);
		$this->setState('filter.element', $filterval);
		$filterval = $this->getUserStateFromRequest($this->context.'.filter.category', 'filter_category', null, 'none', false);
		$this->setState('filter.category', $filterval);
		$filterval = $this->getUserStateFromRequest($this->context.'.filter.f_state', 'filter_f_state', null, 'none', false);
		$this->setState('filter.f_state', $filterval);
		$filterval = $this->getUserStateFromRequest($this->context.'.filter.f_status', 'filter_f_status', null, 'none', false);
		$this->setState('filter.f_status', $filterval);

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
		$id	.= ':'.$this->getState('filter.xpath');
		$id	.= ':'.$this->getState('filter.element');
		$id	.= ':'.$this->getState('filter.category');
		$id	.= ':'.$this->getState('filter.f_state');
		$id	.= ':'.$this->getState('filter.f_status');

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
					
      
        return "";
      
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
					
      
        $this->loadFiles();
        return $this->files;
      
    			}
	
	public function getTotal()
	{
		
      
        $this->loadFiles();
        return FSJ_TM_File_Helper::getCount();
      
    	}

	
      
        function loadFiles() {
          require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'helpers'.DS.'file_helper.php');
          if (isset($this->files))
            return true;
          FSJ_TM_File_Helper::SetModel($this);
          $this->files = FSJ_TM_File_Helper::GetFiles();
        }
      
    
}
