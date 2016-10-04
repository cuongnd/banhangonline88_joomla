<?php
/**
 * @package 	BT Smart Search
 * @version	1.0.1
 * @created	Dec 2012
 * @author	BowThemes
 * @email	support@bowthemes.com
 * @website	http://bowthemes.com
 * @support     Forum - http://bowthemes.com/forum/
 * @copyright   Copyright (C) 2012 Bowthemes. All rights reserved.
 * @license     http://bowthemes.com/terms-and-conditions.html
 *
 */
defined('_JEXEC') or die('Restricted access');
JLoader::register('FinderHelperRoute', JPATH_SITE . '/components/com_finder/helpers/route.php');
require_once dirname(__FILE__) . '/helper.php';
if (!defined('FINDER_PATH_INDEXER'))
{
	define('FINDER_PATH_INDEXER', JPATH_ADMINISTRATOR . '/components/com_finder/helpers/indexer');
}
JLoader::register('FinderIndexerQuery', FINDER_PATH_INDEXER . '/query.php');
require_once dirname(__FILE__) . '/helper.php';
ModBtSmartsearchHelper::fetchHead($params);
$searchfilter = $params->get('searchfilter');
$show_autosuggest = $params->get('show_autosuggest', 1);
$show_advanced = $params->get('show_advanced', 1);
$moduleclass_sfx = $params->get('moduleclass_sfx');
$layout = $params->get('layout', 'default');
$show_button = $params->get('show_button', 1);
$button_pos = $params->get('button_pos', 'right');
$opensearch = $params->get('opensearch', 1);
if($opensearch ==1){
	$opensearch_title = $params->get('opensearch_title',1);
}
else{
	$opensearch_title="";
}

JPATH_SITE . '/components/com_finder/helpers/route.php';
$route = FinderHelperRoute::getSearchRoute($params->get('f', null));
$query = ModBtSmartsearchHelper::getQuery($params);
require (JModuleHelper::getLayoutPath ( 'mod_bt_smartsearch' ));

?>