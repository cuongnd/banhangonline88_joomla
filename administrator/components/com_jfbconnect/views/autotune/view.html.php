<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Restricted access');

jimport('joomla.application.component.view');

class JFBConnectViewAutotune extends JViewLegacy
{
    function display($tpl = null)
    {
        $configModel = JFBCFactory::config();

        if ($this->getLayout() == 'fbapp')
        {
            $appConfig = $this->get('mergedRecommendations');
            $this->assignRef('appConfig', $appConfig);

            $appConfigUpdated = $configModel->getUpdatedDate('autotune_app_config');
            $fieldsUpdated = $configModel->getUpdatedDate('autotune_field_descriptors');
            $this->assignRef('appConfigUpdated', $appConfigUpdated);
            $this->assignRef('fieldsUpdated', $fieldsUpdated);

            $subscriberId = $configModel->getSetting('sc_download_id', 'No ID Set!');
            $this->assignRef('subscriberId', $subscriberId);
        }

        $this->assignRef('config', $configModel);
        $this->addToolbar();

        parent::display($tpl);

        $atModel = $this->getModel('autotune');
        $subStatus = $atModel->getSubscriptionStatus();
        if ($subStatus)
        {
            $subStatus = $subStatus->messages;
            $this->assignRef('subStatus', $subStatus);

            $subStatusUpdated = $configModel->getUpdatedDate('autotune_authorization');
            $subStatusUpdated = strftime("%Y/%m/%d", strtotime($subStatusUpdated));
            $this->assignRef('subStatusUpdated', $subStatusUpdated);
            include('tmpl/subscription_status.php');
        }
    }

    function addToolbar()
    {
        JToolBarHelper::title(JText::_('COM_JFBCONNECT_TITLE_AUTOTUNE'), 'jfbconnect.png');
    }
}
