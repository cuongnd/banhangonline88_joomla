<?php
/**
 * @package	HikaShop for Joomla!
 * @version	2.6.3
 * @author	hikashop.com
 * @copyright	(C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license	GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php
class hikashopUtilityHelper {
	function get_code($title){
		$title = explode(' ', $title);
		$title = array_slice($title, 0, 2);
		$title = implode('_', $title);
		$title = JString::vn_str_filter($title);
		$title = JString::clean($title);
		$title = strtolower($title);
		$title .= '_' . mt_rand(100, 999);
		return $title;
	}
	function get_alias($title){
		$title = str_replace(' ', '-', $title);
		$title=JString::vn_str_filter($title);
		$title=JString::clean($title);
		$title=strtolower($title);
		return $title;
	}
}
