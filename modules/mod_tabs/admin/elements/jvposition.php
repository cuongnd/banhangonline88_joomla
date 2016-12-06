<?php
/**
 * @package ZT Tabs module
 * @author http://www.ZooTemplate.com
 * @copyright (C) 2014- ZooTemplate.Com
 * @license PHP files are GNU/GPL
**/

// Check to ensure this file is included in Joomla!
defined ( '_JEXEC' ) or die ( 'Restricted access' );
jimport('joomla.form.formfield');
class JFormFieldJvPosition extends JFormField {

	var	$type = 'Position';

	function getInput(){
        $JElementJvPosition = new JElementJvPosition();
		return $JElementJvPosition->fetchElement($this->name, $this->value, $this->element, $this->options['control']);
	}
}
jimport('joomla.html.parameter.element');
class JElementJvPosition  {
	var $_name = 'Position';

	function fetchElement($name, $value, &$node, $control_name) {
		$class = $node->attributes ( 'class' );
		if (! $class) {
			$class = "inputbox";
		}
		$db = JFactory::getDbo();
		$query = 'SELECT DISTINCT position'
		. ' FROM #__modules AS a'
		. ' WHERE a.published = 1'
		. ' AND a.position <> \'cpanel\''
		. ' ORDER BY a.position';
		$db->setQuery ( $query );
		$db->getQuery ();
		$options = $db->loadObjectList ();
		$arrOpt = array ();

		for($i = 0; $i < count ( $options ); $i ++) {
			$arrOpt [$i] ['keys'] = $arrOpt [$i] ['value'] = $options [$i]->position;
		}
        // insert array arrOpt with key=value=option[]
		array_unshift ( $arrOpt, JHTML::_ ( 'select.option', '0', '- ' . JText::_ ( 'Select position' ) . ' -', 'keys', 'value' ) );
		$html_return = JHTML::_ ( 'select.genericlist', $arrOpt, '' . $control_name . '[' . $name . ']', 'class=' . $class . ' onchange="javascript:change_position(this.value);"', 'keys', 'value', $value, $control_name . $name );
		return $html_return;
	}
}
?>