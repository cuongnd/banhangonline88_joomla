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

class JFBConnectProviderGoogleWidgetCommunityBadge extends JFBConnectWidget
{
    var $name = "Community Badge";
    var $systemName = "communitybadge";
    var $className = "sc_gcommunitybadge";
    var $tagName = "scgooglecommunitybadge";
    var $examples = array (
        '{SCGoogleCommunityBadge href=https://plus.google.com/communities/104516377666905956948}',
        '{SCGoogleCommunityBadge href=https://plus.google.com/communities/104516377666905956948 layout=portrait theme=light showcoverphoto=true showtagline=true width=300}'
    );

    protected function getTagHtml()
    {
        $tag = '<div class="g-community"';
        $tag .= $this->getField('href', 'url', null, '', 'data-href');
        $tag .= $this->getField('layout', null, null, 'portrait', 'data-layout');
        $tag .= $this->getField('theme', null, null, 'light', 'data-theme');
        $tag .= $this->getField('showowners', null, 'boolean', 'false', 'data-showowners');
        $tag .= $this->getField('showcoverphoto', null, 'boolean', 'true', 'data-showphoto');
        $tag .= $this->getField('showtagline', null, 'boolean', 'true', 'data-showtagline');
        $tag .= $this->getField('width', null, null, '300', 'data-width');
        $tag .= '></div>';

        return $tag;
    }
}
