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

class JFBConnectProviderGoogleWidgetHangout extends JFBConnectWidget
{
    var $name = "Hangout";
    var $systemName = "hangout";
    var $className = "sc_ghangout";
    var $tagName = "scgooglehangout";
    var $examples = array (
        '{SCGoogleHangout}',
        '{SCGoogleHangout initial_apps=[{\'app_id\' : \'184219133185\', \'start_data\' : \'dQw4w9WgXcQ\', \'app_type\' : \'ROOM_APP\' }] hangout_type=onair widget_size=175}'
    );

    protected function getTagHtml()
    {

        //build initial apps js object
        $app_id = $this->getParamValueEx('app_id', null, null, '');
        $start_data = $this->getParamValueEx('start_data', null, null, '');
        $app_type = $this->getParamValueEx('app_type', null, null, 'ROOM_APP');
        $ini_apps = "[{ app_id : '".$app_id."', start_data : '".$start_data."', 'app_type' : '".$app_type."' }]";

        $tag = '<div class="g-hangout" data-render="createhangout"';
        $tag .= $this->getField('topic', null, null, '', 'data-topic');
        $tag .= $this->getField('initial_apps', null, null, $ini_apps, 'data-initial_apps');
        $tag .= $this->getField('hangout_type', null, null, 'normal', 'data-hangout_type');
        $tag .= $this->getField('widget_size', null, null, '136', 'data-widget_size');
        $tag .= '></div>';

        return $tag;
    }

    public function getHeadData()
    {
        $head = '';
        $this->needsJavascript = false;

        if (!defined(HANGOUTJS))
        {
            define(HANGOUTJS, true);

            $uri = JURI::getInstance();
            $scheme = $uri->getScheme();
            //$lang = $this->getParamValueEx('lang', null, null, 'en-US');

            $javascript = "<script type=\"text/javascript\">
                  (function() {
                    var po = document.createElement('script'); po.type = 'text/javascript'; po.async = true;
                    po.src = '" . $scheme . "://apis.google.com/js/platform.js';
                    var s = document.getElementsByTagName('script')[0]; s.parentNode.insertBefore(po, s);
                  })();
                </script>";

            $head .= $javascript;
        }

        return $head;
    }
}
