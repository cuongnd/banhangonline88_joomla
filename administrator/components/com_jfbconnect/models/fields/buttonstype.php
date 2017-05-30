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
JFormHelper::loadFieldClass('list');

class JFormFieldButtonstype extends JFormFieldList
{
    public function getInput()
    {
        $onchange = "if (this.value=='default')
        {
            document.getElementById('loginbutton_custom_notice').style.display='block';
            var fieldsets = document.getElementsByTagName('fieldset');
            for(var i = 0; i < fieldsets.length; i++) {
                if(fieldsets[i].id.indexOf('jform_params_') == 0 && fieldsets[i].id.indexOf('_loginbuttons_') > 0) {
                    fieldsets[i].style.display='none';
                }
            }
        }
        else
        {
            document.getElementById('loginbutton_custom_notice').style.display='none';
            var fieldsets = document.getElementsByTagName('fieldset');
            for(var i = 0; i < fieldsets.length; i++) {
                if(fieldsets[i].id.indexOf('jform_params_') == 0 && fieldsets[i].id.indexOf('_loginbuttons_') > 0) {
                    fieldsets[i].style.display='block';
                }
            }
        }";
        // J3.x
        $this->onchange = $onchange;
        // J2.5
        $this->element->addAttribute('onchange', $onchange);
        return parent::getInput();
    }

    public function getOptions()
    {
        SCStringUtilities::loadLanguage('com_jfbconnect', JPATH_ADMINISTRATOR);
        $options = array();
        $options[] = JHtml::_(
        				'select.option', 'default',
                        JText::_('COM_JFBCONNECT_SOCIAL_LOGIN_BUTTON_TYPE_DEFAULT'), 'value', 'text');
        $options[] = JHtml::_(
        				'select.option', 'custom',
        				JText::_('COM_JFBCONNECT_SOCIAL_LOGIN_BUTTON_TYPE_CUSTOM'), 'value', 'text');
        return $options;
    }
}
