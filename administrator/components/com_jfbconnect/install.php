<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

// no direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.filesystem.folder');
jimport('joomla.installer.helper');
jimport('joomla.installer.adapters.plugin');

class com_JFBConnectInstallerScript
{
    var $jfbcVersion;
    var $packages = array();

    var $systemPluginEnabled = '0';
    var $authPluginEnabled = '0';
    var $userPluginEnabled = '0';
    var $contentPluginEnabled = '0';

    var $installedRow = '0';
    var $installer;

    public function __construct($installer)
    {
        $this->installer = $installer;
        $packages = array();

        $rawModules = JFile::read($this->installer->getParent()->getPath('source') . '/administrator/install/install.modules');
        $rawModules = str_replace("\r\n", "\n", $rawModules);
        $modules = explode("\n", $rawModules);

        foreach ($modules as $m)
        {
            $parts = explode('|', $m);
            if (strpos($parts[0], '[') === 0)
                $group = str_replace(array('[', ']'), '', $parts[0]);
            else
                $packages[$group][] = array('name' => $parts[1], 'file' => 'modules/' . $parts[0] . '.zip', 'install' => true);
        }

        $rawPlugins = JFile::read($this->installer->getParent()->getPath('source') . '/administrator/install/install.plugins');
        $rawPlugins = str_replace("\r\n", "\n", $rawPlugins);
        $plugins = explode("\n", $rawPlugins);

        foreach ($plugins as $p)
        {
            $parts = explode('|', $p);
            if (strpos($parts[0], '[') === 0)
                $group = str_replace(array('[', ']'), '', $parts[0]);
            else
            {
                $pluginFolder = explode('.', $parts[0]);
                $pluginFolder = $pluginFolder[0];
                if ($pluginFolder == 'installer' && version_compare(JVERSION, '3.2.0', '>='))
                    continue; // Don't install the installer plugin on Joomla 3.2+ as we use the extra_query param instead
                $packages[$group][] = array('name' => $parts[1], 'file' => 'plugins/' . str_replace(".", "/", $parts[0]) . '.zip', 'install' => $this->isThirdPartyInstalled($parts[2]));
            }
        }
        // Hard code the SourceCoast library here. Not optimal, but we know we'll need it.
        $packages['Core'][] = array('name' => 'SourceCoast Library', 'file' => 'libraries/sourcecoast.zip', 'install' => true);

        ksort($packages);
        $this->packages = $packages;
    }

    private function isThirdPartyInstalled($component)
    {
        if ($component == 1)
            return true;
        else
            return JFolder::exists(JPATH_SITE . '/components/' . $component);
    }

    public function preflight($type, $parent)
    {
        // Check if Joomla version is correct. Mainly, J2.5 can't be installed on J3.0
        if (version_compare(JVERSION, '2.5.5', '<'))
        {
            Jerror::raiseWarning(null, 'JFBConnect requires Joomla 2.5.5 or higher. Please upgrade Joomla to the latest stable release to use JFBConnect.');
            return false;
        }
        return true;
    }

    public function install($parent)
    {
        return true;
    }

    public function update($parent)
    {
        $this->savePluginState();
        $this->disablePlugins();

        return true;
    }

    public function postflight($type, $parent)
    {
        // Run required updates that may not have happened if user uninstalls and does a fresh install
        $manifest = $parent->getParent()->getManifest();
        $this->jfbcVersion = (string)$manifest->version;
        $this->updateDatabase();
        $this->installPackages();
        $this->migrateProfilePluginSettings();
        $this->migrateProfilePluginSettings51();
        $this->migrateProfilePluginSettings612();

        $this->disableUpdateServers();
        $this->enableInstallerPlugin();

        $this->enablePlugins($this->systemPluginEnabled, $this->authPluginEnabled, $this->userPluginEnabled, $this->contentPluginEnabled);
    }

    /*
         * $parent is the class calling this method
         * uninstall runs before any other action is taken (file removal or database processing).
         */
    public function uninstall($parent)
    {
        $this->disablePlugins();
    }

