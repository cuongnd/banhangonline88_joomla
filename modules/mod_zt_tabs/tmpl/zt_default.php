<?php
use Joomla\Registry\Registry;
/**
 * @package ZT Tabs module
 * @author DucNA
 * @copyright (C) 2014- ZooTemplate.Com
 * @license PHP files are GNU/GPL
 **/
defined( '_JEXEC' ) or die( 'Access Deny' );
$document = JFactory::getDocument();
$uri = JURI::getInstance();
JHtml::_('jquery.framework');
$document->addScript(JUri::root().'media/system/js/Zozo_Tabs_v.6.5/js/zozo.tabs.js');
$document->addStyleSheet(JUri::root().'media/system/js/Zozo_Tabs_v.6.5/css/zozo.tabs.min.css');
$document->addStyleSheet(JUri::root().'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.core.css');
$document->addStyleSheet(JUri::root().'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.responsive.css');
$document->addStyleSheet(JUri::root().'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.clean.css');
$document->addStyleSheet(JUri::root().'media/system/js/Zozo_Tabs_v.6.5/source/zozo.tabs.themes.css');
$jv_selection=$params->get('jv_selection','');
$jv_selection=explode(',',$jv_selection);
$list_module=array();
$module_list=JModuleHelper::getModuleList();
$module_list=JArrayHelper::pivot($module_list,'id');
foreach($jv_selection as $item_module){
    $array_module=explode('_',$item_module);
    $module_id=$array_module[1];
    $object_module=$module_list[$module_id];
    $object_module->module_content=JModuleHelper::renderModule($object_module);
    $temp = new Registry;
    $temp->loadString($object_module->params);
    $object_module->params=$temp;
    $list_module[]=$object_module;
}

?>



<div id="zt-module-tabs_<?php echo $module->id ?>">

    <!-- Tab Navigation Menu -->
    <ul>
        <?php foreach($list_module as $item_module){ ?>
        <li><a><?php if ($image_path = $item_module->params->get('module_image')) { ?><img style="width: 30px" src="<?php echo JUri::root() ?><?php echo  $image_path  ?>"><?php } ?><?php echo $item_module->title ?></a></li>
        <?php } ?>
    </ul>

    <!-- Content container -->
    <div>
        <?php foreach($list_module as $item_module){ ?>
        <div>
            <?php echo $item_module->module_content ?>
        </div>
        <?php } ?>
    </div>

</div>



<?php
$js_content = '';
$doc=JFactory::getDocument();
ob_start();
?>
<script type="text/javascript">
    jQuery(document).ready(function ($) {
        $("#zt-module-tabs_<?php echo $module->id ?>").zozoTabs({
            theme: "<?php echo $params->get('theme','') ?>",
            orientation: "<?php echo $params->get('orientation','horizontal') ?>",
            position: "<?php echo $params->get('position','top-left') ?>",
            size: "medium",
            animation: {
                easing: "easeInOutExpo",
                duration: 400,
                effects: "<?php echo $params->get('effects','face') ?>"
            },
            modes:"menu",
            event: "<?php echo $params->get('event','click') ?>",
            classes: "<?php echo $params->get('classes','z-tabs') ?>",
            defaultTab:"tab1"
        });




    });
</script>
<?php
$js_content = ob_get_clean();
$js_content = JUtility::remove_string_javascript($js_content);
$doc->addScriptDeclaration($js_content);

?>


