<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;
jimport( 'joomla.application.component.view' );
class fsj_mainViewfsj_main extends JViewLegacy
{
    function display($tpl = null)
    {
		if (FSJ_Helper::IsJ3())
		{
			fsj_ToolbarsHelper::addSubmenu(JRequest::getCmd('view', 'fsj_main'));
		}
		$css = ".icon-48-freestyle { background-image: url(../administrator/components/com_fsj_main/assets/images/freestyle-48.png); }";
		$document = JFactory::getDocument();
		$document->addStyleDeclaration($css);
		JToolBarHelper::title( JText::_('FSJ_M_HEADER'), 'freestyle' );
		$this->document->setTitle(JText::_('FSJ_M_HEADER'));
		$this->components = $this->getComponents();
		$this->global_xml = $this->getXML();
        parent::display($tpl);  
    }
	static function getComponents()
	{
		// get a list of installed freestyle components
		$db	= JFactory::getDBO();
		$qry = "SELECT * FROM #__extensions WHERE (element LIKE '%com_fsj_%' OR element LIKE '%com_fss%' OR element LIKE '%com_fst%' OR element LIKE '%com_fsf%') AND type = 'component' AND element != 'com_fsj_main' ORDER BY name";
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
		usort($items, array("fsj_ToolbarsHelper", "compSort"));
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
