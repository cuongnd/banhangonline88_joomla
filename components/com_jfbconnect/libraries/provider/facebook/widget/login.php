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

class JFBConnectProviderFacebookWidgetLogin extends JFBConnectProviderWidgetLogin
{
    function __construct($provider, $fields)
    {
        parent::__construct($provider, $fields, 'jfbcLogin');

        $this->examples = array (
            '{JFBCLogin}',
            '{JFBCLogin text=Login With Facebook logout=true logout_url=http://www.sourcecoast.com}'
        );

        $this->className = 'jfbcLogin';
        $this->tagName = 'JFBCLogin';
    }
}
