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

jimport('joomla.application.component.view');
jimport('sourcecoast.adminHelper');

require_once(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/includes/views.php');

class JFBConnectViewChannels extends JFBConnectAdminView
{
    function display($tpl = null)
    {
        $this->items = $this->get('Items');
        $this->pagination = $this->get('Pagination');
        $this->state = $this->get('State');
        //$this->filterForm = $this->get('FilterForm');
        //$this->activeFilters = $this->get('ActiveFilters');

        JToolbarHelper::addNew('channel.add');
        JToolbarHelper::publish('channels.publish', 'JTOOLBAR_PUBLISH', true);

//        JToolBarHelper::publishList();
        JToolBarHelper::unpublish('channels.unpublish', 'JTOOLBAR_UNPUBLISH', true);
        JToolbarHelper::deleteList('', 'channels.delete', 'JTOOLBAR_TRASH');

        // Check for errors.
        if (count($errors = $this->get('Errors')))
        {
            JError::raiseError(500, implode("\n", $errors));

            return false;
        }

        $title = "JFBConnect: Social Channels";

        /*        if ($layout != 'display' && $layout != 'default')
                {
                    JToolBarHelper::custom('display', 'opengraph.png', 'index.php?option=com_jfbconnect&view=opengraph', 'Open Graph Home', false);
                    JToolBarHelper::divider();
                }*/

        JToolBarHelper::title($title, 'jfbconnect.png');

        SCAdminHelper::addAutotuneToolbarItem();

        parent::display($tpl);
    }
}
