<?php
/**
 * @package    HikaMarket for Joomla!
 * @version    1.7.0
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

function hikamarketBuildRoute(&$query) {
	$segments = array();
	if(!defined('DS'))
		define('DS', DIRECTORY_SEPARATOR);
	$config = null;
	$shopConfig = null;
	if(defined('HIKAMARKET_COMPONENT') || include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikamarket'.DS.'helpers'.DS.'helper.php')) {
		$config = hikamarket::config();
		$shopConfig = hikamarket::config(false);
	}

	$controller = null;
	if(isset($query['ctrl'])) {
		if(substr($query['ctrl'], -6) == 'market')
			$query['ctrl'] = substr($query['ctrl'], 0, -6);
		$segments[] = $query['ctrl'];
		$controller = $query['ctrl'];
		unset( $query['ctrl'] );
		if (isset($query['task']) && strpos($query['task'], '-') === false) {
			$segments[] = $query['task'];
			unset($query['task']);
		}
	} elseif(isset($query['view'])) {
		if(substr($query['view'], -6) == 'market')
			$query['view'] = substr($query['view'], 0, -6);
		$segments[] = $query['view'];
		$controller = $query['view'];
		unset($query['view']);
		if(isset($query['layout'])) {
			$segments[] = $query['layout'];
			unset($query['layout']);
		}
	}

	if(count($segments) == 2 && $segments[0] == 'vendor' && $segments[1] == 'show' && isset($query['Itemid']) && $shopConfig->get('sef_remove_id',0)) {
		$segments = array();
	}

	if(isset($query['cid']) && isset($query['name'])) {
		if($controller == 'vendor' && !empty($shopConfig) && $shopConfig->get('sef_remove_id',0) && !empty($query['name'])) {
			$segments[] = $query['name'];
		} else {
			if(is_numeric($query['name'])) {
				$query['name'] = $query['name'] . '-';
			}
			$segments[] = $query['cid'] . ':' . $query['name'];
		}
		unset($query['cid']);
		unset($query['name']);
	}

	if(!empty($query)) {
		foreach($query as $name => $value) {
			if(!in_array($name, array('option', 'Itemid', 'start', 'format', 'limitstart', 'lang'))) {
				$segments[] = $name . ':' . $value;
				unset($query[$name]);
			}
		}
	}
	return $segments;
}

function hikamarketParseRoute($segments) {
	$vars = array();
	if(!empty($segments)) {
		if(!defined('DS'))
			define('DS', DIRECTORY_SEPARATOR);
		$config = null;
		$shopConfig = null;
		if(defined('HIKAMARKET_COMPONENT') || include_once(rtrim(JPATH_ADMINISTRATOR,DS).DS.'components'.DS.'com_hikamarket'.DS.'helpers'.DS.'helper.php')){
			$config = hikamarket::config();
			$shopConfig = hikamarket::config(false);
		}
		if(count($segments) == 1 && $shopConfig->get('sef_remove_id',0)) {
			$vars['ctrl'] = 'vendor';
			$vars['task'] = 'show';
			if(hikmarket_retrieve_url_id($vars, $segments[0]))
				return $vars;
			unset($vars['ctrl']);
			unset($vars['task']);
		}
		$i = 0;
		foreach($segments as $name) {
			if(isset($vars['ctrl']) && isset($vars['task']) && $shopConfig->get('sef_remove_id',0) && hikmarket_retrieve_url_id($vars, $name))
				continue;
			if(strpos($name, ':')) {
				list($arg, $val) = explode(':',$name);
				if(is_numeric($arg) && !is_numeric($val)) {
					$vars['cid'] = $arg;
					$vars['name'] = $val;
				} else if(is_numeric($arg))
					$vars['Itemid'] = $arg;
				else
					$vars[$arg] = $val;
			} else {
				$i++;
				if($i == 1)
					$vars['ctrl'] = $name;
				elseif($i == 2)
					$vars['task'] = $name;
			}
		}
	}
	return $vars;
}

function hikmarket_retrieve_url_id(&$vars, $name) {
	if(@$vars['ctrl'] !== 'vendor' && @$vars['task'] !== 'show')
		return false;

	if(!empty($vars['cid']))
		return false;

	$db = JFactory::getDBO();
	$shopConfig = hikamarket::config(false);

	if($shopConfig->get('alias_auto_fill', 1)) {
		$db->setQuery('SELECT vendor_id FROM ' . hikamarket::table('vendor').' WHERE vendor_alias = '.$db->Quote(str_replace(':','-',$name)));
		$retrieved_id = $db->loadResult();
		if($retrieved_id) {
			$vars['cid'] = $retrieved_id;
			$vars['name'] = $name;
			return true;
		}
	}

	$name_regex = '^ *' . str_replace(array('-', ':'), '.+', $name) . ' *$';
	$db->setQuery('SELECT * FROM ' . hikamarket::table('vendor') . ' WHERE vendor_alias REGEXP ' . $db->Quote($name_regex) . ' OR vendor_name REGEXP ' . $db->Quote($name_regex));
	$retrieved = $db->loadObject();
	if($retrieved) {
		$vars['cid'] = $retrieved->vendor_id;
		$vars['name'] = $name;

		if($shopConfig->get('alias_auto_fill', 1) && empty($retrieved->vendor_alias)) {
			$retrieved->alias = $retrieved->vendor_name;
			if(!$shopConfig->get('unicodeslugs')) {
				$lang = JFactory::getLanguage();
				$retrieved->alias = $lang->transliterate($retrieved->alias);
			}

			$app = JFactory::getApplication();
			if(method_exists($app,'stringURLSafe'))
				$retrieved->alias = $app->stringURLSafe($retrieved->alias);
			else
				$retrieved->alias = JFilterOutput::stringURLSafe($retrieved->alias);

			$vendorClass = hikamarket::get('class.vendor');
			$element = new stdClass();
			$element->vendor_id = $retrieved->vendor_id;
			$element->vendor_alias = $retrieved->alias;
			$vendorClass->save($element);
		}
		return true;
	}
	return false;
}
