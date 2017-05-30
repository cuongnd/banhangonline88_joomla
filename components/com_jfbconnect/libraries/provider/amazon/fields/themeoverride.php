<?php

/**
 * @package		JFBConnect
 * @copyright (C) 2009-2014 by Source Coast - All rights reserved
 * @license http://www.gnu.org/licenses/gpl-2.0.html GNU/GPL
 */

defined('JPATH_PLATFORM') or die;

jimport('joomla.form.helper');
JFormHelper::loadFieldClass('color');

class JFormFieldThemeOverride extends JFormFieldColor
{
    public $type = 'ThemeOverride';

    protected function getInput()
    {
        $name = $this->name;
        $id = $this->id;

        $fields = array(
            'OUTER_BACKGROUND'=>'outer_bg',
            'INNER_BACKGROUND'=>'inner_bg',
            'BACKGROUND'=>'bg',
            'BORDER'=>'border',
            'HEADER_TEXT'=>'header_txt',
            'LINKED_TEXT'=>'linked_txt',
            'BODY_TEXT'=>'body_txt'
        );

        $html = '<div id="theme-override"><div class="span12">';
        $value = $this->value;

        //set default value
        $defaultval = array(
            'outer_bg' => '#DEDEDE',
            'inner_bg' => '#FFFFFF',
            'bg' => '#FFFFFF',
            'border' => '#636363',
            'header_txt' => '#000000',
            'linked_txt' => '#000000',
            'body_txt' => '#9C0000'
        );

        foreach($fields as $label => $field)
        {
            $this->name = $name.'['.$field.']';
            $this->id = $id.'_'.$field;
            $this->value = is_object($value) && isset($value->$field) ? $value->$field : $defaultval[$field];

            if($field == 'header_txt') $html .= '</div><div class="span12" style="margin: 10px 0 0 0;">';

            $html .= '<div class="span2 theme-override-'.$field.'">';
            $html .= '<small>'.JText::_('COM_JFBCONNECT_WIDGET_AMAZON_MY_FAVORITES_THEME_OVERRIDE_'.$label).'</small><br />';
            $html .= parent::getInput();
            $html .= '</div>';
        }
        $html .= "</div></div>";

        $html .= "
         <script type='text/javascript'>
        jfbcJQuery( document ).ready(function() {
            var arr = jfbcJQuery('#jform_params_widget_settings_theme').val().split('.');
            toogleDesign(arr[0]);
        });

        function toogleDesign(design) {
            if(design == '3' || design == '4' || design == '5'){
                jfbcJQuery('#theme-override .theme-override-outer_bg').addClass('hide');
                jfbcJQuery('#theme-override .theme-override-inner_bg').addClass('hide');
                jfbcJQuery('#theme-override .theme-override-bg').addClass('hide');
                jfbcJQuery('#theme-override .theme-override-border').addClass('hide');
            }

            if(design == '1') {
                jfbcJQuery('#theme-override .theme-override-outer_bg').removeClass('hide');
                jfbcJQuery('#theme-override .theme-override-inner_bg').removeClass('hide');
                jfbcJQuery('#theme-override .theme-override-border').addClass('hide');
                jfbcJQuery('#theme-override .theme-override-bg').addClass('hide');
            }

            if(design == '2') {
                jfbcJQuery('#theme-override .theme-override-outer_bg').removeClass('hide');
                jfbcJQuery('#theme-override .theme-override-bg').removeClass('hide');
                jfbcJQuery('#theme-override .theme-override-border').removeClass('hide');
                jfbcJQuery('#theme-override .theme-override-inner_bg').addClass('hide');
            }
        }
        </script>
        ";

      return $html;
    }

    protected function getLabel()
    {
        return parent::getLabel();
    }
}
