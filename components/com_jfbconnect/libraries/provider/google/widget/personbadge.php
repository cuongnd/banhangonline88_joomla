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

class JFBConnectProviderGoogleWidgetPersonBadge extends JFBConnectWidget
{
    var $name = "Person Badge";
    var $systemName = "personbadge";
    var $className = "sc_gpersonbadge";
    var $tagName = "scgooglepersonbadge";
    var $examples = array (
        '{SCGooglePersonBadge href=https://plus.google.com/+JonathanBeri}',
        '{SCGooglePersonBadge href=https://plus.google.com/+JonathanBeri layout=portrait theme=light showcoverphoto=true showtagline=true width=300}'
    );

    protected function getTagHtml()
    {
        $tag = '<div class="g-person"';
        $tag .= $this->getField('href', 'url', null, '', 'data-href');
        $tag .= $this->getField('layout', null, null, 'portrait', 'data-layout');
        $tag .= $this->getField('theme', null, null, 'light', 'data-theme');
        $tag .= $this->getField('showcoverphoto', null, 'boolean', 'true', 'data-showcoverphoto');
        $tag .= $this->getField('showtagline', null, 'boolean', 'true', 'data-showtagline');
        $tag .= $this->getField('width', null, null, '300', 'data-width');
        $tag .= '></div>';

        return $tag;
    }
}
