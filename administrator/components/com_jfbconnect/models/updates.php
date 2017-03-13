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

class JFBConnectModelUpdates extends JModelLegacy
{
    public function getJfbconnectExtensionId()
    {
        $query = $this->_db->getQuery(true)
                ->select($this->_db->qn('extension_id'))
                ->from($this->_db->qn('#__extensions'))
                ->where($this->_db->qn('type') . ' = ' . $this->_db->q('component'))
                ->where($this->_db->qn('element') . ' = ' . $this->_db->q('com_jfbconnect'));
        $this->_db->setQuery($query);
        return $this->_db->loadResult();
    }

    // Gets the ID from the #__updates table for the JFBConnect update
    // Returns null if no update is available (either due to error or because the latest is installed)
    public function getJFBConnectUpdateId()
    {
        $query = $this->_db->getQuery(true)
                ->select('*')
                ->from($this->_db->qn('#__updates'))
                ->where($this->_db->qn('extension_id') . ' = ' . $this->getJfbconnectExtensionId())
                ->where($this->_db->qn('element') . ' = ' . $this->_db->q('com_jfbconnect'));
        $this->_db->setQuery($query);
        return $this->_db->loadObject();
    }

    public function getUpdateSite()
    {
        $updateSiteIds = $this->getUpdateSiteIds($this->getJfbconnectExtensionId());
        $updateSiteId = $updateSiteIds[0];
        $query = $this->_db->getQuery(true)
                ->select('*')
                ->from($this->_db->qn('#__update_sites'))
                ->where($this->_db->qn('update_site_id') . ' = ' . $updateSiteId);
        $this->_db->setQuery($query);
        return $this->_db->loadObject();
    }


    private function getUpdateSiteIds($extensionId)
    {
        // Get the update sites for our extension
        $query = $this->_db->getQuery(true)
                ->select($this->_db->qn('update_site_id'))
                ->from($this->_db->qn('#__update_sites_extensions'))
                ->where($this->_db->qn('extension_id') . ' = ' . $this->_db->q($extensionId));
        $this->_db->setQuery($query);
        return $this->_db->loadColumn(0);
    }

    /*
     * This code modified from Akeeba Backup source (http://akeebabackup.com/)
     */
    public function refreshUpdateSites($subscriberId)
    {
        if (version_compare(JVERSION, '3.2.2', '<'))
            return;

        $extra_query = null;

        // If I have a valid Download ID I will need to use a non-blank extra_query in Joomla! 3.2+
        if (preg_match('/^([0-9]{1,}:)?[0-9a-f]{32}$/i', $subscriberId))
        {
            $extra_query = 'dlid=' . $subscriberId;
        }

        // Create the update site definition we want to store to the database
        $update_site = array(
                'name' => 'JFBConnect Updates',
                'type' => 'extension',
                'location' => 'http://www.sourcecoast.com/updates/jfbconnect.xml',
                'enabled' => 1,
                'last_check_timestamp' => 0,
                'extra_query' => $extra_query
        );

        $extensionId = $this->getJfbconnectExtensionId();

        if (empty($extensionId))
        {
            return;
        }

        $updateSiteIds = $this->getUpdateSiteIds($extensionId);

        if (!count($updateSiteIds))
        {
            // No update sites defined. Create a new one.
            $newSite = (object)$update_site;
            $this->_db->insertObject('#__update_sites', $newSite);

            $id = $this->_db->insertid();

            $updateSiteExtension = (object)array(
                    'update_site_id' => $id,
                    'extension_id' => $extensionId,
            );
            $this->_db->insertObject('#__update_sites_extensions', $updateSiteExtension);
        }
        else
        {
            // Loop through all update sites... though there should only be 1
            foreach ($updateSiteIds as $id)
            {
                $update_site['update_site_id'] = $id;
                $newSite = (object)$update_site;
                $this->_db->updateObject('#__update_sites', $newSite, 'update_site_id', true);
            }
        }
    }
}
