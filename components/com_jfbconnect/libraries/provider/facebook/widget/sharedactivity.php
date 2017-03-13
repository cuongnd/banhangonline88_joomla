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

class JFBConnectProviderFacebookWidgetSharedactivity extends JFBConnectProviderFacebookWidget
{
    var $name = "Shared Activity";
    var $systemName = "sharedactivity";
    var $className = "jfbcsharedactivity";
    var $tagName = "jfbcsharedactivity";
    var $examples = array (
        '{JFBCSharedActivity}',
        '{JFBCSharedActivity width=300 height=300 font=Arial}'
    );

    protected function getTagHtml()
    {
        $tag = '<div class="fb-shared-activity"';
        $tag .= $this->getField('width', null, null, '', 'data-width');
        $tag .= $this->getField('height', null, null, '', 'data-height');
        $tag .= $this->getField('font', null, null, '', 'data-font');
        $tag .= '></div>';
        return $tag;
    }
}
