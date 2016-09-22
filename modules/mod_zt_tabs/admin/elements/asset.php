<?php

defined('JPATH_BASE') or die;

jimport('joomla.form.formfield');

class JFormFieldAsset extends JFormField {
    protected $type = 'Asset';
    protected function getInput() {

        $doc = JFactory::getDocument();
        $doc->addScript(JURI::root().'modules/mod_zt_tabs/admin/js/mootools-more.js');
        $doc->addScript(JURI::root().'modules/mod_zt_tabs/admin/js/script.js');
        return null;
    }
}
