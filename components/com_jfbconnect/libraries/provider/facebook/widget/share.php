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

class JFBConnectProviderFacebookWidgetShare extends JFBConnectProviderFacebookWidget
{
    var $name = "Share";
    var $systemName = "share";
    var $className = "jfbcshare jfbcsharedialog";
    var $tagName = "jfbcshare";
    var $examples = array (
        '{JFBCShare}',
        '{JFBCShare href=http://www.sourcecoast.com layout=button width=400}'
    );

    protected function getTagHtml()
    {
        $tag = '<div class="fb-share-button"';
        $tag .= $this->getField('href', 'url', null, SCSocialUtilities::getStrippedUrl(), 'data-href');
        $tag .= $this->getField('width', null, null, '', 'data-width');
        $tag .= $this->getField('layout', null, null, '', 'data-type');
        $tag .= '></div>';
        return $tag;
    }
}
