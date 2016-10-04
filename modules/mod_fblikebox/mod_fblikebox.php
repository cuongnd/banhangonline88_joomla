<?php
/*------------------------------------------------------------------------
# mod_fblikebox
# ------------------------------------------------------------------------
# @author - Twitter Slider
# @license - http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
# copyright Copyright (C) 2013 TwitterSlider.com. All Rights Reserved.
# Websites: http://twitterslider.com/
# Technical Support:  Forum - http://twitterslider.com/index.php/forum
-------------------------------------------------------------------------*/
// no direct access
defined( '_JEXEC' ) or die( 'Restricted access' );

if($params->get("fbDynamicLocale", 0)) {
    $lang   = JFactory::getLanguage();
    $locale = $lang->getTag();
    $locale = str_replace("-","_",$locale);
} else {
    $locale = $params->get("fbLocale", "en_US");
}

$facebookLikeAppId ="";
if($params->get("facebookLikeAppId")) {
    $facebookLikeAppId = "&amp;appId=" . $params->get("facebookLikeAppId");
}

// Make Facebook Like Box responsive
if($params->get("facebookResponsive", 0) ) { 
    $css = '
    #fb-root {
      display: none;
    }
    
    .fb_iframe_widget, .fb_iframe_widget span, .fb_iframe_widget span iframe[style] {
      width: 100% !important;
    }';

    $doc = JFactory::getDocument();
    $doc->addStyleDeclaration($css);
}

require JModuleHelper::getLayoutPath('mod_fblikebox', $params->get('layout', 'default'));