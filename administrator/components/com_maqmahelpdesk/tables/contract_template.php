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

class MaQmaHelpdeskTableContractTemplate extends JTable
{
	var $id = null;
	var $id_priority = 0;
	var $name = null;
	var $description = null;
	var $unit = null;
	var $val = null;

	function __construct(&$_db)
	{
		parent::__construct('#__support_contract_template', 'id', $_db);
	}
}