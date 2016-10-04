<?php
/**
 * @package    HikaShop for Joomla!
 * @version    2.6.3
 * @author    hikashop.com
 * @copyright    (C) 2010-2016 HIKARI SOFTWARE. All rights reserved.
 * @license    GNU/GPLv3 http://www.gnu.org/licenses/gpl-3.0.html
 */
defined('_JEXEC') or die('Restricted access');
$doc = JFactory::getDocument();
JHtml::_('jquery.ui');
$doc->addScript(JUri::root().'media/jui/jquery-ui-1.12.0.custom/jquery-ui.js');
$doc->addScript(JUri::root() . 'administrator/components/com_hikashop/assests/js/jquery.view_explorer.js');
$doc->addScript(JUri::root() . 'media/system/js/jquery-cookie-master/src/jquery.cookie.js');
$doc->addLessStyleSheet(JUri::root() . 'administrator/components/com_hikashop/assests/less/view_explorer.less');
?>
    <div class="view-explorer">
        <div
            style="border-top: 1px solid rgb(204, 204, 204); border-bottom: 1px solid rgb(204, 204, 204); background: rgb(221, 225, 230) none repeat scroll 0% 0%; -moz-background-clip: border; -moz-background-origin: padding; -moz-background-inline-policy: continuous; font-weight: bold;margin-bottom:1px"><?php echo JText::_('EXPLORER'); ?></div>
        <?php
        $app=JFactory::getApplication();
        $input=$app->input;
        $control = JRequest::getCmd('control');
        $control1 = JRequest::getCmd('ctrl');
        $filter_id = $input->getInt('filter_id',0);
        if (!empty($control)) {
            $control = '&control=' . $control;
        }
        $tree = hikashop_get('type.categorysub');
        $type = null;
        if ($this->type == 'status')
            $type = array('status');
        $user = JFactory::getUser();
        $db = JFactory::getDbo();
        JHtml::_('jquery.ui');
        $query = $db->getQuery(true);
        //get template menu type
        $query->clear()
            ->select('category.category_id,category.category_type,category.category_parent_id,category.category_name,COUNT(product_category.product_id) AS total_product')
            ->from('#__hikashop_category AS category')
            ->leftJoin('#__hikashop_product_category AS product_category ON product_category.category_id=category.category_id')
            ->group('category.category_id')
        ;
        $db->setQuery($query);
        $list_all_category = $db->loadObjectList('category_id');
        $list_tree_category = array();
        foreach ($list_all_category as $v) {

            $pt = $v->category_parent_id;
            $list = @$list_tree_category[$pt] ? $list_tree_category[$pt] : array();
            array_push($list, $v);
            $list_tree_category[$pt] = $list;
        }
        $tree_data = array();
        $get_tree_data = function ($function_call_back, $root_id = 0, &$tree_data, $list_all_category, $list_tree_category, $level = 0) {

            $item_category = $list_all_category[$root_id];
            $item_category->category_name = $item_category->category_name == 'ROOT' ? 'All' : $item_category->category_name;
            $item_category->text = str_repeat('---', $level) . $item_category->category_name;
            $item_category->id = $item_category->category_id;
            $tree_data[] = $item_category;
            foreach ($list_tree_category[$root_id] as $category) {
                $root_id1 = $category->category_id;
                $level1 = $level + 1;
                $function_call_back($function_call_back, $root_id1, $tree_data, $list_all_category, $list_tree_category, $level1);
            }
        };
        $filter = $this->element['filter'];
        $this->filter_type = $filter ? $filter : 'product';
        $get_tree_ul_li = function ($function_call_back, $root_id = 1, &$tree_ul_li, $list_all_category, $list_tree_category,$control, $level = 0) {

            $item_category = $list_all_category[$root_id];
            if ($item_category->category_type == 'root' || $item_category->category_type == $this->filter_type) {
                $item_category->category_name = $item_category->category_name == 'ROOT' ? 'All' : $item_category->category_name;
                $display=$level>=1?'none':'block';
                $tree_ul_li .= '<li class="item-category level-'.$level.' item-category-'.$item_category->category_id.'" data-category_id="'.$item_category->category_id.'"  data-total_product="'.$item_category->total_product.'"  ><span data-category_id="'.$item_category->category_id.'" class="item level-'.$level.' icon-folder-plus"></span><a href="index.php?option=com_hikashop&ctrl='.$control.'&task=listing&type=product&filter_id='.$item_category->category_id.'">' . $item_category->category_name.' [<span class="total-product">cur:'.$item_category->total_product.'</span>,<span class="sub-total-product"></span>,<span class="all-total-product"></span>]</a>';

                $tree_ul_li .= count($list_tree_category[$root_id]) ? '<ul class="level-'.$level.'" style="display: '.$display.'">' : '';
                foreach ($list_tree_category[$root_id] as $category) {
                    $root_id1 = $category->category_id;
                    $level1 = $level + 1;
                    $function_call_back($function_call_back, $root_id1, $tree_ul_li, $list_all_category, $list_tree_category, $control,$level1);
                }
                $tree_ul_li .= count($list_tree_category[$root_id]) ? '</ul>' : '';
                $tree_ul_li .= '</li>';
            }
        };
        $tree_ul_li = '';
        $tree_ul_li .= '<ul>';
        $get_tree_ul_li($get_tree_ul_li, 1, $tree_ul_li, $list_all_category, $list_tree_category,$control1, 0);
        $tree_ul_li .= '</ul>';
        $html = '<div  class="category-explorer">' . $tree_ul_li . '</div>';
        echo $html;
        ?>
    </div>
<?php
ob_start();
?>
    <script type="text/javascript">
        jQuery(document).ready(function ($) {
            $('.view-explorer').view_explorer({
                filter_id:<?php echo $filter_id ?>

            });
        });

    </script>
<?php
$script_content = ob_get_clean();
$script_content = JUtility::remove_string_javascript($script_content);
$doc->addScriptDeclaration($script_content);