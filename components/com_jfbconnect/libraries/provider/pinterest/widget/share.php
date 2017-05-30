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

class JFBConnectProviderPinterestWidgetShare extends JFBConnectProviderPinterestWidget
{
    var $name = "Share";
    var $systemName = "share";
    var $className = "pinterest sc_pinterest";
    var $tagName = "scpinterestshare";
    var $examples = array (
        '{SCPinterestShare href=http://www.sourcecoast.com image=http://www.sourcecoast.com/templates/sourcecoast/images/logo.png pin_count=above desc=Learn more about JFBConnect}'
    );

    public function getTagHtml()
    {
        if($this->fields->exists('image') && $this->getParamValue('image') != "")
        {
            self::$needsJavascript = true;

            $url = $this->getParamValueEx('href', 'url', null, SCSocialUtilities::getStrippedUrl());
            $layout = $this->getParamValue('layout');

            $url = rawurlencode($url);
            $image = rawurlencode($this->getParamValue('image'));
            $desc = rawurlencode($this->getParamValue('desc'));

            $tagButtonText = '<a href="//pinterest.com/pin/create/button/?url=' . $url;
            if ($image)
                $tagButtonText .= '&media=' . $image;
            if ($desc)
                $tagButtonText .= '&description=' . $desc;
            $tagButtonText .= '"';
            if($layout)
            {
                $tagButtonText .= SCEasyTags::getShareButtonLayout('pinterest', $layout, '"');
            }
            else
            {
                $tagButtonText .= $this->getField('data-pin-config', 'pin_count', null, 'none', 'data-pin-config');
            }
            if(strpos($tagButtonText, 'above')!== false)
            {
                $pinItButton = 'pinit_fg_en_rect_gray_28.png';
                $tagButtonText .= $this->getField('data-pin-height', '', null, '28', 'data-pin-height');
            }
            else
                $pinItButton = 'pin_it_button.png';

            $tagButtonText .= ' data-pin-do="buttonPin" ><img src="//assets.pinterest.com/images/pidgets/'.$pinItButton.'" alt="Share on Pinterest"/></a>';
        } else
            $tagButtonText = '';
        return $tagButtonText;
    }
}
