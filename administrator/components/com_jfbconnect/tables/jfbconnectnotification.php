<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

class TableJFBConnectNotification extends JTable
{
	var $id = null;
	var $fb_request_id = null;
    var $fb_user_to = null;
    var $fb_user_from = null;
    var $jfbc_request_id = null;
    var $status = 0;
    var $created = null;
    var $modified = null;

	function TableJFBConnectNotification(&$db)
	{
		parent::__construct('#__jfbconnect_notification', 'id', $db);
	}

}