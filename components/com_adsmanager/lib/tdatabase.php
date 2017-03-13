<?php
/**
 * @package		AdsManager
 * @copyright	Copyright (C) 2010-2014 Juloa.com. All rights reserved.
 * @license		GNU/GPL
 */
// no direct access
defined('_JEXEC') or die('Restricted access');

class TDatabase {
    public static function loadColumn($sql,$offset=0) {
        $db = JFactory::getDbo();
        
        $db->setQuery($sql);
        
        if(version_compare(JVERSION, '2.5', 'ge')) {
            $return = $db->loadColumn($offset);            
        } else {
            $tmps = $db->loadRowList();
            $return = array();

            foreach($tmps as $tmp) {
                $return[] = $tmp[0];
            }
        }
        return $return;
    }
}