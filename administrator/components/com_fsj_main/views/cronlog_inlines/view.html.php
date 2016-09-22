<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

jimport( 'joomla.application.component.view' );
jimport('joomla.utilities.date');

class fsj_mainViewcronlog_inlines extends JViewLegacy
{
	protected $items;
	protected $pagination;
	protected $state;

	/**
	 * Display the view
	 *
	 * @return	void
	 */
	public function display($tpl = null)
	{	
		// if Joomla 2.5
		if (!FSJ_Helper::IsJ3())
			JHTML::addIncludePath(JPATH_ROOT.DS.'libraries'.DS.'fsj_core'.DS.'html'.DS.'html'.DS.'joomla25');
		
		// add stylesheets and css for the search bar at the top
		FSJ_Page::Script('libraries/fsj_core/assets/js/form/form.searchtools.js');
		FSJ_Page::Style('administrator/components/com_fsj_main/assets/css/fsj_main.less');

		$doc = JFactory::getDocument();
		$script = "
			function fsj_init_search_tools()
			{
				try {
					jQuery('#adminForm').searchtools();
				} catch (e) { }
			}
			
			jQuery(document).ready(function() {
				setTimeout('fsj_init_search_tools()', 500);
			});
		";
		$doc->addScriptDeclaration($script);

		FSJ_Page::Script('libraries/fsj_core/assets/js/form/form.iframe.child.js');
		FSJ_Page::Script('libraries/fsj_core/assets/js/fsj/fsj.utils.js');
				
		JRequest::setVar('tmpl','component');
		JRequest::setVar('mode','inline');




		$this->items		= $this->get('Items');
		$this->pagination	= $this->get('Pagination');
		$this->state		= $this->get('State');

		// Check for errors.
		if (count($errors = $this->get('Errors'))) {
			JError::raiseError(500, implode("\n", $errors));
			return false;
		}


		$this->loadFilters();

		$this->setupOrdering();

		// We don't need toolbar in the modal window.
		if ($this->getLayout() !== 'modal') {
			$this->addToolbar();
		}

		if (FSJ_Helper::IsJ3())
		{
			$option = str_replace("com_","",JRequest::getVar('option'));
			if (JRequest::getVar('tmpl') != 'component')
				fsj_ToolbarsHelper::addSubmenu(JRequest::getCmd('view', $option), 'JHtmlSidebar');
				
			$this->sidebar = JHtmlSidebar::render();
		} else {
			$option = str_replace("com_","",JRequest::getVar('option'));
			
			jimport('fsj_core.lib.j25.sidebar');
			jimport('fsj_core.lib.j25.layout');
			jimport('fsj_core.lib.j25.layout_base');
			jimport('fsj_core.lib.j25.layout_file');

			fsj_ToolbarsHelper::addSubmenu(JRequest::getCmd('view', $option), 'JHtmlSidebar');
			
			$this->sidebar = JHtmlSidebar::render();
		}
			
		parent::display($tpl);
		
	}

