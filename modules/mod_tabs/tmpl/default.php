<?php
defined('_JEXEC') or die;
JHtml::_('jquery.framework');
JHtml::_('jquery.ui');
JHtml::_('jquery.zozo_tab');
JHtml::_('jQuery.lazyload');
JHtml::_('jQuery.appear');
JHtml::_('jQuery.flip');
use Joomla\Registry\Registry;
$doc=JFactory::getDocument();
$doc->addScript(JUri::root().'modules/mod_tabs/assets/js/jquery.mod_tabs.js');
$doc->addLessStyleSheet(JUri::root().'modules/mod_tabs/assets/less/mod_tabs.less');
$params=$module->params;
jimport('joomla.application.module.helper');
$jv_selection = $params->get('jv_selection');
if (trim($jv_selection)!='') {
    $jv_selection = explode(',', $jv_selection);
}
?>
<div class="mod_tabs" id="mod_tabs_<?php echo $module->id ?>">
    <?php if($module->showtitle): ?><h3 class="module-title"><?php echo $module->title ?></h3> <?php endif; ?>
    <div class="tabs" id="tabbed-nav-<?php echo $module->id ?>">
        <!-- Tab Navigation Menu -->
        <ul>
            <?php
            if (count($jv_selection)) {
                foreach ($jv_selection as $item_element) {
                    $item_element = explode('_', $item_element);
                    switch ($item_element[0]) {
                        case "module":
                            $module_id = $item_element[1];
                            $item_module = JModuleHelper::getModuleById($module_id);

                            if ($item_module) {
                                $item_params = Registry::getInstance('module_id_'.$item_module->id);
                                if(empty($item_params->toArray()))
                                {
                                    $item_params->loadString($item_module->params);
                                }
                                $module_image=$item_params->get('module_image','');
                                ?>
                                <li><a><?php if($module_image!=''): ?><img class="icon" src="<?php echo JUri::root().$module_image ?>"><?php endif; ?><?php echo $item_module->title ?></a></li>
                                <?php
                            }
                            break;
                        case "category":
                            //echo "i is bar";
                            break;
                        case "article":
                            //echo "i is cake";
                            break;
                    }
                }
            }

            ?>
        </ul>

        <!-- Content container -->
        <div>
            <?php
            if (count($jv_selection)) {
                foreach ($jv_selection as $item_element) {
                    $item_element = explode('_', $item_element);
                    switch ($item_element[0]) {
                        case "module":
                            $module_id = $item_element[1];
                            $item_module = JModuleHelper::getModuleById($module_id);
                            if ($module) {
                                $attribs['style'] = 'xhtml';
                                ?>
                                <div>
                                    <?php  echo JModuleHelper::renderModule($item_module, $attribs); ?>
                                </div>

                                <?php
                            }
                            break;
                        case "category":
                            //echo "i is bar";
                            break;
                        case "article":
                            //echo "i is cake";
                            break;
                    }
                }
            }

            ?>
        </div>

    </div>
</div>
<?php
$js_content = '';
$doc = JFactory::getDocument();
ob_start();
?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $("#mod_tabs_<?php echo $module->id ?>").mod_tabs({
            module_id:<?php echo $module->id   ?>,
            style: "<?php echo $style ?>",
            lazyload: "<?php echo json_encode($lazyload) ?>",
            deconstruction: "<?php echo json_encode($lazyload) ?>",
            params:<?php echo json_encode($params->toObject()) ?>
        });
    });
</script>
<?php
$js_content = ob_get_clean();
$js_content = JUtility::remove_string_javascript($js_content);
$doc->addScriptDeclaration($js_content);
?>

