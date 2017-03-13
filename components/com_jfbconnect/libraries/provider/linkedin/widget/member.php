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

class JFBConnectProviderLinkedinWidgetMember extends JFBConnectWidget
{
    var $name = "Member Profile";
    var $systemName = "member";
    var $className = "jlinkedMember";
    var $tagName = "jlinkedmember";
    var $examples = array(
        '{JLinkedMember}',
        '{JLinkedMember href=http://www.linkedin.com/in/alexandreae display_mode=inline related=false width=300}',
        '{JLinkedMember href=http://www.linkedin.com/in/alexandreae display_mode=icon_name display_behavior=click display_text=Alex Andreae related=true}',
        '{JLinkedMember href=http://www.linkedin.com/in/alexandreae display_mode=icon display_behavior=hover related=1}',
    );

    protected function getTagHtml()
    {
        $tag = '<span class="IN-canvas-member"><script type="IN/MemberProfile"';
        $tag .= $this->getField('href', 'url', null, '', 'data-id');
        $tag .= $this->getField('related', null, 'boolean', 'true', 'data-related');

        $displayMode = $this->getParamValue('display_mode');
        if ($displayMode == 'inline')
        {
            $tag .= ' data-format="inline"';
            $tag .= $this->getField('width', null, null, '', 'data-width');
        }
        else if ($displayMode == 'icon_name')
        {
            $tag .= $this->getField('display_behavior', null, null, '', 'data-format');
            $tag .= $this->getField('display_text', null, null, '', 'data-text');
        } else if ($displayMode == 'icon')
        {
            $tag .= $this->getField('display_behavior', null, null, '', 'data-format');
        }
        $tag .= '></script></span>';

        return $tag;
    }
}
