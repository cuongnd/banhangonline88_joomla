<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

class TableJFBConnectRequest extends JTable
{
	var $id = null;
	var $published = null;
    var $title = null;
    var $message = null;
    var $destination_url = null;
    var $thanks_url = null;
	var $breakout_canvas = false;
    var $created = null;
    var $modified = null;

	function TableJFBConnectRequest(&$db)
	{
		parent::__construct('#__jfbconnect_request', 'id', $db);
	}
}