<?php
use Joomla\Registry\Registry;
$user=JFactory::getUser();
$params=new Registry();
$config=JFactory::getConfig();
$app = JFactory::getApplication();
$menu = $app->getMenu();
$list = $menu->get_items_by_menu_type($config->get('menutype_panel'));
$active_menu = $menu->getActive() ? $menu->getActive() : $menu->getDefault();
$menu_show = array();

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
    $item1->icon = $item->icon;
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
$get_tree_ul_li = function ($function_call_back, $root_id = 1, &$tree_ul_li, $list_tree_menu, $active_menu, $level = 0) {
    foreach ($list_tree_menu[$root_id] as $menu_item) {
        $icon = $menu_item->icon;
        $active_class = $menu_item->id == $active_menu->id ? ' active ' : '';
        $total=count($list_tree_menu[$menu_item->id]);
        ob_start();
        ?>
        <li class="menu-iem  <?php echo $active_class ?> menu-iem-<?php echo $menu_item->id  ?> level-<?php echo $level ?> <?php echo $total?' treeview ':'' ?>  " >
            <a class="<?php echo $active_class ?>" data-menu_id="<?php echo $menu_item->id  ?>"  href="<?php echo $total?'#':$menu_item->link ?>">
                <?php if($icon){ ?>
                    <i class="<?php echo $icon ?>"></i>
                <?php } ?>
                <span class="title"><?php echo $menu_item->title ?></span>
                <span class="pull-right-container">
                    <small class="label pull-right bg-red"><i class="fa fa-question"></i></small>
                    <?php if($total){ ?>
                        <small class="label pull-right bg-blue"><?php echo $total ?></small>
                    <?php } ?>
                </span>
            </a>
        <?php
        $tree_ul_li .=ob_get_clean();

        if($total){
            $root_id1 = $menu_item->id;
            $level1 = $level + 1;
            $tree_ul_li.='<ul class="level-' . $level1 . ' treeview-menu ">';
            $function_call_back($function_call_back, $root_id1, $tree_ul_li, $list_tree_menu, $active_menu, $level1);
            $tree_ul_li.='</ul>';

        }
        $tree_ul_li .= '</li>';
    }
};
$tree_ul_li = '';
$get_tree_ul_li($get_tree_ul_li, 1, $tree_ul_li, $children_menu_item, $active_menu);

?>
<section class="sidebar">
    <!-- Sidebar user panel -->
    <div class="user-panel">
        <div class="pull-left image">
            <img src="<?php echo JUri::root() ?>libraries/cms/html/dashboard/img/user2-160x160.jpg" class="img-circle" alt="User Image">
        </div>
        <div class="pull-left info">
            <p><?php echo $user->name ?></p>
            <a href="#"><i class="fa fa-circle text-success"></i> Online</a>
        </div>
    </div>
    <!-- search form -->
    <form action="#" method="get" class="sidebar-form">
        <div class="input-group">
            <input type="text" name="q" class="form-control" placeholder="Search...">
          <span class="input-group-btn">
                <button type="submit" name="search" id="search-btn" class="btn btn-flat"><i class="fa fa-search"></i>
                </button>
              </span>
        </div>
    </form>
    <!-- /.search form -->

    <!-- sidebar menu: : style can be found in sidebar.less -->
    <ul class="sidebar-menu" data-widget="tree">
        <li class="header">MAIN NAVIGATION</li>
        <?php
        echo $tree_ul_li;
        ?>

    </ul>
</section>
