<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.model');
jimport('joomla.filesystem.file');

class JFBConnectModelOpenGraphObject extends JModelLegacy
{
    var $objectId;
    var $userId;
    static $instances;

    public function __construct($config = array())
    {
        $user = JFactory::getUser();
        $this->userId = $user->get('id');
        $this->definedObjects = array();

        if (self::$instances === null)
            self::$instances = array();

        parent::__construct($config);
    }

    public function getObject($id, $data = null)
    {
        if (!isset(self::$instances[$id]))
        {
            if (!$data)
            {
                $query = "SELECT * FROM #__opengraph_object WHERE " .
                        " `id` = " . $this->_db->quote($id);
                $this->_db->setQuery($query);
                $data = $this->_db->loadObject();
            }

            $object = new ogObject();
            $object->setProperties($data);
            self::$instances[$id] = $object;
        }

        return self::$instances[$id];
    }

    // When the plugin is running, get the objects of a specific type for the current page.
    // Only returns published objects
    public function getPluginObjects($plugin, $name)
    {
        $this->_db->setQuery("SELECT * FROM #__opengraph_object WHERE " .
                " plugin = " . $this->_db->quote($plugin) .
                " AND system_name = " . $this->_db->quote($name) .
                " AND published = 1");
        $rows = $this->_db->loadObjectList();
        $objects = array();
        foreach ($rows as $row)
            $objects[] = $this->getObject($row->id, $row);

        return $objects;
    }
}

// Should make these objects derive from the same class to share some of the common functionality.
// getNamespace, setProperties, etc.
class ogObject extends JObject
{
    var $id;
    var $plugin;
    var $system_name;
    var $display_name;
    var $type;
    var $fb_built_in;
    var $published;
    var $params;
    var $created;
    var $modified;
    var $associatedActions;

    public function setProperties($data)
    {
        parent::setProperties($data);

        $this->params = new JRegistry($this->params);
    }

    public function loadDefaultObject($plugin, $name)
    {
        $this->id = 0;
        $this->plugin = $plugin;
        $this->system_name = $name;
        $this->display_name = $name;
        $this->type = "";
        $this->fb_built_in = 0;
        $this->extension_key = "";
        $this->published = 0;
        $this->params = new JRegistry();
        $this->created = "--";
        $this->modified = "--";

        $xml = JPATH_SITE . '/plugins/opengraph/' . $plugin . '/objects/' . $name . '.xml';
        if (JFile::exists($xml))
        {
            JFormHelper::addFieldPath(JPATH_SITE . '/plugins/opengraph/' . $plugin . '/objects');
            $form = JForm::getInstance('opengraph.' . $plugin . '.' . $name, $xml);

            foreach ($form->getFieldset() as $field)
            {
                if ($field->value)
                {
                    $this->params->set($field->fieldname, $field->value);
                }
            }
        }
    }

    public function getObjectPath()
    {
        if ($this->fb_built_in == 0)
        {
            $namespace = $this->getNamespace();
            $objectPath = $namespace . ":" . $this->type;
        } else
            $objectPath = $this->type;
        return $objectPath;
    }

    private function getNamespace()
    {
        $appConfig = JFBCFactory::config()->getSetting('autotune_app_config');
        $namespace = $appConfig['namespace'];
        return $namespace;
    }

    public function getAssociatedActions()
    {
        if (!$this->associatedActions)
        {
            $this->associatedActions = array();

            $db = JFactory::getDbo();
            $db->setQuery('SELECT a.* FROM #__opengraph_action_object ao ' .
                    ' INNER JOIN #__opengraph_action a ON ao.action_id = a.id ' .
                    ' WHERE ao.object_id = ' . $this->id .
                    ' AND a.published = 1');
            $actions = $db->loadObjectList();
            if (count($actions) > 0)
            {
                $actionModel = JModelLegacy::getInstance('opengraphaction', 'JFBConnectModel');
                foreach ($actions as $action)
                    $this->associatedActions[] = $actionModel->getAction($action->id, $action);
            }
        }
        return $this->associatedActions;
    }
}