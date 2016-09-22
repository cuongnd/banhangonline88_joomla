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

class MaQmaModelAnnouncements
{
	static function rows()
	{
	}

	static function edit()
	{
	}

	static function delete()
	{
	}

	static function state($cid, $publish)
	{
		$database = JFactory::getDBO();

		// Convert array to string
		$cids = implode(',', $cid);

		// Execute query
		$sql = "UPDATE #__support_announce
				SET `sent`='$publish'
				WHERE id IN (" . $database->quote($cids) . ")";
		$database->setQuery($sql);

		return $database->query();
	}

	static function prepare()
	{
	}

	static function send()
	{
	}
}
