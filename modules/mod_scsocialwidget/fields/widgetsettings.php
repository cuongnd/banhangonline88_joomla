<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('JPATH_PLATFORM') or die;

jimport('sourcecoast.utilities');
jimport('joomla.form.helper');
JFormHelper::loadFieldClass('list');

class JFormFieldWidgetSettings extends JFormField
{
    public $type = 'WidgetSettings';

    protected function getInput()
    {
        $html = '';
        if($this->form->getValue('params.provider_type') && $this->form->getValue('params.widget_type') != 'widget')
        {
            SCStringUtilities::loadLanguage('com_jfbconnect', JPATH_ADMINISTRATOR);
            JForm::addFieldPath(JPATH_ROOT .'/components/com_jfbconnect/libraries/provider/'.$this->form->getValue('params.provider_type').'/widget');
            $xmlFile = JPATH_ROOT .'/components/com_jfbconnect/libraries/provider/'.$this->form->getValue('params.provider_type').'/widget/'.$this->form->getValue('params.widget_type').'.xml';

            if(JFile::exists($xmlFile))
            {
                $options = array('control' => 'jform');
                $form = JForm::getInstance('com_jfbconnect_' . $this->form->getValue('params.widget_type'), $xmlFile, $options);

                $registry = $this->form->getValue('params');

                $settings = new JRegistry();
                $settings->set('params.widget_settings', $registry->widget_settings);
                $form->bind($settings);
                ob_start();
                foreach ($form->getFieldsets() as $fieldsets => $fieldset)
                {
                    foreach ($form->getFieldset($fieldset->name) as $field)
                        $this->formShowField($field);
                }
                $html = ob_get_clean();
            }
        }

        return '<div id="widget_settings">' . $html . '</div>';
    }

    protected function getLabel()
    {
        return '';
    }

    public function formShowField($field)
    {
        echo "  <div class=\"control-group\">\n";
        echo "    <div class=\"control-label\">\n";
        echo "   " . $field->getLabel() . "\n";
        echo "   </div>\n";
        echo "     <div class=\"controls\">\n";
        echo "       " . $field->getInput() . "\n";
        echo "     </div>\n";
        echo "  </div>\n";
    }

    public function getControlGroup()
   	{
        return $this->getInput();
   	}
}
