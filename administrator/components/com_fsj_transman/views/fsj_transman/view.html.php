<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
jimport('joomla.application.component.view' );
jimport('fsj_core.admin.adminhelper');
jimport('fsj_core.lib.utils.plugin_handler');
class fsj_transmanViewfsj_transman extends JViewLegacy
{
    function display($tpl = null)
    {	
		if (!FSJ_Helper::IsJ3())
		{
			jimport('fsj_core.lib.j25.sidebar');
			jimport('fsj_core.lib.j25.layout');
			jimport('fsj_core.lib.j25.layout_base');
			jimport('fsj_core.lib.j25.layout_file');
		}
		fsj_ToolbarsHelper::addSubmenu(JRequest::getCmd('view', 'fsj_transman'), 'JHtmlSidebar');
		$this->sidebar = JHtmlSidebar::render();
		$icon = 'com_fsj_transman';
		$icon_class = 'icon-48-'.preg_replace('#\.[^.]*$#', '', $icon);
		$css = ".$icon_class { background-image: url(../administrator/components/com_fsj_transman/assets/images/$icon-48.png); }";
		$document = JFactory::getDocument();
		$document->addStyleDeclaration($css);
		JToolBarHelper::title( JText::_('com_fsj_transman') , $icon );
		$mainframe = JFactory::getApplication();
		$default = str_replace("com_fsj_","",JRequest::getVar('option'));
		if (JFactory::getUser()->authorise('core.admin', 'com_fsj_transman'))
		{
			$bar = JToolBar::getInstance('toolbar');
			$bar->appendButton('Link', 'options', "FSJ_ADMIN_COMPONENT_SETTINGS", 'index.php?option=com_fsj_main&admin_com='.$default.'&view=settings');
		}
		JToolbarHelper::help('JHELP_CONTENT_ARTICLE_MANAGER');
		$this->xml = simplexml_load_file(JPATH_ROOT.DS."administrator".DS."components".DS."com_fsj_transman".DS.$default.".xml");
		$this->global_xml = $this->getXML();
		$this->components = $this->getComponents();
		$this->templates = FSJ_AdminHelper::GetTemplates();
        parent::display($tpl);
    }
	function getComponents()
	{
		// get a list of installed freestyle components
		$db	= JFactory::getDBO();
		$qry = "SELECT * FROM #__extensions WHERE element LIKE '%com_fsj_%' AND type = 'component' AND element != 'com_fsj_main' ORDER BY name";
		$db->setQuery($qry);
		$items = $db->loadObjectList();
		foreach ($items as $item)
		{
			FSJ_Lang_Helper::Load_Component($item->element);
			$item->displayname = $item->name . "_menu";
			if (strtolower($item->name) == "com_fss") $item->displayname = "Support Portal";
			if (strtolower($item->name) == "com_fsf") $item->displayname = "FAQs 1.x Lite";
			if (strtolower($item->name) == "com_fst") $item->displayname = "Testimonials 1.x Lite";
		}
		usort($items, array("fsj_transmanViewfsj_transman", "compSort"));
		return $items;
	}
	static function compSort($a, $b)
	{
		return strcmp(JText::_($a->displayname), JText::_($b->displayname));
	}
	function getXML()
	{
		$filename = JPATH_ADMINISTRATOR . DS . "components" . DS . "com_fsj_main" . DS . "main.xml";
		return simplexml_load_file($filename);		
	}
}
