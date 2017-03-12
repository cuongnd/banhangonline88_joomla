<?php/** * @package     Joomla.Site * @subpackage  mod_menu * * @copyright   Copyright (C) 2005 - 2016 Open Source Matters, Inc. All rights reserved. * @license     GNU General Public License version 2 or later; see LICENSE.txt */use Joomla\Registry\Registry;require_once JPATH_ROOT . DS . 'administrator/components/com_hikashop/helpers/helper.php';$image=hikashop_get('helper.image');defined('_JEXEC') or die;$max_items_root = $params->get('max_items_root', 0);$doc = JFactory::getDocument();JHtml::_('jquery.scrollbar');JHtml::_('jQuery.utility');JHtml::_('jQuery.lazyload');$image = hikashop_get('helper.image');$id = '';if (($tagId = $params->get('tag_id', ''))) {    $id = ' id="' . $tagId . '"';}$base = $params->get('base', 1);$menu_show = $params->get('menu_show', array());if (in_array(0, $menu_show)) {    $menu_show = array();}$mod_menu_helper=ModMenuHelper::getInstance();$list=$mod_menu_helper->getList($params);// The menu class is deprecated. Use nav instead$children_menu_item = array();$list = JArrayHelper::pivot($list, 'id');$list1 = array();foreach ($list as $item) {    $item1 = new stdClass();    $item1->id = $item->id;    $item1->parent_id = $item->parent_id;    $item1->title = $item->title;    $item1->link = $item->link;    $item1->flink = $item->flink;    $item1->params = $item->params;    $list1[] = $item1;}foreach ($list1 as $v) {    $pt = $v->parent_id;    $temp = new Registry();    $temp->set('menu_image', $v->params->get('menu_image', ''));    $temp->set('jv_selection', $v->params->get('jv_selection', ''));    $v->params = $temp;    $pt = ($pt == '' || $pt == $v->id) ? 'list_root' : $pt;    $temp_list = @$children_menu_item[$pt] ? $children_menu_item[$pt] : array();    array_push($temp_list, $v);    $children_menu_item[$pt] = $temp_list;}$list_color = array('#d60c0c', '#ff9800', '#7cb342', '#2bafa4', '#105aa6', '#ca64c2', '#f57aa5', '#ddaa62');ob_start();?>    <div id="homeverticalmenutag_category_<?php echo $module->id ?>" class="hidden-xs-to-lg device-xxxs-to-xxs collapse">        <ul  class=" level-0" id="accordion_<?php echo $module->id ?>" role="tablist" aria-multiselectable="true">            <?php foreach ($children_menu_item[$base] as $menu_item) { ?>                <?php                $menu_image = $menu_item->params->get('menu_image', '');                //$menu_image=JFile::exists(JPATH_ROOT.DS.$menu_image)?JUtility::createThumbs_image(JPATH_ROOT.DS.$menu_image,array('20x20')):false;                //optimized file overwrites original one                ?>                <li class="menu-iem menu-iem-<?php echo $menu_item->id ?> panel none level-1"                    data-menu_id="<?php echo $menu_item->id ?>"                    data-color="<?php echo $list_color[array_rand($list_color)] ?>"><a                        href="javascript:void(0)" role="tab" data-parent="#accordion_<?php echo $module->id ?>" data-toggle="collapse" data-target="#container_sub_<?php echo $menu_item->id ?>" aria-expanded="false" aria-controls="container_sub_<?php echo $menu_item->id ?>" ><?php echo $menu_image ? '<img src="' .JRoute::_("/".$menu_image) . '">' : '' ?><?php echo $menu_item->title ?><i class="icon pull-right glyphicon glyphicon-plus"></i></a>                    <!--load sub menu-->                    <div id="container_sub_<?php echo $menu_item->id ?>" class="container-home-page panel-collapse collapse" >                        <div id="container-<?php echo $menu_item->id ?>" data-group_menu_item_id="<?php echo $menu_item->id ?>"                             class="container-content hidden-phone">                            <?php                            //require JModuleHelper::getLayoutPath('mod_menu', 'homeverticalmenutag_subcontent');                            ?>                        </div>                    </div>                    <!--end load sub menu-->                </li>            <?php } ?>        </ul>    </div>    <div class="hidden-xxxs-to-xxs device-xs-to-lg">        <ul id="homeverticalmenutag_category_<?php echo $module->id ?>" class=" level-0">            <?php foreach ($children_menu_item[$base] as $menu_item) { ?>                <?php                $menu_image = $menu_item->params->get('menu_image', '');                //$menu_image=JFile::exists(JPATH_ROOT.DS.$menu_image)?JUtility::createThumbs_image(JPATH_ROOT.DS.$menu_image,array('20x20')):false;                ?>                <li class="menu-iem menu-iem-<?php echo $menu_item->id ?> level-1"                    data-menu_id="<?php echo $menu_item->id ?>"                    data-color="<?php echo $list_color[array_rand($list_color)] ?>"><a                        href="<?php echo $menu_item->flink ?>"><?php echo $menu_image ? '<img src="' .JRoute::_("/".$menu_image). '">' : '' ?><?php echo $menu_item->title ?></a>                    <!--load sub menu-->                    <div class="container-home-page">                        <div id="container-<?php echo $menu_item->id ?>"  data-group_menu_item_id="<?php echo $menu_item->id ?>"                             class="container-content hidden-phone">                            <?php                            //require JModuleHelper::getLayoutPath('mod_menu', 'homeverticalmenutag_xstolg');                            ?>                        </div>                    </div>                    <!--end load sub menu-->                </li>            <?php } ?>        </ul>    </div><?php$html = ob_get_clean();echo $html;$uri = JFactory::getUri();ob_start();?>    <script type="text/javascript">        jQuery(document).ready(function ($) {            $("#mod_menu_<?php echo $module->id ?>").homeverticalmenutag({                module_id:<?php echo $module->id   ?>            });        });    </script><?php$js_content = ob_get_clean();$js_content = JUtility::remove_string_javascript($js_content);$doc->addScriptDeclaration($js_content);