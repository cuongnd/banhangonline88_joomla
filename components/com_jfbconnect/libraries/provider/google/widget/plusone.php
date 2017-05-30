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

class JFBConnectProviderGoogleWidgetPlusone extends JFBConnectWidget
{
    var $name = "Plus One";
    var $systemName = "plusone";
    var $className = "sc_gplusone";
    var $tagName = "scgoogleplusone";
    var $examples = array (
        '{SCGooglePlusOne}',
        '{SCGooglePlusOne href=http://www.sourcecoast.com annotation=inline size=standard width=475 align=left expandTo=top,right recommendations=true}'
    );

    protected function getTagHtml()
    {
        JFBCFactory::addStylesheet('jfbconnect.css');
        $tag = '<div class="g-plusone"';
        if($this->fields->exists('layout'))
        {
            $tag .= SCEasyTags::getShareButtonLayout('google', $this->fields->get('layout'), '"');
        }
        else
        {
            $tag .= $this->getField('size', null, null, '', 'data-size');
            $tag .= $this->getField('annotation', null, null, '', 'data-annotation');

        }
        $tag .= $this->getField('href', 'url', null, SCSocialUtilities::getStrippedUrl(), 'data-href');
        $tag .= $this->getField('width', null, null, '', 'data-width');
        $tag .= $this->getField('align', null, null, '', 'data-align');
        $tag .= $this->getField('expandTo', null, null, '', 'expandTo');
        $tag .= $this->getField('recommendations', null, 'boolean', 'true', 'data-recommendations');
        $tag .= ' data-callback="plusone_callback"';
        $tag .= '></div>';

        return $tag;
    }
}
