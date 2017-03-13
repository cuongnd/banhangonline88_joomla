<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

jimport('sourcecoast.plugins.socialprofile');

class plgSocialProfilesCustomdb extends SocialProfilePlugin
{
    function __construct(&$subject, $params)
    {
        $this->_componentFolder = '';
        $this->_componentFile = '';
        parent::__construct($subject, $params);

        $this->defaultSettings->set('import_avatar', '0');
        $this->defaultSettings->set('import_always', '1');
        $this->defaultSettings->set('db_name', '');
        $this->defaultSettings->set('db_user', '');
        $this->defaultSettings->set('db_password', '');
        $this->defaultSettings->set('db_table', '');
        $this->defaultSettings->set('db_table_columns', '');
        $this->defaultSettings->set('db_key_column', '');
        $this->defaultSettings->set('db_key_value', '');
        $this->defaultSettings->set('field_map', null);
    }

    protected function createUser($profileData)
    {
        $this->importSocialProfile();
        return true;
    }

    /*     * * End Trigger Overrides ** */
    protected function saveProfileField($fieldId, $value)
    {
        if ($this->settings->get('db_table') == '' || $this->settings->get('db_key_column') == '')
            return;

        $settings = $this->settings;
        if ($settings->get('db_name') != "")
        {
            $options = array();
            $options['user'] = $settings->get('db_user');
            $options['password'] = $settings->get('db_password');
            $options['database'] = $settings->get('db_name');
            $dbo = JDatabase::getInstance($options);
        }
        else
            $dbo = JFactory::getDBO();

        $query = $dbo->getQuery(true);
        $query->select('count(*)')
                ->from($dbo->qn($settings->get('db_table')))
                ->where($dbo->qn($settings->get('db_key_column')) . '=' . $dbo->q($this->joomlaId));
        $dbo->setQuery($query);
        $rowId = $dbo->loadResult();

        $query = $dbo->getQuery(true);
        if ($rowId)
        {
            $query->update($dbo->qn($settings->get('db_table')))
                    ->set($dbo->qn($fieldId) . '=' . $dbo->q($value))
                    ->where($dbo->qn($settings->get('db_key_column')) . '=' . $dbo->q($this->joomlaId));
        }
        else
        {
            $query->insert($dbo->qn($settings->get('db_table')))
                    ->set($dbo->qn($fieldId) . '=' . $dbo->q($value))
                    ->set($dbo->qn($settings->get('db_key_column')) . '=' . $dbo->q($this->joomlaId));
        }
        $dbo->setQuery($query);
        $dbo->query();
    }

    protected function getProfileFields()
    {
        $settings = $this->loadSettings('facebook');
        $fields = array();

        $default = array((object)array('id' => 'xyz', 'name' => 'Configure DB Settings First'));

        if ($settings->get('db_table') == "")
            return $default;
        else
        {
            if ($settings->get('db_name') != "")
            {
                $options = array();
                $options['user'] = $settings->get('db_user');
                $options['password'] = $settings->get('db_password');
                $options['database'] = $settings->get('db_name');
                $dbo = JDatabase::getInstance($options);
            }
            else
                $dbo = JFactory::getDBO();

            $columns = $dbo->getTableColumns($settings->get('db_table'));
            if (!$columns)
                return $default;

            foreach ($columns as $key => $type)
            {
                $fields[] = (object)array('id' => $key, 'name' => $key);
            }
            return $fields;
        }
    }
}