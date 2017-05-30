<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

class JFBConnectCache
{
    protected $cache;

    public function __construct()
    {
        $this->cache = JFactory::getCache('com_jfbconnect', '');
        // Only enable caching if Debug Mode is off
        $debug = JFBCFactory::config()->get('facebook_display_errors');
        $cacheTimeout = JFBCFactory::config()->get('cache_duration');

        $this->cache->setCaching(!$debug && $cacheTimeout != 0);
        $this->cache->setLifeTime($cacheTimeout);
    }

    public function store($value, $name)
    {
        $this->cache->store($value, $name);
    }

    public function get($name)
    {
        return $this->cache->get($name);
    }

}