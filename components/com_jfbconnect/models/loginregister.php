<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.modelform');
jimport('joomla.event.dispatcher');
jimport('joomla.plugin.helper');
jimport('sourcecoast.utilities');

class JFBConnectModelLoginRegister extends JModelLegacy
{
    function createNewUser($provider)
    {
        $provider->setInitialRegistration();

        $app = JFactory::getApplication();

        if (!JRequest::checkToken())
        {
            $app->enqueueMessage("Your session timed out. Please try again", 'error');
            return false;
        }
        // Save the whole POST data since we may need some of the fields after a redirection (on user creation failure)
        $this->getLoginPostData();

        $providerUserId = $provider->getProviderUserId();

        // User Registration Controller code
        JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_users/models');
        jimport('joomla.form.form');
        JForm::addFormPath(JPATH_SITE . '/components/com_users/models/forms');
        JForm::addFieldPath(JPATH_SITE . '/components/com_users/models/fields');
        $language = JFactory::getLanguage();
        $language->load('com_users');

        $userModel = JModelLegacy::getInstance('Registration', 'UsersModel');
        $requestData = JRequest::getVar('jform', array(), 'post', 'array');

        // Validate the posted data.
        $form = $userModel->getForm();
        if (!$form)
        {
            JError::raiseError(500, $userModel->getError());
            return false;
        }
        $formData = $userModel->validate($form, $requestData);

        // Check for validation errors.
        if ($formData === false)
        {
            // Get the validation messages.
            $errors = $userModel->getErrors();

            // Push up to three validation messages out to the user.
            for ($i = 0, $n = count($errors); $i < $n && $i < 3; $i++)
            {
                if (JError::isError($errors[$i]))
                {
                    $app->enqueueMessage($errors[$i]->getMessage(), 'warning');
                }
                else
                {
                    $app->enqueueMessage($errors[$i], 'warning');
                }
            }

            // Save the data in the session.
            $app->setUserState('com_users.registration.data', $requestData);

            return false;
        }

        // Register the user and return the newly created user ID
        $useractivation = $this->getActivationMode();
        $jUser = $this->register($formData, $useractivation);

        // Check for errors.
        if ($jUser === false)
        {
            // Save the data in the session.
            $app->setUserState('com_users.registration.data', $requestData);
            // Redirect back to the edit screen.
            $app->enqueueMessage(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $this->getError()), 'warning');
            return false;
        }

        $lang = JRequest::getVar(JApplication::getHash('language'), '', 'COOKIE');
        $jUser->setParam('language', $lang);
        $jUser->save();

        // Flush the data from the session.
        $app->setUserState('com_users.registration.data', null);
        $app->setUserState('com_jfbconnect.registration.data', null);

        #Send the new user confirmation email and admin notify emails
        $this->_newUserPassword = $formData['password1'];

        $this->onAfterRegister($provider, $jUser);

        //SCSocialUtilities::clearJFBCNewMappingEnabled();

