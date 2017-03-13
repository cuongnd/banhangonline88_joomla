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
jimport('sourcecoast.adminHelper');

require_once(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/includes/views.php');

class JFBConnectViewConfig extends JFBConnectAdminView
{
    function display($tpl = null)
    {
        JForm::addFieldPath(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/models/fields');
        $this->formLoad('config', JPATH_ADMINISTRATOR . '/components/com_jfbconnect/models/forms/config.xml');
        $this->formBind('config', JFBCFactory::config()->getSettings());

        foreach (JFBCFactory::getAllProviders() as $p)
        {
            $configPath = JPATH_SITE . '/components/com_jfbconnect/libraries/provider/' . strtolower($p->systemName) . '/config/';
            JForm::addFieldPath($configPath . 'fields');
            $this->formLoad($p->systemName, $configPath . 'config.xml');
            $loginField = '<form>' .
                    '<fieldset name="login_button" label="' . JText::_('COM_JFBCONNECT_PROVIDER_MENU_LOGIN_BUTTON') . '">' .
                    '<field type="providerloginbutton"
                    label="' . JText::_('COM_JFBCONNECT_CONFIG_LOGIN_BUTTON_DEFAULT_LABEL') . '"
                    description="' . JText::_('COM_JFBCONNECT_CONFIG_LOGIN_BUTTON_DEFAULT_DESC') . '"
                    provider="' . $p->systemName . '"
                    name="' . $p->systemName . '_login_button"
                    required="true"
                    default="icon_label.png"
                    />' .
                    '</fieldset>' .
                    '</form>';
            $this->forms[$p->systemName]->load($loginField);

            $this->formBind($p->systemName, JFBCFactory::config()->getSettings());
        }
        parent::display($tpl);
    }

    function addToolbar()
    {
        JToolBarHelper::title('JFBConnect', 'jfbconnect.png');
        JToolBarHelper::apply('apply', JText::_('COM_JFBCONNECT_BUTTON_APPLY_CHANGES'));
        SCAdminHelper::addAutotuneToolbarItem();
    }
}
