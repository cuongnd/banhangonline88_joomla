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

class JFBConnectProviderFacebookWidgetAction extends JFBConnectProviderFacebookWidget
{
    var $name = "Action";
    var $systemName = "action";
    var $className = "jfbcaction";
    var $tagName = "jfbcaction";
    var $examples = array(
        "{JFBCAction text=I'm cooking this! inactive=I cooked this!}",
        "{JFBCAction text=I'm cooking this inactive=I cooked this active_image=http://www.sourcecoast.com/images/jfbconnect/logo-jfbconnect.png inactive_image=http://www.sourcecoast.com/images/jlinked/logo-jlinked.png}",
    );

    protected function getTagHtml()
    {
        $actionID = $this->getParamValue('id');

        if ($actionID)
        {
            JFBCFactory::addStylesheet('jquery-ui/jquery-ui-autocomplete.css');
            $actionText = $this->getParamValue('text');
            $triggeredText = $this->getParamValue('inactive');
            $actionImage = $this->getParamValue('active_image');
            $triggeredImage = $this->getParamValue('inactive_image');

            if($triggeredImage)
            {
                $triggeredText = '<img src="'.$triggeredImage.'"'.($triggeredText ? ' alt="'.$triggeredText.'"' : '') .'/>';
            }
            if($actionImage)
                $actionText = '<img src="'.$actionImage.'"'.($actionText ? ' alt="'.$actionText.'"' : '').'/>';

            $jfbcOgActionModel = JModelLegacy::getInstance('OpenGraphAction', 'JFBConnectModel');
            $action = $jfbcOgActionModel->getAction($actionID);
            // Build up the Javascript params list
            $caps = array();
            $capabilities = $action->params->get('og_capabilities');
            if($capabilities)
            {
                foreach ( $capabilities as $cap => $enable)
                    $caps[] = $cap;
            }

            // Add an href parameter to the tag eventually as well and use that instead of this
            $uri = JURI::getInstance();
            $url = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port', 'path', 'query'));

            if ($action->actionReady($jfbcOgActionModel->getUniqueKey($url)))
                $tagText = '<button id="share-button" class="btn" onclick=\'jfbc.share.display_modal(' . $actionID . ', ' . json_encode($caps) . ');\' type="button">' . $actionText . '</button>';
            else
                $tagText = '<button id="share-button" class="btn disabled" type="button">' . $triggeredText . '</button>';
        } else
            $tagText = "JFBCAction ID not found!";

        return $tagText;
    }
}