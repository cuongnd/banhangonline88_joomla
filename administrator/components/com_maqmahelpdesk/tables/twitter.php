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

class mosSupportTwitter extends JTable
{
	var $id = null;
	var $id_workgroup = null;
	var $consumer_key = null;
	var $consumer_secret = null;
	var $account = null;
	var $last_check = null;
	var $last_id = null;
	var $ignore_rt = 1;
	var $published = 1;

	function __construct(&$_db)
	{
		parent::__construct('#__support_twitter', 'id', $_db);
	}
}