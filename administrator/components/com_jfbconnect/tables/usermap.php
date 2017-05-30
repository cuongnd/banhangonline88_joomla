<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

class TableUserMap extends JTable
{
    var $id = null;
    var $created_at = null;
    var $updated_at = null;
    var $authorized = 1;

    var $j_user_id = null;
    var $provider_user_id = null;
    var $provider = null;
    var $access_token = null;
    var $params = null;

    function TableUserMap(&$db)
    {
        parent::__construct('#__jfbconnect_user_map', 'id', $db);
    }
}