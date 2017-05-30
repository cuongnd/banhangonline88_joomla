<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

class JFBConnectProviderFacebookWidgetCommentscount extends JFBConnectProviderFacebookWidget
{
    var $name = "Comments Count";
    var $systemName = "commentscount";
    var $className = "jfbccomments_count";
    var $tagName = "jfbccommentscount";
    var $examples = array (
        '{JFBCCommentsCount}',
        '{JFBCCommentsCount href=http://www.sourcecoast.com}'
    );

    protected function getTagHtml()
    {
        //Get the Comments Count string
        $tagString = '<div class="fb-comments-count"';
        $tagString .= $this->getField('href', 'url', null, SCSocialUtilities::getStrippedUrl(), 'data-href');
        $tagString .= '></div>';

        SCStringUtilities::loadLanguage('com_jfbconnect');

        $tag = JText::sprintf('COM_JFBCONNECT_COMMENTS_COUNT', $tagString);
        return $tag;
    }
}
