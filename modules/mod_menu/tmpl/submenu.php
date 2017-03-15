<?php
use Joomla\Registry\Registry;
$app=JFactory::getApplication();
$input=$app->input;
$post=json_decode(file_get_contents('php://input'));
$menu_id=$post->menu_id;
$mod_menu_helper=ModMenuHelper::getInstance();
$list=$mod_menu_helper->getList($params);

// The menu class is deprecated. Use nav instead
$children_menu_item = array();
$list = JArrayHelper::pivot($list, 'id');

$list1 = array();
foreach ($list as $item) {
    $item1 = new stdClass();
    $item1->id = $item->id;
    $item1->parent_id = $item->parent_id;
    $item1->title = $item->title;
    $item1->link = $item->link;
    $item1->flink = $item->flink;
    $item1->params = $item->params;
    $list1[] = $item1;
}
foreach ($list1 as $v) {
    $pt = $v->parent_id;
    $temp = new Registry();
    $temp->set('menu_image', $v->params->get('menu_image', ''));
    $temp->set('jv_selection', $v->params->get('jv_selection', ''));
    $v->params = $temp;
    $pt = ($pt == '' || $pt == $v->id) ? 'list_root' : $pt;
    $temp_list = @$children_menu_item[$pt] ? $children_menu_item[$pt] : array();
    array_push($temp_list, $v);
    $children_menu_item[$pt] = $temp_list;
}


$menu_item=$list[$menu_id];
$jv_selection = $menu_item->params->get('jv_selection');
if (trim($jv_selection) != '') {
    $jv_selection = explode(',', $jv_selection);
    jimport('joomla.application.module.helper');
    foreach ($jv_selection as $item_element) {
        $item_element = explode('_', $item_element);
        switch ($item_element[0]) {
            case "module":
                $module_id = $item_element[1];
                $item_module = JModuleHelper::getModuleById($module_id);
                if ($module) {
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
    }
} else { ?>

    <ul class="list_menu_level_2_3">
        <li><label><?php echo JText::_('QUICK_SEARCH') ?><input type="text" class="quick_search"></label></li>
        <?php
        $list_sub_menu = $children_menu_item[$menu_item->id];
        // returns "a", "b", and "c"
        ?>

        <?php foreach ($list_sub_menu as $a_menu_item) { ?>
            <?php $menu_image = $a_menu_item->params->get('menu_image', ''); ?>
            <li class="tags master" data-group_menu_id="<?php echo $a_menu_item->id ?>"
                data-menu_id="<?php echo $a_menu_item->id ?>"><a
                    href="<?php echo $a_menu_item->flink ?>"><?php echo $menu_image ? '<img class="icon icon-level-2" data-src="' . JUri::root() . $menu_image . '">' : '' ?><?php echo $a_menu_item->title ?></a>
            </li>
            <?php
            $list_sub_menu_level_3 = $children_menu_item[$a_menu_item->id];
            $list_sub_menu_level_3 = array_slice($list_sub_menu_level_3, 0, 4);
            ?>
            <?php foreach ($list_sub_menu_level_3 as $a_menu_item_level_3) { ?>
                <li class="tags sub" data-group_menu_id="<?php echo $a_menu_item->id ?>"
                    data-menu_id="<?php echo $a_menu_item_level_3->id ?>"><a
                        href="<?php echo $a_menu_item_level_3->flink ?>"><?php echo $a_menu_item_level_3->title ?></a>
                </li>
            <?php } ?>
        <?php } ?>
    </ul>
<?php } ?>