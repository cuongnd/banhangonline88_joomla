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

class JFBConnectProviderFacebookWidgetEmbeddedposts extends JFBConnectProviderFacebookWidget
{
    var $name = "Embedded Posts";
    var $systemName = "embeddedposts";
    var $className = "jfbcembeddedposts";
    var $tagName = "jfbcembeddedposts";
    var $examples = array (
        '{JFBCEmbeddedPosts}',
        '{JFBCEmbeddedPosts href=https://www.facebook.com/FacebookDevelopers/posts/10151471074398553}'
    );

    protected function getTagHtml()
    {
        $tag = '';
        $href = $this->getField('href', 'url', null, '', 'data-href');

        if($href)
            $tag = '<div class="fb-post"' . $href . '></div>';

        return $tag;
    }
}
