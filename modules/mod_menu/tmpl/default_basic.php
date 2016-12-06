<?php
$doc = JFactory::getDocument();
$doc->addLessStyleSheet(JUri::root() . 'modules/mod_menu/assests/less/default.less');
$doc->addScript(JUri::root().'modules/mod_menu/assests/js/mod_menu_default.js');
$doc = JFactory::getDocument();
JHtml::_('jQuery.lazyload');
$menu = JFactory::getApplication()->getMenu();
$active_menu = $menu->getActive() ? $menu->getActive() : $menu->getDefault();
defined('_JEXEC') or die;
use Joomla\Registry\Registry;

$max_items_root = $params->get('max_items_root', 0);
$base = $params->get('base', 1);
$showAllChildren = $params->get('showAllChildren', 0);
$class_sfx = $params->get('class_sfx', "");
$id = '';
if (($tagId = $params->get('tag_id', ''))) {
    $id = ' id="' . $tagId . '"';
}
$menu_show = $params->get('menu_show', array());
if (in_array(0, $menu_show)) {
    $menu_show = array();
}
// The menu class is deprecated. Use nav instead
$children_menu_item = array();
$mod_menu_helper=ModMenuHelper::getInstance();
$list=$mod_menu_helper->getList($params);
$list1 = JArrayHelper::pivot($list, 'id');
foreach ($list1 as $v) {
    $pt = $v->parent_id;
    /*	$temp=new Registry();
        $temp->set('menu_image',$v->params->get('menu_image',''));
        $v->params=$temp;*/
    $pt = ($pt == '' || $pt == $v->id) ? 'list_root' : $pt;
    $temp_list = @$children_menu_item[$pt] ? $children_menu_item[$pt] : array();
    array_push($temp_list, $v);
    $children_menu_item[$pt] = $temp_list;
}
$params=$module->params;

$lazyload=(boolean)$params->get('lazyload',false);

$get_tree_ul_li = function ($function_call_back, $root_id = 1, &$tree_ul_li, $list_all_menu, $list_tree_menu, $menu_show, $active_menu, $level = 0) {
    $menu_item = $list_all_menu[$root_id];
    $active_class = $menu_item->id == $active_menu->id ? ' active ' : '';
    if ($menu_item) {
        $menu_image = $menu_item->params->get('menu_image', '');
        $tree_ul_li .= '<li class="menu-iem ' . $active_class . ' menu-iem-' . $menu_item->id . ' level-' . $level . '" ><a class="' . $active_class . '" data-menu_id="' . $menu_item->id . '"  href="' . $menu_item->flink . '">' . ($menu_image ? '<img class="icon icon-' . $menu_image->id . '" data-src="' . JUri::root() . $menu_image . '">' : '') . '<span class="title">' . $menu_item->title . '</span></a>';
        $tree_ul_li .= count($list_tree_menu[$root_id]) ? '<ul class="level-' . $level . '">' : '';
    }
    foreach ($list_tree_menu[$root_id] as $a_menu_item) {
        if (count($menu_show) > 1 && !in_array($a_menu_item->id, $menu_show)) {
            continue;
        }
        $root_id1 = $a_menu_item->id;
        $level1 = $level + 1;
        $function_call_back($function_call_back, $root_id1, $tree_ul_li, $list_all_menu, $list_tree_menu, $menu_show, $active_menu, $level1);
    }
    if ($menu_item) {
        $tree_ul_li .= count($list_tree_menu[$root_id]) ? '</ul>' : '';
        $tree_ul_li .= '</li>';
    }
};
$tree_ul_li = '';
$tree_ul_li .= '<ul class="level-0">';
$get_tree_ul_li($get_tree_ul_li, $base, $tree_ul_li, $list1, $children_menu_item, $menu_show, $active_menu, 0);
$tree_ul_li .= '</ul>';
?>
<h3 class="title"><?php echo $module->title ?></h3>
<?php echo $tree_ul_li; ?>