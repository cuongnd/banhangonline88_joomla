<?php
/**
 * @package    HikaSerial for Joomla!
 * @version    1.10.4
 * @author     Obsidev S.A.R.L.
 * @copyright  (C) 2011-2016 OBSIDEV. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
?><?php

function hikaserialBuildRoute( &$query )
{
	$segments = array();
	if(isset($query['ctrl'])) {
		$segments[] = $query['ctrl'];
		unset( $query['ctrl'] );
		if (isset($query['task'])) {
			$segments[] = $query['task'];
			unset( $query['task'] );
		}
	} elseif(isset($query['view'])) {
		$segments[] = $query['view'];
		unset( $query['view'] );
		if(isset($query['layout'])) {
			$segments[] = $query['layout'];
			unset( $query['layout'] );
		}
	}

	if(!empty($query)) {
		foreach($query as $name => $value) {
			if(!in_array($name, array('option', 'Itemid', 'start', 'limitstart', 'lang'))) {
				if(is_array($value)) $value = implode('-', $value);
				$segments[] = $name.':'.$value;
				unset($query[$name]);
			}
		}
	}
	return $segments;
}

function hikaserialParseRoute( $segments )
{
	$vars = array();
	if(empty($segments))
		return $vars;

	$i = 0;
	foreach($segments as $name) {
		if(strpos($name,':')) {
			list($arg,$val) = explode(':',$name);
			if(is_numeric($arg)) $vars['Itemid'] = $arg;
			else $vars[$arg] = $val;
		} else {
			$i++;
			if($i == 1) $vars['ctrl'] = $name;
			elseif($i == 2) $vars['task'] = $name;
		}
	}
	return $vars;
}
