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

class MaQmaHelpdeskTableCategory extends JTable
{
	var $id = null;
	var $name = null;
	var $show = 1;
	var $id_workgroup = 0;
	var $parent = 0;
	var $kb = 0;
	var $tickets = 0;
	var $downloads = 0;
	var $discussions = 0;
	var $bugtracker = 0;
	var $glossary = 0;
	var $level = 1;
	var $slug = null;

	function __construct(&$_db)
	{
		parent::__construct('#__support_category', 'id', $_db);
	}
}