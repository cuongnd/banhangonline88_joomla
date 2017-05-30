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

class JFBConnectProviderLinkedinWidgetShare extends JFBConnectWidget
{
    var $name = "Share";
    var $systemName = "share";
    var $className = "jlinkedShare";
    var $tagName = "jlinkedshare";
    var $examples = array(
        '{JLinkedShare}',
        '{JLinkedShare counter=top}',
        '{JLinkedShare href=http://www.sourcecoast.com/jlinked/ counter=right showzero=0}'
    );

    protected function getTagHtml()
    {
        JFBCFactory::addStylesheet('jfbconnect.css');
        $tag = '<script type="IN/Share"';
        $tag .= $this->getField('href', 'url', null, SCSocialUtilities::getStrippedUrl(), 'data-url');
        $tag .= $this->getField('showzero', 'show_zero', 'boolean', 'false', 'data-showzero');
        if($this->fields->exists('layout'))
            $tag .= SCEasyTags::getShareButtonLayout('linkedin', $this->fields->get('layout'), '"');
        else
            $tag .= $this->getField('counter', null, null, '', 'data-counter');

        $tag .= ' data-onsuccess="jfbc.social.linkedin.share"';
        $tag .= '></script>';
        return $tag;
    }
}
