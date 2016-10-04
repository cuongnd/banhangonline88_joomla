<?php
/*
* Pixel Point Creative - Cinch Menu Module
* License: GNU General Public License version
* See: http://www.gnu.org/copyleft/gpl.html
* Copyright (c) Pixel Point Creative LLC.
* More info at http://www.pixelpointcreative.com
* Last Updated: 3/14/13
*/

defined('_JEXEC') or die('Restricted access');
JFormHelper::loadFieldClass('list');

class JFormFieldProductCategory extends JFormFieldList
{

    public $type = 'productcategory';

    protected function getInput()
    {
        $doc=JFactory::getDocument();
        $doc->addScript(JUri::root().'modules/mod_products/elements/jquery.productcategory.js');
        $doc->addScript(JUri::root().'media/jui/jquery-ui-1.12.0.custom/jquery-ui.js');
        $doc->addLessStyleSheet(JUri::root().'modules/mod_products/elements/productcategory.less');
        $db = JFactory::getDbo();
        JHtml::_('jquery.ui');
        $query = $db->getQuery(true);
        //get template menu type
        $query->clear()
            ->select('category.category_id,category.category_type,category.category_parent_id,category.category_name')
            ->from('#__hikashop_category AS category');
        $db->setQuery($query);
        $list_all_category = $db->loadObjectList('category_id');
        $list_tree_category = array();
        foreach ($list_all_category as $v) {

            $pt = $v->category_parent_id;
            $list = @$list_tree_category[$pt] ? $list_tree_category[$pt] : array();
            array_push($list, $v);
            $list_tree_category[$pt] = $list;
        }
        $tree_data=array();
        $get_tree_data=function($function_call_back, $root_id=0, &$tree_data,$list_all_category, $list_tree_category, $level=0){

            $item_category=$list_all_category[$root_id];
            $item_category->category_name=$item_category->category_name=='ROOT'?'All':$item_category->category_name;
            $item_category->text=str_repeat('---',$level).$item_category->category_name;
            $item_category->id=$item_category->category_id;
            $tree_data[]=$item_category;
            foreach($list_tree_category[$root_id] as $category)
            {
                $root_id1 = $category->category_id;
                $level1=$level+1;
                $function_call_back($function_call_back,$root_id1, $tree_data,$list_all_category,$list_tree_category,$level1);
            }
        };
        $filter=$this->element['filter'];
        $this->filter_type=$filter?$filter:'product';
        $get_tree_ul_li=function($function_call_back, $root_id=1, &$tree_ul_li, $list_all_category, $list_tree_category, $level=0){

            $item_category=$list_all_category[$root_id];
            if($item_category->category_type=='root'||$item_category->category_type==$this->filter_type) {
                $item_category->category_name = $item_category->category_name == 'ROOT' ? 'All' : $item_category->category_name;
                $tree_ul_li .= '<li ><span class="item icon-folder-plus"></span> <input  type="checkbox" '.(in_array($item_category->category_id,$this->value)?'checked':'').' value="'.$item_category->category_id.'" name="'.$this->name.'">' . $item_category->category_name;
                $tree_ul_li .= count($list_tree_category[$root_id]) ? '<ul>' : '';
                foreach ($list_tree_category[$root_id] as $category) {
                    $root_id1 = $category->category_id;
                    $level1 = $level + 1;
                    $function_call_back($function_call_back, $root_id1, $tree_ul_li, $list_all_category, $list_tree_category, $level1);
                }
                $tree_ul_li .= count($list_tree_category[$root_id]) ? '</ul>' : '';
                $tree_ul_li .= '</li>';
            }
        };
        $tree_ul_li='';
        $tree_ul_li.='<ul>';
        $get_tree_ul_li($get_tree_ul_li,1,$tree_ul_li,$list_all_category,$list_tree_category,0);
        $tree_ul_li.='</ul>';
        $html='<div id="product_category_'.$this->id.'" class="product_category">'.$tree_ul_li.'</div>';
        ob_start();
        ?>
        <script type="text/javascript">
            jQuery(document).ready(function($){
                $('#product_category_<?php echo $this->id ?>').productcategory({

                });
            });

        </script>
        <?php
        $script_content = ob_get_clean();
        $script_content = JUtility::remove_string_javascript($script_content);
        $doc->addScriptDeclaration($script_content);


        return $html;
    }
}
