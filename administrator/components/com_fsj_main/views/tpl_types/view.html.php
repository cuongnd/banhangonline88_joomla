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

class fsj_mainViewtpl_types extends JViewLegacy
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




		if (!JFactory::getUser()->authorise('core.manager', 'com_fsj_main')) {
			return JError::raiseWarning(404, JText::_('JERROR_ALERTNOAUTHOR'));
		}

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
	
		$icon = 'plugins';
		$icon_class = 'icon-48-'.preg_replace('#\.[^.]*$#', '', $icon);
		$css = ".$icon_class { background-image: url(../administrator/components/com_fsj_main/assets/images/{$icon}-48.png); }\n";
		$document = JFactory::getDocument();
		$document->addStyleDeclaration($css);

		if (JRequest::getVar('option') == "com_fsj_main")
		{
			JToolBarHelper::title(JText::_('COM_FSJ_'.$admin_com.'_SHORT' ). ": " . JText::_('COM_fsj_main_ITEMS_tpl_typeS' ), $icon);
		} else {
			JToolBarHelper::title(JText::_(JRequest::getVar('option').'_SHORT' ). ": " . JText::_('COM_fsj_main_ITEMS_tpl_typeS' ), $icon);
		}




		if ($canDo->get('core.edit.state')) {

		}

		if ($this->state->get('filter.published') == -2 && $canDo->get('core.delete')) {
			JToolBarHelper::deleteList('', 'tpl_types.delete', 'JTOOLBAR_EMPTY_TRASH');
			JToolBarHelper::divider();
		} elseif ($canDo->get('core.edit.state')) {
		}


	}
	
	function setupOrdering()
	{
		$orders = array();
		
																								
											$orders['a.component ASC'] = JText::_('FSJ_MAIN_FORM_TPL_TYPE_COMPONENT') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['a.component DESC'] = JText::_('FSJ_MAIN_FORM_TPL_TYPE_COMPONENT') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																																																																																						
											$orders['a.type ASC'] = JText::_('FSJ_MAIN_FORM_TPL_TYPE_TYPE') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['a.type DESC'] = JText::_('FSJ_MAIN_FORM_TPL_TYPE_TYPE') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																																																			$orders['a.title ASC'] = JText::_('Template Set') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
				$orders['a.title DESC'] = JText::_('Template Set') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																																																																			
											$orders['a.count_templates ASC'] = JText::_('FSJ_MAIN_FORM_TPL_TYPE_COUNT_TEMPLATES') . " " . JText::_("JGLOBAL_ORDER_ASCENDING");
						$orders['a.count_templates DESC'] = JText::_('FSJ_MAIN_FORM_TPL_TYPE_COUNT_TEMPLATES') . " " . JText::_("JGLOBAL_ORDER_DESCENDING");
																
		$this->orderings = $orders;	
	}
}
