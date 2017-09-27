<?php
/**
 * @package ZT Tabs module 
 * @author http://www.ZooTemplate.com
 * @copyright (C) 2014- ZooTemplate.Com
 * @license PHP files are GNU/GPL
**/
// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die( 'Restricted access' ); 
jimport('joomla.form.formfield');
class JFormFieldJvselection extends JFormField {

	var	$type = 'jvselection';

	function getInput(){
        $doc = JFactory::getDocument();
        $doc->addStyleSheet(JURI::root().'modules/mod_zt_tabs/admin/css/adminstyle.css');

        $JElementJvselection = new JElementJvselection();
        return $JElementJvselection->fetchElement($this->name, $this->value, $this->element, $this->options['control']);
	} 
} 
jimport('joomla.html.parameter.element');
class JElementJvselection
{ 
	var	$_name = 'jvselection';
	function fetchElement($name, $value, &$node, $control_name){
		$class = $node->attributes ( 'class' );
		if (! $class) {
			$class = "inputbox";
		}
		$db = JFactory::getDBO();
		$cId = JRequest::getVar('cid','');
		if($cId !='') $cId = $cId[0];
		if($cId == ''){
			$cId = JRequest::getVar('id');
		}
		$cId=(int)$cId;
		$sql = "SELECT params FROM #__modules WHERE id=$cId";
		$db->setQuery($sql);
		$db->setQuery($sql);
		$data = $db->loadResult();
        $params = json_decode($data) ;
        if(!is_null($params)){
            $params=get_object_vars($params);
            $jvType = isset($params['type']) ? $params['type'] : 'moduleID';

            $titlePos = isset($params['title_position']) ? $params['title_position'] : 'top';

        } else {
            $jvType='';
            $titlePos='';
        }
        $selection = array();
		$arySelection = array();	
		//Progress get module, content

		//Module
		$query = 'SELECT a.*'
		. ' FROM #__modules AS a'
		. ' WHERE a.published = 1'			//TODO: rem it if don't want to check
		. ' AND a.module <> \'mod_zt_tabs\''
		//. ' ORDER BY a.id'
		;
        // lay tat ca cac thong tin cua cac module duoc public khac module mod_zt_tabs
		$db->setQuery($query);
        // luu thông tin querry vào $options
		$options = $db->loadObjectList();		
		$position_str='';
		$i=0;
        //luu ra 3 mang mod_id_str, position_str, modtitle_str tu mảng option theo cấu trúc mod_id_str='module_2','module_5'...
		foreach($options as $option){			
			if ($i==0){
				$mod_id_str = '\'module_'.$option->id . '\'';
				$position_str = '\'' . $option->position . '\'';
				$modtitle_str = '\''. $option->title . '\'';
			}else{
				$mod_id_str = $mod_id_str . ',' . '\'module_'. $option->id . '\'';
				$position_str = $position_str . ',' . '"' . $option->position .'"';
				$modtitle_str = $modtitle_str . ',' . '"' . $option->title . '"';
			}
			$i++;
		}
		//End module

		//Content component 
		$query = 'SELECT c.id, c.title' .
			' FROM #__categories AS c' . 
			' WHERE c.published = 1' .
			' AND c.extension = \'com_content\' '.
			' AND c.parent_id >0'.
			' ORDER BY c.title'; 
		$db->setQuery($query);
		$options = $db->loadObjectList();
		$i=0;
		foreach($options as $option){
			if ($i==0){
				$category_id_str = '\'category_'.$option->id . '\'';
				$cattitle_str = '\'' . $option->title . '\'';
			}else{
				$category_id_str = $category_id_str . ',' . '\'category_'. $option->id . '\'';
				$cattitle_str = $cattitle_str . ',' . '"' . $option->title . '"';
			}
			$i++;
		}
		//End content 

		$baseURL = JURI::base();
        $html_return = '';
		$html_return .= '<div id="jv_catagory_info" class="jv_catagory_info">';
        //begin zt-load
        $html_return .= '<div id="zt-load">';
		$html_return.= JHTML::_('select.genericlist',  $selection, 'jv_selection', 'class="'.$class.'" MULTIPLE size="10"', 'id', 'title', $value, $control_name.$name );
        $html_return .= '</div>';
        //end zt-load
        $html_return.='<div id="zt-process">
				<input class="button" type="button" value="Add" id ="list-selection-add" name="Add" onClick="addSelection();" />
				<input class="button" type="button" value="Remove" id ="list-selection-remove" onClick="removeSelection();" name="Remove" />
				</div>';
        $html_return.='<div id="zt-save">';
		$html_return.= JHTML::_('select.genericlist',  $arySelection, 'jv_selection_selected', 'class="'.$class.'" MULTIPLE size="10" ', 'id', 'title', $value, 'jv_selection_selected' );
        $html_return.='</div>';

        $html_return.='<div id="zt-replace">
		<span id="jv_up_arrow"><i class="fa fa-arrow-up"></i></span>
		<span id="jv_down_arrow"><i class="fa fa-arrow-down"></i></span>
		</div>';
        $html_return .='</div>';


        $html_return.="<input type='hidden' name='jform[params][jv_selection]' id='jv_hid_selection' value='".$value."' />";
	  
		return $html_return;
	}

}
