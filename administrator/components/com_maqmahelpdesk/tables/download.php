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

class MaQmaHelpdeskTableDownload extends JTable
{
	var $id = null;
	var $id_category = "";
	var $pname = "";
	var $description = "";
	var $url = "";
	var $ordering = 0;
	var $published = 1;
	var $plataform = "";
	var $date = "";
	var $hits = 0;
	var $id_license = 0;
	var $groupid = "";
	var $features = "";
	var $requirements = "";
	var $limitations = "";
	var $expired = "";
	var $updated = "";
	var $offline = 0;
	var $image = "";
	var $evaluation = "";
	var $download_version = 0;
	var $download_previous = 0;
	var $template_file = "";
	var $registered_only = 0;
	var $image_view = "";
	var $slug = null;

	function __construct(&$_db)
	{
		parent::__construct('#__support_dl', 'id', $_db);
	}
}