    // CUSTOM JFBCONNECT FUNCTIONS
    private function savePluginState()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select($db->qn('element'))
                ->select($db->qn('enabled'))
                ->from($db->qn('#__extensions'))
                ->where("(" . $db->qn('element') . '=' . $db->q('jfbconnectauth') . ' AND ' .
                $db->qn('folder') . '=' . $db->q('authentication') . ' AND ' .
                $db->qn('type') . '=' . $db->q('plugin') .
                ') OR (' .
                $db->qn('element') . '=' . $db->q('jfbcsystem') . ' AND ' .
                $db->qn('folder') . '=' . $db->q('system') . ' AND ' .
                $db->qn('type') . '=' . $db->q('plugin') .
                ') OR (' .
                $db->qn('element') . '=' . $db->q('jfbccontent') . ' AND ' .
                $db->qn('folder') . '=' . $db->q('content') . ' AND ' .
                $db->qn('type') . '=' . $db->q('plugin') .
                ') OR (' .
                $db->qn('element') . '=' . $db->q('jfbconnectuser') . ' AND ' .
                $db->qn('folder') . '=' . $db->q('user') . ' AND ' .
                $db->qn('type') . '=' . $db->q('plugin') .
                ')');

        $db->setQuery($query);
        $pluginValues = $db->loadObjectList();

