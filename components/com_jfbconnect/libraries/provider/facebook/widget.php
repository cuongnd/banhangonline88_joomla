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

class JFBConnectProviderFacebookWidget extends JFBConnectWidget
{
    public function render()
    {
        $class[] = "sourcecoast facebook";
        $class[] = $this->systemName;

        //Facebook javascript is added automatically
        JFBCFactory::addStylesheet('jfbconnect.css');
        $tag = $this->getTagHtml();

        if($tag)
        {
            $this->provider->widgetRendered = true;
            if($this->className)
                $class[] = $this->className;

            $classString = implode(' ', $class);
            $tag = '<div class="'.$classString.'">' . $tag . '</div>';
        }

        return $tag;
    }
}
