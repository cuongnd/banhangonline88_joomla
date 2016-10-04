<?php

/**
 * Created by PhpStorm.
 * User: cuongnd
 * Date: 9/8/2016
 * Time: 8:57 AM
 */
class mod_search
{
    public static $list_md5;
    protected static $instances =null;
    public static function getInstance()
    {
        if (empty(static::$instances))
        {
            static::$instances = new static();
        }
        return static::$instances;
    }
    public static function get_list_category_product($params){
        $db=JFactory::getDbo();
        $query=$db->getQuery(true);
        require_once JPATH_ROOT.DS.'administrator/components/com_hikashop/helpers/helper.php';
        $categoryHelper = hikashop_get('class.category');
        $list_all_category=$categoryHelper->get_all_category();
        $list_all_category=JArrayHelper::pivot($list_all_category,'category_id');
        $list_tree_category = array();
        foreach ($list_all_category as $v) {
            $pt = $v->category_parent_id;
            $pt = ($pt == '' || $pt == $v->id) ? 'list_root' : $pt;
            $list = @$list_tree_category[$pt] ? $list_tree_category[$pt] : array();
            array_push($list, $v);
            $list_tree_category[$pt] = $list;
        }



        $get_tree_data=function($function_call_back, $root_id=0, &$tree_data, $list_tree_category, $level=0){
            foreach($list_tree_category[$root_id] as $category)
            {
                $root_id1 = $category->category_id;
                if(!in_array($category->category_id,$tree_data))
                {
                    $tree_data[]=$category->category_id;
                }
                $level1=$level+1;
                $function_call_back($function_call_back,$root_id1, $tree_data,$list_tree_category,$level1);
            }
        };
        $list_return_category=array();
        $categories=$params->get('categories','');
        $list_all_category_id[]=0;
        foreach($categories as $cat_id)
        {
            $tree_data=array();
            $tree_data[]=$cat_id;
            $get_tree_data($get_tree_data,$cat_id,$tree_data,$list_tree_category,0);
            $list_all_category_id=array_merge($list_all_category_id,$tree_data);
            $list_return_category[$cat_id]->list_category=$tree_data;
            $list_return_category[$cat_id]->detail=$list_all_category[$cat_id];
        }

        $query->clear()
            ->select('product.product_id,product_category.category_id,product.product_name,product.product_code')
            ->from('#__hikashop_product AS product')
            ->leftJoin('#__hikashop_product_category AS product_category ON product_category.product_id=product.product_id')
            ->where('product_category.category_id IN('.implode(',',$list_all_category_id).')')
            ->where('product.product_published=1')
            ->leftJoin('#__hikashop_file AS file ON file.file_ref_id=product.product_id')
            ->where('file.file_type='.$query->q('product'))
            ->select('GROUP_CONCAT(file.file_path SEPARATOR  ";") AS list_image')


            ->leftJoin('#__hikashop_price AS price ON price.price_product_id=product.product_id')
            ->select('price.price_value')
            ->group('product.product_id')

        ;


        $order_by=$params->get('order_by','best_sale');
        $sort_type=$params->get('sort_type','ASC');
        if($order_by=='best_sale'){
            $query->order("product_sales $sort_type");
        }elseif($order_by=='last_product'){
            $query->order("product_created $sort_type");
        }elseif($order_by=='new_update'){
            $query->order("product_modified $sort_type");
        }elseif($order_by=='random'){
            $query->order("RAND()");
        }elseif($order_by=='hot'){
            $query->order("product_total_vote $sort_type");
        }elseif($order_by=='hit'){
            $query->order("product_hit $sort_type");
        }
        $manufacturer=$params->get('manufacturer','');


        $md5_query=md5($query);
        if(!isset(static::$list_md5[$md5_query])){
            $db->setQuery($query);
            static::$list_md5[$md5_query]=$db->loadObjectList();
        }
        $list_product=static::$list_md5[$md5_query];
        $max_product=$params->get('max_product',20);
        foreach($list_product as $product)
        {
            foreach($list_return_category as $key => $item)
            {
                $list_category=$item->list_category;
                if(in_array($product->category_id,$list_category))
                {
                    $list_return_category[$key]->list=!is_array($list_return_category[$key]->list)?array():$list_return_category[$key]->list;

                    if(is_array($list_return_category[$key]->list)&& count($list_return_category[$key]->list)<=20)
                    {
                        $list_return_category[$key]->list[]=$product;

                    }
                }
            }
        }
        return $list_return_category;

    }
}