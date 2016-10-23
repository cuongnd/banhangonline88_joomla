<?php
//namespace components\com_jchat\models\fields;
/**  
 * @package JCHAT::components::com_jchat 
 * @subpackage models
 * @subpackage fields
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html   
 */ 
defined( '_JEXEC' ) or die( 'Restricted access' );
if(!class_exists('JFormFieldList')) {
	require_once JPATH_SITE . '/libraries/joomla/form/fields/list.php';
}

/**
 * Form Field for menu tree
 * @package JCHAT::components::com_jchat
 * @subpackage models 
 * @subpackage fields
 * @since 1.0
 */
class JFormFieldMenuMultiselect extends JFormFieldList {
	/**
	 * Tree recursion menu
	 * 
	 * @access private 
	 * @param int $id
	 * @param string $indent
	 * @param array $list
	 * @param array $children
	 * @param int $maxlevel
	 * @param int $level
	 * @param int $type
	 * @return array
	 */
	private static function treeRecurse($id, $indent, $list, &$children, $maxlevel = 9999, $level = 0, $type = 1) {
		if (@$children [$id] && $level <= $maxlevel) {
			foreach ( $children [$id] as $v ) {
				$id = $v->id;
	
				if ($type) {
					$pre = '<sup>|_</sup>&nbsp;';
					$spacer = '.&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;';
				} else {
					$pre = '- ';
					$spacer = '&nbsp;&nbsp;';
				}
	
				if ($v->parent == 0) {
					$txt = $v->name;
				} else {
					$txt = $pre . $v->name;
				}
				$pt = $v->parent;
				$list [$id] = $v;
				$list [$id]->treename = "$indent$txt";
				$list [$id]->children = count ( @$children [$id] );
				$list = self::TreeRecurse ( $id, $indent . $spacer, $list, $children, $maxlevel, $level + 1, $type );
			}
		}
		return $list;
	}
	
	/**
	 * Build the multiple select list for Menu Links/Pages
	 * 
	 * @access public
	 * @return array
	 */
	protected function getOptions() {
		$db = JFactory::getDBO ();
		
		// get a list of the menu items
		$query = 'SELECT m.id, m.parent_id AS parent, m.title AS name, m.menutype, t.title' .
				' FROM #__menu AS m' .
				' INNER JOIN #__menu_types AS t' .
				' ON m.menutype = t.menutype' .
				' WHERE m.published = 1' .
				' AND m.client_id = 0' .
				' ORDER BY m.menutype, m.parent_id, m.lft';
		$db->setQuery ( $query );
		$mitems = $db->loadObjectList ();
		$mitems_temp = $mitems;
		
		if(empty($mitems)) {
			return $mitems;
		}
		
		// establish the hierarchy of the menu
		$children = array ();
		// first pass - collect children
		foreach ( $mitems as $v ) {
			$id = $v->id;
			$pt = $v->parent;
			$list = @$children [$pt] ? $children [$pt] : array ();
			array_push ( $list, $v );
			$children [$pt] = $list;
		}
		// second pass - get an indent list of the items
		$list = self::treeRecurse ( intval ( $mitems [0]->parent ), '', array (), $children, 9999, 0, 0 );
		
		// Code that adds menu name to Display of Page(s)
		$mitems_spacer = $mitems_temp [0]->menutype;
		
		$mitems = array ();
		$lastMenuType = null;
		$tmpMenuType = null;
		foreach ( $list as $list_a ) {
			if ($list_a->menutype != $lastMenuType) {
				if ($tmpMenuType) {
					$mitems [] = JHTML::_ ( 'select.option', '</OPTGROUP>' );
				}
				$mitems [] = JHTML::_ ( 'select.option', '<OPTGROUP>', $list_a->title );
				$lastMenuType = $list_a->menutype;
				$tmpMenuType = $list_a->menutype;
			}
			
			$mitems [] = JHTML::_ ( 'select.option', $list_a->id, $list_a->treename );
		}
		if ($lastMenuType !== null) {
			$mitems [] = JHTML::_ ( 'select.option', '</OPTGROUP>' );
		}
		
		$noActiveOption = JHtml::_('select.option', '0', $this->multiple ? JText::_('COM_JCHAT_NO_PAGE_EXCLUSION') : JText::_('COM_JCHAT_NO_SELECTION'));
		array_unshift($mitems, $noActiveOption);
		
		return $mitems;
	}
}