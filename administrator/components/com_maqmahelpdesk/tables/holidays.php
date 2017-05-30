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

class MaQmaHelpdeskTableHolidays extends JTable
{
	var $id = null;
	var $holiday_date = '0000-00-00';
	var $name = null;

	function __construct(&$_db)
	{
		parent::__construct('#__support_holidays', 'id', $_db);
	}
}
