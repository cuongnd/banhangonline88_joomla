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

class MaQmaHelpdeskTableGlossary extends JTable
{
	var $id = null;
	var $term = null;
	var $description = null;
	var $published = 0;
	var $anonymous_access = 0;
	var $id_category = 0;

	function __construct(&$_db)
	{
		parent::__construct('#__support_glossary', 'id', $_db);
	}
}