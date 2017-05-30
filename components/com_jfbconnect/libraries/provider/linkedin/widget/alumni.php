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

class JFBConnectProviderLinkedinWidgetAlumni extends JFBConnectWidget
{
    var $name = "Alumni";
    var $systemName = "alumni";
    var $className = "jlinkedAlumni";
    var $tagName = "jlinkedalumni";
    var $examples = array (
        '{JLinkedAlumni}',
        '{JLinkedAlumni schoolid=18483}'
    );

    protected function getTagHtml()
    {
        $this->provider->extraJS = array_merge($this->provider->extraJS, array("extensions: 'AlumniFacet@//www.linkedin.com/edu/alumni-facet-extension-js'"));
        $tag = '<script type="IN/AlumniFacet"';
        $tag .= $this->getField('schoolid', null, null, '', 'data-linkedin-schoolid');
        $tag .= '></script>';
        return $tag;
    }
}
