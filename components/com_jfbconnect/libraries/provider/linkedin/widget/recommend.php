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

class JFBConnectProviderLinkedinWidgetRecommend extends JFBConnectWidget
{
    var $name = "Recommend";
    var $systemName = "recommend";
    var $className = "jlinkedRecommend";
    var $tagName = "jlinkedrecommend";
    var $examples = array(
        '{JLinkedRecommend}',
        '{JLinkedRecommend companyid=365848 productid=201714 counter=top}'
    );

    protected function getTagHtml()
    {
        $tag = '<script type="IN/RecommendProduct"';
        $tag .= $this->getField('companyid', null, null, '', 'data-company');
        $tag .= $this->getField('productid', null, null, '', 'data-product');
        $tag .= $this->getField('counter', null, null, '', 'data-counter');
        $tag .= '></script>';
        return $tag;
    }
}
