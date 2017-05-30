<?php
/**
 * MaQma Helpdesk Component
 * www.imaqma.com
 *
 * @package MaQma Helpdesk
 * @copyright (C) 2006-2013 Components Lab, Lda.
 * @license GNU/GPL
 *
 * $Id: maqmahelpdeskcreateclient.php 646 2012-05-22 08:20:58Z pdaniel $
 * $LastChangedDate: 2012-05-22 09:20:58 +0100 (Ter, 22 Mai 2012) $
 *
 */

// Check to ensure this file is included in Joomla!
defined('_JEXEC') or die('Restricted access');

jimport('joomla.plugin.plugin');

class plgUserMaqmaHelpdesk_CreateClient extends JPlugin
{
	/**
     * Constructor
     *
     * For php4 compatability we must not use the __constructor as a constructor for plugins
     * because func_get_args ( void ) returns a copy of all passed arguments NOT references.
     * This causes problems with cross-referencing necessary for the observer design pattern.
     *
     * @param   object $subject The object to observe
     * @param   array  $config  An array that holds the plugin configuration
     * @since   1.5
     *
     */
	function plgUserMaqmaHelpdesk_CreateClient(& $subject, $config)
	{
        parent::__construct($subject, $config);
    }

    public function onUserLogin($user, $options = array())
    {
        $this->onLoginUser($user, $options);
    }

    /**
     * This method should handle any login logic and report back to the subject
     *
     * @access    public
     * @param     array    holds the user data
     * @param     array    array holding options (remember, autoregister, group)
     * @return    boolean  True on success
     * @since     1.5
     *
     */
    function onLoginUser($user, $options = array())
    {
        $db = JFactory::getDBO();

        $sql = "SELECT `id`, `name`, `email` FROM `#__users` WHERE `username`='" . $user['username'] . "' AND `email`='" . $user['email'] . "'";
        $db->setQuery($sql);
        $userdata = $db->loadObject();

        $sql = "SELECT COUNT(*) FROM `#__support_client_users` WHERE `id_user`='" . $userdata->id . "'";
        $db->setQuery($sql);
        $result = $db->loadResult();

        if (!$result && !$this->_IsSupport($userdata->id))
        {
            // Creates client
            $sql = "INSERT INTO `#__support_client`(`date_created`, `clientname`, `email`, `block` )
					VALUES('" . date("Y-m-d") . "', '" . $userdata->name . "', '" . $userdata->email . "', '0')";
            $db->setQuery($sql);
            $db->query();
            $clientid = $db->insertid();

            // Gives permissions to all workgroups
            $sql = "INSERT INTO `#__support_client_wk`(`id_client`, `id_workgroup`)
					VALUES('" . $clientid . "', '0')";
            $db->setQuery($sql);
            $db->query();

            // Relates user with client
            $sql = "INSERT INTO `#__support_client_users`(`id_client`, `id_user`, `manager`)
					VALUES('" . $clientid . "', '" . $userdata->id . "', '1')";
            $db->setQuery($sql);
            $db->query();

            // Relates user with client group
	        if ($this->params->get('id_group', 0))
	        {
		        $sql = "INSERT INTO `#__support_dl_users`(`id_user`, `id_group`)
						VALUES('" . $clientid . "', '" . $this->params->get('id_group', 0) . "')";
		        $db->setQuery($sql);
		        $db->query();
	        }
        }

        return true;
    }

    private function _IsSupport($id)
    {
        $db = JFactory::getDBO();

        $sql = "SELECT COUNT(*) FROM `#__support_permission` WHERE `id_user`=" . (int)$id;
        $db->setQuery($sql);

        return $db->loadResult();
    }
}
