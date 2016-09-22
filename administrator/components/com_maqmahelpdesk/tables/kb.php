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

class MaQmaHelpdeskTableKB extends JTable
{
	var $id = null;
	var $kbcode = null;
	var $id_user = null;
	var $content = null;
	var $keywords = null;
	var $publish = 1;
	var $views = 0;
	var $kbtitle = null;
	var $date_created = null;
	var $date_updated = null;
	var $anonymous_access = 0;
	var $faq = 0;
	var $approved = 1;
	var $slug = null;

	function __construct(&$_db)
	{
		parent::__construct('#__support_kb', 'id', $_db);
	}
}