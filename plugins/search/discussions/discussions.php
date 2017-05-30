<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 * $Id: discussions.php 646 2012-05-22 08:20:58Z pdaniel $
 * $LastChangedDate: 2012-05-22 09:20:58 +0100 (Ter, 22 Mai 2012) $
 *
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

$lang = JFactory::getLanguage();
$language_tag = $lang->getTag();
$lang->load('com_maqmahelpdesk', JPATH_SITE, $language_tag, true);

jimport('joomla.plugin.plugin');

$option = JRequest::getVar('option', '', 'REQUEST', 'string');

class plgSearchDiscussions extends JPlugin
{
    function onContentSearchAreas()
    {
        /*$description = JText::_('discussions');
          static $areas = array(
              'discussions' => $description
          );
          return $areas;*/
    }

    function onContentSearch($text, $phrase = '', $ordering = '', $areas = null)
    {
        $database = JFactory::getDBO();

        $text = trim($text);
        if ($text == '') {
            return array();
        }

        $section = JText::_('discussions');

        $wheres = array();
        switch ($phrase) {
            case 'exact':
                $wheres2 = array();
                $wheres2[] = "LOWER(a.content) LIKE '%$text%'";
                $where = '(' . implode(') OR (', $wheres2) . ')';
                break;

            case 'all':
            case 'any':
            default:
                $words = explode(' ', $text);
                $wheres = array();
                foreach ($words as $word) {
                    $wheres2 = array();
                    $wheres2[] = "LOWER(a.content) LIKE '%$word%'";
                    $wheres2[] = "LOWER(a.tags) LIKE '%$word%'";
                    $wheres[] = implode(' OR ', $wheres2);
                }
                $where = '(' . implode(($phrase == 'all' ? ') AND (' : ') OR ('), $wheres) . ')';
                break;
        }

        switch ($ordering) {
            case 'oldest':
                $order = 'a.date_created ASC';
                break;

            case 'popular':
                $order = 'a.views DESC';
                break;

            case 'alpha':
                $order = 'a.title ASC';
                break;

            case 'category':
                $order = 'a.title ASC';
                break;

            case 'newest':
            default:
                $order = 'a.date_created DESC';
        }

        // Get the Workgroups the user can access to filter below
        // TODO ...

        // Get the Item ID
        $database->setQuery("select id from `#__menu` where link='index.php?option=com_maqmahelpdesk'");
        $item_id = $database->loadResult();

        $query = "SELECT a.title,"
            . "\n a.content AS metadesc,"
            . "\n a.tags AS metakey,"
            . "\n a.content AS text,"
            . "\n a.date_created AS created,"
            . "\n CONCAT_WS( ' / ', '$section', d.wkdesc ) AS section,"
            . "\n 'slug' AS slug,"
            . "\n 'catslug' AS catslug,"
            . "\n '2' AS browsernav,"
            . "\n CONCAT('index.php?option=com_maqmahelpdesk&Itemid=', $item_id, '&id_workgroup=', d.id, '&task=discussions_view&id=', a.id) AS href"
            . "\n FROM #__support_discussions AS a"
            . "\n LEFT JOIN #__support_workgroup AS d ON d.id = a.id_workgroup"
            . "\n WHERE ($where)"
            . "\n AND a.published = 1"
            . "\n ORDER BY $order";

        $database->setQuery($query);
        $rows = $database->loadObjectList();

        return $rows;
    }
}
