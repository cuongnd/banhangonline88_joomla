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

class JFBConnectToolbar
{
    private $buttons = array();
    private $enabled;
    public function __construct()
    {
        $this->enabled = !JFactory::getUser()->guest && JFBCFactory::config()->get('social_toolbar_enable') &&
                JFactory::getUser()->authorise('jfbconnect.channels.post', 'com_jfbconnect');
    }

    public function onAfterDispatch()
    {
        if ($this->enabled)
        {
            $path = JPATH_SITE . '/components/com_jfbconnect/libraries/toolbar/button/';
            $files = JFolder::files($path, '\.php$');
            // Probably need to add some way to order these things..
            foreach ($files as $f)
            {
                $class = 'JFBConnectToolbarButton' . ucfirst(str_replace('.php', '', $f));
                if (class_exists($class))
                {
                    $obj = new $class();
                    $this->buttons[$obj->order] = $obj;
                }
            }
            ksort($this->buttons);

            $doc = JFactory::getDocument();
            JFBCFactory::addStylesheet('jfbconnect.css');
            if (JFBCFactory::config()->get('facebook_display_errors'))
            {
//                $doc->addScript('media/sourcecoast/js/jquery-ui.js');
                $doc->addStyleSheet('media/sourcecoast/css/jquery-ui/jquery-ui.css');
            }
            else
            {
//                $doc->addScript('media/sourcecoast/js/jquery-ui.min.js');
                $doc->addStyleSheet('media/sourcecoast/css/jquery-ui/jquery-ui.min.css');
            }
        }
    }

    public function onAfterRender()
    {
        if ($this->enabled)
        {
            $buttons = array();
            $buttonHtml = array();
            $buttonJavascript = array();
            foreach ($this->buttons as $b)
            {
                $buttons[] = '<button name="' . $b->systemName . '">' . $b->displayName . '</button>';
                $buttonHtml[] = $b->html();
                $buttonJavascript[$b->systemName] = $b->javascript();
            }
            $html = '<div id="social-toolbar" class="ui-widget-header ui-corner-all" style="position:fixed; bottom: 10px">';
            $html .= implode($buttons);
            $html .= '</div>';
            $html .= implode($buttonHtml);

            $html .= $this->generateJavascript($buttonJavascript);

            $body = JResponse::getBody();
            $body = str_ireplace("</body>", $html . "</body>", $body);
            JResponse::setBody($body);
        }
    }

    private function generateJavascript($jsArray)
    {
        $code = '<script>jfbc.toolbar = {};';
        foreach ($jsArray as $name => $javascript)
        {
            $code .= $javascript;
            $code .= 'jfbc.toolbar.' . $name . ' = ' . $name . ';';

        }
        $code .= '</script>';
        return $code;
    }
}