        if (JFBCFactory::usermap()->map($jUser->id, $providerUserId, $provider->systemName, $provider->client->getToken()))
        {
            $app->enqueueMessage(JText::sprintf('COM_JFBCONNECT_MAP_USER_SUCCESS', $provider->name));
            return true;
        }
        else
        {
            $app->enqueueMessage(JText::sprintf('COM_JFBCONNECT_MAP_USER_FAIL', $provider->name));
            return false;
        }
    }

    public function getLoginRedirect($provider = null)
    {
        if ((($provider !== null && $provider->initialRegistration) || JFactory::getApplication()->getUserState('com_jfbconnect.registration.alternateflow', false))
                && JFBCFactory::config()->getSetting('facebook_new_user_redirect') != ''
        )
        {
            $itemId = JFBCFactory::config()->getSetting('facebook_new_user_redirect', '0');
            $redirect = SCSocialUtilities::getLinkFromMenuItem($itemId, false);
        }
        else if (JFBCFactory::config()->getSetting('facebook_login_redirect') != '')
        {
            $itemId = JFBCFactory::config()->getSetting('facebook_login_redirect', '0');
            $redirect = SCSocialUtilities::getLinkFromMenuItem($itemId, false);
        }
        else
        {
            // Get whatever has been saved in the com_jfbconnect.login.redirect state variable.
            $redirect = JFactory::getApplication()->getUserState('com_jfbconnect.login.return', 'index.php');
            //JFactory::getApplication()->setUserState('com_jfbconnect.login.return', null);
        }

        return $redirect;
    }

    /**
     * Method to save the form data. Primary implementation from Joomla 1.6 com_users/models/registration.php
     *
     * @param    array        The form data.
     * @return    mixed        The user id on success, false on failure.
     * @since    1.6
     */
    private function register($temp, $useractivation)
    {
        // Initialise the table with JUser.
        JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_users/models');
        $userModel = JModelLegacy::getInstance('Registration', 'UsersModel');

        $user = new JUser;
        $data = (array)$userModel->getData();

        // Merge in the registration data.
        foreach ($temp as $k => $v)
        {
            $data[$k] = $v;
        }

        // Prepare the data for the user object.
        $data['email'] = $data['email1'];
        $data['password'] = $data['password1'];

        // Check if the user needs to activate their account.
        if (($useractivation == 1) || ($useractivation == 2))
        {
            jimport('joomla.user.helper');
            $data['activation'] = JApplication::getHash(JUserHelper::genRandomPassword());
            $data['block'] = 1;
        }

        // Bind the data.
        if (!$user->bind($data))
        {
            $this->setError(JText::sprintf('COM_USERS_REGISTRATION_BIND_FAILED', $user->getError()));
            return false;
        }

        // Load the users plugin group.
        JPluginHelper::importPlugin('user');

        // Store the data.
        if (!$user->save())
        {
            $this->setError(JText::sprintf('COM_USERS_REGISTRATION_SAVE_FAILED', $user->getError()));
            return false;
        }

        return $user;
    }

    private function getLoginPostData()
    {
        $postData = JRequest::get('post');

        if (isset($postData['jform']['password1']))
            $postData['jform']['password1'] = '';
        if (isset($postData['jform']['password2']))
            $postData['jform']['password2'] = '';

        $app = JFactory::getApplication();
        $app->setUserState('com_jfbconnect.registration.data', $postData);
    }

    function autoCreateUser($providerUserId, $provider)
    {
        $provider->setInitialRegistration();

        $profile = $provider->profile->fetchProfile($providerUserId, array('first_name', 'last_name', 'email', 'full_name'));
        if ($profile == null || $profile->get('email') == null) # not enough information returned to auto-create account
        return false;

        $newEmail = $profile->get('email');
        $fullname = $profile->get('full_name');

        $user['fullname'] = $fullname;
        $user['email'] = $newEmail;

        // Create random password for FB User Only, but save so we can email to the user on account creation
        if (JFBCFactory::config()->getSetting('generate_random_password'))
        {
            $this->_newUserPassword = JUserHelper::genRandomPassword();
            $user['password_clear'] = $this->_newUserPassword;

            // Check for Joomla 3.2.1's new hashPassword functions and use those, if exist
            if (method_exists('JUserHelper', 'hashPassword'))
            {
                $user['password'] = JUserHelper::hashPassword($this->_newUserPassword);
            }
            else // fallback to Joomla <3.2.0 password hashing
            {
                $salt = JUserHelper::genRandomPassword(32);
                $crypt = JUserHelper::getCryptedPassword($this->_newUserPassword, $salt);
                $user['password'] = $crypt . ':' . $salt;
            }
        }
        else
        {
            $user['password_clear'] = "";
            $this->_newUserPassword = '';
        }

        $lang = JRequest::getVar(JApplication::getHash('language'), '', 'COOKIE');
        $user['language'] = $lang;

        $usernamePrefixFormat = JFBCFactory::config()->getSetting('auto_username_format');
        $username = SCUserUtilities::getAutoUsername($profile->get('first_name'), $profile->get('last_name'), $profile->get('email'), $provider->usernamePrefix, $providerUserId, $usernamePrefixFormat);
        $user['username'] = $username;

        $useractivation = $this->getActivationMode();
        $jUser = $this->getBlankUser($user, $useractivation);

        if ($jUser && $jUser->get('id', null)) // If it's not set, there was an error
        {
            $this->onAfterRegister($provider, $jUser);
            SCSocialUtilities::clearJFBCNewMappingEnabled();

            $app = JFactory::getApplication();
            if (JFBCFactory::usermap()->map($jUser->get('id'), $providerUserId, $provider->systemName, $provider->client->getToken()))
            {
                $app->enqueueMessage(JText::sprintf('COM_JFBCONNECT_MAP_USER_SUCCESS', $provider->name));
                return true;
            }
            else
                $app->enqueueMessage(JText::sprintf('COM_JFBCONNECT_MAP_USER_FAIL', $provider->name));
        }
        return false; // User creation failed for some reason
    }

    private function onAfterRegister($provider, $jUser)
    {
        $this->activateUser();
        $this->sendNewUserEmails($jUser, $provider->systemName);

        # New user, set their new user status and trigger the OnRegister event
        $args = array($provider->systemName, $jUser, $provider->getProviderUserId());
        JFactory::getApplication()->triggerEvent('socialProfilesOnRegister', $args);
    }

    function &getBlankUser($user, $activationMode)
    {
        jimport('joomla.application.component.helper');
        $config = JComponentHelper::getParams('com_users');

        $instance = JUser::getInstance();
        // Default to Registered.
        $defaultUserGroup = $config->get('new_usertype', 2);
        $instance->set('usertype', 'deprecated');
        $instance->set('groups', array($defaultUserGroup));

        $instance->set('id', 0);
        $instance->set('name', $user['fullname']);
        $instance->set('username', $user['username']);
        if (array_key_exists('password', $user) && $user['password'] != "")
            $instance->set('password', $user['password']);

        if (array_key_exists('password_clear', $user))
            $instance->set('password_clear', $user['password_clear']);

        $instance->set('email', $user['email']); // Result should contain an email (check)
        $instance->setParam('language', $user['language']);

        if ($activationMode != 0)
        {
            jimport('joomla.user.helper');
            $instance->set('activation', JApplication::getHash(JUserHelper::genRandomPassword()));
            $instance->set('block', 1);
        }
        else
            $instance->set('block', 0);

        if (!$instance->save())
        {
            JFactory::getApplication()->enqueueMessage($instance->getError(), 'error');
            $instance = null;
        }

        return $instance;
    }

    /** Activation and new user email functions *****/
    private function activateUser()
    {
        $useractivation = $this->getActivationMode();
        $language = JFactory::getLanguage();
        $app = JFactory::getApplication();

        # Send out the new registration email
        // figure out activation
        $language->load('com_users');
        if ($useractivation == 2)
            $app->enqueueMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_VERIFY'));
        else if ($useractivation == 1)
            $app->enqueueMessage(JText::_('COM_USERS_REGISTRATION_COMPLETE_ACTIVATE'));

        if ($useractivation == 0)
            return true;
        else
            return false;
    }

    private function getActivationMode()
    {
        if (JFBCFactory::config()->getSetting('joomla_skip_newuser_activation'))
        {
            return 0;
        }
        else
        {
            $params = JComponentHelper::getParams('com_users');
            $useractivation = $params->get('useractivation');
            return $useractivation;
        }
    }

    private function sendNewUserEmails(&$user, $providerName)
    {
        $app = JFactory::getApplication();
        $sendEmail = true;
        $profileEmails = $app->triggerEvent('socialProfilesSendsNewUserEmails');
        foreach ($profileEmails as $pe)
        {
            if ($pe)
                $sendEmail = false;
        }
        if (!$sendEmail)
            return;

        $useractivation = $this->getActivationMode();

        $newEmail = $user->get('email');
        if (SCStringUtilities::endswith($newEmail, "@unknown.com"))
            return;

        // Compile the notification mail values.
        $config = JFactory::getConfig();
        $params = JComponentHelper::getParams('com_users');
        $language = JFactory::getLanguage();
        $language->load('com_users');
        SCStringUtilities::loadLanguage('com_jfbconnect');

        $data = $user->getProperties();
        $data['fromname'] = $config->get('fromname');
        $data['mailfrom'] = $config->get('mailfrom');
        $data['sitename'] = $config->get('sitename');
        $data['siteurl'] = JUri::base();

        $uri = JURI::getInstance();
        $base = $uri->toString(array('scheme', 'user', 'pass', 'host', 'port'));
        $data['activate'] = $base . JRoute::_('index.php?option=com_users&task=registration.activate&token=' . $data['activation'], false);

        if (JFBCFactory::config()->get('registration_send_new_user_email') || $useractivation > 0)
        {
            $emailSubject = JText::sprintf(
                'COM_USERS_EMAIL_ACCOUNT_DETAILS',
                $data['name'],
                $data['sitename']
            );

            // Handle account activation/confirmation emails.
            if ($useractivation == 2)
            {
                // Set the link to confirm the user email.
                if ($this->_newUserPassword == '')
                {
                    $emailBody = JText::sprintf('COM_JFBCONNECT_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY_NOPASSWORD',
                        $data['name'],
                        $data['sitename'],
                            $data['siteurl'] . 'index.php?option=com_users&task=registration.activate&token=' . $data['activation'],
                        $data['siteurl'],
                        $providerName
                    );
                }
                else
                {
                    $emailBody = JText::sprintf('COM_JFBCONNECT_EMAIL_REGISTERED_WITH_ADMIN_ACTIVATION_BODY',
                        $data['name'],
                        $data['sitename'],
                            $data['siteurl'] . 'index.php?option=com_users&task=registration.activate&token=' . $data['activation'],
                        $data['siteurl'],
                        $providerName,
                        $data['username'],
                        $this->_newUserPassword
                    );
                }
            }
            else if ($useractivation == 1)
            {
                // Set the link to activate the user account.
                if ($this->_newUserPassword == '')
                {
                    $emailBody = JText::sprintf(
                        'COM_JFBCONNECT_EMAIL_REGISTERED_WITH_ACTIVATION_BODY_NOPASSWORD',
                        $data['name'],
                        $data['sitename'],
                            $data['siteurl'] . 'index.php?option=com_users&task=registration.activate&token=' . $data['activation'],
                        $data['siteurl'],
                        $providerName
                    );
                }
                else
                {
                    $emailBody = JText::sprintf(
                        'COM_JFBCONNECT_EMAIL_REGISTERED_WITH_ACTIVATION_BODY',
                        $data['name'],
                        $data['sitename'],
                            $data['siteurl'] . 'index.php?option=com_users&task=registration.activate&token=' . $data['activation'],
                        $data['siteurl'],
                        $providerName,
                        $data['username'],
                        $this->_newUserPassword
                    );
                }
            }
            else
            {
                $emailSubject = JText::sprintf(
                    'COM_USERS_EMAIL_ACCOUNT_DETAILS',
                    $data['name'],
                    $data['sitename']
                );

                if ($this->_newUserPassword == '')
                    $emailBody = JText::sprintf('COM_JFBCONNECT_EMAIL_REGISTERED_BODY_NOPASSWORD', $data['name'], $data['sitename'], $data['siteurl'], $providerName);
                else
                    $emailBody = JText::sprintf('COM_JFBCONNECT_EMAIL_REGISTERED_BODY', $data['name'], $data['sitename'], $data['siteurl'], $providerName, $data['username'], $this->_newUserPassword);
            }

            // Send the registration email.
            $return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $data['email'], $emailSubject, $emailBody);
        }
        else
        {
            $return = true;
        }

        if (($useractivation < 2) && ($params->get('mail_to_admin') == 1))
        {
            $emailSubject = JText::sprintf(
                'COM_USERS_EMAIL_ACCOUNT_DETAILS',
                $data['name'],
                $data['sitename']
            );

            $emailBodyAdmin = JText::sprintf(
                'COM_USERS_EMAIL_REGISTERED_NOTIFICATION_TO_ADMIN_BODY',
                $data['name'],
                $data['username'],
                $data['siteurl']
            );

            // get all admin users
            $query = 'SELECT name, email, sendEmail' .
                    ' FROM #__users' .
                    ' WHERE sendEmail=1';

            $db = JFactory::getDBO();
            $db->setQuery($query);
            $rows = $db->loadObjectList();

            // Send mail to all superadministrators id
            foreach ($rows as $row)
            {
                $return = JFactory::getMailer()->sendMail($data['mailfrom'], $data['fromname'], $row->email, $emailSubject, $emailBodyAdmin);

                // Check for an error.
                if ($return !== true)
                {
                    $this->setError(JText::_('COM_USERS_REGISTRATION_ACTIVATION_NOTIFY_SEND_MAIL_FAILED'));
                    return false;
                }
            }
        }

        // Check for an error.
        if ($return !== true)
        {
            $this->setError(JText::_('COM_USERS_REGISTRATION_SEND_MAIL_FAILED'));

            // Send a system message to administrators receiving system mails
            $db = JFactory::getDBO();
            $q = "SELECT id
        FROM #__users
        WHERE block = 0
        AND sendEmail = 1";
            $db->setQuery($q);
            $sendEmail = $db->loadColumn();
            if (count($sendEmail) > 0)
            {
                $jdate = new JDate();
                // Build the query to add the messages
                $q = "INSERT INTO `#__messages` (`user_id_from`, `user_id_to`, `date_time`, `subject`, `message`)
            VALUES ";
                $messages = array();
                foreach ($sendEmail as $userid)
                {
                    $messages[] = "(" . $userid . ", " . $userid . ", '" . $jdate->toSql() . "', '" . JText::_('COM_USERS_MAIL_SEND_FAILURE_SUBJECT') . "', '" . JText::sprintf('COM_USERS_MAIL_SEND_FAILURE_BODY', $return, $data['username']) . "')";
                }
                $q .= implode(',', $messages);
                $db->setQuery($q);
                $db->execute();
            }
            return false;
        }
    }
}

?>