	protected function loadFilters()
	{
		$db = JFactory::getDBO();
		$this->filters = array();
		
	
		$filter = array();
		$filter[] = JHTML::_('select.option', '', '- ' . JText::_('FSJ_MAIN_FORM_MAIN_CRONLOG__FILTER_SUCCESS') . ' -', 'id', 'title');
		
		
		
					
			
          
 		        $filter[] = JHTML::_('select.option', '1', JText::_('Success'), 'id', 'title');
		        $filter[] = JHTML::_('select.option', '-1', JText::_('Fail'), 'id', 'title');
         
        	
			
		
		$this->filters['success'] = JHTML::_('select.genericlist',  $filter, 'filter_success', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'title', $this->state->get('filter.success'));

	
		$filter = array();
		$filter[] = JHTML::_('select.option', '', '- ' . JText::_('FSJ_MAIN_FORM_MAIN_CRONLOG__FILTER_SOURCE') . ' -', 'id', 'title');
		
		
		
					
			
          
            $query = 'SELECT source, source_id, event FROM #__fsj_main_cron WHERE 1 ';
		        $sort = ' ORDER BY event';
				    $query .= $sort;
		        $db->setQuery($query);
            $data = $db->loadObjectList();
		        foreach ($data as $item)
            {
                $obj = new stdClass();
                $obj->id = $item->source."-".$item->source_id;
                $obj->title = $item->event;
                $filter[] = $obj;
            }
          
        	
			
		
		$this->filters['source'] = JHTML::_('select.genericlist',  $filter, 'filter_source', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'title', $this->state->get('filter.source'));

	
		$filter = array();
		$filter[] = JHTML::_('select.option', '', '- ' . JText::_('FSJ_MAIN_FORM_MAIN_CRONLOG__FILTER_WHENDATE') . ' -', 'id', 'title');
		
		
		
					
			
          
            $query = 'SELECT whendate as id, whendate as title FROM #__fsj_main_cronlog WHERE 1 ';
		        $sort = ' GROUP BY whendate ORDER BY whendate ';
				    $query .= $sort;
		        $db->setQuery($query);
 		        $filter = array_merge($filter, $db->loadObjectList());
          
        	
			
		
		$this->filters['whendate'] = JHTML::_('select.genericlist',  $filter, 'filter_whendate', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'title', $this->state->get('filter.whendate'));

	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$canDo	= fsj_mainHelper::getActions($this->state->get('filter.category_id'));
		$user		= JFactory::getUser();
		
		$mainframe = JFactory::getApplication();
		$default = str_replace("com_fsj_","",JRequest::getVar('option'));
		if ($default == "main")
		{
			$admin_com = $mainframe->getUserState( "com_fsj_main.admin_com", $default );
			
			$lang = JFactory::getLanguage();
			$lang->load("com_fsj_" . $admin_com);

		} else {
			$admin_com = $default;
		}
	
		$icon = 'main_cronlog';
		$icon_class = 'icon-48-'.preg_replace('#\.[^.]*$#', '', $icon);
		$css = ".$icon_class { background-image: url(../administrator/components/com_fsj_main/assets/images/{$icon}-48.png); }\n";
		$document = JFactory::getDocument();
		$document->addStyleDeclaration($css);

		if (JRequest::getVar('option') == "com_fsj_main")
		{
			JToolBarHelper::title(JText::_('COM_FSJ_'.$admin_com.'_SHORT' ). ": " . JText::_('COM_fsj_main_ITEMS_main_cronlogS' ), $icon);
		} else {
			JToolBarHelper::title(JText::_(JRequest::getVar('option').'_SHORT' ). ": " . JText::_('COM_fsj_main_ITEMS_main_cronlogS' ), $icon);
		}




		if ($canDo->get('core.edit.state')) {

		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'cronlog_inlines.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} elseif ($canDo->get('core.edit.state')) {
		}


	}
	
	function setupOrdering()
	{
		$orders = array();
		
																																												
											$orders['a.event ASC'] = JText::_('FSJ_MAIN_FORM_MAIN_CRONLOG_EVENT') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['a.event DESC'] = JText::_('FSJ_MAIN_FORM_MAIN_CRONLOG_EVENT') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																																																																																																																				
											$orders['a.whentime ASC'] = JText::_('FSJ_MAIN_FORM_MAIN_CRONLOG_WHENTIME') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['a.whentime DESC'] = JText::_('FSJ_MAIN_FORM_MAIN_CRONLOG_WHENTIME') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																																																																																																																														
											$orders['a.success ASC'] = JText::_('FSJ_MAIN_FORM_MAIN_CRONLOG_SUCCESS') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['a.success DESC'] = JText::_('FSJ_MAIN_FORM_MAIN_CRONLOG_SUCCESS') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																																																																																																																				
											$orders['a.result ASC'] = JText::_('FSJ_MAIN_FORM_MAIN_CRONLOG_RESULT') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['a.result DESC'] = JText::_('FSJ_MAIN_FORM_MAIN_CRONLOG_RESULT') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																																																																																																																				
											$orders['a.log ASC'] = JText::_('FSJ_MAIN_FORM_MAIN_CRONLOG_LOG') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['a.log DESC'] = JText::_('FSJ_MAIN_FORM_MAIN_CRONLOG_LOG') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																
		$this->orderings = $orders;	
	}
}
