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

class JFBConnectToolbarButtonPreview extends JFBConnectToolbarButton
{
    var $order = '20';
    var $displayName = "Preview";
    var $systemName = "preview";

    protected function generateJavascript()
    {
        return "display: function() {
                window.open('http://socialdebug.com/results?url=' + encodeURIComponent(document.location), '_blank');
            }";
    }

}