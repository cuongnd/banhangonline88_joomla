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

class MaQmaHelpdeskTableClientContract extends JTable
{
	var $id = null;
	var $id_contract = 0;
	var $id_client = 0;
	var $contract_number = 0;
	var $creation_date = null;
	var $date_start = null;
	var $date_end = null;
	var $unit = null;
	var $value = 0;
	var $actual_value = 0;
	var $status = null;
	var $remarks = null;
	var $id_user = 0;
	var $overdue = 0;
	var $overdue_update = null;

	function __construct(&$_db)
	{
		parent::__construct('#__support_contract', 'id', $_db);
	}
}