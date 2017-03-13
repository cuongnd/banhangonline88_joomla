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

class JFBConnectProviderTwitterWidgetShare extends JFBConnectWidget
{
    var $name = "Share";
    var $systemName = "share";
    var $className = "sc_twittershare";
    var $tagName = "sctwittershare";
    var $examples = array (
        '{SCTwitterShare}',
        '{SCTwitterShare href=http://www.sourcecoast.com text=SourceCoast makes great Joomla extensions data-count=horizontal via=sourcecoast related=mandreae hashtags=Joomla,Facebook size=medium dnt=false}'
    );

    protected function getTagHtml()
    {
        JFBCFactory::addStylesheet('jfbconnect.css');

        $tagButtonText = '<a href="http://twitter.com/share" class="twitter-share-button" ';
        $tagButtonText .= $this->getField('href', 'url', null, SCSocialUtilities::getStrippedUrl(), 'data-url');

        if($this->fields->exists('layout'))
            $tagButtonText .= SCEasyTags::getShareButtonLayout('twitter', $this->fields->get('layout'), '"');
        else
            $tagButtonText .= $this->getField('data-count', 'data_count', null, '', 'data-count');

        $tagButtonText .= $this->getField('via', null, null, '', 'data-via');
        $tagButtonText .= $this->getField('text', null, null, '', 'data-text');
        $tagButtonText .= $this->getField('related', null, null, '', 'data-related');
        $tagButtonText .= $this->getField('lang', null, null, '', 'data-lang');
        $tagButtonText .= $this->getField('counturl', null, null, '', 'data-counturl');
        $tagButtonText .= $this->getField('hashtags', null, null, '', 'data-hashtags');
        $tagButtonText .= $this->getField('size', null, null, '', 'data-size');
        $tagButtonText .= $this->getField('dnt', null, 'boolean', 'false', 'data-dnt');
        $tagButtonText .= '>Tweet</a>';

        return $tagButtonText;
    }
}
