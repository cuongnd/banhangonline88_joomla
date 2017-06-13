<?php
defined('_JEXEC') or die;
$app=JFactory::getApplication();
$input=$app->input;
$session=JFactory::getSession();
$post=json_decode(file_get_contents('php://input'));
$tab_index=$post->tab_index;
$params = $module->params;
jimport('joomla.application.module.helper');
$jv_selection = $params->get('jv_selection');
if (trim($jv_selection) != '') {
    $jv_selection = explode(',', $jv_selection);
}
$session->set(mod_tabs_helper::MOD_TABS_TAB_ACTIVE . $module->id,$tab_index);
$item_element=$jv_selection[$tab_index];
$item_element = explode('_', $item_element);
switch ($item_element[0]) {
    case "module":
        $module_id = $item_element[1];
        $item_module = JModuleHelper::getModuleById($module_id);
        if ($item_module) {
            $attribs['style'] = 'xhtml';
            echo JModuleHelper::renderModule($item_module, $attribs);
        }
        break;
    case "category":
        //echo "i is bar";
        break;
    case "article":
        //echo "i is cake";
        break;
}
