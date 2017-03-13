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

class JFBConnectProviderGoogleWidgetPageBadge extends JFBConnectWidget
{
    var $name = "Page Badge";
    var $systemName = "pagebadge";
    var $className = "sc_gpagebadge";
    var $tagName = "scgooglepagebadge";
    var $examples = array (
        '{SCGooglePageBadge href=https://plus.google.com/+GooglePlusDevelopers}',
        '{SCGooglePageBadge href=https://plus.google.com/+GooglePlusDevelopers layout=portrait theme=light showcoverphoto=true showtagline=true width=300}'
    );

    protected function getTagHtml()
    {
      $tag = '<div class="g-page"';
      $tag .= $this->getField('href', 'url', null, '', 'data-href');
      $tag .= $this->getField('layout', null, null, 'portrait', 'data-layout');
      $tag .= $this->getField('theme', null, null, 'light', 'data-theme');
      $tag .= $this->getField('showcoverphoto', null, 'boolean', 'true', 'data-showcoverphoto');
      $tag .= $this->getField('showtagline', null, 'boolean', 'true', 'data-showtagline');
      $tag .= $this->getField('width', null, null, '300', 'data-width');
      $tag .= '></div>';   
    
      return $tag;
    }
}
