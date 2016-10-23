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
        $doc->addStyleSheet('//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css');

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

        //finish
        $html_return.="<script type=\"text/javascript\">
			var array_mod_id = new Array(".$mod_id_str.");
			var array_mod_position = new Array(".$position_str.");
			var array_mod_title = new Array(".$modtitle_str.");
			var array_cat_id = new Array(".$category_id_str.");
			var array_cat_title = new Array(".$cattitle_str.");

			var titlePos = '".$titlePos."';



			function addSelection(){
				var select_box = $('jformparamsjv_selection');
				var selected_box = $('jv_selection_selected');
				for(i=0;i<select_box.options.length;i++){
					if(select_box.options[i].selected){
						var opt       = document.createElement('option');
						opt.value     = select_box.options[i].value;
						opt.innerHTML = select_box.options[i].innerHTML;
						selected_box.appendChild(opt);
						select_box.removeChild(select_box.options[i]);
					}
				}
				save_selection_list();
			}
			function removeSelection(){
				var type = $('jformparamstype').value;
				var selected_box = $('jv_selection_selected');
				for (var i=selected_box.options.length-1; i >= 0;i--) {
				   if (selected_box.options[i].selected) {
						selected_box.removeChild(selected_box.options[i]);
				   }
				}
				save_selection_list();
				if(type=='moduleID') reload_modulelist($('jformparamsmoduleID-position').value);
				if(type=='categoryID') reload_category();

			}
			function addCategory(){
				var select_box = $('jformparamsjv_selection');
				var selected_box = $('jv_selection_selected');
				for(i=0;i<select_box.options.length;i++){
					if(select_box.options[i].selected){
						var opt       = document.createElement('option');
						opt.value     = select_box.options[i].value;
						opt.innerHTML = select_box.options[i].innerHTML;
						selected_box.appendChild(opt);
						select_box.removeChild(select_box.options[i]);
					}
				}
				save_selection_list();
			}
			function save_selection_list(){
				var selected_box = $('jv_selection_selected');
				var selection_hidden_list = $('jv_hid_selection');
				selection_hidden_list.value = '';
				for(i=0;i<selected_box.options.length;i++){
					if(selection_hidden_list.value == ''){
						selection_hidden_list.value = selected_box.options[i].value;
					} else {
						selection_hidden_list.value+= ',' +selected_box.options[i].value;
					}
				}
			}
			function reload_modulelist(position){
				var select_box = $('jformparamsjv_selection');
				var selected_box = $('jv_selection_selected');
				$('jformparamsjv_selection').empty();
				var list_selection = $('jv_hid_selection').value.split(',');
				$('jformparamsjv_selection').empty();
				$('jformparamsjv_selection').length =0;
				$('jv_selection_selected').options.length =0;
				for(i=0;i<list_selection.length;i++){
					for(j=0;j<array_cat_id.length;j++){
						if(list_selection[i] == array_cat_id[j]){
							var opt       = document.createElement('option');
							opt.value     = list_selection[i];
							opt.innerHTML = array_cat_title[j];
							$('jv_selection_selected').appendChild(opt);
						}
					}
					for(j=0;j<array_mod_id.length;j++){
						if(list_selection[i] == array_mod_id[j]){
							var opt       = document.createElement('option');
							opt.value     = list_selection[i];
							opt.innerHTML = array_mod_position[j]+'-'+array_mod_title[j];
							$('jv_selection_selected').appendChild(opt);
						}
					}

				}
				//If module,append child to select box
				for(var i=0; i<array_mod_id.length;i++){
					var isSelected=false;
					for(var j=0; j<list_selection.length; j++){
						if (list_selection[j] == array_mod_id[i]){
							isSelected=true;
							break;
						}
					}
					if (array_mod_position[i]==position){
						if (isSelected==false){
							var opt       = document.createElement('option');
							opt.value     = array_mod_id[i];
							opt.innerHTML = array_mod_title[i];
							select_box.appendChild(opt);
						}
					}
				}
				//End module
			}
			function reload_category(){
				var moduleList = $('jform_params_categoryID-list');
				var position = $('jformparamsmoduleID-position').value;
				var list_selection = $('jv_hid_selection').value.split(',');
				var select_box = $('jformparamsjv_selection');
				var selected_box = $('jv_selection_selected');
				$('jformparamsjv_selection').empty();
				$('jformparamsjv_selection').length =0;
				$('jv_selection_selected').options.length =0;
				for(i=0;i<list_selection.length;i++){
					for(j=0;j<array_cat_id.length;j++){
						if(list_selection[i] == array_cat_id[j]){
							var opt       = document.createElement('option');
							opt.value     = list_selection[i];
							opt.innerHTML = array_cat_title[j];
							$('jv_selection_selected').appendChild(opt);
						}
					}
					for(j=0;j<array_mod_id.length;j++){
						if(list_selection[i] == array_mod_id[j]){
							var opt       = document.createElement('option');
							opt.value     = list_selection[i];
							opt.innerHTML = array_mod_position[j]+'-'+array_mod_title[j];
							$('jv_selection_selected').appendChild(opt);
						}
					}

				}
				//Category
				for(i=0;i<array_cat_id.length;i++){
					var isSelected=false;
					for(var j=0; j<list_selection.length; j++){
						if (list_selection[j] == array_cat_id[i]){
							isSelected=true;
							break;
						}
					}
					if(!isSelected){
						var opt       = document.createElement('option');
						opt.value     = array_cat_id[i];
						opt.innerHTML = array_cat_title[i];
						$('jformparamsjv_selection').appendChild(opt);
					}
				}
				//End category
			}

			function reload_selection(){
				var type = $('jformparamstype').value;
				switch(type){
					case 'moduleID':
					var pos_select_box = $('jformparamsmoduleID-position').value;
					reload_modulelist(pos_select_box);
					break;
					case 'categoryID':
					reload_category();
					break;

				}
			}
			function moveUp(element) {
			  for(i = 0; i < element.options.length; i++) {
				if(element.options[i].selected == true) {
				  if(i != 0) {
					var temp = new Option(element.options[i-1].text,element.options[i-1].value);
					var temp2 = new Option(element.options[i].text,element.options[i].value);
					element.options[i-1] = temp2;
					element.options[i-1].selected = true;
					element.options[i] = temp;
				  }
				}
			  }
			}
			function moveDown(element) {
			  for(i = (element.options.length - 1); i >= 0; i--) {
				if(element.options[i].selected == true) {
				  if(i != (element.options.length - 1)) {
					var temp = new Option(element.options[i+1].text,element.options[i+1].value);
					var temp2 = new Option(element.options[i].text,element.options[i].value);
					element.options[i+1] = temp2;
					element.options[i+1].selected = true;
					element.options[i] = temp;
				  }
				}
			  }
			}
			function updateList(){
				var list = $('jv_selection_selected');
				var textBox = $('jv_hid_selection');
				textBox.value = '';
				for(i = 0; i < list.options.length; i++) {
					if (i == 0) {
					  textBox.value += list.options[i].value;
					} else {
					  textBox.value += ',' + list.options[i].value;
					}
				}
			}
			function change_position(value) {
				reload_modulelist(value);
			}

			function selectType(type){
			        var selectPosition = $('jform_params_moduleID_position-lbl').getParent().getParent();
					switch(type){
						case 'moduleID':
                             selectPosition.show();
							var pos_select_box = $('jformparamsmoduleID-position').value;
							reload_modulelist(pos_select_box);
							break;
						case 'categoryID':
                             selectPosition.hide();

							reload_category();//Reload select box
							break;
                    //default :
                            //selectPosition.hide();
                            //break;

					}
				};
			/*
			*
			*
			*
			*/
			window.addEvent('load',function(){
			    //var selectPosition = $('jform_params_moduleID_position-lbl').getParent().getParent();
		        //selectPosition.hide();
                $('jformparamsjv_selection').show();
			    $('jv_selection_selected').show();
			   // $('jformparamstype').show();
			    //$('jformparamsmoduleID-position').show();





				selectType('".$jvType."');


				//$('jformparamstype').addEvent('change',function(){

					//selectType(this.value);
				//});
				reload_selection();
				$('jv_up_arrow').addEvent('click',function(){
					moveUp($('jv_selection_selected'));
					updateList();
				});
				$('jv_down_arrow').addEvent('click',function(){
					moveDown($('jv_selection_selected'));
					updateList();
				});
			});
		</script>
		";
        $html_return.="<input type='hidden' name='jform[params][jv_selection]' id='jv_hid_selection' value='".$value."' />";
	  
		return $html_return;
	}

}
