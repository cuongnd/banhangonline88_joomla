<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2012 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

class MaQmaHelpdeskTableClientUser extends JTable
{
	var $id = null;
	var $id_client = 0;
	var $id_user = 0;
	var $manager = 0;
	var $default = 0;
	var $create_ticket = 0;

	function __construct(&$_db)
	{
		parent::__construct('#__support_client_users', 'id', $_db);
	}
}