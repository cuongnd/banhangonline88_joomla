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

class JFBConnectProviderGoogleWidgetEmbeddedPosts extends JFBConnectWidget
{
    var $name = "Embedded Posts";
    var $systemName = "embeddedposts";
    var $className = "sc_gembeddedposts";
    var $tagName = "scgoogleembeddedposts";
    var $examples = array (
        '{SCGoogleEmbeddedPosts href=https://plus.google.com/110105427116687355332/posts/GBeXiZgZhFH}'
    );

    protected function getTagHtml()
    {
      $tag = '<div class="g-post"';
      $tag .= $this->getField('href', 'url', null, '', 'data-href'); 
      $tag .= '></div>';
      
      return $tag;     
    }
}
