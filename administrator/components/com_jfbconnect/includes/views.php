<?php
/**
 * @package         JFBConnect
 * @copyright (c)   2009-2014 by SourceCoast - All Rights Reserved
 * @license         http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 * @version         Release v6.2.2
 * @build-date      2014/10/03
 */

defined('_JEXEC') or die('Unauthorized Access');

class JFBConnectAdminView extends JViewLegacy
{
    protected $forms = array();

    public function display($tpl = null)
    {
        $this->addToolbar();
        if (defined('SC16')) :
            JFactory::getDocument()->addStyleDeclaration(
                '.sourcecoast .form-horizontal .control-label { text-align:left !important; }
                .sourcecoast .well { background-color:white; }
                ');
        endif; // SC16
        parent::display($tpl);
    }

    public function addToolbar()
    {
    }

    public function tabsStart($tabGroup, $active)
    {
        if (defined('SC30')) :
            echo JHtml::_('bootstrap.startTabSet', $tabGroup, array('active' => $active));
        endif; // SC30
        if (defined('SC16')):
            echo JHTML::_('tabs.start');
        endif; //SC16
    }

    public function tabsEnd()
    {
        if (defined('SC16')) :
            echo JHtml::_('tabs.end');
        endif; // SC16
        if (defined('SC30')) :
            echo JHtml::_('bootstrap.endTabSet');
        endif; // SC30
    }

    public function tabStart($tabGroup, $tabName, $label)
    {
        if (defined('SC30')) :
            echo JHtml::_('bootstrap.addTab', $tabGroup, $tabName, $label, true);
        endif;
        if (defined('SC16')) :
            echo JHtml::_('tabs.panel', $label, $tabName);
        endif; // SC16

    }

    public function tabEnd()
    {
        if (defined('SC30')) :
            echo JHtml::_('bootstrap.endTab');
        endif;
    }

    // This form stuff should be broken into it's own class
    public function formLoad($name, $formPath, $options = array())
    {
        $form = JForm::getInstance('com_jfbconnect_' . $name, $formPath, $options);
        $this->forms[$name] = $form;
    }

    public function formBind($name, $data = null)
    {
        if (!isset($this->forms[$name]))
            return false;

        if (!$data)
            $data = JFBCFactory::config()->getSettings();
        $this->forms[$name]->bind($data);
    }

    public function formShowField($field)
    {
        if ($field->hidden)
            echo $field->input;
        else
        {
            $labelClass = $field->type == 'Providerloginbutton' ? 'login-button' : '';
            echo "  <div class=\"control-group\">\n";
            echo "   " . $field->label . "\n";
            echo "     <div class=\"controls " . $labelClass . "\">\n";
            echo "       " . $field->input . "\n";
            echo "     </div>\n";
            echo "  </div>\n";
        }
    }

    public function formDisplay($name, $columns = null)
    {
        $form = $this->forms[$name];
        // use a 2 column layout (by default) for more than one fieldset, if not passed in
        $blocks = count($form->getFieldsets());
        if (!$columns)
            $columns = $blocks > 1 ? 2 : 1;
        $span = $columns == 1 ? 12 : 6;
        $split = ceil($blocks / 2);
        $column = 0;
        echo "\n<div class=\"row-fluid\">\n";
        foreach ($form->getFieldsets() as $fiedsets => $fieldset)
        {
            if ($column == 0 || $column == $split)
                echo '<div class="span' . $span . '">' . "\n";
            echo "<div class=\"well\">\n";
            if ($fieldset->label)
                $label = $fieldset->label;
            else
                $label = JText::_(strtoupper($form->getName()) . '_MENU_' . strtoupper($fieldset->name));
            echo '<legend>' . $label . "</legend>\n";
            foreach ($form->getFieldset($fieldset->name) as $field)
                $this->formShowField($field);
            echo "</div>\n";
            $column++;
            if ($column == $split)
                echo "</div>\n";
        }
        echo ($column > $split) ? "</div>\n" : '';
        echo "</div>\n";
    }
}