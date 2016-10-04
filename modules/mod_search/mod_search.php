<?php
/**
 * @copyright	Copyright © 2016 - All rights reserved.
 * @license		GNU General Public License v2.0
 * @generator	http://xdsoft/joomla-module-generator/
 */
defined('_JEXEC') or die;

$doc = JFactory::getDocument();
require_once JPATH_ROOT.DS.'modules/mod_search/helper.php';
$helper=mod_search::getInstance();
$list_category_product=$helper->get_list_category_product($params);
require JModuleHelper::getLayoutPath('mod_search', $params->get('layout', 'default'));