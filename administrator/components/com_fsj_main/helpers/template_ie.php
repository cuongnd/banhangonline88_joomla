<?php
/**
 * @package Freestyle Joomla
 * @author Freestyle Joomla
 * @copyright (C) 2013 Freestyle Joomla
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
**/
defined('_JEXEC') or die;

class FSJ_Template_IE {
	static function Export()
	{
		$id = JRequest::getInt('id');
		
		$db = JFactory::getDBO();
		
		$qry = "SELECT * FROM #__fsj_tpl_template WHERE id = " . $id;
		$db->setQuery($qry);
		$template = $db->loadObject();
		$template->params = json_decode($template->params);

		$filename = "{$template->component}.{$template->type}.{$template->name}.xml";

		header('Content-type: text/xml');
		header('Content-Disposition: attachment; filename="'.$filename.'"');
		
		require_once(JPATH_ADMINISTRATOR.DS.'components'.DS.'com_fsj_main'.DS.'tmpl'.DS.'template'.DS.'export.xml');		
		
		exit;
	}
}