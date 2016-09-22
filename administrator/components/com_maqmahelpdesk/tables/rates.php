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

class MaQmaHelpdeskTableRates extends JTable
{
	var $id = null;
	var $description = null;
	var $multiplier = 1.0;
	var $isdefault = 0;
	var $published = 1;

	function __construct(&$_db)
	{
		parent::__construct('#__support_activity_rate', 'id', $_db);
	}
}