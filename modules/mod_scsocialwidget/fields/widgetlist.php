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

class JFormFieldWidgetList extends JFormField
{
    public $type = 'WidgetList';

    protected function getInput()
    {
        JFactory::getDocument()->addScript(JUri::root() . 'media/sourcecoast/js/jq-bootstrap-1.8.3.js');
        JFactory::getDocument()->addScript('components/com_jfbconnect/assets/jfbconnect-admin.js');

        $modProvider = $this->form->getValue('params.provider_type');
        $widgets = JFBCFactory::getAllWidgets($modProvider);

        $options = array();
        $options[] = JHtml::_('select.option', "widget", "--Select your widget--");
        foreach ($widgets as $widget)
        {
            $options[] = JHtml::_('select.option', $widget->getSystemName(), $widget->getName());
        }

        $html = JHTML::_('select.genericlist', $options, 'jform[params][widget_type]', 'onchange="jfbcAdmin.scsocialwidget.fetchSettings(this.value);"', 'value', 'text', $this->form->getValue('params.widget_type'));
        return '<div id="widget_list">' . $html . '</div>';
    }

    protected function getLabel()
    {
        return '';
    }
}
