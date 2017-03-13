<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

class TableChannel extends JTable
{
    var $id = null;
    var $provider = null;
    var $type = null;
    var $title = null;
    var $description = null;
    var $attribs = null;
    var $published = 0;
    var $created = null;
    var $modified = null;

    function TableChannel(&$db)
    {
        parent::__construct('#__jfbconnect_channel', 'id', $db);
    }

    function bind($src, $ignore = array())
    {
        if (isset($src['attribs']) && is_array($src['attribs']))
        {
            $attribs = new JRegistry();
            $attribs->loadArray($src['attribs']);
            $src['attribs'] = (string)$attribs;
        }
        return parent::bind($src, $ignore);
    }

    public function load($keys = null, $reset = true)
    {
        $return = parent::load($keys, $reset);
        $this->attribs = json_decode($this->attribs);
        return $return;
    }
}