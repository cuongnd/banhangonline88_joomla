<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

// No direct access
defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');
jimport('joomla.user.helper');
jimport('sourcecoast.utilities');

define('USERNAME_LEAVE_BLANK', 0);
define('USERNAME_GENERATE_SHOW', 1);
define('USERNAME_GENERATE_HIDE', 2);
define('USERNAME_GENERATE_DISABLE', 3);
define('EMAIL_HIDE', 0);
define('EMAIL_SHOW', 1);
define('EMAIL_SHOW_DISABLE', 2);
define('NAME_HIDE', 0);
define('NAME_SHOW', 1);

class JFBConnectViewLoginregister extends JViewLegacy
{
    function display($tpl = null)
    {
        $app = JFactory::getApplication();
        $input = JFactory::getApplication()->input;
        $provider = $input->getCmd('provider', null);
        if (!$provider)
            $app->redirect('index.php');

        $provider = JFBCFactory::provider($provider);
        $providerUserId = $provider->getProviderUserId();
        $profile = $provider->profile->fetchProfile($providerUserId, array('first_name', 'last_name', 'email', 'full_name'));

        if ($providerUserId == null)
            $app->redirect('index.php');

        $args = array(strtolower($provider->name));
        $profileFields = $app->triggerEvent('socialProfilesOnShowRegisterForm', $args);

        // Get previously filled in values
        $postData = $app->getUserState('com_jfbconnect.registration.data', array());

        $email1 = '';
        $email2 = '';

        SCUserUtilities::getDisplayEmail($postData, $profile->get('email'), $email1, $email2);
        $config = JFBCFactory::config();

        $providerUsername = '';
        $postUsername = SCUserUtilities::getPostData($postData, 'username');
        if ($postUsername != '')
            $providerUsername = $postUsername;
        else if (JFBCFactory::config()->get('registration_show_username') > USERNAME_LEAVE_BLANK)
        {
            $usernamePrefixFormat = $config->get('auto_username_format');
            $providerUsername = SCUserUtilities::getAutoUsername($profile->get('first_name'), $profile->get('last_name'), $profile->get('email'), $provider->usernamePrefix, $providerUserId, $usernamePrefixFormat);
        }

        $providerMemberName = SCUserUtilities::getDisplayNameByFullName($postData, $profile->get('full_name'));

        $language = JFactory::getLanguage();
        $language->load('com_users');
        JModelLegacy::addIncludePath(JPATH_SITE . '/components/com_users/models');
        $userModel = JModelLegacy::getInstance('Registration', 'UsersModel');
        $this->data = $userModel->getData();
        JForm::addFormPath(JPATH_SITE . '/components/com_users/models/forms');
        JForm::addFieldPath(JPATH_SITE . '/components/com_users/models/fields');
        $this->form = $userModel->getForm();

        // Setup the fields we can pre-populate
        // To do: Give option to show/hide the name on the form
        $this->form->setValue('name', null, $providerMemberName);
        if($config->get('registration_show_name') == NAME_HIDE && $providerMemberName != '')
        {
            $this->form->setFieldAttribute('name', 'type', 'hidden');
        }

        $this->form->setValue('username', null, $providerUsername);
        if ($providerUsername != '')
        {
            if ($config->get('registration_show_username') == USERNAME_GENERATE_HIDE)
                $this->form->setFieldAttribute('username', 'type', 'hidden');
            else if ($config->get('registration_show_username') == USERNAME_GENERATE_DISABLE)
                $this->form->setFieldAttribute('username', 'readonly', 'true');
        }

        $this->form->setValue('email1', null, $email1);
        $this->form->setValue('email2', null, $email2);
        if ($email1 != '' && $email2 != '')
        {
            if ($config->get('registration_show_email') == EMAIL_HIDE)
            {
                $this->form->setFieldAttribute('email1', 'type', 'hidden');
                $this->form->setFieldAttribute('email2', 'type', 'hidden');
            }
            else if ($config->get('registration_show_email') == EMAIL_SHOW_DISABLE)
            {
                $this->form->setFieldAttribute('email1', 'readonly', 'true');
                $this->form->setFieldAttribute('email2', 'readonly', 'true');
            }
        }

        if ($config->get('registration_show_password') == 1)
        {
            $password = JUserHelper::genRandomPassword();
            $this->form->setValue('password1', null, $password);
            $this->form->setValue('password2', null, $password);
            $this->form->setFieldAttribute('password1', 'type', 'hidden');
            $this->form->setFieldAttribute('password2', 'type', 'hidden');
        }

        // Set an inputbox style on all the input elements so that inherited template styles look better
        $this->form->setFieldAttribute('name', 'class', 'inputbox required');
        $this->form->setFieldAttribute('username', 'class', 'validate-username inputbox required');
        $this->form->setFieldAttribute('email1', 'class', 'inputbox required');
        $this->form->setFieldAttribute('email2', 'class', 'inputbox required');
        $this->form->setFieldAttribute('password1', 'class', 'validate-password inputbox required');
        $this->form->setFieldAttribute('password2', 'class', 'validate-password inputbox required');

        //Check for form validation from each of the plugins
        $areProfilesValidating = $app->triggerEvent('socialProfilesAddFormValidation');
        $defaultValidationNeeded = true;
        foreach ($areProfilesValidating as $hasDoneValidation)
        {
            if ($hasDoneValidation == true)
            {
                $defaultValidationNeeded = false;
                break;
            }
        }

        // Setup the view appearance
        // TODO: Make the addStyleSheet into a Utilities function to be used elsewhere.
        $displayType = $config->get('registration_display_mode');;
        $css = JPath::find($this->_path['template'], 'loginregister.css');
        $css = str_replace(JPATH_SITE, '', $css);
        $css = str_replace('\\', "/", $css); //Windows support for file separators
        $doc = JFactory::getDocument();
        $doc->addStyleSheet($css);

        // get the other providers, for showing their login buttons
        $altParams = array();
        $allProviders = JFBCFactory::getAllProviders();
        $altProviders = array();
        foreach ($allProviders as $p)
        {
            if ($p->name != $provider->name)
                $altParams['providers'][] = $p->systemName;
        }
        $altParams['image'] = 'icon.png';
        // Set the session bit to check for a new login on next page load
        SCSocialUtilities::setJFBCNewMappingEnabled();

        $this->altParams = $altParams;
        $this->assignRef('providerUserId', $providerUserId);
        $this->assignRef('profile', $profile);
        $this->assignRef('configModel', $config);
        $this->assignRef('profileFields', $profileFields);
        $this->assignRef('defaultValidationNeeded', $defaultValidationNeeded);
        $this->assignRef('displayType', $displayType);
        $this->assignRef('providerName', $provider->name);
        $this->assignRef('altProviders', $altProviders);

        parent::display($tpl);
    }

}
