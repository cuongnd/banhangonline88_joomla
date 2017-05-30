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

class JFormFieldProviderloginbutton extends JFormField
{
    protected function getInput()
    {
        $html = array();
        $provider = $this->element['provider'] ? (string)$this->element['provider'] : null;
        $style = $this->element['style'] ? (string)$this->element['style'] . '"' : '';
        // Initialize some field attributes.

        $class = !empty($this->class) ? ' class="radio ' . $this->class . '"' : ' class="radio"';
        $required = $this->required ? ' required aria-required="true"' : '';
        $autofocus = $this->autofocus ? ' autofocus' : '';
        $disabled = $this->disabled ? ' disabled' : '';
        $readonly = $this->readonly;

        $style = 'style="float:left;' . $style . '"';

        $html[] = '<div style="clear: both"> </div>';

        // Start the radio field output.
        $html[] = '<fieldset id="' . $this->id . '"' . $class . $required . $autofocus . $disabled . $style . ' >';

        // Get the field options.
        $options = $this->getOptions();

        $p = JFBCFactory::provider($provider);
        // Build the radio field output.
        $html[] = '<label class="providername">' . $p->name . '</label>';

        foreach ($options as $i => $option)
        {
            // Initialize some option attributes.
            $checked = ((string)$option->value == (string)$this->value) ? ' checked="checked"' : '';
            $class = !empty($option->class) ? ' class="' . $option->class . '"' : '';

            $disabled = !empty($option->disable) || ($readonly && !$checked);

            $disabled = $disabled ? ' disabled' : '';

            $html[] = '<input type="radio" id="' . $this->id . $i . '" name="' . $this->name . '" value="'
                    . htmlspecialchars($option->value, ENT_COMPAT, 'UTF-8') . '"' . $checked . $class . $required . $disabled . ' />';

            $html[] = '<label for="' . $this->id . $i . '"' . $class . ' >' .
                    '<img src="' . JUri::root() . 'media/sourcecoast/images/provider/' . $provider . '/' . $option->value . '" />' .
                    #. JText::alt($option->text, preg_replace('/[^a-zA-Z0-9_\-]/', '_', $this->fieldname)) . '</label>'
                    '</label>' .

                    $required = '';
        }

        // End the radio field output.
        $html[] = '</fieldset>';
        $html[] = '<div style="clear: both"> </div>';

        return implode($html);
    }

    protected function getOptions()
    {
        // Scan the /media/sourcecoast/images/provider directory for this provider's buttons
        // Merge in any custom images from ??
        $provider = $this->element['provider'] ? (string)$this->element['provider'] : null;

        $options = array();
        $buttons = $this->getButtons('/media/sourcecoast/images/provider/' . $provider);
        if ($buttons)
        {
            foreach ($buttons as $button)
            {
                $options[] = JHtml::_('select.option', $button, $button, 'value', 'text', false);
            }
        }

        reset($options);
        return $options;
    }

    private function getButtons($folder)
    {
        $folder = JPATH_SITE . $folder;
        $buttons = array();

        if (JFolder::exists($folder))
        {
            $buttons = JFolder::files($folder, '^' . '.*(\.png|\.jpg|\.gif)$');
        }
        return $buttons;
    }
}
