<?php
/**
 * @package ShareThisBar Plugin for Joomla! 3.x
 * @version $Id: sharethisbar.php 3.7 2016-02-27 17:00:33Z Dusanka $
 * @author Dusanka Ilic
 * @copyright (C) 2016 - Dusanka Ilic, All rights reserved.
 * @authorEmail: gog27.mail@gmail.com
 * @license GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html, see LICENSE.txt
 **/

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');


/**
 * Joomla! ShareThisBar Plugin
 *
 * @package  ShareThisBar Plugin
 *
 */
class plgSystemSharethisbar extends JPlugin
{

    /**
     * Constructor
     *
     * @access    protected
     * @param    object $subject The object to observe
     * @param    array $config An array that holds the plugin configuration
     * @since
     */
    function __construct(& $subject, $config)
    {
        parent::__construct($subject, $config);

        $config = JFactory::getConfig();
        $app = JFactory::getApplication();

        if ($app->isAdmin() && (JRequest::getVar('option') == 'com_plugins') && (JRequest::getVar('view') == 'plugin')) {

            $lang = JFactory::getLanguage();

            if (!$this->params->get('langmode')) {
                $activateLanguage = $this->params->get('langcode');
                $lang->setLanguage($activateLanguage);
            }

        } // asAdmin

    }

    /** showInMenu
     * @return:
     * true to show ShareThisBar in menu
     * false not to show ShareThisBar in menu
     * @since 3.5
     */
    function showInMenu()
    {

        $app = JFactory::getApplication();

        // array contains menus to exclude
        $exclMenus = array();

        $excl_menu1 = $this->params->get('excl_menu');

        if ($excl_menu1 == '') {
            return true;
        }

        if (is_array($excl_menu1)) {
            $exclMenus = $excl_menu1;
        } else {
            $exclMenus[] = $excl_menu1;
        }

        $menuexin = $this->params->get('menuexin');

        $menu = $app->getMenu();
        $active = $menu->getActive();

        // Menus for exclusion/inclusion selected ?
        if (count($exclMenus)) {

            // active menu in selected ?
            if (in_array((string)$active->id, (array)$exclMenus)) {

                if ($menuexin) {
                    return false;
                } else {
                    return true;
                }

            } else {

                if ($menuexin) {
                    return true;
                } else {
                    return false;
                }

            }

        }  //   endif (count($exclMenus)) {...

        return true;

    }

    /**
     * showInURL
     * @return:
     * true to show ShareThisBar in URI
     * false not to show ShareThisBar in URI
     * @since 3.5
     */

    function showInURL()
    {

        $urlsExclude = $this->params->get('excludeurls');

        if ($urlsExclude == '') return true;

        // 1 for exclude, 0 for include
        $uriexin = $this->params->get('uriexin');

        // URIs for which bar is not/showing up, separated with new line.
        $arrUrls = split("[\n]", $urlsExclude);

        $urii =& JURI::getInstance();
        $uri = $urii->toString();

        for ($x = 0; $x < count($arrUrls); $x++) {
            if ($arrUrls[$x] != '') {
                $arrUrls1[] = trim($arrUrls[$x]);
            }
        }
        $arrUrls = $arrUrls1;

        if (count($arrUrls)) {

            // is active uri in entered uris ?
            if (in_array($uri, $arrUrls)) {

                if ($uriexin) {
                    return false;
                } else {
                    return true;
                }

            } else {

                if ($uriexin) {
                    return true;
                } else {
                    return false;
                }

            }

        } //if (count($arrUrls)) {

        return true;
    }

