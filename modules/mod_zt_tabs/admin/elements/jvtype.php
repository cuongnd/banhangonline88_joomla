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
class JFormFieldJvType extends JFormField {

	var	$type = 'JvType';

	function getInput(){
        $JElementJvType = new JElementJvType();
		return $JElementJvType->fetchElement($this->name, $this->value, $this->element, $this->options['control']);

	} 
} 
jimport('joomla.html.parameter.element');
class JElementJvType
{ 
	var	$_name = 'JvType';

	function fetchElement($name, $value, &$node, $control_name)
	{
		$options = array ();
		$db = JFactory::getDBO();

		$val = "";
		$text = "- Select type -";
		$options[] = JHTML::_('select.option', $val, JText::_($text));

		$val = "moduleID";
		$text = "Modules";
		$options[] = JHTML::_('select.option', $val, JText::_($text));

		$val = "categoryID";
		$text = "Contents";
		$options[] = JHTML::_('select.option', $val, JText::_($text));

		$class = ( $node->attributes('class') ? 'class="'.$node->attributes('class').'"' : 'class="inputbox"' );
		//$class .= " onchange=\"javascript: switchGroup(this)\""; 
		 $str = JHTML::_('select.genericlist',  $options, ''.$name.'',  'class=' . $class . ' onchange="javascript:selectType(this.value);"' ,'value', 'text', $value, $control_name.$name);

		$cId = JRequest::getVar('cid','');
		if($cId !='') $cId = $cId[0];

		if($cId == ''){
			$cId = JRequest::getVar('id');
		}
		$cId=(int)$cId;
		$sql = "SELECT params FROM #__modules WHERE id=$cId";
		$db->setQuery($sql);
		$paramsConfigObj = $db->loadObjectList();
		$db->setQuery($sql);
		$data = $db->loadResult();
        $params = json_decode($data);

        if(!is_null($params)){
            $params=get_object_vars($params);
            $tabType = isset($params['type']) ? $params['type'] : 'moduleID';
            $viewContent = isset($params['jv_tabs_style']) ? $params['jv_tabs_style'] : 'zt_default';
        }

        ?>
		
	<?php 	
		return $str;

	}
} 
?>
