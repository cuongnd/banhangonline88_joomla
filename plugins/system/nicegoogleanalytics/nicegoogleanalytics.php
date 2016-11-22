<?php
/**
 * Nice Google Analytics Plugin
 * Plugin Version 1.01 - Joomla! Version 1.6
 * Author: Michael Babcock
 * info@trinitronic.com
 * http://trinitronic.com
 * Copyright (c) 2012-2013 TriniTronic. All Rights Reserved.
 * License: GNU/GPL 2, http://www.gnu.org/licenses/gpl-2.0.html
 * Nice User Info is free software. This version may have been modified pursuant
 * to the GNU General Public License, and as distributed it includes or
 * is derivative of works licensed under the GNU General Public License or
 * other free or open source software licenses.
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Direct Access to this location is not allowed.');

jimport('joomla.plugin.plugin');

/**
 * Constructor
 *
 */
class plgSystemNiceGoogleAnalytics extends JPlugin
{

    var $googletrackingid;
    var $siteurl;
    var $googletrackingtype;
    var $googleanalytics_kludge;

    function plgSystemNiceGoogleAnalytics(&$subject, $params)
    {

        parent::__construct($subject, $params);
        $this->googletrackingid = $this->params->get('googletrackingid', '');
        $this->siteurl = $this->params->get('siteurl', JURI::base());
        $this->googletrackingtype = $this->params->get('googletrackingtype', '0');
        $this->googleanalytics_kludge = $this->params->get('googleanalytics_kludge', '');

    }
    function onAfterRender()
    {
        return;
        $app = JFactory::getApplication();

        if ($app->isAdmin() || JRequest::getCmd('task') == 'edit' || JRequest::getCmd('layout') == 'edit') {
            return;
        }

        $c = JResponse::getBody();
        $headpos = stripos($c, '</head>');

        if ($headpos !== false) {

            $ga = $this->createGoogleAnalyticsCode();
            $head = substr($c, 0, $headpos);
            $body = stristr($c, '</head>');
            $c = $head . $ga . $body;
            JResponse::setBody($c);

        }

        return true;
    }


    function createGoogleAnalyticsCode()
    {

        if ($this->googleanalytics_kludge != '') {

            return '<script type="text/javascript">' . $this->googleanalytics_kludge . '</script>';

        } else {

            $r = '';

            switch ($this->googletrackingtype) {

                case 0:

                    //Single domain

                    $r .= '<script type="text/javascript">';

                    $r .= "var _gaq = _gaq || [];
          _gaq.push(['_setAccount', '" . $this->googletrackingid . "']);
          _gaq.push(['_trackPageview']);

          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();

          </script>";

                    break;

                case 1:

                    //One domain with mutiple subdomains

                    $r .= '<script type="text/javascript">';

                    $r .= "var _gaq = _gaq || [];
          _gaq.push(['_setAccount', '" . $this->googletrackingid . "']);
          _gaq.push(['_setDomainName', '" . $this->siteurl . "']);
          _gaq.push(['_trackPageview']);

          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();

          </script>";

                    break;

                case 2:

                    //Multiple top level domains

                    $r .= '<script type="text/javascript">';

                    $r .= "var _gaq = _gaq || [];
          _gaq.push(['_setAccount', '" . $this->googletrackingid . "']);
          _gaq.push(['_setDomainName', '" . $this->siteurl . "']);
          _gaq.push(['_setAllowLinker', true]);
          _gaq.push(['_trackPageview']);

          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();

          </script>";

                    break;

                default:

                    //Single domain

                    $r .= '<script type="text/javascript">';

                    $r .= "var _gaq = _gaq || [];
          _gaq.push(['_setAccount', '" . $this->googletrackingid . "']);
          _gaq.push(['_trackPageview']);

          (function() {
            var ga = document.createElement('script'); ga.type = 'text/javascript'; ga.async = true;
            ga.src = ('https:' == document.location.protocol ? 'https://ssl' : 'http://www') + '.google-analytics.com/ga.js';
            var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(ga, s);
          })();

          </script>";

            }

            return $r;

        }

    }

}

?>