    /**
     * onAfterDispatch handler
     *
     * @access    public
     * @return null
     */
    function onAfterDispatch()
    {
        $app = JFactory::getApplication();
        if ($app->isAdmin()) {
            // Load mooTools. I MooTools Core i MooTools More (drugi argument je true).
            // \media\system\js\mootools-core.js i mootools-more.js
            JHtml::_('behavior.framework', true);
            return;
        }

        // Check to see if it has already been loaded.
        static $loaded;
        if (!empty($loaded)) {
            return;
        }
        JHtml::_('behavior.framework',true);
        JHtml::_('behavior.formvalidation');
        JHtml::_('behavior.modal');

        $arrParams = $this->params->toArray();

        $iShowInMenu = (int)$this->showInMenu();
        $iShowInURL = (int)$this->showInURL();

        if ($iShowInMenu && $iShowInURL) {

            $js = "
           /* ShareThisBar plugin for Joomla 2.5 
              Copyright (C) 2012 Dusanka Ilic. All rights reserved. 
              AuthorSite : extensionshub.com
              License GNU/GPLv3 */ \n";
            $js .= " window.addEvent('domready', function() {";

            $js .= "SocialBarObj = new SocialBar(";

            $strParms = "";
            foreach ($arrParams as $k => $v) {

                if ($v != '') {

                    if (is_array($v)) {

                        $strParms .= $k . ":[";

                        foreach ($v as $k1 => $v1) {
                            $strParms .= "'" . $v1 . "',";
                        }

                        //Trim last comma
                        $strParms = rtrim($strParms, ',');

                        $strParms .= "]";
                        $strParms .= ",";

                    } else {

                        /* Moram da uklonim \n i \r za novi red inače puca */
                        $replacements = array('/' => '\/', '"' => '\"');
                        $replacements["'"] = "\'";
                        $replacements["\n"] = "";
                        $replacements["\r"] = "";
                        $strParms .= $k . ":'" . strtr($v, $replacements) . "',";

                    }  // if (is_array($v)) {

                }   // end if ($v != '') {

            }  // end foreach $arrParams    

            //Trim last comma
            $strParms = rtrim($strParms, ',');

            $js .= "{" . $strParms . "}); ";

            $js .= " 
         
            // Call prepareSocialNetworks() only if at least one button is selected.  
            // od 3.7: if (SocialBarObj.arrActiveBtns.length > 0) {    
            if ((SocialBarObj.arrActiveBtns.length > 0) && (SocialBarObj.boolShowBar == 1)) { 
        
             SocialBarObj.prepareSocialNetworks();
             
             var fbdl = parseInt(SocialBarObj.options.delaybarshowup)*1000;
             
             if (fbdl > 0)  
             {               
                // Call function with delay. Delay in seconds is determined in plugin parameters.   
                (function(){SocialBarObj.correctBar();}).delay(fbdl);    
             } else {   
                SocialBarObj.correctBar();  
             }
                    
            }  ";

            $js .= " (function() {window.fireEvent('resize');})(); ";

            $js .= " }); // domready  ]]> ";

            $document = JFactory::getDocument();

            // Get bar style.
            $btnStyle = (string)$this->params->get('btnstyle');

            // After installation, need style.
            if ($btnStyle == '') {
                $btnStyle = ".stbbtnstyle {margin:3px auto!important; text-align:center!important;}
               /*Please use suffix !important*/  #socialbar {background-color:#fefefe!important;border:1px solid #cbcbcb!important;
               box-shadow:1px 1px 3px #dbdbdb!important;border-radius:5px 5px 5px 5px!important;z-index:100000!important;} #stbpi {margin:13px 0 0 0!important}";
            }

            // Load the css style into the document head.
            $document->addStyleDeclaration($btnStyle);

            // Javascript for stb.
            $document->addScript('plugins/system/sharethisbar/js/stb.js?v=3.7');

            // Load the script into the document head.
            $document->addScriptDeclaration($js);


            $doctype = $document->getType();

            // Only render for HTML output
            if ($doctype !== 'html') {
                return;
            }

            // FB meta tag og:image
            $fbimg = (string)$this->params->get('fb_ogimg');
            if ($fbimg != '') {
                $custTag = '<meta property="og:image" content="' . $fbimg . '" />';
                $document->addCustomTag($custTag);
            }

            // FB meta tag fb:app_id
            $fbAppkey = (string)$this->params->get('fb_appkey');
            if ($fbAppkey) {
                $custTag = '<meta property="fb:app_id" content="' . $fbAppkey . '" />';
                $document->addCustomTag($custTag);
            }

            // Ensure the files aren't loaded more than once.
            $loaded = true;

        }  // endif   if ($this->showInURL() && $this->showInMenu())

    }  // onAfterDispatch


    function onAfterRender()
    {
        $app = JFactory::getApplication();

        if ($app->isAdmin()) {
            return;
        }

        $document = JFactory::getDocument();
        $doctype = $document->getType();

        // Only render for HTML output
        /*if ($doctype !== 'html') {
            return;
        }*/

        $body = JResponse::getBody();

        if ($this->params->get('fb_xmlns')) {
            // If user wants Open Graph meta tags.
            $body = str_replace("<html ", "<html xmlns:fb=\"http://www.facebook.com/2008/fbml\" xmlns:og=\"http://ogp.me/ns#\" ", $body);
        }

        JResponse::setBody($body);
    }

}  //class plgSystemSocialbar

