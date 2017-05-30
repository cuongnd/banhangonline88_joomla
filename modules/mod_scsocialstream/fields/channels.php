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
JFormHelper::loadFieldClass('checkboxes');

class JFormFieldChannels extends JFormFieldCheckboxes
{
    public $type = 'Channels';

    protected function getOptions()
    {
        JModelLegacy::addIncludePath(JPATH_ADMINISTRATOR . '/components/com_jfbconnect/models');
        $model = JModelLegacy::getInstance('Channels', 'JFBConnectModel');
        $channels = $model->getChannels(array('published' => 1));

        $options = array();

        foreach ($channels as $option)
        {
            // Create a new option object based on the <option /> element.
            $tmp = JHtml::_('select.option', $option->id, $option->title, 'value', 'text');
            $tmp->checked = false;

            // Add the option object to the result set.
            $options[] = $tmp;
        }

        reset($options);

        return $options;
    }

    protected function getLabel()
    {
        if (count($this->getOptions()) == 0)
            return "<label><strong>There are no Social Channels configured and published to display</strong></label>";

        return parent::getLabel();
    }
}
