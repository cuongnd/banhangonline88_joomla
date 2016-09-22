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

class HelpdeskKB
{
	static function GetVotes($id, $table)
	{
		$database = JFactory::getDBO();

		$database->setQuery("SELECT COUNT(*) FROM #__support_rate WHERE id_table='" . $id . "' AND source=" . $database->quote($table));
		return $database->loadResult();
	}

	static function getArticlesForCategory($category)
	{
		global $is_manager;

		$database = JFactory::getDBO();
		$user = JFactory::getUser();
		$supportConfig = HelpdeskUtility::GetConfig();
		$is_support = HelpdeskUser::IsSupport();
		$id_workgroup = JRequest::getInt('id_workgroup', 0);

		$sql = "SELECT k.id, k.kbcode as code, k.kbtitle as title, k.views, u.name as author, k.date_created, k.date_updated, k.content
				FROM #__support_category as c
					 INNER JOIN #__support_kb_category AS kc ON c.id=kc.id_category
					 INNER JOIN #__support_kb AS k           ON kc.id_kb=k.id
					 INNER JOIN #__users AS u                ON u.id=k.id_user
				WHERE c.`show`='1'
				  AND c.kb=1
				  AND c.id_workgroup='" . $id_workgroup . "'
				  AND k.publish='1' " . ($supportConfig->kb_approvement && $is_manager < 7 ? "AND k.approved=1" : "") . "
				  AND kc.id_category='" . $category . "'
				  AND k.anonymous_access<=" . (!$user->id ? '0' : ($is_support ? '2' : '1')) . "
				  AND (k.faq='1'" . ($supportConfig->faq_kb_hits ? " OR ((k.faq='0' OR k.faq='1') AND k.views>=" . $supportConfig->faq_kb_nhits . ")" : "") . ")
				GROUP BY k.id, k.kbcode, k.kbtitle, k.views
				ORDER BY k.date_updated DESC";
		$database->setQuery($sql);
		$articles = $database->loadObjectList();

		return $articles;
	}
}