        if ($pluginValues)
        {
            foreach ($pluginValues as $plugin)
            {
                $pluginName = $plugin->element;
                $pluginPublished = $plugin->enabled;

                if ($pluginName == 'jfbconnectauth')
                    $this->authPluginEnabled = $pluginPublished;
                else if ($pluginName == 'jfbconnectuser')
                    $this->userPluginEnabled = $pluginPublished;
                else if ($pluginName == 'jfbcsystem')
                    $this->systemPluginEnabled = $pluginPublished;
                else if ($pluginName == 'jfbccontent')
                    $this->contentPluginEnabled = $pluginPublished;
            }
        }
    }

    private function disablePlugins()
    {
        $this->enablePlugins("0", "0", "0", "0");
    }

    private function enablePlugins($enableSystem, $enableAuth, $enableUser, $enableContent)
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->update($db->qn('#__extensions'))
                ->set($db->qn('enabled') . '=' . $db->q($enableSystem))
                ->where($db->qn('element') . '=' . $db->q('jfbcsystem'))
                ->where($db->qn('folder') . '=' . $db->q('system'))
                ->where($db->qn('type') . '=' . $db->q('plugin'));
        $db->setQuery($query);
        $db->execute();

        $query = $db->getQuery(true);
        $query->update($db->qn('#__extensions'))
                ->set($db->qn('enabled') . '=' . $db->q($enableAuth))
                ->where($db->qn('element') . '=' . $db->q('jfbconnectauth'))
                ->where($db->qn('folder') . '=' . $db->q('authentication'))
                ->where($db->qn('type') . '=' . $db->q('plugin'));
        $db->setQuery($query);
        $db->execute();

        $query = $db->getQuery(true);
        $query->update($db->qn('#__extensions'))
                ->set($db->qn('enabled') . '=' . $db->q($enableUser))
                ->where($db->qn('element') . '=' . $db->q('jfbconnectuser'))
                ->where($db->qn('folder') . '=' . $db->q('user'))
                ->where($db->qn('type') . '=' . $db->q('plugin'));
        $db->setQuery($query);
        $db->execute();

        $query = $db->getQuery(true);
        $query->update($db->qn('#__extensions'))
                ->set($db->qn('enabled') . '=' . $db->q($enableContent))
                ->where($db->qn('element') . '=' . $db->q('jfbccontent'))
                ->where($db->qn('folder') . '=' . $db->q('content'))
                ->where($db->qn('type') . '=' . $db->q('plugin'));
        $db->setQuery($query);
        $db->execute();
    }

    private function updateDatabase()
    {
        // If user uninstalled JFBConnect and is re-installing, the tables will exist, but upgrade SQL files won't be called
        // Add SQL calls here as a backup
        $db = JFactory::getDBO();
        $db->setDebug(0);
        $query = $db->getQuery(true);
        $query->select($db->qn('value'))
                ->from($db->qn('#__jfbconnect_config'))
                ->where($db->qn('setting') . '=' . $db->q('db_version'));
        $db->setQuery($query);
        $dbVersion = $db->loadResult();

        // Get the update files for the current database type
        $files = JFolder::files(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/install/sql/updates/', '\.' . $this->getDbType() . '\.sql');
        foreach ($files as $f)
        {
            $updateVersion = str_replace('.' . $this->getDbType() . '.sql', '', $f);
            if (version_compare($dbVersion, $updateVersion, '<'))
                $this->runUpdateSQL($updateVersion);
        }
    }

    private function getDbType()
    {
        return JFactory::getDBO()->name == "postgresql" ? 'postgre' : 'mysql';
    }

    private function runUpdateSQL($version)
    {
        $db = JFactory::getDBO();
        $buffer = file_get_contents(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/install/sql/updates/' . $version . '.' . $this->getDbType() . '.sql');

        // Graceful exit and rollback if read not successful
        if ($buffer === false)
        {
            JError::raiseWarning(1, JText::_('JLIB_INSTALLER_ERROR_SQL_READBUFFER'));
            return false;
        }

        // Create an array of queries from the sql file
        //$queries = JDatabaseDriver::splitSql($buffer); // Joomla 3.x+
        $queries = JDatabase::splitSql($buffer);

        $update_count = 0;
        if (count($queries) != 0)
        {
            // Process each query in the $queries array (split out of sql file).
            foreach ($queries as $query)
            {
                $query = trim($query);
                if ($query != '' && $query{0} != '#')
                {
                    $db->setQuery($query);

                    if (!$db->execute())
                    {
                        JLog::add(JText::sprintf('JLIB_INSTALLER_ERROR_SQL_ERROR', $db->stderr(true)), JLog::WARNING, 'jerror');
                        return false;
                    }
                    $update_count++;
                }
            }
        }
        // All done. Update the db_version row to the latest number
        $query = $db->getQuery(true);
        $query->select($db->qn('value'))
                ->from('#__jfbconnect_config')
                ->where($db->qn('setting') . '=' . $db->q('db_version'));
        $db->setQuery($query);
        $ver = $db->loadResult();

        $query->clear();
        if ($ver)
        {
            $query->update('#__jfbconnect_config')
                    ->where($db->qn('setting') . '=' . $db->q('db_version'))
                    ->set($db->qn('value') . '=' . $db->q($version))
                    ->set($db->qn('updated_at') . '= NOW()');
        }
        else
        {
            $query->insert('#__jfbconnect_config')
                    ->columns($db->qn('setting') . "," . $db->qn('value') . ',' . $db->qn('created_at') . "," . $db->qn('updated_at'))
                    ->values($db->q('db_version') . ',' . $db->q($version) . ', NOW(), NOW()');
        }

        $db->setQuery($query);
        $db->execute();
    }

    private function installPackages()
    {
        // Get current version number
        ?>

        <table>
            <tr>
                <td width="100px"><img
                            src="<?php print JURI::root(); ?>/administrator/components/com_jfbconnect/assets/images/jfbconn.png"
                            width="100px"></td>
                <td><h2>JFBConnect v<?php echo $this->jfbcVersion; ?></h2></td>
            </tr>
        </table>
        <?php echo $this->showDoneMessage(); ?>
        <h3>Installation Successful!</h3>

        <?php
        echo '<table class="adminlist table table-striped" width="100%">';
        echo '<thead><tr><th class="title">Extension</th><th width="40%">Status</th></tr></thead>';
        echo '<tbody>';
        echo '<tr class="row0"><td>JFBConnect Component</td><td><span style="color:green; font-weight:bold">Installed</span></td></tr>';
        foreach ($this->packages as $group => $packages)
        {
            echo '<tr><th><strong>' . $group . '</strong></th><th><center>Status</center></th></tr>';
            echo $this->installPackageList($packages);
        }
        echo '</tbody>';
        echo '</table>';
    }

    private function installPackageList($packages)
    {
        foreach ($packages as $package)
        {
            $installer = new JInstaller();
            $installer->setOverwrite(true);
            $pkgName = $package['name'];
            $pkgFile = $package['file'];
            $pkgInstall = $package['install'];

            $this->installedRow++;

            if (!$pkgInstall)
                $installed = '<span style="color:blue; font-weight:bold">Not Installed. Third Party Extension not found.</span>';
            else
            {
                $pkg = JInstallerHelper::unpack($this->installer->getParent()->getPath('source') . '/packages/' . $pkgFile);
                if ($installer->install($pkg['dir']))
                    $installed = '<span style="color:green; font-weight:bold">Installed</span>';
                else
                    $installed = '<span style="color:red; font-weight:bold">Not Installed. Please install manually.</span>';

                JFolder::delete($pkg['extractdir']);
            }
            ?>
            <tr class="row<?php echo($this->installedRow % 2); ?>">
                <td><?php echo $pkgName; ?></td>
                <td><?php echo $installed; ?></td>
            </tr>
        <?php
        }
    }

    private function showDoneMessage()
    {
        ?>
        <p style="font-weight:bold; margin-top:20px">To configure and optimize JFBConnect, it's recommended to run Autotune whenever you install or upgrade:</p>
        <center><a href="index.php?option=com_jfbconnect&view=autotune"
                   style="background-color:#025A8D;color:#FFFFFF;height:35px;padding:15px 45px;font-weight:bold;font-size:18px;line-height:60px;
                   text-decoration:none;text-shadow:0 -1px 1px #565656;-webkit-border-radius:7px;border-radius:7px;-webkit-box-shadow:0 1px 3px 0 #565656;
                   box-shadow:0 1px 3px 0 #565656;">Run Autotune Now</a></center>
    <?php
    }

    /*
     * JFBConnect v5.0 - Removed JFBCProfile plugins and moved to SocialProfiles plugins. Old plugins used a 1-row per setting in
     * #__jfbconnect_config. New plugins use a 1-row json variable for all settings that are loaded into a registry.
     * This function converts old settings to new format, if they exist.
     */
    private function migrateProfilePluginSettings()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        $query->select("setting,value")->from($db->qn('#__jfbconnect_config'))->where($db->qn('setting') . ' LIKE ' . $db->q('profiles_%'))->order('setting');
        $rows = $db->setQuery($query)->loadObjectList();

        if (!empty($rows))
        {
            require_once(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/models/config.php');
            $configModel = new JFBConnectModelConfig();
            $pluginName = "";
            $reg = new JRegistry();
            foreach ($rows as $row)
            {
                $values = explode("_", $row->setting);
                if ($pluginName != $values[1])
                {
                    $pluginName = $values[1];
                    // Remove the previous profiles_xyz settings from the config table
                    $query = $db->getQuery(true);
                    $query->delete($db->qn('#__jfbconnect_config'))->where($db->qn('setting') . ' LIKE ' . $db->q('profiles_' . $pluginName . '%'));
                    $db->setQuery($query)->execute();
                }
                unset($values[0]);
                unset($values[1]);
                $settingName = implode("_", $values);
                $value = $row->value;
                if ($settingName == "status_updates_pull_from_fb")
                    $settingName = "import_status";
                if ($settingName == "field_map")
                {
                    $fieldMap = unserialize($value);
                    $newMap = new stdClass();
                    foreach ($fieldMap as $key => $value)
                    {
                        if ($pluginName == "jomsocial")
                            $key = 'field' . $key;
                        $newMap->$key = $value;
                    }
                    $value = $newMap;
                }
                $reg->set($settingName, $value);
                $configModel->update('profile_' . $pluginName, $reg->toString());
            }
        }
    }

    private function migrateProfilePluginSettings51()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        // fixed PHP notice from invalid query
        $query->select("setting,value")->from($db->qn("#__jfbconnect_config"))->where($db->qn('setting') . " LIKE " . $db->q("profile_%"))->order('setting');
        $rows = $db->setQuery($query)->loadObjectList();
        if (!empty($rows))
        {
            require_once(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/models/config.php');
            $configModel = new JFBConnectModelConfig();
            foreach ($rows as $row)
            {
                $values = explode("_", $row->setting);
                $pluginName = $values[1];

                $settings = new JRegistry();
                $settings->loadString($row->value);
                if ($settings->exists('field_map') && !$settings->exists('field_map.facebook'))
                {
                    $fieldMap = clone($settings->get('field_map'));
                    if ($pluginName == "jomsocial")
                    {
                        $newMap = new stdClass();
                        foreach ($fieldMap as $key => $value)
                        {
                            $newKey = str_replace('field', '', $key);
                            $newMap->$newKey = $value;
                        }
                        $fieldMap = $newMap;
                    }
                    $settings->set("field_map", null);
                    $settings->set("field_map.facebook", $fieldMap);
                    $configModel->update('profile_' . $pluginName, $settings->toString());
                }
            }
        }
    }

    private function migrateProfilePluginSettings612()
    {
        $db = JFactory::getDBO();
        $query = $db->getQuery(true);
        // fixed PHP notice from invalid query
        $query->select("setting,value")->from($db->qn("#__jfbconnect_config"))->where($db->qn('setting') . " LIKE " . $db->q("profile_%"))->order('setting');
        $rows = $db->setQuery($query)->loadObjectList();
        if (!empty($rows))
        {
            require_once(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/models/config.php');
            $configModel = new JFBConnectModelConfig();
            foreach ($rows as $row)
            {
                $values = explode("_", $row->setting);
                $pluginName = $values[1];

                $settings = new JRegistry();
                $settings->loadString($row->value);
                if ($settings->exists('field_map') && $settings->exists('field_map.facebook'))
                {
                    $fieldMap = clone($settings->get('field_map'));
                    foreach ($fieldMap->facebook as $key => $value)
                    {
                        if ($value == "sex")
                            $fieldMap->facebook->$key = "gender";
                        if ($value == "profile_url")
                            $fieldMap->facebook->$key = "link";
                        if ($value == "about_me")
                            $fieldMap->facebook->$key = "bio";
                        if ($value == "tv")
                            $fieldMap->facebook->$key = "television";
                        if (in_array($value, array("hometown_location.city", "hometown_location.state", "hometown_location.country") ) )
                            $fieldMap->facebook->$key = "hometown.name";
                        if (in_array($value, array("current_location.city", "current_location.state", "current_location.country") ) )
                            $fieldMap->facebook->$key = "location.name";
                    }
                    $settings->set("field_map", null);
                    $settings->set("field_map", $fieldMap);
                    $configModel->update('profile_' . $pluginName, $settings->toString());
                }
            }
        }
    }

    // Introduced in v5.2 (Nov, 2013). Should remove in 1 year
    private function disableUpdateServers()
    {
        $ext = array(
            'mod_jfbccomments.module',
            'mod_jfbcfan.module',
            'mod_jfbcfeed.module',
            'mod_jfbcfollow.module',
            'mod_jfbcfriends.module',
            'mod_jfbclike.module',
            'mod_jfbcrecommendations.module',
            'mod_jfbcrecommendationsbar.module',
            'mod_jfbcrequest.module',
            'mod_jfbcsend.module',
            'mod_jfbcsharedactivity.module',
            'mod_jfbcsharedialog.module',

            'content.plugin.opengraph',
            'custom.plugin.opengraph',
            'easyblog.plugin.opengraph',
            'jomsocial.plugin.opengraph',
            'jreviews.plugin.opengraph',
            'k2.plugin.opengraph',

            'agora.plugin.socialprofiles',
            'communitybuilder.plugin.socialprofiles',
            'jomsocial.plugin.socialprofiles',
            'k2.plugin.socialprofiles',
            'kunena.plugin.socialprofiles',
            'virtuemart2.plugin.socialprofiles',

            'sourcecoast.library'
        );

        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->select($db->qn('extension_id'))
                ->from($db->qn("#__extensions"));

        if (!JPluginHelper::importPlugin('extension', 'joomla'))
            return;

        $app = JFactory::getApplication();
        foreach ($ext as $e)
        {
            $query->clear('where');
            $parts = explode('.', $e);
            $query->where($db->qn('element') . '=' . $db->q($parts[0]));
            $query->where($db->qn('type') . '=' . $db->q($parts[1]));
            if (isset($parts[2]))
                $query->where($db->qn('folder') . '=' . $db->q($parts[2]));

            $db->setQuery($query);
            $eid = $db->loadResult();
            if ($eid)
            {
                $args = array($eid, $eid, $eid);
                $app->triggerEvent('onExtensionAfterUninstall', $args);
            }
        }
    }

    private function enableInstallerPlugin()
    {
        $db = JFactory::getDbo();
        $query = $db->getQuery(true);
        $query->update($db->qn('#__extensions'))
                ->set($db->qn('enabled') . '=1')
                ->where($db->qn('element') . '=' . $db->q('jfbconnect'))
                ->where($db->qn('folder') . '=' . $db->q('installer'))
                ->where($db->qn('type') . '=' . $db->q('plugin'));
        $db->setQuery($query);
        $db->execute();
    }
}