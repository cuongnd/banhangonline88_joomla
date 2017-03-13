<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

class JFBConnectMigratorJlinked
{
    private $db;

    private $extensions = array(
                'com_jlinked',
                'jlinkedauthentication',
                'jlinkedsystem',
                'jlinkedcontent',
                'jlinkeduser'
            );

    public function __construct()
    {
        $this->db = JFactory::getDbo();
    }

    public function isInstalled()
    {
        $tables = $this->db->getTableList();
        return in_array($this->db->getPrefix() . 'jlinked_user_map', $tables);
    }

    public function migrationDone()
    {
        return JFBCFactory::config()->get('jlinked_migration_done', false);
    }

    public function filesPresent()
    {
        foreach ($this->extensions as $e)
        {
            $eid = $this->getExtensionId($e);
            if ($eid)
                return true;
        }
        return false;
    }

    public function uninstall()
    {
        JFactory::getLanguage()->load('com_installer');
        // Should we actually try to uninstall here? My guess is 'no'.. Let the user do it.
        // We just provide an option to remove the tables since the uninstaller won't do that.
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_installer/models');
        $model = JModelLegacy::getInstance('Manage', 'InstallerModel');
        $return = true;
        foreach ($this->extensions as $e)
        {
            $eid = $this->getExtensionId($e);
            if ($eid)
            {
                $eid = array($eid);
                $return = $model->remove($eid);
                if (!$return)
                    break; // Stop here if something went wrong.
            }
        }

        return $return;
    }

    private function getExtensionId($element)
    {
        $query = $this->db->getQuery(true);
        $query->select('extension_id')
                ->from('#__extensions')
                ->where($this->db->qn('element') . '=' . $this->db->q($element));
        $this->db->setQuery($query);
        $eid = $this->db->loadResult();
        return $eid;
    }

    public function removeTables()
    {
        $this->db->dropTable('#__jlinked_config');
        $this->db->dropTable('#__jlinked_user_map');
        return true;
    }

    public function migrate()
    {
        $this->migrateMappings();
        $this->migrateSettings();
        JFBCFactory::config()->update('jlinked_migration_done', true);
        return true;
    }

    private function migrateSettings()
    {
        $query = $this->db->getQuery(true);
        $query->select('value')
                ->from('#__jlinked_config')
                ->where($this->db->qn('setting') . '=' . $this->db->q('linkedin_api_key'));
        $this->db->setQuery($query);
        $apiKey = $this->db->loadResult();
        JFBCFactory::config()->update('linkedin_app_id', $apiKey);

        $query->clear('where')
                ->where($this->db->qn('setting') . '=' . $this->db->q('linkedin_secret_key'));
        $this->db->setQuery($query);
        $secretKey = $this->db->loadResult();
        JFBCFactory::config()->update('linkedin_secret_key', $secretKey);
    }

    private function migrateMappings()
    {
        $query = $this->db->getQuery(true);
        $query->select('*')
                ->from('#__jlinked_user_map');
        $this->db->setQuery($query);
        $jlUsers = $this->db->loadObjectList();

        $count = 0;
        if ($jlUsers)
        {
            foreach ($jlUsers as $u)
            {
                // JLinked used OAuth1. JFBConnect uses OAuth2, so the token can't be migrated.
                if (JFBCFactory::usermap()->map($u->joomla_id, $u->linkedin_id, 'linkedin'))
                    $count++;
            }
        }
    }
}