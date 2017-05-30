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

class fsj_transmanViewfiles extends JViewLegacy
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
		FSJ_Page::Style('administrator/components/com_fsj_transman/assets/css/fsj_transman.less');

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
		$filter[] = JHTML::_('select.option', '', '- ' . JText::_('FSJ_TRANSMAN_FORM_TRANSMAN_FILE__FILTER_XPATH') . ' -', 'id', 'title');
		
		
		
					
			
          
            $filter[] = JHTML::_('select.option', '0|g.general', JText::_('FSJ_TM_SITE'), 'id', 'title');
            $filter[] = JHTML::_('select.option', '1|g.general', JText::_('FSJ_TM_ADMIN'), 'id', 'title');
            require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_transman'.DS.'helpers'.DS.'folder_helper.php');
            $folder_extra = FSJ_TM_Folder_Helper::ScanForComponentLanguages();
            foreach ($folder_extra as $folder)
            {
                $admin = (int)$folder->admin;
                $key = "{$admin}|{$folder->prefix}.{$folder->component}";
                $display = FSJ_TM_Folder_Helper::describePath($admin, $folder->prefix, $folder->component);
                $filter[] = JHTML::_('select.option', $key, $display, 'id', 'title');
            }   
         
        	
			
		
		$this->filters['xpath'] = JHTML::_('select.genericlist',  $filter, 'filter_xpath', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'title', $this->state->get('filter.xpath'));

	
		$filter = array();
		$filter[] = JHTML::_('select.option', '', '- ' . JText::_('FSJ_TRANSMAN_FORM_TRANSMAN_FILE__FILTER_ELEMENT') . ' -', 'id', 'title');
		
		
		
					
			$query = 'SELECT name as title, element as id FROM #__extensions WHERE type = "language" GROUP BY element';
			$sort = '';
			
						
			$query .= $sort;
			$db->setQuery($query);
			//echo "Filter Qry : $query<br>";
			$filter = array_merge($filter, $db->loadObjectList());
			
						
					
		$this->filters['element'] = JHTML::_('select.genericlist',  $filter, 'filter_element', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'title', $this->state->get('filter.element'));

	
		$filter = array();
		$filter[] = JHTML::_('select.option', '', '- ' . JText::_('FSJ_TRANSMAN_FORM_TRANSMAN_FILE__FILTER_CATEGORY') . ' -', 'id', 'title');
		
		
		
					
			
          
          // Code for category dropdown
          $filter[] = JHTML::_('select.option', '--none--', 'No Category', 'id', 'title');
          foreach (FSJ_TM_File_Helper::$catlist as $cat)
          {
              $filter[] = JHTML::_('select.option', $cat, $cat, 'id', 'title');
          }       
          
        	
			
		
		$this->filters['category'] = JHTML::_('select.genericlist',  $filter, 'filter_category', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'title', $this->state->get('filter.category'));

	
		$filter = array();
		$filter[] = JHTML::_('select.option', '', '- ' . JText::_('FSJ_TRANSMAN_FORM_TRANSMAN_FILE__FILTER_F_STATE') . ' -', 'id', 'title');
		
									$filter[] = JHTML::_('select.option', '0', JText::_('FSJ_TM_STATE_NOT_STARTED'), 'id', 'title');
							$filter[] = JHTML::_('select.option', '1', JText::_('FSJ_TM_STATE_PUB'), 'id', 'title');
							$filter[] = JHTML::_('select.option', '3', JText::_('FSJ_TM_STATE_UNPUB'), 'id', 'title');
							$filter[] = JHTML::_('select.option', '2', JText::_('FSJ_TM_STATE_NOT_IN_BASE'), 'id', 'title');
					
		
		
		$this->filters['f_state'] = JHTML::_('select.genericlist',  $filter, 'filter_f_state', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'title', $this->state->get('filter.f_state'));

	
		$filter = array();
		$filter[] = JHTML::_('select.option', '', '- ' . JText::_('FSJ_TRANSMAN_FORM_TRANSMAN_FILE__FILTER_F_STATUS') . ' -', 'id', 'title');
		
									$filter[] = JHTML::_('select.option', '0', JText::_('FSJ_TM_STATE_NOT_STARTED'), 'id', 'title');
							$filter[] = JHTML::_('select.option', '1', JText::_('FSJ_TM_INPROGRESS'), 'id', 'title');
							$filter[] = JHTML::_('select.option', '100', JText::_('FSJ_TM_COMPLETED'), 'id', 'title');
							$filter[] = JHTML::_('select.option', '99', JText::_('FSJ_TM_INCOMPLETE'), 'id', 'title');
					
		
		
		$this->filters['f_status'] = JHTML::_('select.genericlist',  $filter, 'filter_f_status', 'class="inputbox" size="1" onchange="document.adminForm.submit( );"', 'id', 'title', $this->state->get('filter.f_status'));

	}

	/**
	 * Add the page title and toolbar.
	 *
	 * @since	1.6
	 */
	protected function addToolbar()
	{
		$canDo	= fsj_transmanHelper::getActions($this->state->get('filter.category_id'));
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
	
		$icon = 'file';
		$icon_class = 'icon-48-'.preg_replace('#\.[^.]*$#', '', $icon);
		$css = ".$icon_class { background-image: url(../administrator/components/com_fsj_transman/assets/images/{$icon}-48.png); }\n";
		$document = JFactory::getDocument();
		$document->addStyleDeclaration($css);

		if (JRequest::getVar('option') == "com_fsj_main")
		{
			JToolBarHelper::title(JText::_('COM_FSJ_'.$admin_com.'_SHORT' ). ": " . JText::_('COM_fsj_transman_ITEMS_transman_fileS' ), $icon);
		} else {
			JToolBarHelper::title(JText::_(JRequest::getVar('option').'_SHORT' ). ": " . JText::_('COM_fsj_transman_ITEMS_transman_fileS' ), $icon);
		}

		// add any custom buttons
		if (!FSJ_Helper::IsJ3())
		{
							$icon = 'publish';
				$icon_class = 'icon-32-'.preg_replace('#\.[^.]*$#', '', $icon);
				$css = ".$icon_class { background-image: url(../administrator/components/com_fsj_transman/assets/images/{$icon}-32.png); }\n";
				$document->addStyleDeclaration($css);
				JToolBarHelper::custom('files.pub', 'publish', 'publish', 'FSJ_TM_PUBLISH', true);
							$icon = 'unpublish';
				$icon_class = 'icon-32-'.preg_replace('#\.[^.]*$#', '', $icon);
				$css = ".$icon_class { background-image: url(../administrator/components/com_fsj_transman/assets/images/{$icon}-32.png); }\n";
				$document->addStyleDeclaration($css);
				JToolBarHelper::custom('files.unpub', 'unpublish', 'unpublish', 'FSJ_TM_UNPUBLISH', true);
							$icon = 'arrow-up';
				$icon_class = 'icon-32-'.preg_replace('#\.[^.]*$#', '', $icon);
				$css = ".$icon_class { background-image: url(../administrator/components/com_fsj_transman/assets/images/{$icon}-32.png); }\n";
				$document->addStyleDeclaration($css);
				JToolBarHelper::custom('transman.languages', 'arrow-up', 'arrow-up', 'FSJ_TM_LANGUAGES', false);
							$icon = 'folder';
				$icon_class = 'icon-32-'.preg_replace('#\.[^.]*$#', '', $icon);
				$css = ".$icon_class { background-image: url(../administrator/components/com_fsj_transman/assets/images/{$icon}-32.png); }\n";
				$document->addStyleDeclaration($css);
				JToolBarHelper::custom('transman.update_category', 'folder', 'folder', 'FSJ_TM_UPDATE_CATEGORY', false);
							$icon = 'warning';
				$icon_class = 'icon-32-'.preg_replace('#\.[^.]*$#', '', $icon);
				$css = ".$icon_class { background-image: url(../administrator/components/com_fsj_transman/assets/images/{$icon}-32.png); }\n";
				$document->addStyleDeclaration($css);
				JToolBarHelper::custom('files.createbase', 'warning', 'warning', 'FSJ_TM_CREATE_BASE', true);
						JToolBarHelper::divider();
		} elseif (FSJ_Helper::IsJ3())
		{
							JToolBarHelper::custom('files.pub', 'publish', 'publish', 'FSJ_TM_PUBLISH', true);
							JToolBarHelper::custom('files.unpub', 'unpublish', 'unpublish', 'FSJ_TM_UNPUBLISH', true);
							JToolBarHelper::custom('transman.languages', 'arrow-up', 'arrow-up', 'FSJ_TM_LANGUAGES', false);
							JToolBarHelper::custom('transman.update_category', 'folder', 'folder', 'FSJ_TM_UPDATE_CATEGORY', false);
							JToolBarHelper::custom('files.createbase', 'warning', 'warning', 'FSJ_TM_CREATE_BASE', true);
				}	


		if (($canDo->get('core.edit')) || ($canDo->get('core.edit.own'))) {
			JToolBarHelper::editList('file.edit');
		}

		if ($canDo->get('core.edit.state')) {

		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'files.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} elseif ($canDo->get('core.edit.state')) {
		}

		if ($canDo->get('core.admin') && JRequest::getVar('tmpl') == "" && !FSJ_Helper::IsJ3()) {
			JToolBarHelper::divider();
			$bar = JToolBar::getInstance('toolbar');
			$bar->appendButton('Popup', 'options', "FSJ_ADMIN_COMPONENT_SETTINGS", 'index.php?option=com_fsj_main&admin_com='.$admin_com.'&view=settings&tmpl=component', 875, 550, 0, 0, '');
		}

	}
	
	function setupOrdering()
	{
		$orders = array();
		
																																																																										
											$orders['a.filename ASC'] = JText::_('FSJ_TRANSMAN_FORM_TRANSMAN_FILE_FILENAME') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['a.filename DESC'] = JText::_('FSJ_TRANSMAN_FORM_TRANSMAN_FILE_FILENAME') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																																																																																																																																																																																																																																												
											$orders['a.description ASC'] = JText::_('FSJ_TRANSMAN_FORM_TRANSMAN_FILE_DESCRIPTION') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['a.description DESC'] = JText::_('FSJ_TRANSMAN_FORM_TRANSMAN_FILE_DESCRIPTION') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																																																																																																																																																		
											$orders['a.category ASC'] = JText::_('FSJ_TRANSMAN_FORM_TRANSMAN_FILE_CATEGORY') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['a.category DESC'] = JText::_('FSJ_TRANSMAN_FORM_TRANSMAN_FILE_CATEGORY') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																																																																																																																														
											$orders['a.tstate ASC'] = JText::_('FSJ_TRANSMAN_FORM_TRANSMAN_FILE_TSTATE') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['a.tstate DESC'] = JText::_('FSJ_TRANSMAN_FORM_TRANSMAN_FILE_TSTATE') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																																																																																																																																																																																
											$orders['a.status ASC'] = JText::_('FSJ_TRANSMAN_FORM_TRANSMAN_FILE_STATUS') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['a.status DESC'] = JText::_('FSJ_TRANSMAN_FORM_TRANSMAN_FILE_STATUS') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																																																																																																																																																																																
											$orders['a.phrases ASC'] = JText::_('FSJ_TRANSMAN_FORM_TRANSMAN_FILE_PHRASES') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['a.phrases DESC'] = JText::_('FSJ_TRANSMAN_FORM_TRANSMAN_FILE_PHRASES') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																																																																																																																																																																																																																								
											$orders['a.download ASC'] = JText::_('FSJ_TRANSMAN_FORM_TRANSMAN_FILE_DOWNLOAD') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['a.download DESC'] = JText::_('FSJ_TRANSMAN_FORM_TRANSMAN_FILE_DOWNLOAD') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																
		$this->orderings = $orders;	
	}
}
