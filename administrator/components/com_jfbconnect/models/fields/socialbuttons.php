<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.helper');
jimport('joomla.filesystem.folder');

class JFormFieldSocialbuttons extends JFormField
{
    protected function getInput()
    {
        require_once(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/models/fields/providerloginbutton.php');
        $html = array();
        $data = $this->form->getValue($this->fieldname, $this->group, null);

        // Support for SCLogin module
        $loginbuttonstype = $this->form->getValue('loginbuttonstype', 'params', null);
        if (!$loginbuttonstype) // Support for SCSocialWidget module
            $loginbuttonstype = $this->form->getValue('loginbuttonstype', 'params.widget_settings', 'default');

        if ($loginbuttonstype == 'default')
        {
            $noticeStyle = 'style="display:block"';
            $buttonStyle = "display:none";
        }
        else
        {
            $noticeStyle = 'style="display:none"';
            $buttonStyle = "display:block";
        }

        jimport('sourcecoast.utilities');
        SCStringUtilities::loadLanguage('com_jfbconnect', JPATH_ADMINISTRATOR);
        $html[] = '<div class="fieldsocialbuttons">';
        JFactory::getDocument()->addStyleDeclaration('.fieldsocialbuttons label { display: inline; float:left; }
            .fieldsocialbuttons input[type="radio"] { margin: 0 8px; }
            label.providername { width:60px; }');
        $html[] = '<div style="clear:both"> </div>';
        $html[] = '<fieldset id="loginbutton_custom_notice" ' . $noticeStyle . '>' . JText::_('COM_JFBCONNECT_LOGIN_BUTTON_CUSTOM_INSTRUCTIONS') . '</fieldset>';
        $providerFound = false;
        foreach (JFBCFactory::getAllProviders() as $p)
        {
            if ($p->appId)
            {
                $providerFound = true;
                $value = (is_array($data) && array_key_exists($p->systemName, $data)) ? $data[$p->systemName] : 'icon_label.png';
                $field = '<field type="providerloginbutton"
                    label="Default Login Button"
                    provider="' . $p->systemName . '"
                                name="' . $p->systemName . '"
                                required="true"
                                style="' . $buttonStyle . '"
                                />';
                $element = new SimpleXMLElement($field);
                $node = new JFormFieldProviderloginbutton($this->form);
                $node->setup($element, $value, $this->group . '.' . $this->fieldname);

                $html[] = $node->getInput();
            }
        }
        if (!$providerFound)
            $html[] = '<label>No social networks are enabled. Please use the JFBConnect configuration area to set your App IDs and Secret Keys</label>';

        $html[] = '</div>';
        return implode($html);
    }

}
