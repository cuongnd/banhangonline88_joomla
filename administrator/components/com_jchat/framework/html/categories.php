<?php
// namespace administrator\components\com_jchat\framework\html;
/**  
 * @package JCHAT::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage html
 * @author Joomla! Extensions Store
 * @Copyright (C) 2015 - Joomla! Extensions Store
 * @license GNU/GPLv2 http://www.gnu.org/licenses/gpl-2.0.html  
 */
defined ( '_JEXEC' ) or die ( 'Restricted access' );

/**
 * Content categories for multiselect
 *
 * @package JCHAT::administrator::components::com_jchat
 * @subpackage framework
 * @subpackage html
 *        
 */
class JChatHtmlCategories extends JObject {
	/**
	 * Build the multiple select list for Menu Links/Pages
	 * 
	 * @access public
	 * @return array
	 */
	public static function getCategories() {
		$categories = array();
		$categories[] = JHtml::_('select.option', '0', JText::_('COM_JCHAT_NOCATS'), 'value', 'text');
		$categories = array_merge($categories, JHtml::_('category.options', 'com_content'));
		
		return $categories;
	}
}