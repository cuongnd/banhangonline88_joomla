<?php
use Joomla\Registry\Registry;
require_once JPATH_ROOT . DS . 'administrator/components/com_hikashop/helpers/helper.php';
$image=hikashop_get('helper.image');
defined('_JEXEC') or die;
$max_items_root = $params->get('max_items_root', 0);
$doc = JFactory::getDocument();
$image = hikashop_get('helper.image');
$id = '';
if (($tagId = $params->get('tag_id', ''))) {
$id = ' id="' . $tagId . '"';
}
$base = $params->get('base', 1);
$menu_show = $params->get('menu_show', array());
if (in_array(0, $menu_show)) {
$menu_show = array();
}
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
$treerecurse =function ($function_call_back,$id, $indent, &$list, &$children, $maxlevel = 9999, $level = 0, $type = 1)
{
    if (@$children[$id] && $level <= $maxlevel)
    {
        foreach ($children[$id] as $v)
        {
            $id = $v->id;
            if ($type)
            {
                $pre = '<sup>|_</sup>&#160;';
                $spacer = '.&#160;&#160;&#160;&#160;&#160;&#160;';
            }
            else
            {
                $pre = '- ';
                $spacer = '&#160;&#160;';
            }
            if ($v->parent_id == 0)
            {
                $txt = $v->title;
            }
            else
            {
                $txt = $pre . $v->title;
            }
            $item_store=$v;
            $item_store->treename = $indent . $txt;
            $item_store->total_sub_menu = count($children[$id]);
            $item_store->children = array();
            $function_call_back($function_call_back,$id, $indent . $spacer, $item_store->children, $children, $maxlevel, $level + 1, $type);
            $list[]=$item_store;
        }
    }
    return $list;
};
$list=array();
$treerecurse($treerecurse,$base,"",$list,$children_menu_item,0);
echo json_encode($list);?>