<?php
/**
 * @package         SourceCoast Extensions
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('sourcecoast.utilities');

define('SC_TYPE_ALL', '0');
define('SC_TYPE_INCLUDE', '1');
define('SC_TYPE_EXCLUDE', '2');
define('SC_VIEW_NONE', "0");
define('SC_VIEW_TOP', "1");
define('SC_VIEW_BOTTOM', "2");
define('SC_VIEW_BOTH', "3");
define('SC_INTRO_CHARS', '1');
define('SC_INTRO_WORDS', '2');

class SCArticleContent
{
    static function getCurrentURL($article, $isJoomla)
    {
        if ($isJoomla)
            return SCArticleContent::_getCurrentArticleURL($article);
        else
            return SCArticleContent::_getCurrentItemURL($article);
    }

    private static function _getCurrentArticleURL($article)
    {
        require_once(JPATH_SITE . '/components/com_content/helpers/route.php');

        if (isset($article->catslug) && isset($article->slug))
            $url = ContentHelperRoute::getArticleRoute($article->slug, $article->catslug);
        else if (isset($article->catid) && isset($article->slug))
            $url = ContentHelperRoute::getArticleRoute($article->slug, $article->catid);
        else if (isset($article->slug))
            $url = ContentHelperRoute::getArticleRoute($article->slug);
        else
            $url = 'index.php?option=com_content&view=article&id=' . $article->id;

        $url = SCArticleContent::_getCompleteURL($url);
        return $url;
    }

    private static function _getCurrentItemURL($article)
    {
        require_once(JPATH_SITE . '/components/com_k2/helpers/route.php');
        $url = K2HelperRoute::getItemRoute($article->id . ":" . urlencode($article->alias), $article->catid);
        $url = SCArticleContent::_getCompleteURL($url);
        return $url;
    }

    private static function _getCompleteURL($url)
    {
        $url = JRoute::_($url, true);
        $jUri = JURI::getInstance();
        $url = rtrim($jUri->toString(array('scheme', 'host', 'port')), '/') . $url;
        $url = SCSocialUtilities::forceScheme($url);
        return $url;
    }
}