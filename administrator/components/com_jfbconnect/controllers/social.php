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

class JFBConnectControllerSocial extends JFBConnectController
{
    function apply()
    {
        $app = JFactory::getApplication();
        $configs = JRequest::get('POST', 4);
        $model = $this->getModel('config');
        $model->saveSettings($configs);
        $app->enqueueMessage(JText::_('COM_JFBCONNECT_MSG_SETTINGS_UPDATED'));
        $this->setRedirect('index.php?option=com_jfbconnect&view=social');
    }

}