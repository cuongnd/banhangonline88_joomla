<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package   MaQma_Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license   GNU General Public License version 2 or later; see LICENSE.txt
 *
 */

defined('_JEXEC') or die('Direct Access to this location is not allowed.');

$lang = JFactory::getLanguage();
$language_tag = $lang->getTag();
$lang->load('com_maqmahelpdesk', JPATH_SITE, $language_tag, true);

$database = JFactory::getDBO();
$user = JFactory::getUser();
$prev_lvl = '';
$output = '';

$sql = "SELECT COUNT(*), p.level
		FROM #__support_permission p,
			 #__support_workgroup w
		WHERE p.id_user='" . $user->id . "'
		  AND w.id = p.id_workgroup
		LIMIT 0, 1";
$database->setQuery($sql);
$is_support = ($database->loadResult() ? 1 : 0);

if ($is_support)
{
	$support = $database->loadObject();

	$sql = "SELECT DISTINCT(p.id_user), s.time, u.name, p.level
			FROM #__support_permission AS p,
				 #__users AS u,
				 #__session AS s
			WHERE p.id_workgroup=p.id_workgroup
			  AND u.id=p.id_user
			  AND s.userid=u.id
			  AND p.level>={$support->level}
			  AND s.client_id=0
			ORDER BY p.level, u.name";
	$database->setQuery($sql);
	$rows = $database->loadObjectList();

	for ($i = 0; $i < count($rows); $i++)
	{
		$row = $rows[$i];
		if ($prev_lvl != $row->level)
		{
			$prev_lvl = $row->level;
			$output .= '<br /><h4>Level ' . $row->level . '</h4>';
		}
		$output .= '&nbsp;&nbsp;&bull;&nbsp;' . $row->name . ($row->id_user == $user->id ? ' <span style="color:#ff0000;font-size:10px;">(me)</span>' : '') . ': <b>' . date("H:i", $row->time) . '</b><br />';
	}
}

echo $output;
