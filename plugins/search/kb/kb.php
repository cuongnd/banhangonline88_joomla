<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 * $Id: kb.php 646 2012-05-22 08:20:58Z pdaniel $
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

class plgSearchKB extends JPlugin
{
    public function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);
    }

    function onContentSearchAreas()
    {
        /*static $areas = array(
              'kb' => JText::_('kb')
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

        $section = JText::_('kb');

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
                    $wheres2[] = "LOWER(a.keywords) LIKE '%$word%'";
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
                $order = 'a.kbtitle ASC';
                break;

            case 'category':
                $order = 'd.wkdesc ASC, c.name ASC, a.kbtitle ASC';
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

        $query = "SELECT a.kbtitle AS title,"
            . "\n a.content AS text,"
            . "\n a.date_created AS created,"
            . "\n CONCAT_WS( ' / ', '$section', d.wkdesc, c.name ) AS section,"
            . "\n '2' AS browsernav,"
            . "\n CONCAT('index.php?option=com_maqmahelpdesk&Itemid=', $item_id, '&id_workgroup=', d.id, '&task=kb_view&id=', a.id) AS href"
            . "\n FROM #__support_kb AS a"
            . "\n INNER JOIN #__support_kb_category AS b ON b.id_kb = a.id"
            . "\n INNER JOIN #__support_category AS c ON c.id = b.id_category"
            . "\n INNER JOIN #__support_workgroup AS d ON d.id = c.id_workgroup"
            . "\n WHERE ($where)"
            . "\n AND a.publish = 1"
            . "\n ORDER BY $order";
        $database->setQuery($query);
        $rows = $database->loadObjectList();

        return $rows;
    }
}
