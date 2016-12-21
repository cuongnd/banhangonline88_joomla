<?php
/**
 * @package     Joomla.Libraries
 * @subpackage  Menu
 *
 * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved.
 * @license     GNU General Public License version 2 or later; see LICENSE.txt
 */

defined('JPATH_PLATFORM') or die;

/**
 * JMenu class.
 *
 * @since  1.5
 */
class JMenuAdministrator extends JMenu
{
    public function __construct($options = array())

    {

        // Extract the internal dependencies before calling the parent constructor since it calls $this->load()

        $this->app      = isset($options['app']) && $options['app'] instanceof JApplicationCms ? $options['app'] : JFactory::getApplication();

        $this->db       = isset($options['db']) && $options['db'] instanceof JDatabaseDriver ? $options['db'] : JFactory::getDbo();

        $this->language = isset($options['language']) && $options['language'] instanceof JLanguage ? $options['language'] : JFactory::getLanguage();



        parent::__construct($options);

    }

    public function load()
    {
        $app = JFactory::getApplication();
        $session = JFactory::getSession();
        $lang = JFactory::getLanguage();
        $db = $this->db;
        $query = $db->getQuery(true)
            ->select('m.id, m.menutype, m.title, m.alias, m.note, m.path AS route, m.link, m.type, m.level, m.language')
            ->select($db->quoteName('m.browserNav') . ', m.access, m.params, m.home, m.img, m.template_style_id, m.component_id, m.parent_id')
            ->select('e.element as component')
            ->from('#__menu AS m')
            ->join('LEFT', '#__extensions AS e ON m.component_id = e.extension_id')
            ->where('m.published = 1')
            ->where('m.parent_id > 0')
            ->where('m.client_id = 0')
            //->leftJoin('#__falang_content AS falang_content')
            ->order('m.lft');
        // Set the query
        $db->setQuery($query);
        try {
            $this->_items = $db->loadObjectList('id');

        } catch (RuntimeException $e) {
            JError::raiseWarning(500, JText::sprintf('JERROR_LOADING_MENUS', $e->getMessage()));
            return false;

        }
        return true;

    }

}
