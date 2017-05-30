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

class MaQmaHelpdeskTableMailFetch extends JTable
{
	var $id = null;
	var $id_workgroup = null;
	var $email = null;
	var $server = null;
	var $username = null;
	var $password = null;
	var $type = null;
	var $port = null;
	var $remove = 1;
	var $extra_info = null;
	var $queue = 0;
	var $id_status = 0;
	var $id_category = 0;
	var $label = 'INBOX';
	var $notls = 0;
	var $thrash = 'TRASH';
	var $ssl = 0;
	var $published = 1;

	function __construct(&$_db)
	{
		parent::__construct('#__support_mail_fetch', 'id', $_db);
	}
}