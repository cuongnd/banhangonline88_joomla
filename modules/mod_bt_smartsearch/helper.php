<?php
/**
 * @package 	BT Smart Search
 * @version	1.0
 * @created	Dec 2012
 * @author	BowThemes
 * @email	support@bowthemes.com
 * @website	http://bowthemes.com
 * @support     Forum - http://bowthemes.com/forum/
 * @copyright   Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license     http://bowthemes.com/terms-and-conditions.html
 *
 */
JLoader::register('FinderHelperLanguage', JPATH_ADMINISTRATOR . '/components/com_finder/helpers/language.php');
FinderHelperLanguage::loadPluginLanguage();
JFactory::getLanguage()->load($module->module,JPATH_SITE,null,true);

defined('_JEXEC') or die('Restricted Access');
class ModBtSmartsearchHelper
{
	public static function fetchHead($params) {
		
		$checkJqueryLoaded = false;
		$checkChosen = false;
        $document = &JFactory::getDocument();
        $header = $document->getHeadData();
        foreach ($header['scripts'] as $scriptName => $scriptData) {
            if (substr_count($scriptName, '/jquery')) {
                $checkJqueryLoaded = true;
            }
			 if (substr_count($scriptName, 'chosen')) {
                $checkChosen = true;
            }
        }
		if (!$checkJqueryLoaded) {
		$document->addScript(JURI::root() . 'modules/mod_bt_smartsearch/tmpl/js/jquery.min.js');		
		}
		if(!$checkChosen){
			$document->addScript(JURI::root() . 'modules/mod_bt_smartsearch/tmpl/js/chosen.jquery.min.js');	
		}		
		
		$document->addStyleSheet(JURI::root() . 'modules/mod_bt_smartsearch/tmpl/css/form.css');
		$document->addStyleSheet(JURI::root() . 'modules/mod_bt_smartsearch/tmpl/css/chosen.css');		
		 
	 }	 
	public static function Selectbox($selectid)
	{		
		$mainframe = &JFactory::getApplication();				
		$author = JRequest::getVar('t');		
		$db = &JFactory::getDBO();	
		if(Count($selectid)==0){		
		$query = "SELECT * FROM #__finder_taxonomy WHERE parent_id=1  AND state=1 ORDER BY ID DESC ";
		$db->setQuery($query);
		$rows = $db->loadObjectList();
		if ($db->getErrorNum()) {
			echo $db->stderr();
			return false;
		}			
		echo "<div class=\"btsmartsearch\">";
		echo "<div class=\"btsmartspace\">";
		foreach ($rows as $row){
		echo "<select  name=\"t[]\" class=\"inputboxsmart\">";		
			echo '<option class="option-results" value="">'.JText::_('MOD_BT_SMART_SEARCHBY').JText::_(FinderHelperLanguage::branchSingular($row->title)).'</option>';
			$string ="SELECT * FROM #__finder_taxonomy WHERE parent_id='$row->id' AND state=1 ORDER BY ID DESC ";
			$db->setQuery($string);
			$items = $db->loadObjectList();
			foreach ($items as $pro){
			$selected ='';
			if($author){
				foreach ($author as $au){
					if (($au == $pro->id)) {
					$selected = ' selected="selected"';
					break;
					} else {
					 $selected = '';
					}
				}
			}
				echo '<option class="option-results" value="'.$pro->id.'"'.$selected.'>'.$pro->title.'</option>';
			}		
		echo "</select>";
		}				
		echo"</div>";
		echo"</div>";
		}
		else{		
		echo "<div class=\"btsmartsearch\">";
		echo "<div class=\"btsmartspace\">";
			foreach ($selectid AS $key=>$value){
				echo "<select  name=\"t[]\" class=\"inputboxsmart\">";				
				echo '<option class="option-results" value="">'.JText::_('MOD_BT_SMART_SEARCHBY').JText::_(FinderHelperLanguage::branchSingular($key)).'</option>';
				foreach ($value As $title=>$id){
					$selected ='';
					if($author){
					foreach ($author as $au){
						if (($au == $id)) {
						$selected = ' selected="selected"';	
						break;
						} else {
						 $selected = '';
						}
					}
					}
					echo '<option class="option-results" value="'.$id.'"'.$selected.'>'.$title.'</option>';
				}
				echo "</select>";
			}
		echo"</div>";
		echo"</div>";
		}
				 
	}
	public static function getGetFields($route = null)
	{
		$fields = null;
		$uri = JURI::getInstance(JRoute::_($route));
		$uri->delVar('q');
		$needId = true;
		foreach ($uri->getQuery(true) as $n => $v)
		{
			$fields .= '<input type="hidden" name="' . $n . '" value="' . $v . '" />';
			if ($n == 'Itemid') {
				$needId = false;
			}
		}
		if ($needId) {
			$fields .= '<input type="hidden" name="Itemid" value="' . JFactory::getApplication()->input->get('Itemid', '0', 'int') . '" />';
		}
		return $fields;
	}
	public static function getQuery($params)
	{
		$app = JFactory::getApplication();
		$input = $app->input;
		$request = $input->request;
		$filter = JFilterInput::getInstance();
		$options = array();
		$options['filter'] = ($request->get('f', 0, 'int') != 0) ? $request->get('f', '', 'int') : $params->get('searchfilter');
		$options['filter'] = $filter->clean($options['filter'], 'int');
		$options['filters'] = $request->get('f', '', 'array');
		$options['filters'] = $filter->clean($options['filters'], 'array');
		JArrayHelper::toInteger($options['filters']);		
		$query = new FinderIndexerQuery($options);
		return $query;
	}

}


?>